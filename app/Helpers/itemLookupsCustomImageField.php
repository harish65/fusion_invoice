<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use FI\Modules\CustomFields\Models\InvoiceItemCustom;
use FI\Modules\CustomFields\Models\ItemLookupCustom;
use FI\Modules\CustomFields\Models\QuoteItemCustom;
use FI\Modules\CustomFields\Models\RecurringInvoiceItemCustom;
use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\CustomFields\Support\CustomFieldsTransformer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function findItemLookupsCustomImageField($id, $itemId, $itemName)
{

    $customFields          = CustomFieldsParser::getFields('item_lookups');
    $itemLookUpCustom      = ItemLookupCustom::whereItemLookupId($id)->first();
    $itemLookupImageColumn = '';

    $itemCustomFields      = CustomFieldsParser::getFields($itemName);
    $customFieldColumnName = '';
    foreach ($itemCustomFields as $itemCustomField)
    {
        if ($itemCustomField->field_type == 'image')
        {
            $customFieldColumnName = $itemCustomField->column_name;
        }
    }

    foreach ($customFields as $customField)
    {
        if ($customField->field_type == 'image')
        {
            $itemLookupImageColumn = $customField->column_name;
            if ($itemLookUpCustom[$itemLookupImageColumn] != '')
            {
                $imageDetail                                   = $itemLookUpCustom->imagePath($customField->column_name);
                $itemLookUpCustom->{$customField->column_name} = createFileObject(public_path($imageDetail));
            }
            else
            {
                $itemLookUpCustom->{$customField->column_name} = '';
                if ($itemId != false)
                {
                    if ($itemName == 'quote_items')
                    {
                        $itemCustomField = QuoteItemCustom::whereQuoteItemId($itemId)->first();
                    }
                    if ($itemName == 'invoice_items')
                    {
                        $itemCustomField = InvoiceItemCustom::whereInvoiceItemId($itemId)->first();
                    }
                    if ($itemName == 'recurring_invoice_items')
                    {
                        $itemCustomField = RecurringInvoiceItemCustom::whereRecurringInvoiceItemId($itemId)->first();
                    }

                    if ($itemCustomField[$customFieldColumnName] != '')
                    {
                        $existingFile = $itemName . DIRECTORY_SEPARATOR . $itemCustomField[$customFieldColumnName];
                        if (Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->exists($existingFile))
                        {
                            try
                            {
                                Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->delete($existingFile);
                                $itemCustomField[$customFieldColumnName] = null;
                                $itemCustomField->save();
                            }
                            catch (Exception $e)
                            {

                            }
                        }
                    }
                }
            }
        }
    }

    return ['customFieldsColumnName' => $customFieldColumnName, 'itemLookupImageColumn' => $itemLookupImageColumn, 'itemLookUpCustom' => $itemLookUpCustom];
}

function createFileObject($url)
{
    $path_parts = pathinfo($url);

    $newPath = $path_parts['dirname'];

    if (!is_dir($newPath))
    {
        mkdir($newPath, 0777);
    }

    $newUrl = $newPath . $path_parts['basename'];

    copy($url, $newUrl);
    $imgInfo = getimagesize($newUrl);

    $file = new UploadedFile(
        $newUrl,
        $path_parts['basename'],
        $imgInfo['mime'],
        filesize($url),
        false,
        TRUE
    );
    return $file;
}

function removeCustomFieldImage($customFieldColumnName, $itemName, $itemId)
{

    if ($itemName == 'quote_items')
    {
        $itemCustomField = QuoteItemCustom::whereQuoteItemId($itemId)->first();
    }
    if ($itemName == 'invoice_items')
    {
        $itemCustomField = InvoiceItemCustom::whereInvoiceItemId($itemId)->first();
    }
    if ($itemName == 'recurring_invoice_items')
    {
        $itemCustomField = RecurringInvoiceItemCustom::whereRecurringInvoiceItemId($itemId)->first();
    }
    $existingFile = $itemName . DIRECTORY_SEPARATOR . $itemCustomField[$customFieldColumnName];
    if (Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->exists($existingFile))
    {
        try
        {
            Storage::disk(CustomFieldsTransformer::STORAGE_DISK_NAME)->delete($existingFile);
            $itemCustomField[$customFieldColumnName] = null;
            $itemCustomField->save();
        }
        catch (Exception $e)
        {

        }
    }
}