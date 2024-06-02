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

use FI\Modules\Currencies\Models\Currency;
use FI\Support\NumberFormatter;
use FI\Traits\CustomFieldValidator;

class RecurringInvoiceUpdateRequest extends RecurringInvoiceStoreRequest
{
    use CustomFieldValidator;

    private $customFieldType         = 'recurring_invoices';
    private $lineItemCustomFieldType = 'recurring_invoice_items';

    public function prepareForValidation()
    {
        $request  = $this->all();
        $currency = ($request['currency_code']) ? Currency::getByCode($request['currency_code']) : NULL;
        if (isset($request['items']))
        {
            foreach ($request['items'] as $key => $item)
            {
                $request['items'][$key]['quantity'] = NumberFormatter::unformat($item['quantity']);
                $request['items'][$key]['price']    = NumberFormatter::unformat($item['price'], $currency);
            }
        }

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'summary'             => 'max:255',
            'exchange_rate'       => 'required|numeric',
            'template'            => 'required',
            'next_date'           => 'required_without:stop_date',
            'recurring_frequency' => 'numeric|required',
            'recurring_period'    => 'required',
            'items'               => 'required',
            'items.*.name'        => 'required',
            'items.*.quantity'    => 'required_with:items.*.price,items.*.name|numeric',
            'items.*.price'       => 'required_with:items.*.name,items.*.quantity|numeric',
        ];
    }
}