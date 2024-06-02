<?php

namespace Addons\TimeTracking\Models;

class TimeTrackingProjectObserver
{
    public function creating($project)
    {
        $project->status = 'active';
    }

    public function deleted($project)
    {
        TimeTrackingTimer::whereIn('time_tracking_task_id', function ($query) use ($project)
        {
            $query->select('id')->from('time_tracking_tasks')->where('time_tracking_project_id', $project->id);
        })->delete();

        TimeTrackingTask::where('time_tracking_project_id', $project->id)->delete();
    }
}