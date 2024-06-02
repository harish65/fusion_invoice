<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Expenses\Requests;

use FI\Support\NumberFormatter;
use FI\Traits\CustomFieldValidator;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    use CustomFieldValidator;

    private $customFieldType = 'expenses';

    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'user_id'            => trans('fi.user'),
            'company_profile_id' => trans('fi.company_profile'),
            'vendor_id'          => trans('fi.vendor'),
            'expense_date'       => trans('fi.date'),
            'category_name'      => trans('fi.category'),
            'description'        => trans('fi.description'),
            'amount'             => trans('fi.amount'),
        ];
    }

    public function prepareForValidation()
    {
        $request = $this->all();

        if (isset($request['amount']))
        {
            $request['amount'] = NumberFormatter::unformat($request['amount']);
        }

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'user_id'            => 'required',
            'company_profile_id' => 'required',
            'vendor_name'        => 'required',
            'expense_date'       => 'required',
            'category_name'      => 'required',
            'description'        => 'max:255',
            'amount'             => 'required|numeric',
        ];
    }
}