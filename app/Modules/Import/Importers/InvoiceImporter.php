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

use FI\Modules\Clients\Models\Client;
use FI\Modules\CustomFields\Models\CustomField;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\DocumentNumberSchemes\Models\DocumentNumberScheme;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\DateFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'invoice_date'              => '* ' . trans('fi.date'),
            'client_name'               => '* ' . trans('fi.client_name'),
            'invoice_number'            => '* ' . trans('fi.invoice_number'),
            'company_profile'           => trans('fi.company_profile'),
            'due_at'                    => trans('fi.due_date'),
            'summary'                   => trans('fi.summary'),
            'terms'                     => trans('fi.terms_and_conditions'),
            'footer'                    => trans('fi.footer'),
        ];

        foreach (CustomField::forTable('invoices')->get() as $customField)
        {
            $fields['custom_' . $customField->column_name] = $customField->field_label;
        }
        return $fields;
    }

    public function getMapRules()
    {
        return [
            'invoice_date'   => 'required',
            'client_name'    => 'required',
            'invoice_number' => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
                'client_id'          => 'required',
                'company_profile_id' => 'required|integer',
                'number'             => 'required|unique:invoices,number',
            ]
        );
    }

    public function importData($input)
    {
        $this->file           = storage_path('invoices.csv');
        $row                  = 1;
        $fields               = $customFields = [];
        $companyProfiles      = CompanyProfile::get();
        $documentNumberScheme = DocumentNumberScheme::get();
        $userId               = auth()->user()->id;
        $type                 = 'invoice';
        $response             = ['success' => false, 'total_records' => 0, 'message' => ''];

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

            DB::transaction(function () use (&$handle, &$fields, &$response, &$row, &$companyProfiles, &$documentNumberScheme, &$userId, &$customFields, &$type)
            {
                while (($data = fgetcsv($handle, $this->max_records, ',')) !== false)
                {
                    if ($row !== 1 && $data !== array(null))
                    {
                        $record = $customRecord = [];

                        // Create the initial record from the file line
                        foreach ($fields as $key => $field)
                        {
                            $record[$field] = trim($data[$key]);
                        }
                        // Replace the client name with the client id
                        if ($client = Client::findByName($record['client_name']))
                        {
                            $record['client_id'] = $client->id;
                        }
                        else
                        {
                            $record['client_id'] = Client::create(['name' => $record['client_name']])->id;
                        }

                        unset($record['client_name']);

                        // Replace the company profile name with the company profile id
                        if (isset($record['company_profile']) && !empty($record['company_profile']))
                        {
                            $companyProfile = $companyProfiles->where('company', $record['company_profile'])->first();
                        }
                        else
                        {
                            $companyProfile = $companyProfiles->where('is_default', 1)->first();
                        }

                        if ($companyProfile)
                        {
                            $record['company_profile_id'] = $companyProfile->id;
                        }

                        unset($record['company_profile']);
                        $record['number'] = $record['invoice_number'];
                        unset($record['invoice_number']);
                        // Format the invoice date
                        if (strtotime($record['invoice_date']))
                        {
                            $record['invoice_date'] = date('Y-m-d', strtotime($record['invoice_date']));
                        }

                        // Attempt to format this date if it exists.
                        if (isset($record['due_at']) and $record['due_at'] != "" and strtotime($record['due_at']))
                        {
                            $record['due_at'] = date('Y-m-d', strtotime($record['due_at']));
                        }
                        else
                        {
                            $record['due_at'] = DateFormatter::incrementDateByDays($record['invoice_date'], config('fi.invoicesDueAfter'));
                        }

                        // Attempt to convert the group name to an id if it exists.
                        if (isset($record['document_number_scheme_id']) && $record['document_number_scheme_id'] != '')
                        {
                            $documentNumberScheme = $documentNumberScheme->where('name', $record['document_number_scheme_id'])->first();

                            if ($documentNumberScheme)
                            {
                                $record['document_number_scheme_id'] = $documentNumberScheme->id;
                            }

                        }

                        // Assign the invoice to the current logged in user
                        $record['user_id'] = $userId;

                        // Assume Type is invoice
                        $record['type'] = $type;

                        // The record *should* validate, but just in case...
                        if ($this->validateRecord($record, $row))
                        {
                            $invoice = Invoice::create($record);

                            if ($customFields)
                            {
                                foreach ($customFields as $field => $key)
                                {
                                    if (isset($data[$key]))
                                    {
                                        $customRecord[$field] = $data[$key];
                                    }
                                }

                                $invoice->custom->update($customRecord);

                            }
                            $response['total_records'] = ($response['total_records'] + 1);
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