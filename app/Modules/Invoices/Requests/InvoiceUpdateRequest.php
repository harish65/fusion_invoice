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

use FI\Modules\Invoices\Rule\InvoiceStatusCheck;
use FI\Modules\Currencies\Models\Currency;
use FI\Support\NumberFormatter;
use FI\Traits\CustomFieldValidator;

class InvoiceUpdateRequest extends InvoiceStoreRequest
{
    use CustomFieldValidator;

    private $customFieldType         = 'invoices';
    private $lineItemCustomFieldType = 'invoice_items';

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
                
                if (isset($request['items'][$key]['discount']))
                {
                    $request['items'][$key]['discount'] = NumberFormatter::unformat($item['discount'], $currency);
                }
            }
        }

        $this->replace($request);
    }

    public function rules()
    {
        return [
            'summary'          => 'max:255',
            'invoice_date'     => 'required',
            'due_at'           => 'required',
            'number'           => 'required',
            'exchange_rate'    => 'required|numeric',
            'template'         => 'required',
            'items'            => 'required',
            'items.*.name'     => 'required',
            'items.*.quantity' => 'required_with:items.*.price,items.*.name|numeric',
            'items.*.price'    => 'required_with:items.*.name,items.*.quantity|numeric',
            'items.*.discount' => function ($attribute, $value, $fail)
            {
                $index = explode('.', $attribute);
                if (isset($this['items'][$index[1]]['discount_type']) && $this['items'][$index[1]]['discount_type'] == 'percentage')
                {
                    if ($value > 99.99)
                    {
                        $fail(trans('fi.line_item_discount_limit'));
                    }
                    return true;
                }
                return true;
            },
            'status'           => new InvoiceStatusCheck($this['invoice_id']),
        ];
    }
}