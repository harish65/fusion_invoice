<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecurringInvoiceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function attributes()
    {
        return [
            'company_profile_id'        => trans('fi.company_profile'),
            'client_id'                 => trans('fi.client'),
            'next_date'                 => trans('fi.start_date'),
            'recurring_frequency'       => trans('fi.frequency'),
            'recurring_period'          => trans('fi.frequency'),
            'document_number_scheme_id' => trans('fi.document_number_schemes'),
            'stop_date'                 => trans('fi.stop_date')
        ];
    }

    public function rules()
    {
        return [
            'company_profile_id'  => 'required',
            'client_id'           => 'required',
            'user_id'             => 'required',
            'next_date'           => 'required',
            'recurring_frequency' => 'numeric|required',
            'recurring_period'    => 'required',
        ];
    }
}