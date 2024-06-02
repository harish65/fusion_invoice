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
use Addons\Commission\Models\InvoiceItemCommission;
use Illuminate\Queue\SerializesModels;

class AddInvoiceItemCommissionTransition extends Event
{
    use SerializesModels;

    public $invoiceItemCommission;
    public $actionType;
    public $previousValue;
    public $currentValue;

    public function __construct(InvoiceItemCommission $invoiceItemCommission, $actionType, $previousValue = null, $currentValue = null)
    {
        $this->invoiceItemCommission = $invoiceItemCommission;
        $this->actionType            = $actionType;
        $this->previousValue         = $previousValue;
        $this->currentValue          = $currentValue;
        $this->id                    = $invoiceItemCommission->id;
        $this->client_id             = $invoiceItemCommission->invoiceItem->invoice->client_id;

        $this->detail = [
            'id'              => $invoiceItemCommission->id,
            'number'          => $invoiceItemCommission->invoiceItem->invoice->number,
            'amount'          => $invoiceItemCommission->amount,
            'name'            => $invoiceItemCommission->invoiceItem->name,
            'sales_person'    => $invoiceItemCommission->user->name,
            'sales_person_id' => $invoiceItemCommission->user_id,
        ];

    }
}
