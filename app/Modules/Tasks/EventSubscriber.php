<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Tasks;

use FI\Modules\Notifications\Models\Notification;
use FI\Modules\Tasks\Events\AddNotification;
use FI\Modules\Users\Models\User;

class EventSubscriber
{

    public function addNotification(AddNotification $event)
    {
        $notification                  = new Notification();
        $notification->user_id         = User::whereUserType('system')->first()->id;
        $notification->notifiable_id   = null;
        $notification->notifiable_type = null;
        $notification->action_type     = $event->actionType;
        $notification->type            = 'info';
        if (!empty($event->detail))
        {
            $notification->detail = json_encode($event->detail);
        }
        $notification->save();
    }

    public function subscribe($events)
    {
        $events->listen('FI\Modules\Tasks\Events\AddNotification', 'FI\Modules\Tasks\EventSubscriber@addNotification');
    }
}
