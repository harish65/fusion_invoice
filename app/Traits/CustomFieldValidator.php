<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Traits;

use DB;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use Illuminate\Http\UploadedFile;

trait CustomFieldValidator
{

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {
            if (request('custom'))
            {
                foreach (request('custom') as $key => $item)
                {
                    $field = CustomFieldsParser::getFieldByColumnName($this->customFieldType, $key);

                    $customItemModule = $this->request->get('custom_items_module') ? $this->request->get('custom_items_module') : null;
                    $customId         = $this->request->get($this->request->get('custom_module') . '_id') ? $this->request->get($this->request->get('custom_module') . '_id') : null;
                    $this->customFieldValidator($field, $key, $item, $validator, $customId, $this->request->get('custom_module'), $customItemModule);

                }
            }
            if (request('items'))
            {
                foreach (request('items') as $value)
                {
                    if (isset($value['custom']))
                    {
                        foreach ($value['custom'] as $key => $item)
                        {

                            $field = CustomFieldsParser::getFieldByColumnName($this->lineItemCustomFieldType, $key);
                            $this->customFieldValidator($field, $key, $item, $validator, $value['id'], $this->request->get('custom_module'), $this->request->get('custom_items_module'));

                        }
                    }
                }
            }
        });
    }

    public function customFieldValidator($field, $key, $item, $validator, $customId, $custom_module, $custom_items_module)
    {
        $input = $this->all();

        switch ($field->field_type)
        {
            case "checkbox":
                if (($field->is_required == 1) && ($item == 0))
                {
                    $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                }
                break;
            case "textarea":
            case "date":
            case "datetime":
            case "text":
                if ($field->is_required == 1 && empty($item))
                {
                    $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                }
                break;

            case "image":
                if ($item instanceof UploadedFile && !in_array($item->getMimeType(), ['image/jpg', 'image/jpeg', 'image/gif', 'image/png']))
                {
                    $validator->errors()->add('custom.' . $key, trans('fi.custom_image_validate', ['label' => $field->field_label]));
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        if ($customId)
                        {
                            if ($field->tbl_name == $custom_module . 's')
                            {
                                $customData = DB::table($field->tbl_name . '_custom')
                                                ->where($custom_module . '_id', '=', $customId)
                                                ->select($field->column_name)->first();

                                if ($customData->{$field->column_name} == null)
                                {
                                    $validator->errors()->add($field->column_name, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                                }
                            }
                            else
                            {
                                $customItemData = DB::table($field->tbl_name . '_custom')->where($custom_items_module . '_id', '=', $customId)
                                                    ->select($field->column_name)->first();

                                if ($customItemData->{$field->column_name} == null)
                                {
                                    $validator->errors()->add($field->column_name, trans('fi.custom_item_text_validate', ['label' => $field->field_label]));
                                }

                            }
                        }
                        else
                        {
                            $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                        }
                    }
                }
                break;
            case "dropdown":
            case "radio":
                $meta    = json_decode($field->field_meta, true);
                $options = isset($meta['options']) ? $meta['options'] : [];
                if (!empty($item) && ($item != 'null'))
                {
                    if (!array_key_exists($item, $options))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_dropdown_validate', ['label' => $field->field_label]));
                    }
                }
                else
                {
                    if ($field->is_required == 1 && ($item == 'null'))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                break;
            case "integer":
                if (!empty($item) && filter_var($item, FILTER_VALIDATE_INT) == false)
                {
                    $validator->errors()->add('custom.' . $key, trans('fi.custom_integer_validate', ['label' => $field->field_label]));
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                break;
            case "currency":
            case "phone":
                
                if (!empty($item))
                {
                    $phone = str_replace([' ', '.', '-', '(', ')'], '', $item);
                    if (!is_numeric($phone))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                break;
            case "url":
                if (!empty($item))
                {
                    $scheme = parse_url($item, PHP_URL_SCHEME);
                    if (empty($scheme))
                    {
                        $item                  = 'http://' . ltrim($item, '/');
                        $input['custom'][$key] = $item;
                        $this->request->replace($input);
                    }
                    if (!preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                break;
            case "email":
                if (!empty($item) && filter_var($item, FILTER_VALIDATE_EMAIL) == false)
                {
                    $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                    }
                }
                break;
            case "decimal":
                if (!empty($item) && filter_var($item, FILTER_VALIDATE_FLOAT) == false)
                {
                    $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));

                    }
                }
                break;
            case "tagselection":
                // Need to collect options key value pair from meta
                if (is_array($item))
                {
                    $meta    = json_decode($field->field_meta, true);
                    $options = isset($meta['options']) ? $meta['options'] : [];
                    if (count(array_intersect_key(array_flip($item), $options)) != count($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_tag_validate', ['label' => $field->field_label]));
                    }
                }
                else
                {
                    if ($field->is_required == 1 && empty($item))
                    {
                        $validator->errors()->add('custom.' . $key, trans('fi.custom_text_validate', ['label' => $field->field_label]));

                    }
                }
                break;
            default:
        }
    }
}