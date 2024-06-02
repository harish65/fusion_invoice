<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PricingFormula\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemPriceFormulaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name' => trans('fi.name'),
        ];
    }

    public function rules()
    {
        if ($this->route('id'))
        {
            return [
                'name'    => 'required|max:255|unique:item_price_formulas,name,' . $this->route('id'),
                'formula' => 'required',
            ];
        }
        else
        {
            return [
                'name'    => 'required|max:255|unique:item_price_formulas,name',
                'formula' => 'required',
            ];
        }
    }
}