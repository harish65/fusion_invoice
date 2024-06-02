<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission;

use Addons\Commission\Models\InvoiceItemCommission;
use Addons\Commission\Models\InvoiceItemCommissionObserver;
use Addons\Commission\Models\RecurringInvoiceItemCommission;
use Addons\Commission\Models\RecurringInvoiceItemCommissionObserver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        'Addons\Commission\EventSubscriber',
    ];

    public function boot()
    {
        InvoiceItemCommission::observe(InvoiceItemCommissionObserver::class);
        RecurringInvoiceItemCommission::observe(RecurringInvoiceItemCommissionObserver::class);

        Validator::extend('IsValidFormula', function ($attribute, $value, $parameters, $validator)
        {
            $validFormulas = [
                '$invoice_items->amount->subtotal',
                '$invoice_items->quantity',
                '$invoice->total',
                '$recurring_invoice->amount->total',
            ];

            $formula = explode(' ', $value);

            return array_search(trim($formula[0]), $validFormulas) !== false ? true : false;
        });
    }
}
