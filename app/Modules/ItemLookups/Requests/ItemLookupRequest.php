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

use FI\Support\NumberFormatter;
use FI\Traits\CustomFieldValidator;
use Illuminate\Foundation\Http\FormRequest;

class ItemLookupRequest extends FormRequest
{
    use CustomFieldValidator;

    private $customFieldType = 'item_lookups';

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'     => trans('fi.name'),
            'price'    => trans('fi.price'),
            'quantity' => trans('fi.quantity'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        $request['price'] = NumberFormatter::unformat($request['price']);

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'name'       => 'required|unique:item_lookups,name',
            'price'      => 'required|numeric',
            'quantity'   => 'numeric',
            'formula_id' => 'exists:item_price_formulas,id',
        ];
    }
}