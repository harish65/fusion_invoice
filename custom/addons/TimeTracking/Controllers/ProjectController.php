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

use Addons\TimeTracking\Models\TimeTrackingPresetTask;
use Addons\TimeTracking\Models\TimeTrackingPresetTaskItem;
use Addons\TimeTracking\Models\TimeTrackingProject;
use Addons\TimeTracking\Models\TimeTrackingTask;
use Addons\TimeTracking\ProjectStatuses;
use Addons\TimeTracking\Requests\GetPresetTaskItemRequest;
use Addons\TimeTracking\Requests\PresetTaskItemRequest;
use Addons\TimeTracking\Requests\PresetTasksRequest;
use Addons\TimeTracking\Validators\ProjectValidator;
use FI\Http\Controllers\Controller;
use FI\Modules\Clients\Models\Client;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Mru\Events\MruLog;
use FI\Traits\ReturnUrl;

class ProjectController extends Controller
{
    use ReturnUrl;

    private $projectValidator;

    public function __construct(ProjectValidator $projectValidator)
    {
        $this->projectValidator = $projectValidator;
    }

    public function index()
    {
        $this->setReturnUrl();

        $projects = TimeTrackingProject::getSelect()
            ->companyProfileId(request('company_profile'))
            ->status(request('status'))
            ->orderBy('created_at', 'desc')
            ->paginate(config('fi.resultsPerPage'));

        return view('time_tracking.project_index')
            ->with('projects', $projects)
            ->with('statuses', ['' => trans('fi.all_statuses')] + ProjectStatuses::lists())
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList());
    }

    public function create()
    {
        return view('time_tracking.project_create')
            ->with('clients', Client::getDropDownList())
            ->with('companyProfiles', CompanyProfile::getList());
    }

    public function store()
    {
        $input = request()->all();

        $validator = $this->projectValidator->getValidator($input);

        if ($validator->fails())
        {
            return redirect()->route('timeTracking.projects.create')
                ->with('editMode', false)
                ->withErrors($validator)
                ->withInput();
        }

        $input['user_id'] = auth()->user()->id;

        $project = TimeTrackingProject::create($input);

        return redirect()->route('timeTracking.projects.edit', [$project->id]);
    }

    public function edit($id)
    {
        $this->setReturnUrl();

        $project = TimeTrackingProject::getSelect()->find($id);

        event(new MruLog(['module' => 'time_tracking', 'action' => 'edit', 'id' => $project->id, 'title' => $project->name]));

        return view('time_tracking.project_edit')
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('project', $project)
            ->with('tasks', $project->tasks()->getSelect()->unbilled()->orderBy('display_order')->get())
            ->with('tasksBilled', $project->tasks()->getSelect()->billed()->orderBy('display_order')->get())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('clients', Client::getDropDownList())
            ->with('statuses', ProjectStatuses::lists())
            ->with('enableTimerPopup', false);
    }

    public function update($id)
    {
        $input = request()->all();

        $validator = $this->projectValidator->getValidator($input);

        if ($validator->fails())
        {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400);
        }

        TimeTrackingProject::find($id)
            ->fill($input)
            ->save();

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $timeTrackingProject = TimeTrackingProject::whereId($id)->with('tasks')->first();
        $timeTrackerSession  = session()->get('timeTracker', []);

        foreach ($timeTrackingProject->tasks as $task)
        {
            unset($timeTrackerSession[$task->id]);
        }
        session()->put('timeTracker', $timeTrackerSession);

        $timeTrackingProject->delete();

        return redirect()->route('timeTracking.projects.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }

    public function refreshTaskList()
    {
        $project = TimeTrackingProject::find(request('project_id'));

        $tasks = TimeTrackingTask::getSelect()
            ->where('time_tracking_project_id', request('project_id'))
            ->orderBy('display_order')
            ->orderBy('created_at')
            ->unbilled()
            ->get();

        return view('time_tracking._task_list')
            ->with('project', $project)
            ->with('tasks', $tasks);
    }

    public function refreshTotals()
    {
        return view('time_tracking._project_edit_totals')
            ->with('project', TimeTrackingProject::getSelect()->find(request('project_id')));
    }

    public function deleteModal()
    {
        try
        {
            return view('time_tracking._delete_project_modal_details')->with('url', request('action'))->with('message', request('message'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function presetTaskApplyModal()
    {
        try
        {
            return view('time_tracking._project_preset_task_apply_modal')->with('projectId', request('project_id'))->with('presetTasks', TimeTrackingPresetTask::getList());
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function getPresetTasksApply()
    {
        try
        {
            $timeTrackingPresetTasks = TimeTrackingPresetTask::all();
            return view('time_tracking._table_project_preset_tasks')->with('presetTasks', $timeTrackingPresetTasks)
                ->with('taskItemsCount', TimeTrackingPresetTask::taskItemsCount())->with('editMode', false);

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function presetTaskModal()
    {
        try
        {
            return view('time_tracking._project_preset_task_modal')
                ->with('projectId', request('project_id'))
                ->with('presetTasks', TimeTrackingPresetTask::getList());
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function getPresetTasks()
    {
        try
        {
            $timeTrackingPresetTasks = TimeTrackingPresetTask::all();
            return view('time_tracking._table_project_preset_tasks')
                ->with('presetTasks', $timeTrackingPresetTasks)
                ->with('taskItemsCount', TimeTrackingPresetTask::taskItemsCount())
                ->with('editMode', true)->render();
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function presetTaskStore(PresetTasksRequest $request)
    {
        try
        {
            TimeTrackingPresetTask::updateOrCreate(
                ['id' => request('id')],
                ['list_name' => request('list_name')]
            );

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted'),], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function deletePresetTask($id)
    {
        try
        {
            $timeTrackingPresetTask = TimeTrackingPresetTask::find($id);
            $timeTrackingPresetTask->delete();
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }

    }

    public function presetTaskApply()
    {
        try
        {
            $timeTrackingPresetTask = TimeTrackingPresetTask::find(request('id'));

            if ($timeTrackingPresetTask && (isset($timeTrackingPresetTask->item) && $timeTrackingPresetTask->item->count() > 0))
            {
                foreach ($timeTrackingPresetTask->item as $item)
                {

                    TimeTrackingTask::create([
                        'time_tracking_project_id' => request('project_id'),
                        'name'                     => $item->task_name,
                        'display_order'            => $item->display_order,
                    ]);
                }
            }
            else
            {
                return response()->json(['success' => false, 'message' => trans('TimeTracking::lang.not_task_found')], 400);
            }

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated'),], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('TimeTracking::lang.error_task_apply')], 400);
        }
    }

    public function deleteTasksModal()
    {
        try
        {
            return view('layouts._delete_modal_details')->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }


    public function presetTaskItemModal()
    {
        try
        {
            return view('time_tracking._project_preset_task_item_modal')->with('projectId', request('project_id'))->with('presetId', request('preset_id'))->with('presetTaskNames', TimeTrackingPresetTask::getList())->with('presetTaskItems', TimeTrackingPresetTaskItem::getList());
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }

    public function presetTaskItemStore(PresetTaskItemRequest $request)
    {
        try
        {
            $timeTrackingPresetTaskItem                = TimeTrackingPresetTaskItem::updateOrCreate(['time_tracking_preset_tasks_id' => request('time_tracking_preset_tasks_id'), 'id' => request('id')], ['task_name' => request('task_name')]);
            $timeTrackingPresetTaskItem->display_order = ($timeTrackingPresetTaskItem->display_order == null) ? $timeTrackingPresetTaskItem->id : $timeTrackingPresetTaskItem->display_order;
            $timeTrackingPresetTaskItem->save();

            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated'),], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function getPresetTaskItems(GetPresetTaskItemRequest $request)
    {
        try
        {
            $timeTrackingPresetTaskItems = TimeTrackingPresetTaskItem::getPresetTaskItems(request('time_tracking_preset_tasks_id'));
            return view('time_tracking._table_project_preset_task_items')->with('items', $timeTrackingPresetTaskItems)->render();
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function deletePresetTaskItem($id)
    {
        try
        {
            $timeTrackingPresetTaskItems = TimeTrackingPresetTaskItem::find($id);
            $timeTrackingPresetTaskItems->delete();
            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_deleted')], 200);

        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }

    }

    public function presetTaskItemsReorder()
    {
        try
        {
            foreach (request('ids') as $key => $dataArrays)
            {
                $data                       = explode('###', $dataArrays);
                $timeTrackingPresetTaskItem = TimeTrackingPresetTaskItem::find($data[0]);
                if ($timeTrackingPresetTaskItem)
                {
                    $timeTrackingPresetTaskItem->display_order = $data[1] + 1;
                    $timeTrackingPresetTaskItem->save();
                }
            }


            return response()->json(['success' => true, 'message' => trans('fi.record_successfully_updated'),], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => trans('fi.unknown_error')], 400);
        }
    }

    public function deleteTasksItemModal()
    {
        try
        {
            return view('time_tracking._delete_preset_task_item_modal')->with('url', request('action'))->with('returnURL', request('returnURL'))->with('modalName', request('modalName'))->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }


}