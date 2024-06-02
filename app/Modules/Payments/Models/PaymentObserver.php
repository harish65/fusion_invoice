<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Payments\Models;

use FI\Modules\CustomFields\Models\PaymentCustom;
use FI\Modules\Mru\Models\Mru;
use FI\Modules\Payments\Events\AddTransition;

class PaymentObserver
{
    function created(Payment $payment)
    {
        // Create the default custom record.
        $payment->custom()->save(new PaymentCustom());
    }

    public function creating(Payment $payment)
    {
        if (!$payment->paid_at)
        {
            $payment->paid_at = date('Y-m-d');
        }
    }

    public function deleting(Payment $payment)
    {
        foreach ($payment->mailQueue as $mailQueue)
        {
            $mailQueue->delete();
        }

        $payment->custom()->delete();

        $paymentInvoices = $payment->paymentInvoice;
        foreach ($paymentInvoices as $paymentInvoice)
        {
            $paymentInvoice->delete();
        }

        Mru::whereUserId(auth()->user()->id)->whereModule('payments')->whereElementId($payment->id)->delete();
    }

    public function updating(Payment $payment)
    {
        if ($payment->isDirty('note'))
        {
            if ($payment->note == '')
            {
                event(new AddTransition($payment, 'payment_note_deleted', $payment->getOriginal('note'), $payment->note));
            }
            elseif ($payment->getOriginal('note') == '')
            {
                event(new AddTransition($payment, 'payment_note_added', $payment->getOriginal('note'), $payment->note));
            }
            else
            {
                event(new AddTransition($payment, 'payment_note_updated', $payment->getOriginal('note'), $payment->note));
            }
        }
    }
}