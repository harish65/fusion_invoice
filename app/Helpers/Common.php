<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FI\Modules\Settings\Models\UserSetting;
use FI\Support\CurrencyFormatter;

function refactorColumnSetting($list_columns, $columns)
{
    foreach ($list_columns as $key => $index)
    {
        if (array_key_exists($index, request('columns')))
        {
            $columnsValues   = $columns[$index];
            $separatedValues = explode("###", $columnsValues);
            $columns[$index] = $separatedValues;
        }
        else
        {
            $columns[$index] = [0 => "off", 1 => "notSortable"];
        }
    }
    return json_encode($columns);
}

function refactorClientColumnSetting($column, $status)
{
    $userSettings = UserSetting::whereSettingKey($column)->whereUserId(auth()->user()->id)->first();
    if ($userSettings == null)
    {
        $userSetting                = new UserSetting();
        $userSetting->user_id       = auth()->user()->id;
        $userSetting->setting_key   = $column;
        $userSetting->setting_value = $status;
        $userSetting->save();
    }
    else
    {
        $userSettings->setting_value = $status;
        $userSettings->save();
    }
}

function onlinePaymentChargesDetail($invoice)
{

    $feePercentage   = config('fi.feePercentage') != null ? (config('fi.feePercentage') / 100) : null;
    $feeCharges      = (number_format($invoice->amount->balance, 2, '.', '') * $feePercentage);
    $addChargeAsItem = true;

    if ($invoice->online_payment_processing_fee == 'yes')
    {
        $amountValue = round((number_format($invoice->amount->balance, 2, '.', '') + $feeCharges), 2);
    }
    else
    {
        $addChargeAsItem = false;
        $amountValue     = number_format($invoice->amount->balance, 2, '.', '');
    }

    return ['amountValue' => $amountValue, 'addChargesItem' => $addChargeAsItem, 'feeCharges' => $feeCharges];
}

function onlineConvenienceCharges($invoice)
{
    if ($invoice->online_payment_processing_fee == 'yes' && config('fi.feePercentage') != '')
    {
        $feePercentage                      = (config('fi.feePercentage') / 100);
        $feeCharges                         = (number_format($invoice->amount->balance, 2, '.', '') * $feePercentage);
        $totalCharges                       = $invoice->total_convenience_charges + $feeCharges;
        $invoice->total_convenience_charges = number_format($totalCharges, 2, '.', '');
        $invoice->save();
    }
}

function manageTags($module, $tagUpdateAction, $tagDeleteAction, $model, $isDirtyNote = true)
{
    $addTags = $removeTags = null;

    $newTagsArray = request('tags', []);
    $oldTags      = $module->tags;
    $oldTagsArray = $oldNonIndexTagsArray = [];

    if (isset($oldTags) && isset($newTagsArray))
    {
        foreach ($oldTags as $oldTag)
        {
            $nameTag                       = $oldTag->tag->name;
            $oldNonIndexTagsArray[]        = $nameTag;
            $oldTagsArray[$oldTag->tag_id] = $nameTag;
        }

        if ($newTagsArray != $oldNonIndexTagsArray)
        {
            $addTags    = array_diff($newTagsArray, $oldTagsArray);
            $removeTags = array_diff($oldTagsArray, $newTagsArray);
        }
    }
    $eventPath = 'FI\Modules\\' . $model . '\Events\AddTransitionTags';
    if (isset($removeTags))
    {
        $removeTagIds = $removeTagNames = [];
        foreach ($removeTags as $removeTagId => $removeTag)
        {
            $removeTagIds[]   = $removeTagId;
            $removeTagNames[] = $removeTag;

        }
        if (!empty($removeTagIds))
        {
            event(new $eventPath($module, $tagDeleteAction, '', '', '', implode(', ', $removeTagNames), $removeTagIds, $isDirtyNote));
        }

    }
    if (isset($addTags))
    {
        $addTagNames = [];

        foreach ($addTags as $addTag)
        {
            $addTagNames[] = $addTag;
        }
        if (!empty($addTagNames))
        {
            event(new $eventPath($module, $tagUpdateAction, '', '', '', implode(', ', $addTagNames), '', $isDirtyNote));
        }
    }

    return $addTags;
}

