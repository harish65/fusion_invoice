<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\ItemLookups\Requests;

use FI\Traits\CustomFieldValidator;

class ItemLookupUpdateRequest extends ItemLookupRequest
{
    use CustomFieldValidator;

    private $customFieldType = 'item_lookups';

    public function rules()
    {
        $rules         = parent::rules();
        $rules['name'] = 'required|unique:item_lookups,name,' . $this->route('itemLookup');

        return $rules;
    }
}