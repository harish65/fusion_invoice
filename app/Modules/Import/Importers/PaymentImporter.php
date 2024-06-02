<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Import\Importers;

use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\PaymentMethods\Models\PaymentMethod;
use FI\Modules\Payments\Models\Payment;
use FI\Modules\Payments\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'date'           => '* ' . trans('fi.date'),
            'invoice_number' => '* ' . trans('fi.invoice_number'),
            'amount'         => '* ' . trans('fi.amount'),
            'payment_method' => '* ' . trans('fi.payment_method'),
            'note'           => trans('fi.note'),
        ];

        foreach (CustomField::forTable('payments')->get() as $customField)
        {
            $fields['custom_' . $customField->column_name] = $customField->field_label;
        }
        return $fields;
    }

    public function getMapRules()
    {
        return [
            'date'           => 'required',
            'invoice_number' => 'required',
            'amount'         => 'required',
            'payment_method' => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
            'paid_at'           => 'required',
            'invoice_id'        => 'required|exists:invoices,id',
            'amount'            => 'required|numeric',
            'payment_method_id' => 'required',
        ]);
    }

    public function importData($input)
    {
        $this->file = storage_path('payments.csv');
        $row        = 1;

        $fields = $customFields = [];
        $userId = auth()->user()->id;

        // Assume payment has a one to one relation to the invoice and that the payment was completely used up going toward the invoice. 
        $type             = 'single';
        $remainingBalance = 0.00;
        $response         = ['success' => false, 'total_records' => 0, 'message' => ''];
        try
        {
            foreach ($input as $field => $key)
            {
                if ($key != '')
                {
                    if (substr($field, 0, 7) != 'custom_')
                    {
                        $fields[$key] = $field;
                    }
                    else
                    {
                        $customFields[substr($field, 7)] = $key;
                    }
                }
            }

            $handle = fopen($this->file, 'r');

            if (!$handle)
            {
                $this->messages->add('error', 'Could not open the file');

                return $response;
            }

            $validationResponse = $this->validateTotalRecords($this->file);

            if ($validationResponse !== 'ok')
            {
                $response['message'] = $validationResponse;
                $this->messages->add('error', $response['message']);
                return $response;
            }

            // Completed: user_id (auth user), client_id, type='single', remaining_balance
            // Need to populate payment_invoices table: payment_id, invoice_id, invoice_amount_paid

            DB::transaction(function () use (&$handle, &$fields, &$response, &$row, &$userId, &$type, &$customFields, &$remainingBalance)
            {
                while (($data = fgetcsv($handle, $this->max_records, ',')) !== false)
                {
                    if ($row !== 1 && $data !== array(null))
                    {
                        $record = $piRecord = [];

                        foreach ($fields as $key => $field)
                        {
                            $record[$field] = trim($data[$key]);
                        }

                        // Attempt to format the date, otherwise use today
                        if (isset($record['date']) && strtotime($record['date']))
                        {
                            $record['paid_at'] = date('Y-m-d', strtotime($record['date']));
                        }
                        else
                        {
                            $record['paid_at'] = date('Y-m-d');
                        }
                        unset($record['date']);

                        // Transform the invoice number to the id
                        $record['invoice_id'] = Invoice::where('number', $record['invoice_number'])->first()->id;

                        // Fetch the client_id from the invoice
                        $record['client_id'] = Invoice::where('id', $record['invoice_id'])->first()->client_id;
                        unset($record['invoice_number']);
                        // Transform the payment method to the id
                        if ($record['payment_method'] <> 'NULL')
                        {
                            $record['payment_method_id'] = PaymentMethod::firstOrCreate(['name' => $record['payment_method']])->id;
                        }
                        else
                        {
                            $record['payment_method_id'] = PaymentMethod::firstOrCreate(['name' => 'Other'])->id;
                        }

                        if (!isset($record['note']))
                        {
                            $record['note'] = '';
                        }

                        // Assign the invoice to the current logged in user
                        $record['user_id']           = $userId;
                        $record['type']              = $type;
                        $record['remaining_balance'] = $remainingBalance;

                        if ($this->validateRecord($record, $row))
                        {
                            $payment                   = Payment::create($record);
                            $response['total_records'] = ($response['total_records'] + 1);
                            if ($payment)
                            {
                                // Create the payment_invoice record info
                                $piRecord['payment_id']          = $payment->id;
                                $piRecord['invoice_id']          = $record['invoice_id'];
                                $piRecord['invoice_amount_paid'] = $record['amount'];
                                $piRecord['convenience_charges'] = 0;
                                PaymentInvoice::create($piRecord);
                            }

                            if ($customFields)
                            {
                                foreach ($customFields as $field => $key)
                                {
                                    if (isset($data[$key]))
                                    {
                                        $customRecord[$field] = $data[$key];
                                    }
                                }

                                $payment->custom->update($customRecord);

                            }
                        }
                        else
                        {
                            $response['message'] = $this->messages;
                            $this->errors        = true;
                            return $response;
                        }
                    }
                    $row++;
                }
            });

            fclose($handle);

            if ($this->errors != true && $response['total_records'] == 0)
            {
                $response['success'] = false;
                $this->errors        = true;
                $response['message'] = trans('fi.no_import_records');
                $this->messages->add('error', $response['message']);
            }

            if ($this->errors != true)
            {
                $response['success'] = true;
                $this->errors        = false;
            }
        }
        catch (\Throwable $e)
        {
            Log::error($e->getMessage());
            $response['message'] = $e->getMessage();
            $this->messages->add('error', $e->getMessage());
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
            $response['message'] = $e->getMessage();
            $this->messages->add('error', $e->getMessage());
        }

        return $response;
    }
}