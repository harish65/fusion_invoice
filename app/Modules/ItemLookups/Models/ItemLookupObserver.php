<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ItemLookups\Models;

use FI\Modules\CustomFields\Models\ItemLookupCustom;

class ItemLookupObserver
{

    public function created(ItemLookup $itemLookup)
    {
        // Create the custom invoice record.
        $itemLookup->custom()->save(new ItemLookupCustom());
    }

    public function deleted(ItemLookup $itemLookup)
    {
        $itemLookup->custom()->delete();
    }

    public function creating(ItemLookup $itemLookup)
    {
        if (request('category_name'))
        {
            $itemLookup->category_id = ItemCategory::firstOrCreate(['name' => request('category_name')])->id;
        }
    }

    public function updating(ItemLookup $itemLookup)
    {
        if (request('category_name'))
        {
            $itemLookup->category_id = ItemCategory::firstOrCreate(['name' => request('category_name')])->id;
        }
    }
}