<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Merchant\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Invoices\Models\Invoice;
use FI\Modules\Merchant\Support\Drivers\StripeDriver;

class StripeController extends Controller
{
    private $driver;

    public function __construct(StripeDriver $driver)
    {
        $this->driver = $driver;
    }

    public function pay($urlKey)
    {
        $invoice = Invoice::where('url_key', $urlKey)->first();

        if ($invoice->amount->balance ==  0 && $invoice->amount->total > 0 && $invoice->status != 'canceled')
        {
            return redirect()->back()->with('error', trans('fi.invoice_already_paid'));
        }
        else
        {
            return $this->driver->pay($invoice);
        }

    }

    public function success($urlKey)
    {
        return $this->driver->success(Invoice::where('url_key', $urlKey)->first());
    }

    public function cancel($urlKey)
    {
        $invoice = Invoice::where('url_key', $urlKey)->first();
        return $this->driver->cancel($urlKey, $invoice->token);
    }
}