<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\EventHandlers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // From here we can listen for the various
        // events listed in the app/Eventsc folder.
        // As shown in the example below, the event
        // must be referenced with the full namespace.

        Event::listen('FI\Events\PaymentCreated', function ($event)
        {
            // The payment created is available here as $event->payment.
            // Perhaps we'll call another class that handles the payment:

            $library = new Addons\EventHandlers\SomeLibrary();

            $library->doSomething(
                $event->payment->invoice->number,
                $event->payment->paid_at,
                $event->payment->amount
            );

            // Or perhaps we'll send the payment data as a POST request
            // somewhere using cURL:

            $response = \FI\Support\cURL::post('https://www.theurl.com', [
                'invoice_number' => $event->payment->invoice->number,
                'payment_date'   => $event->payment->paid_at,
                'payment_amount' => $event->payment->amount
            ]);

        });
    }

    public function register()
    {
        //
    }
}
