<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\Commission;

use Addons\Commission\Events\AddInvoiceItemCommissionTransition;
use Addons\Commission\Events\AddRecurringInvoiceItemCommissionTransition;
use FI\Modules\Transitions\Models\Transitions;
use FI\Modules\Users\Models\User;

class EventSubscriber
{

    public function addInvoiceItemCommissionTransition(AddInvoiceItemCommissionTransition $event)
    {
        $eventUserId = isset($event->userId) ? $event->userId : null;
        $userId = isset(auth()->user()->id) ? auth()->user()->id : $eventUserId;
        
        if ($userId == null)
        {
            $userId = User::whereUserType('system')->first()->id;
        }

        $transition                      = new Transitions();
        $transition->user_id             = $userId;
        $transition->client_id           = $event->client_id;
        $transition->transitionable_id   = $event->id;
        $transition->transitionable_type = 'Addons\Commission\Models\InvoiceItemCommission';
        $transition->action_type         = $event->actionType;
        if (!empty($event->detail))
        {
            $transition->detail = json_encode($event->detail);
        }
        $transition->previous_value = $event->previousValue;
        $transition->current_value  = $event->currentValue;
        $transition->save();

    }

    public function addRecurringInvoiceItemCommissionTransition(AddRecurringInvoiceItemCommissionTransition $event)
    {
        $userId = isset(auth()->user()->id) ? auth()->user()->id : $event->userId;

        if ($userId == null)
        {
            $userId = User::whereUserType('system')->first()->id;
        }

        $transition                      = new Transitions();
        $transition->user_id             = $userId;
        $transition->client_id           = $event->client_id;
        $transition->transitionable_id   = $event->id;
        $transition->transitionable_type = 'Addons\Commission\Models\RecurringInvoiceItemCommission';
        $transition->action_type         = $event->actionType;
        if (!empty($event->detail))
        {
            $transition->detail = json_encode($event->detail);
        }
        $transition->previous_value = $event->previousValue;
        $transition->current_value  = $event->currentValue;
        $transition->save();

    }

    public function subscribe($events)
    {
        $events->listen('Addons\Commission\Events\AddInvoiceItemCommissionTransition', 'Addons\Commission\EventSubscriber@addInvoiceItemCommissionTransition');
        $events->listen('Addons\Commission\Events\AddRecurringInvoiceItemCommissionTransition', 'Addons\Commission\EventSubscriber@addRecurringInvoiceItemCommissionTransition');
    }
}
