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

use Addons\Commission\Events\AddInvoiceItemCommissionTransition;

class InvoiceItemCommissionObserver
{

    public function created(InvoiceItemCommission $invoiceItemCommission)
    {
        event(new AddInvoiceItemCommissionTransition($invoiceItemCommission, 'invoice_commission_created'));
    }

    public function saving(InvoiceItemCommission $invoiceItemCommission)
    {
        if ($invoiceItemCommission->type->method == 'formula')
        {
            $invoice_items                 = $invoiceItemCommission->invoiceItem;
            $invoice                       = $invoiceItemCommission->invoiceItem->invoice;
            $recurring_invoice             = $invoiceItemCommission->invoiceItem->recurringInvoice;
            $invoice_item_amounts          = $invoiceItemCommission->invoiceItem->amount;
            $amount                        = eval("return " . $invoiceItemCommission->type->formula . ";");
            $invoiceItemCommission->amount = $amount;
        }
    }

    public function updating(InvoiceItemCommission $invoiceItemCommission)
    {

        event(new AddInvoiceItemCommissionTransition($invoiceItemCommission, 'invoice_commission_updated'));
    }

}
