<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DateRangeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'from_date' => trans('fi.from_date'),
            'to_date'   => trans('fi.to_date'),
        ];
    }

    public function rules()
    {
        return [
            'from_date' => 'required',
            'to_date'   => 'required_if:from_date,!=,NULL'
        ];
    }

    public function messages()
    {
        return [
            'from_date.required' => trans('fi.date-range-required'),
            'to_date.required'   => trans('fi.date-range-required'),
        ];
    }
}