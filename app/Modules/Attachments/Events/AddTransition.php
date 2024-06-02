<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Attachments\Events;

use FI\Events\Event;
use FI\Modules\Attachments\Models\Attachment;
use Illuminate\Queue\SerializesModels;

class AddTransition extends Event
{
    use SerializesModels;

    public $attachment;
    public $actionType;
    public $detail;
    public $client;

    public function __construct(Attachment $attachment, $actionType, $client)
    {
        $this->client     = $client;
        $this->attachment = $attachment;
        $this->actionType = $actionType;
        $this->detail     = [
            'filename'    => $attachment->filename,
            'client_name' => $this->client['name'],
            'client_id'   => $this->client['id']
        ];
    }
}
