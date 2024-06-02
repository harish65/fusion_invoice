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
use FI\Modules\ItemLookups\Models\ItemCategory;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItemLookupImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'name'          => '* ' . trans('fi.name'),
            'description'   => '* ' . trans('fi.description'),
            'price'         => '* ' . trans('fi.price'),
            'tax_rate_id'   => trans('fi.tax_1'),
            'tax_rate_2_id' => trans('fi.tax_2'),
            'item_category' => trans('fi.item_category'),
        ];

        foreach (CustomField::forTable('item_lookups')->get() as $customField)
        {
            $fields['custom_' . $customField->column_name] = $customField->field_label;
        }
        return $fields;
    }

    public function getMapRules()
    {
        return [
            'name'        => 'required',
            'description' => 'required',
            'price'       => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
                'name'  => 'required|unique:item_lookups',
                'price' => 'required|numeric',
            ]
        );
    }

    public function importData($input)
    {
        $this->file = storage_path('itemLookups.csv');
        $row        = 1;

        $fields = $customFields = [];

        $taxRates = TaxRate::get();
        $response = ['success' => false, 'total_records' => 0, 'message' => ''];
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

            DB::transaction(function () use (&$handle, &$fields, &$response, &$taxRates, &$customFields, &$row)
            {
                while (($data = fgetcsv($handle, $this->max_records, ',')) !== false)
                {
                    if ($row !== 1 && $data !== array(null))
                    {
                        $record = [];

                        foreach ($fields as $key => $field)
                        {
                            $record[$field] = utf8_encode($data[$key]);
                        }

                        if (!isset($record['tax_1']))
                        {
                            $record['tax_rate_id'] = 0;
                        }
                        else
                        {
                            if ($taxRate = $taxRates->where('name', $record['tax_1'])->first())
                            {
                                $record['tax_rate_id'] = $taxRate->id;
                            }
                            else
                            {
                                $record['tax_rate_id'] = 0;
                            }
                            unset($record['tax_1']);
                        }

                        if (!isset($record['tax_2']))
                        {
                            $record['tax_rate_2_id'] = 0;
                        }
                        else
                        {
                            if ($taxRate = $taxRates->where('name', $record['tax_2'])->first())
                            {
                                $record['tax_rate_2_id'] = $taxRate->id;
                            }
                            else
                            {
                                $record['tax_rate_2_id'] = 0;
                            }
                            unset($record['tax_2']);
                        }


                        if (isset($record['item_category']) && $record['item_category'] != '')
                        {
                            $record['category_id'] = ItemCategory::firstOrCreate(['name' => $record['item_category']])->id;
                        }

                        unset($record['item_category']);

                        if ($this->validateRecord($record, $row))
                        {
                            $itemLookup = ItemLookup::create($record);

                            if ($customFields)
                            {
                                foreach ($customFields as $field => $key)
                                {
                                    if (isset($data[$key]))
                                    {
                                        $customRecord[$field] = $data[$key];
                                    }
                                }

                                $itemLookup->custom->update($customRecord);

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