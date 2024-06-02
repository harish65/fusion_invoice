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

use FI\Modules\RecurringInvoices\Rules\CreateLiveInvoice;
use Illuminate\Foundation\Http\FormRequest;


class RecurringInvoiceCreateLiveInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', new CreateLiveInvoice()],
        ];
    }
}