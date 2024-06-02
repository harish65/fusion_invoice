<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Models;

class InvoiceObserver
{
    public function deleted($invoice)
    {
        TimeTrackingTask::where('invoice_id', $invoice->id)->update(['invoice_id' => 0, 'billed' => 0]);
    }
}