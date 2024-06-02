<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Models;

class TimeTrackingTaskObserver
{
    public function creating($task)
    {
        $maxDisplayOrder = TimeTrackingTask::where('time_tracking_project_id', $task->time_tracking_project_id)->max('display_order');

        $task->display_order = $maxDisplayOrder + 1;
    }

    public function deleted($task)
    {
        TimeTrackingTimer::where('time_tracking_task_id', $task->id)->delete();
    }
}