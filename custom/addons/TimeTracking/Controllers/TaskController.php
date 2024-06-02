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

use Addons\TimeTracking\Models\TimeTrackingProject;
use Addons\TimeTracking\Models\TimeTrackingTask;
use Addons\TimeTracking\Validators\TaskValidator;
use FI\Http\Controllers\Controller;

class TaskController extends Controller
{
    private $taskValidator;

    public function __construct(TaskValidator $taskValidator)
    {
        $this->taskValidator = $taskValidator;
    }

    public function create()
    {
        return view('time_tracking._task_create_modal')
            ->with('project', TimeTrackingProject::find(request('project_id')));
    }

    public function store()
    {
        $validator = $this->taskValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors'  => $validator->messages()->toArray(),
            ], 400);
        }

        TimeTrackingTask::create(request()->all());
    }

    public function edit()
    {
        $task = TimeTrackingTask::find(request('id'));

        return view('time_tracking._task_edit_modal')
            ->with('project', $task->project()->getSelect()->first())
            ->with('task', $task);
    }

    public function update()
    {
        $validator = $this->taskValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors'  => $validator->messages()->toArray(),
            ], 400);
        }

        TimeTrackingTask::find(request('id'))->fill(request()->only(['name', 'description']))->save();
    }

    public function updateDisplayOrder()
    {
        $displayOrder = 1;

        foreach (request('task_ids') as $id)
        {
            $id = str_replace('task_id_', '', $id);

            TimeTrackingTask::where('id', $id)->update(['display_order' => $displayOrder]);

            $displayOrder++;
        }
    }

    public function delete()
    {
        try
        {
            foreach (request('ids') as $id)
            {
                TimeTrackingTask::destroy($id);
                $session = session()->get('timeTracker', []);
                unset($session[$id]);
                session()->put('timeTracker', $session);
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.TimeTracking::lang.tasks_delete_error')], 400);
        }
    }

    public function deleteModal()
    {
        try
        {
            return view('time_tracking._delete_project_task_modal_details')
                ->with('url', request('action'))
                ->with('ids', json_encode(request('ids')))
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
}