<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission\Events;

use FI\Events\Event;
use Addons\Commission\Models\RecurringInvoiceItemCommission;
use Illuminate\Queue\SerializesModels;

class AddRecurringInvoiceItemCommissionTransition extends Event
{
    use SerializesModels;

    public $invoiceItemCommission;
    public $actionType;
    public $previousValue;
    public $currentValue;

    public function __construct(RecurringInvoiceItemCommission $recurringInvoiceItemCommission, $actionType, $previousValue = null, $currentValue = null)
    {
        $this->invoiceItemCommission = $recurringInvoiceItemCommission;
        $this->actionType            = $actionType;
        $this->previousValue         = $previousValue;
        $this->currentValue          = $currentValue;
        $this->id                    = $recurringInvoiceItemCommission->id;
        $this->client_id             = $recurringInvoiceItemCommission->invoiceItem->recurringInvoice->client_id;

        $this->detail = [
            'id'              => $recurringInvoiceItemCommission->id,
            'number'          => $recurringInvoiceItemCommission->invoiceItem->recurringInvoice->id,
            'amount'          => $recurringInvoiceItemCommission->amount,
            'name'            => $recurringInvoiceItemCommission->invoiceItem->name,
            'sales_person'    => $recurringInvoiceItemCommission->user->name,
            'sales_person_id' => $recurringInvoiceItemCommission->user_id,
        ];

    }
}
