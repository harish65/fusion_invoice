<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class FileNames
{
    public static function invoice($invoice)
    {
        return trans('fi.invoice') . '_' . str_replace('/', '-', $invoice->number) . '_' . $invoice->id . '.pdf';
    }

    public static function quote($quote)
    {
        return trans('fi.quote') . '_' . str_replace('/', '-', $quote->number) . '_' . $quote->id . '.pdf';
    }

    public static function payment($payment)
    {
        return str_replace(' ', '', trans('fi.payment_receipt')) . '_' . $payment->id . '.pdf';
    }
}