function countColumns($defaultSequenceColumnData, $columnSettings)
{
    $columnIndex = 0;

    foreach ($defaultSequenceColumnData as $defaultColumnIndex => $defaultSequenceColumn)
    {
        foreach ($columnSettings as $columnSettingIndex => $columnSetting)
        {
            if ($defaultColumnIndex == $columnSettingIndex && $defaultSequenceColumn[0] == $columnSetting[0])
            {
                $columnIndex++;
                if ($defaultColumnIndex == 'total' && $defaultSequenceColumn[0] == 'on')
                {
                    return $columnIndex--;
                }
                else
                {
                    if ($defaultColumnIndex == 'balance' && $defaultSequenceColumn[0] == 'on')
                    {
                        return $columnIndex--;
                    }
                }
            }
        }
    }

    return 0;
}

function darkModeForInvoiceAndQuoteTemplate($htmlTemplate)
{
    return str_replace("table.table-dark tr:nth-child(odd) td {background: #2c3034;}  table.table-dark {color: #fff !important;background-color: #212529 !important;}  body.body-dark {color: #fff !important;background: #454d55 !important;}  body.body-dark a {color: #4ba1df;border-bottom: 1px solid currentColor;text-decoration: none;}  table.table-dark td, .table-dark th, .table-dark thead th {border-color: #32383e;color: white;}  table.body-dark .section-header {border-color: #32383e;color: white;}  table.body-dark .info {color: #b2b2b2;font-weight: bold;}  table.body-dark h1 {color: #b2b2b2;font-weight: bold;}", " ", $htmlTemplate);
}

function calculateDiscount($discount_type, $discount, $price)
{
    if ($discount_type == 'percentage')
    {
        return ['price' => $price - (($price * $discount) / 100), 'previous_price' => (($price * $discount) / 100)];
    }
    else if ($discount_type == 'flat_amount')
    {
        return ['price' => $price - $discount, 'previous_price' => $discount];
    }
    else
    {
        return ['price' => $price - $discount, 'previous_price' => 0];
    }
}

function removeCalculateDiscount($discount_type, $discount, $price)
{
    if ($discount_type == 'percentage')
    {
        return ['price' => $price + $discount];
    }
    else if ($discount_type == 'flat_amount')
    {
        return ['price' => $price + $discount];
    }
    else
    {
        return ['price' => $price];
    }
}

function userSettingUpdate($key, $value, $id)
{
    $userSettings = UserSetting::whereSettingKey($key)->whereUserId($id)->first();
    if ($userSettings == null)
    {
        $userSetting                = new UserSetting();
        $userSetting->user_id       = $id;
        $userSetting->setting_key   = $key;
        $userSetting->setting_value = $value;
        $userSetting->save();
    }
    else
    {
        $userSettings->setting_value = $value;
        $userSettings->save();
    }
}

function noteFormatter($id, $title, $detail)
{
    if (isset($detail->short_text))
    {
        $formatNote = str_replace(['&gt;', '&lt;', '&nbsp;'], ['', '', ''], strip_tags($detail->short_text));
        if (str_word_count(strip_tags($formatNote)) > 50)
        {
            return '<div id="module" class="note-container">' . $title . '
                        <div class="collapse note-collapse" id="collapse' . $id . '" aria-expanded="false">' . $detail->short_text . '</div>
                        <a role="button" class="collapsed note-collapsed" data-toggle="collapse" href="#collapse' . $id . '" aria-expanded="false" aria-controls="#collapse' . $id . '">' . trans("fi.show_more") . '</a>
                    </div>';
        }
        elseif (isset($detail->short_text))
        {
            return $title . '<br>' . trans('fi.text') . ': ' . $detail->short_text;
        }
        else
        {
            return $title;
        }
    }
}

function jsFormattedAddress($data)
{
    $patterns     = ["/\\\\/", '/\n/', '/\r/', '/\t/', '/\v/', '/\f/'];
    $replacements = ['\\\\\\', '\n', '\r', '\t', '\v', '\f'];
    $address      = preg_replace($patterns, $replacements, $data);
    return $address;
}

function iframeThemeColor()
{
    return '.theme-color {color: #000000 !important;}';
}

function revenueByClientCurrencyFormatter($amount = 0)
{
    return CurrencyFormatter::format($amount);
}