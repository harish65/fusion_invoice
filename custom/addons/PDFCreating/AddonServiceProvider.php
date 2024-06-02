<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\PDFCreating;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Event::listen('FI\Events\InvoiceHTMLCreating', function($event)
        {
            foreach ($event->invoice->items as $item)
            {
                $item->name = 'HAHAHA! Invoice Item!';
            }
        });

        Event::listen('FI\Events\QuoteHTMLCreating', function($event)
        {
            foreach ($event->quote->items as $item)
            {
                $item->name = 'HAHAHA! Quote Item!';
            }
        });
    }

    public function register()
    {
        //
    }
}
