<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) Sqware Pig, LLC <hello@squarepiginteractive.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\TaskList\Controllers;

use Carbon\Carbon;
use FI\Http\Controllers\Controller;
use FI\Jobs\GenerateTaskDueNotification;
use FI\Modules\Clients\Models\Client;
use FI\Modules\TaskList\Models\Task;
use FI\Modules\TaskList\Models\TaskSection;
use FI\Modules\TaskList\Requests\TaskCompletionNoteRequest;
use FI\Modules\TaskList\Requests\TaskReorderRequest;
use FI\Modules\TaskList\Requests\TaskStoreRequest;
use FI\Modules\TaskList\Requests\TaskUpdateRequest;
use FI\Modules\Users\Models\User;
use FI\Support\Frequency;
use FI\Traits\ReturnUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Session;

class TaskController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();
        $sortable = ['task_section_id' => 'asc'];
        if (request()->has('s') && request()->has('o'))
        {
            Cookie::queue(Cookie::forever('task_sort_column', request()->get('s')));
            Cookie::queue(Cookie::forever('task_sort_order', request()->get('o')));
        }
        elseif (Cookie::get('task_sort_column') && Cookie::get('task_sort_order'))
        {
            request()->merge(['s' => Cookie::get('task_sort_column'), 'o' => Cookie::get('task_sort_order')]);
        }
        $dateRangeFrom = request('from_date', null);
        $dateRangeTo   = request('to_date', null);
        $tasks         = Task::select('tasks.*')->with(['client'])
            ->keywords(request('search', ''), true, true, true, true)
            ->status(request('status', 'open'))
            ->taskFilters(request('taskFilters', 'my_tasks'))
            ->dateRange($dateRangeFrom, $dateRangeTo)
            ->sortable($sortable)
            ->paginate(config('fi.defaultNumPerPage'));

        return view('tasks.index')
            ->with('tasks', $tasks)
            ->with('me', auth()->user())
            ->with('users', User::getUserList())
            ->with('client', request('client'))
            ->with('searchPlaceholder', trans('fi.search_tasks'))
            ->with('taskFilters', ['my_tasks' => trans('fi.my_tasks'), 'assigned_to_others' => trans('fi.assigned_to_others'), 'assigned_from_others' => trans('fi.assigned_from_others'), 'all_tasks' => trans('fi.all_tasks_and_all_users')])
            ->with('statuses', ['all' => trans('fi.all_statuses'), 'closed' => trans('fi.closed'), 'overdue' => trans('fi.overdue'), 'open' => trans('fi.open')])
            ->with('clients', Client::getClientList());

    }

    public function taskList()
    {
        if (request('assignee'))
        {
            Cookie::queue(Cookie::make('assignee', request('assignee', null)));
        }
        if (request('status'))
        {
            Cookie::queue(Cookie::make('status', request('status')));
        }
        if (request('from_date'))
        {
            Cookie::queue(Cookie::make('dataFrom', request('from_date', null)));
        }
        if (request('to_date'))
        {
            Cookie::queue(Cookie::make('dataTo', request('to_date', null)));
        }
        if (request('search'))
        {
            Cookie::queue(Cookie::make('search', request('search', null)));
        }

        $filterBy            = request('filterBy', []);
        $filterByDescription = $filterBy['description'] ?? 0;
        $filterByTitle       = $filterBy['title'] ?? 0;
        $filterByClient      = $filterBy['client'] ?? 0;
        $filterByAssignee    = $filterBy['assignee'] ?? 0;
        $dateRangeFrom       = request('from_date', null);
        $dateRangeTo         = request('to_date', null);
        $taskSection         = request('taskSection');
        $filterByDescription == 1 ? Session::put('filter_by_task_description', 1) : Session::put('filter_by_task_description', 0);
        $filterByTitle == 1 ? Session::put('filter_by_title', 1) : Session::put('filter_by_title', 0);
        $filterByClient == 1 ? Session::put('filter_by_client', 1) : Session::put('filter_by_client', 0);
        $filterByAssignee == 1 ? Session::put('filter_by_assignee', 1) : Session::put('filter_by_assignee', 0);

        /** @var Collection $tasksCollection */
        $tasksTodayCollection = Task::select('tasks.*')->with(['assignee', 'client', 'taskSection'])
            ->keywords(request('search', ''), $filterByDescription, $filterByTitle, $filterByClient, $filterByAssignee)
            ->ownTasks(auth()->user()->id)
            ->status(request('status', 'open'))
            ->assignee(request('assignee', null))
            ->dateRange($dateRangeFrom, $dateRangeTo)
            ->where('tasks.task_section_id', '=', $taskSection)
            ->sort('due_date', 'ASC')
            ->paginate(10);
        $taskSectionLists     = TaskSection::getTaskSectionList();

        return view('tasks.widget.list')
            ->with('tasks', $tasksTodayCollection)
            ->with('sectionId', $taskSection)
            ->with('section', $taskSection)
            ->with('sectionSlug', $taskSectionLists[$taskSection])
            ->with('sectionName', trans('fi.' . $taskSectionLists[$taskSection]))
            ->with('users', User::getUserList())
            ->with('client', request('client'))
            ->with('clients', Client::getClientList());
    }

    public function createWidget()
    {
        return view('tasks.widget.create_edit_modal')
            ->with('editMode', false)
            ->with('users', User::getUserList())
            ->with('taskSections', TaskSection::getTaskSectionListByName())
            ->with('client', request('client'))
            ->with('frequencies', Frequency::lists())
            ->with('clients', Client::getClientList())
            ->with('tab', request('tab', ''));
    }

    public function storeWidget(TaskStoreRequest $request)
    {
        Task::create([
            'user_id'             => auth()->user()->id,
            'title'               => $request->post('title'),
            'description'         => $request->post('description'),
            'due_date'            => $request->post('due_date_timestamp'),
            'assignee_id'         => $request->post('assignee_id'),
            'client_id'           => $request->post('client_id'),
            'task_section_id'     => $request->post('task_section_id'),
            'is_recurring'        => $request->post('is_recurring', 0),
            'recurring_frequency' => $request->post('recurring_frequency'),
            'recurring_period'    => $request->post('recurring_period'),
        ]);

        return response()->json([
            'message' => trans('fi.task_successfully_created'),
        ]);
    }

    public function editWidget($id)
    {
        $user_id = auth()->user()->id;

        $task = Task::whereId($id)->where(function ($query) use ($user_id) {
            $query->orWhere('user_id', $user_id)->orWhere('assignee_id', $user_id);
        })->first();

        return view('tasks.widget.create_edit_modal')
            ->with('editMode', true)
            ->with('task', $task)
            ->with('taskSections', TaskSection::getTaskSectionListByName())
            ->with('users', User::getUserList())
            ->with('frequencies', Frequency::lists())
            ->with('clients', Client::getClientList())
            ->with('tab', '');
    }

    public function updateWidget(TaskUpdateRequest $request, $id)
    {
        $task                      = Task::find($id);
        $task->title               = $request->post('title');
        $task->description         = $request->post('description');
        $task->due_date            = $request->post('due_date_timestamp') != '' ? $request->post('due_date_timestamp') : null;
        $task->assignee_id         = $request->post('assignee_id');
        $task->client_id           = $request->post('client_id');
        $task->task_section_id     = $request->post('task_section_id');
        $task->is_recurring        = $request->post('is_recurring', 0);
        $task->recurring_frequency = $request->post('recurring_frequency');
        $task->recurring_period    = $request->post('recurring_period');
        if ($request->post('is_complete') != 'undefined')
        {
            $task->is_complete = $request->post('is_complete');
        }
        $task->completion_note = ($request->post('completion_note') == 'undefined') ? null : $request->post('completion_note');
        $task->save();

        return response()->json([
            'message' => trans('fi.task_successfully_updated'),
        ]);
    }

    public function create()
    {
        return view('tasks.form')
            ->with('editMode', false)
            ->with('users', User::getUserList())
            ->with('taskSections', TaskSection::getTaskSectionListByName())
            ->with('client', request('client'))
            ->with('returnUrl', $this->getReturnUrl())
            ->with('frequencies', Frequency::lists())
            ->with('clients', Client::getClientList());
    }

    public function store(TaskStoreRequest $request)
    {
        $task = Task::create([
            'user_id'             => auth()->user()->id,
            'title'               => $request->post('title'),
            'description'         => $request->post('description'),
            'due_date'            => $request->post('due_date_timestamp'),
            'assignee_id'         => $request->post('assignee_id'),
            'client_id'           => $request->post('client_id'),
            'task_section_id'     => $request->post('task_section_id'),
            'is_recurring'        => $request->post('is_recurring', 0),
            'recurring_frequency' => $request->post('recurring_frequency'),
            'recurring_period'    => $request->post('recurring_period'),
            'next_date'           => ($request->post('is_recurring') == 1) ? Carbon::now()->format('Y-m-d') : null,
        ]);

        return response()->json([
            'message' => trans('fi.task_successfully_created'),
            'task_id' => $task->id,
        ]);
    }

    public function edit($id)
    {
        $user_id = auth()->user()->id;

        $task = Task::whereId($id)->where(function ($query) use ($user_id) {
            $query->orWhere('user_id', $user_id)->orWhere('assignee_id', $user_id);
        })->first();

        return view('tasks.form')
            ->with('editMode', true)
            ->with('task', $task)
            ->with('taskSections', TaskSection::getTaskSectionListByName())
            ->with('users', User::getUserList())
            ->with('returnUrl', $this->getReturnUrl())
            ->with('frequencies', Frequency::lists())
            ->with('clients', Client::getClientList());
    }

    public function update(TaskUpdateRequest $request, $id)
    {
        $task                      = Task::find($id);
        $task->title               = $request->post('title');
        $task->description         = $request->post('description');
        $task->due_date            = $request->post('due_date_timestamp');
        $task->assignee_id         = $request->post('assignee_id');
        $task->client_id           = $request->post('client_id');
        $task->task_section_id     = $request->post('task_section_id');
        $task->is_recurring        = $request->post('is_recurring', 0);
        $task->recurring_frequency = $request->post('recurring_frequency');
        $task->recurring_period    = $request->post('recurring_period');
        if ($request->post('is_complete') != 'undefined')
        {
            $task->is_complete = $request->post('is_complete');
        }
        $task->completion_note = $request->post('completion_note');

        $task->save();

        return response()->json([
            'message' => trans('fi.task_successfully_updated'),
            'task_id' => $task->id,
        ]);
    }

    public function show($id)
    {
        $user_id = auth()->user()->id;

        $task = Task::whereId($id)->where(function ($query) use ($user_id) {
            $query->orWhere('user_id', $user_id)->orWhere('assignee_id', $user_id);
        })->first();

        if ($task)
        {
            return view('tasks.view')
                ->with('me', auth()->user())
                ->with('returnUrl', $this->getReturnUrl())
                ->with('task', $task);
        }
        else
        {
            return redirect(route('task.index'))->with('error', trans('fi.no_auth_to_view_task'));
        }
    }

    public function completeToggle($id, $complete)
    {
        $task               = Task::find($id);
        $task->is_complete  = $complete;
        $task->completed_at = Carbon::now();
        $task->save();

        return response()->json([
            'message' => ($complete) ? trans('fi.task_completed') : trans('fi.task_marked_incomplete'),
        ]);
    }

    public function delete($taskId)
    {
        $user_id = auth()->user()->id;
        $task    = Task::whereId($taskId)->where(function ($query) use ($user_id) {
            $query->orWhere('user_id', $user_id)->orWhere('assignee_id', $user_id);

        })->first();

        if ($task->id)
        {
            $task->destroy($task->id);
        }
    }

    public function reorder(TaskReorderRequest $request)
    {
        $ids = $request->get('ids');
        foreach ($ids as $key => $id)
        {
            Task::whereId($id)->update(['sequence' => $key, 'task_section_id' => $request->get('task_section_id')]);
        }
    }

    public function refresh()
    {
        Cookie::queue(Cookie::forget('assignee'));
        Cookie::queue(Cookie::forget('status'));
        Cookie::queue(Cookie::forget('dataFrom'));
        Cookie::queue(Cookie::forget('dataTo'));
        Cookie::queue(Cookie::forget('search'));
        GenerateTaskDueNotification::dispatch(auth()->user());
        $sections = TaskSection::all();
        $later    = $today = $tomorrow = '';
        foreach ($sections as $section)
        {
            if ($section->slug == 'later')
            {
                $later = $section->id;
            }
            elseif ($section->slug == 'today')
            {
                $today = $section->id;
            }
            elseif ($section->slug == 'tomorrow')
            {
                $tomorrow = $section->id;
            }
        }
        $tasks = Task::select('tasks.*')->taskSections([$later, $tomorrow])->get();
        foreach ($tasks as $task)
        {
            if (Carbon::parse($task->due_date)->format('Y-m-d') == Carbon::now()->format('Y-m-d'))
            {
                $task->task_section_id = $today;
                $task->save();
            }
            elseif (Carbon::parse($task->due_date)->format('Y-m-d') == Carbon::now()->addDay()->format('Y-m-d'))
            {
                $task->task_section_id = $tomorrow;
                $task->save();
            }
        }
    }

    public function orderBy(Request $request)
    {
        $tasksCollection = Task::select('tasks.*')->with(['assignee', 'client', 'taskSection'])
            ->ownTasks(auth()->user()->id)
            ->where('task_section_id', $request->get('sectionId'))
            ->status(request('status', 'open'))
            ->orderBy('due_date', $request->get('dir'))
            ->paginate(10);

        $taskSectionLists = TaskSection::getTaskSectionList();

        return view('tasks.widget._task_sorting')
            ->with('tasks', $tasksCollection)
            ->with('sectionId', $request->get('sectionId'))
            ->with('section', $request->get('sectionId'))
            ->with('sectionSlug', $taskSectionLists[$request->get('sectionId')])
            ->with('sectionName', trans('fi.' . $taskSectionLists[$request->get('sectionId')]))
            ->with('users', User::getUserList())
            ->with('client', request('client'))
            ->with('clients', Client::getClientList());
    }

    public function taskCompleteModal($id)
    {
        $task = Task::find($id);
        return view('tasks.complete_with_note_modal')
            ->with('id', $id)
            ->with('title', $task->title)
            ->with('redirectUrl', request('redirect'))
            ->with('widgetDashboard', request('widget'))
            ->with('completionNote', $task->completion_note);
    }

    public function completeWithNote(TaskCompletionNoteRequest $request)
    {
        try
        {
            $task                  = Task::find(request('id'));
            $task->is_complete     = 1;
            $task->completion_note = request('completion_note');
            $task->completed_at    = Carbon::now();
            $task->save();
            return response()->json([
                'message' => trans('fi.task_successfully_updated'),
                'task_id' => request('id'),
                'success' => true,
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage(),
                'task_id' => request('id'),
                'success' => false,
            ]);
        }
    }

    public function deleteModal()
    {
        try
        {
            return view('tasks._modal_task_delete')
                ->with('url', request('action'))
                ->with('returnURL', request('returnURL'))
                ->with('tab', request('tab', null))
                ->with('widgetTask', request('widgetTask'))
                ->with('modalName', request('modalName'))
                ->with('isReload', request('isReload'));
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'message' => ($e->getCode() == 0) ? trans('fi.modal_not_found') : $e->getMessage()], 400);
        }
    }
}