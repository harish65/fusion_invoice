<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission\Models;

use Addons\Commission\Events\AddRecurringInvoiceItemCommissionTransition;

class RecurringInvoiceItemCommissionObserver
{
    public function created(RecurringInvoiceItemCommission $recurringInvoiceItemCommission)
    {
        event(new AddRecurringInvoiceItemCommissionTransition($recurringInvoiceItemCommission, 'recurring_invoice_commission_created'));
    }

    public function saving(RecurringInvoiceItemCommission $recurringInvoiceItemCommission)
    {
        if ($recurringInvoiceItemCommission->type->method == 'formula')
        {

            $invoice_items                          = $recurringInvoiceItemCommission->invoiceItem;
            $invoice                                = $recurringInvoiceItemCommission->invoiceItem->invoice;
            $recurring_invoice                      = $recurringInvoiceItemCommission->invoiceItem->recurringInvoice;
            $invoice_item_amounts                   = $recurringInvoiceItemCommission->invoiceItem->amount;
            $amount                                 = eval("return " . $recurringInvoiceItemCommission->type->formula . ";");
            $recurringInvoiceItemCommission->amount = $amount;

        }

    }

    public function updating(RecurringInvoiceItemCommission $recurringInvoiceItemCommission)
    {

        event(new AddRecurringInvoiceItemCommissionTransition($recurringInvoiceItemCommission, 'recurring_invoice_commission_updated'));
    }
}
