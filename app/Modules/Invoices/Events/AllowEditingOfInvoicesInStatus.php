<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Invoices\Events;

use FI\Events\Event;
use FI\Modules\Invoices\Models\Invoice;
use Illuminate\Queue\SerializesModels;

class AllowEditingOfInvoicesInStatus extends Event
{
    use SerializesModels;

    public $invoice;
    public $status;
    public $detail;
    public $actionType;

    public function __construct(Invoice $invoice, $status = null)
    {
        $this->invoice    = $invoice;
        $this->status     = $status;
        $this->actionType = ($this->status . '_invoice_opened');
        $this->detail     = ['number' => $invoice->number];
    }
}
