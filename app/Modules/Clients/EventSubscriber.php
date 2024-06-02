<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Clients;

use FI\Modules\Clients\Events\AddTransition;
use FI\Modules\Clients\Events\AddTransitionTags;
use FI\Modules\Clients\Models\ClientTag;
use FI\Modules\Transitions\Models\Transitions;
use FI\Modules\Users\Models\User;

class EventSubscriber
{
    public function addTransition(AddTransition $event)
    {
        $userId = isset(auth()->user()->id) ? auth()->user()->id : '';

        if ($userId == null)
        {
            $userId = User::whereUserType('system')->first()->id;
        }

        $transition                      = new Transitions();
        $transition->user_id             = $userId;
        $transition->client_id           = $event->client->id;
        $transition->transitionable_id   = $event->client->id;
        $transition->transitionable_type = 'FI\Modules\Clients\Models\Client';
        $transition->action_type         = $event->actionType;
        if (!empty($event->detail))
        {
            $transition->detail = json_encode($event->detail);
        }
        $transition->previous_value = $event->previousValue;
        $transition->current_value  = $event->currentValue;
        $transition->save();

    }

    public function addTransitionTags(AddTransitionTags $event)
    {
        $userId = isset(auth()->user()->id) ? auth()->user()->id : $event->userId;

        $transition                      = new Transitions();
        $transition->user_id             = $userId;
        $transition->client_id           = $event->client->id;
        $transition->transitionable_id   = $event->client->id;
        $transition->transitionable_type = 'FI\Modules\Clients\Models\Client';
        $transition->action_type         = $event->actionType;
        if (!empty($event->detail))
        {
            $transition->detail = json_encode($event->detail);
        }
        $transition->previous_value = $event->previousValue;
        $transition->current_value  = $event->currentValue;
        $transition->save();

        if ($event->actionType == 'client_tag_deleted')
        {
            foreach ($event->tagId as $removeTagId)
            {
                ClientTag::whereClientId($event->client->id)->whereTagId($removeTagId)->delete();
            }
        }

    }

    public function subscribe($events)
    {
        $events->listen('FI\Modules\Clients\Events\AddTransition', 'FI\Modules\Clients\EventSubscriber@addTransition');
        $events->listen('FI\Modules\Clients\Events\AddTransitionTags', 'FI\Modules\Clients\EventSubscriber@addTransitionTags');
    }
}
