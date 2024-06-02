<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\TaskList\Models;

use FI\Modules\Attachments\Events\CheckAttachment;
use FI\Modules\Notifications\Models\Notification;
use FI\Modules\TaskList\Events\AddNotification;
use FI\Modules\TaskList\Events\AddTransition;
use FI\Modules\TaskList\Events\TaskCompleteNotification;

class TaskObserver
{
    public function deleted(Task $task)
    {
        foreach ($task->attachments as $attachment)
        {
            $attachment->delete();
        }

        foreach ($task->notifications as $notification)
        {
            $notification->delete();
        }
    }

    public function created(Task $task)
    {
        event(new AddTransition($task, 'created'));
        event(new AddNotification($task, 'created'));
    }

    public function updating(Task $task)
    {

        $getUserId = Task::whereId($task->id)->first();
        if ($getUserId)
        {
            Notification::whereNotifiableId($getUserId->id)->whereUserId($getUserId->assignee_id)->whereIsViewed(0)->delete();
        }

        if ($task->isDirty('is_complete') && $task->is_complete)
        {
            Notification::whereNotifiableId($task->id)->whereNotifiableType('FI\Modules\TaskList\Models\Task')->update(['is_viewed' => 1]);
            event(new AddTransition($task, 'completed'));
            event(new TaskCompleteNotification($task, 'completed'));
        }
        else
        {
            event(new AddTransition($task, 'updated'));
        }

        if ($task->isDirty('assignee_id') && $task->assignee_id != '')
        {
            event(new AddNotification($task, 'created'));
        }
    }

    public function saved(Task $task)
    {
        event(new CheckAttachment($task));
    }
}