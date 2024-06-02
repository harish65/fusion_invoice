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

class AddTransitionTags extends Event
{
    use SerializesModels;

    public $invoice;
    public $actionType;
    public $previousValue;
    public $currentValue;
    public $detail;
    public $userId;
    public $tagName;
    public $tagId;

    public function __construct(Invoice $invoice, $actionType, $previousValue = null, $currentValue = null, $userId = null, $tagName = null, $tagId = null)
    {
        $this->invoice       = $invoice;
        $this->actionType    = $actionType;
        $this->previousValue = $previousValue;
        $this->currentValue  = $currentValue;
        $this->userId        = $userId;
        $this->tagId         = $tagId;
        if ($actionType == 'invoice_tag_deleted' || $actionType == 'invoice_tag_updated')
        {
            $this->detail = [
                'number'   => $invoice->number,
                'tag_name' => '<i><b>&ldquo;' . $tagName . '&rdquo;</b></i>',
            ];
        }

    }
}
