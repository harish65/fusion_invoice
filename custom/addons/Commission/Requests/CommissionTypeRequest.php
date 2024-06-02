<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'name'    => trans('Commission::lang.name'),
            'formula' => trans('Commission::lang.formula'),
            'method'  => trans('Commission::lang.method'),
        ];
    }

    public function rules()
    {
        if ($this->route('id'))
        {
            return [
                'name'    => 'required|max:255|unique:commission_types,name,' . $this->route('id'),
                'method'  => 'required',
                'formula' => 'required_if:method,formula|IsValidFormula',
            ];
        }
        else
        {
            return [
                'name'    => 'required|max:255|unique:commission_types,name',
                'method'  => 'required',
                'formula' => 'required_if:method,formula|IsValidFormula',
            ];
        }
    }

    public function messages()
    {
        return [
            'formula.is_valid_formula' => trans('Commission::lang.invalid-formula'),
        ];
    }
}
