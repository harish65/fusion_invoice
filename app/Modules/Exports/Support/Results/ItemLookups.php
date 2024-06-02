<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\CustomFields\Support\CustomFieldsParser;
use FI\Modules\ItemLookups\Models\ItemLookup;

class ItemLookups implements SourceInterface
{
    public function getResults($params = [])
    {
        $customFields = CustomFieldsParser::getFields('item_lookups');
        $invoiceItem  = ItemLookup::select('item_lookups.id', 'item_lookups.created_at', 'item_lookups.name',
            'item_lookups.description', 'item_lookups.price', 'item_categories.name AS item_category', 'tax_rate_1.name AS tax_rate_1_name',
            'tax_rate_1.percent AS tax_rate_1_percent', 'tax_rate_1.is_compound AS tax_rate_1_is_compound',
            'tax_rate_2.name AS tax_rate_2_name',
            'tax_rate_2.percent AS tax_rate_2_percent', 'tax_rate_2.is_compound AS tax_rate_2_is_compound')
            ->leftJoin('item_categories', 'item_lookups.category_id', '=', 'item_categories.id')
            ->leftJoin('item_lookups_custom', 'item_lookups.id', '=', 'item_lookups_custom.item_lookup_id')
            ->leftJoin('tax_rates AS tax_rate_1', 'tax_rate_1.id', '=', 'item_lookups.tax_rate_id')
            ->leftJoin('tax_rates AS tax_rate_2', 'tax_rate_2.id', '=', 'item_lookups.tax_rate_2_id')
            ->orderBy('item_lookups.name');

        foreach ($customFields as $customField)
        {
            $invoiceItem->addSelect("item_lookups_custom." . $customField->column_name . " AS " . $customField->field_label);
        }

        return $invoiceItem->get()->toArray();
    }
}