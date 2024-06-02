<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Notes;

use FI\Modules\Notes\Events\AddTransition;
use FI\Modules\Notes\Events\AddTransitionTags;
use FI\Modules\Notes\Models\NoteTag;
use FI\Modules\Transitions\Models\Transitions;

class EventSubscriber
{

    public function addTransition(AddTransition $event)
    {
        $transition          = new Transitions();
        $transition->user_id = auth()->user()->id;
        $notable             = $event->note->notable;
        if ($event->note->notable_type == 'FI\Modules\Clients\Models\Client')
        {
            $transition->client_id = $notable->id;
        }
        else
        {
            $transition->client_id = (isset($notable->client_id)) ? $notable->client_id : null;
        }
        $transition->transitionable_id   = $event->note->id;
        $transition->transitionable_type = 'FI\Modules\Notes\Models\Note';
        $transition->action_type         = $event->actionType;
        if (!empty($event->detail))
        {
            $transition->detail = json_encode($event->detail);
        }
        $transition->previous_value = $event->previousValue;
        $transition->current_value  = $event->currentValue;
        if ($transition->client_id)
        {
            $transition->save();
        }

    }

    public function addTransitionTags(AddTransitionTags $event)
    {
        if ($event->isDirtyNote == true)
        {
            $userId = isset(auth()->user()->id) ? auth()->user()->id : $event->note->user_id;

            $transition          = new Transitions();
            $transition->user_id = $userId;
            $notable             = $event->note->notable;
            if ($event->note->notable_type == 'FI\Modules\Clients\Models\Client')
            {
                $transition->client_id = $notable->id;
            }
            else
            {
                $transition->client_id = (isset($notable->client_id)) ? $notable->client_id : null;
            }
            $transition->transitionable_id   = $event->note->id;
            $transition->transitionable_type = 'FI\Modules\Notes\Models\Note';
            $transition->action_type         = $event->actionType;
            if (!empty($event->detail))
            {
                $transition->detail = json_encode($event->detail);
            }
            $transition->previous_value = $event->previousValue;
            $transition->current_value  = $event->currentValue;

            if ($transition->client_id)
            {
                $transition->save();
            }
        }
        if ($event->actionType == 'note_tag_deleted')
        {
            foreach ($event->tagId as $removeTagId)
            {
                NoteTag::whereNoteId($event->note->id)->whereTagId($removeTagId)->delete();
            }
        }

    }

    public function subscribe($events)
    {
        $events->listen('FI\Modules\Notes\Events\AddTransition', 'FI\Modules\Notes\EventSubscriber@addTransition');
        $events->listen('FI\Modules\Notes\Events\AddTransitionTags', 'FI\Modules\Notes\EventSubscriber@addTransitionTags');
    }
}
