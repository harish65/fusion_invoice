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
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\ItemLookups\Models\ItemLookup;
use FI\Modules\Quotes\Events\QuoteModified;
use FI\Modules\Quotes\Models\Quote;
use FI\Modules\TaxRates\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuoteItemImporter extends AbstractImporter
{
    public function getFields()
    {
        $fields = [
            'quote_number'  => '* ' . trans('fi.quote_number'),
            'name'          => '* ' . trans('fi.product'),
            'quantity'      => '* ' . trans('fi.quantity'),
            'price'         => '* ' . trans('fi.price'),
            'description'   => trans('fi.description'),
            'tax_rate_id'   => trans('fi.tax_1'),
            'tax_rate_2_id' => trans('fi.tax_2'),
        ];
        foreach (CustomField::forTable('quote_items')->get() as $customField)
        {
            $fields['custom_' . $customField->column_name] = $customField->field_label;
        }
        return $fields;
    }

    public function getMapRules()
    {
        return [
            'quote_number' => 'required',
            'name'         => 'required',
            'quantity'     => 'required',
            'price'        => 'required',
        ];
    }

    public function getValidator($input)
    {
        return Validator::make($input, [
                'quote_number' => 'required|exists:quotes,number',
                'name'         => 'required',
                'quantity'     => 'required|numeric',
                'price'        => 'required|numeric',
            ]
        );
    }

    public function importData($input)
    {
        $this->file = storage_path('quoteItems.csv');
        $row        = 1;
        $fields     = $customFields = $labels = [];
        $taxRates   = TaxRate::get();
        $response   = ['success' => false, 'total_records' => 0, 'message' => ''];

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

            DB::transaction(function () use (&$handle, &$fields, &$response, &$taxRates, &$customFields, &$row, &$labels)
            {
                while (($data = fgetcsv($handle, $this->max_records, ',')) !== false)
                {
                    if ($row !== 1 && $data !== array(null))
                    {
                        $record = $customRecord = [];

                        foreach ($fields as $key => $field)
                        {
                            $record[$field] = trim($data[$key]);
                        }

                        $quote = Quote::where('number', $record['quote_number'])->first();

                        if ($quote)
                        {
                            $record['quote_id'] = $quote->id;

                            if (!isset($record['tax_rate_id']))
                            {
                                $record['tax_rate_id'] = 0;
                            }
                            else
                            {
                                if ($taxRate = $taxRates->where('name', $record['tax_rate_id'])->first())
                                {
                                    $record['tax_rate_id'] = $taxRate->id;
                                }
                                else
                                {
                                    $record['tax_rate_id'] = 0;
                                }
                            }

                            if (!isset($record['tax_rate_2_id']))
                            {
                                $record['tax_rate_2_id'] = 0;
                            }
                            else
                            {
                                if ($taxRate = $taxRates->where('name', $record['tax_rate_2_id'])->first())
                                {
                                    $record['tax_rate_2_id'] = $taxRate->id;
                                }
                                else
                                {
                                    $record['tax_rate_2_id'] = 0;
                                }
                            }

                            $record['display_order'] = 0;

                            if ($this->validateRecord($record, $row))
                            {
                                if (!isset($record['description']))
                                {
                                    $record['description'] = '';
                                };

                                $quoteItem = $quote->items()->create($record);

                                if ($customFields)
                                {

                                    foreach ($customFields as $field => $key)
                                    {
                                        if (isset($data[$key]) && $data[$key] != '')
                                        {
                                            $customRecord[$field] = $data[$key];
                                        }
                                        else
                                        {
                                            $customFieldDetail    = CustomFieldsParser::getFieldByFieldLabel('item_lookups', $labels[$key]);
                                            $itemLookup           = ItemLookup::whereName($record['name'])->first();
                                            $customRecord[$field] = $itemLookup->custom->{$customFieldDetail->column_name};
                                        }
                                    }
                                    $quoteItem->custom->update($customRecord);
                                }
                                event(new QuoteModified($quote));

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
                    else
                    {
                        $labels = $data;
                        $row++;
                    }
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