<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Addons\TimeTracking\Controllers;

use Addons\TimeTracking\Models\TimeTrackingTask;
use Addons\TimeTracking\Models\TimeTrackingTimer;
use Addons\TimeTracking\Validators\TimerValidator;
use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Modules\Settings\Models\UserSetting;
use Illuminate\Support\Facades\DB;

class TimerController extends Controller
{
    private $timerValidator;
    private $timeTrackingTask;

    public function __construct(
        TimerValidator $timerValidator,
        TimeTrackingTask $timeTrackingTask
    )
    {
        $this->timerValidator   = $timerValidator;
        $this->timeTrackingTask = $timeTrackingTask;
    }

    public function index()
    {
        $sessions = session()->get('timeTracker');
        return view('time_tracking.widget._ajax_task_list')
            ->with('timeTrackerTasks', $sessions)
            ->with('show', isset($sessions) && count($sessions) > 0)
            ->with('mySqlTime', DB::select('SELECT NOW() as datetime')[0]->datetime)
            ->render();
    }

    public function start()
    {
        if (TimeTrackingTimer::where('time_tracking_task_id', request('task_id'))->where('end_at', '0000-00-00')->count() == 0)
        {
            $timer = new TimeTrackingTimer([
                'time_tracking_task_id' => request('task_id'),
                'start_at'              => date('Y-m-d H:i:s'),
            ]);

            $timer->save();

            $data = [
                'name'            => $timer->task->name,
                'seconds'         => $this::seconds(),
                'project_id'      => request('project_id'),
                'activeTimer'     => false,
                'formatted_hours' => 00.00,
            ];

            $session                     = session()->get('timeTracker', []);
            $session[request('task_id')] = array_merge($timer->toArray(), $data);
            session()->put('timeTracker', $session);

        }
    }

    public function stop()
    {
        $endAt = date('Y-m-d H:i:s');

        $timer = TimeTrackingTimer::find(request('timer_id'));

        $startAt = Carbon::parse($timer->start_at);
        $endAt   = Carbon::parse($endAt);

        $task = TimeTrackingTask::getSelect()
            ->where('time_tracking_project_id', request('project_id'))
            ->where('id', request('task_id'))
            ->orderBy('display_order')
            ->orderBy('created_at')
            ->unbilled()
            ->first();

        $timer->end_at = $endAt;
        $timer->hours  = $endAt->diffInSeconds($startAt) / 60 / 60;
        $timer->save();

        $sessions = session()->get('timeTracker');

        foreach ($sessions as $key => $session)
        {
            if ($session['id'] == request('timer_id'))
            {
                if (request('remove') == 1)
                {
                    unset($sessions[$key]);
                }
                else
                {
                    $sessions[$key]['activeTimer']     = true;
                    $sessions[$key]['formatted_hours'] = $task->formatted_hours;
                    $sessions[$key]['project_id']      = request('project_id');
                }
            }
        }

        session()->put('timeTracker', $sessions);
    }

    public function show()
    {
        $task = TimeTrackingTask::find(request('time_tracking_task_id'));

        return view('time_tracking._timer_modal')
            ->with('task', $task)
            ->with('project', $task->project)
            ->with('timers', $task->timers->sortByDesc('start_at'));
    }

    public function store()
    {
        $validator = $this->timerValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors'  => $validator->messages()->toArray(),
            ], 400);
        }

        if (request('id') != '')
        {
            $timer = TimeTrackingTimer::find(request('id'));

            $startAt = Carbon::parse(request('start_at'));
            $endAt   = Carbon::parse(request('end_at'));

            $timer->start_at = $startAt;
            $timer->end_at   = $endAt;
            $timer->hours    = $endAt->diffInSeconds($startAt) / 60 / 60;
            $timer->save();
        }
        else
        {
            $timer = new TimeTrackingTimer(request()->all());

            $startAt = Carbon::parse(request('start_at'));
            $endAt   = Carbon::parse(request('end_at'));

            $timer->start_at = $startAt;
            $timer->end_at   = $endAt;
            $timer->hours    = $endAt->diffInSeconds($startAt) / 60 / 60;
            $timer->save();
        }

    }

    public function delete()
    {
        try
        {
            if (TimeTrackingTimer::destroy(request('id')) == true)
            {
                return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('fi.record_not_found')], 400);
            }

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public static function seconds()
    {
        $seconds = 0;

        $timers = TimeTrackingTimer::where('time_tracking_task_id', request('task_id'))->get();

        foreach ($timers as $timer)
        {
            if ($timer->end_at != '0000-00-00 00:00:00')
            {
                $endAt = Carbon::parse($timer->end_at);
            }
            else
            {
                $endAt = Carbon::now();
            }

            $startAt = Carbon::parse($timer->start_at);

            $seconds += $endAt->diffInSeconds($startAt);
        }

        return $seconds;
    }

    public function refreshList()
    {
        $timers = TimeTrackingTimer::where('time_tracking_task_id', request('time_tracking_task_id'))
            ->orderBy('start_at', 'desc')
            ->get();

        return view('time_tracking._timer_list')
            ->with('timers', $timers);
    }

    public function deleteModal()
    {
        try
        {
            return view('time_tracking._delete_project_task_timer_modal_details')
                ->with('url', request('action'))
                ->with('id', request('id'))
                ->with('message', request('message'))
                ->with('returnURL', request('returnURL'))
                ->with('modalName', request('modalName'))
                ->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function settingModal()
    {
        try
        {
            return view('time_tracking.widget._modal_enable_task_popup');
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function enableDisable()
    {
        try
        {
            UserSetting::saveByKey('floatingTimeTrackingAddon', request('flag'), auth()->user());
            $enableDisable = request('flag') == 1 ? 'enable' : 'disable';

            return response()->json(['success' => true, 'message' => trans('TimeTracking::lang.floating_tta_' . $enableDisable)], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('TimeTracking::lang.setting_update_error')], $e->getCode());
        }
    }
}