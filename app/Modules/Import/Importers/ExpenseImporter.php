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
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Expenses\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'expense_date'    => '* ' . trans('fi.date'),
            'vendor_name'     => '* ' . trans('fi.vendor_name'),
            'amount'          => '* ' . trans('fi.amount'),
            'description'     => trans('fi.description'),
            'category_name'   => trans('fi.category'),
            'client_name'     => trans('fi.client'),
            'tax'             => trans('fi.tax'),
            'company_profile' => trans('fi.company_profile'),
        ];

        foreach (CustomField::forTable('expenses')->get() as $customField)
        {
            $fields['custom_' . $customField->column_name] = $customField->field_label;
        }

        return $fields;
    }

    public function getMapRules()
    {
        return [
            'expense_date'  => 'required',
            'vendor_name'   => 'required',
            'amount'        => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
            'expense_date'  => 'required',
            'vendor_name'   => 'required',
            'amount'        => 'required|numeric',
        ])->setAttributeNames([
            'user_id'            => trans('fi.user'),
            'company_profile_id' => trans('fi.company_profile'),
            'expense_date'       => trans('fi.date'),
            'category_name'      => trans('fi.category'),
            'description'        => trans('fi.description'),
            'amount'             => trans('fi.amount'),
        ]);
    }

    public function importData($input)
    {
        $this->file = storage_path('expenses.csv');
        $row             = 1;
        $fields          = $customFields = [];
        $companyProfiles = CompanyProfile::get();
        $response        = ['success' => false, 'total_records' => 0, 'message' => ''];

        try
        {
            foreach ($input as $key => $field)
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

            DB::transaction(function () use (&$handle, &$fields, &$response, &$row, &$companyProfiles, &$customFields)
            {
                while (($data = fgetcsv($handle, $this->max_records, ',')) !== false)
                {
                    if ($row !== 1 && $data !== array(null))
                    {
                        $record = $customRecord = [];

                        foreach ($fields as $field => $key)
                        {
                            $record[$field] = (isset($data[$key])) ? utf8_encode($data[$key]) : "";
                        }

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

                        if ($this->validateRecord($record, $row))
                        {
                            $expense = Expense::create($record);

                            if ($customFields)
                            {
                                foreach ($customFields as $field => $key)
                                {
                                    if (isset($data[$key]))
                                    {
                                        $customRecord[$field] = $data[$key];
                                    }
                                }

                                $expense->custom->update($customRecord);

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