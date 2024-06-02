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

class RecurringInvoiceItemCommissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id'                   => 'required',
            'recurring_invoice_item_id' => 'required',
            'type_id'                   => 'required',
            'stop_date'                 => 'required',
            'amount'                    => 'numeric',
        ];
    }
}
