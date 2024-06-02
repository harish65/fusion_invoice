<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\RecurringInvoices\Rules;

use Carbon\Carbon;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use Illuminate\Contracts\Validation\Rule;

class CreateLiveInvoice implements Rule
{

    public function __construct()
    {

    }

    public function passes($attribute, $value)
    {
        $recurringInvoice = RecurringInvoice::liveRecurNow()->find($value);
        if ($recurringInvoice)
        {
            return ($recurringInvoice->next_date < Carbon::now()->addDays(30)->format('Y-m-d'));
        }
        else
        {
            return false;
        }
    }

    public function message()
    {
        return trans('fi.live_invoice_not_generate');
    }
}