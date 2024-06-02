<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking;

use Addons\TimeTracking\Events\StopTimeTrackerTasks;
use Addons\TimeTracking\Models\TimeTrackingProject;
use Addons\TimeTracking\Models\TimeTrackingTimer;

class EventSubscriber
{
    public function stopTimeTrackerTasks(StopTimeTrackerTasks $event)
    {
        $timeTrackingProjects = TimeTrackingProject::whereUserId($event->userId)->get();

        if ($timeTrackingProjects->count() > 0)
        {
            foreach ($timeTrackingProjects as $timeTrackingProject)
            {
                if ($timeTrackingProject->tasks->count() > 0)
                {
                    foreach ($timeTrackingProject->tasks as $task)
                    {
                        $timeTrackingTimers = TimeTrackingTimer::where('time_tracking_task_id', $task->id)->where('end_at', '0000-00-00')->get();
                        if ($timeTrackingTimers->count() > 0)
                        {
                            foreach ($timeTrackingTimers as $timeTrackingTimer)
                            {
                                $timeTrackingTimer->end_at = date('Y-m-d H:i:s');
                                $timeTrackingTimer->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public function subscribe($events)
    {
        $events->listen('Addons\TimeTracking\Events\StopTimeTrackerTasks', 'Addons\TimeTracking\EventSubscriber@stopTimeTrackerTasks');
    }
}
