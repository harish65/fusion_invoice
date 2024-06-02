<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Tasks\Events;

use FI\Events\Event;
use Illuminate\Queue\SerializesModels;

class AddNotification extends Event
{
    use SerializesModels;

    public $type;
    public $detail;
    public $actionType;

    public function __construct($task, $actionType)
    {
        $this->type       = $task['type'];
        $this->actionType = $actionType;
        $this->detail     = $task;
    }
}