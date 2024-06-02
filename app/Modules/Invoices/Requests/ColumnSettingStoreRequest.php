<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColumnSettingStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'columns' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'columns.required' => trans('fi.minimum_one_column_check_is_required'),
        ];
    }
}