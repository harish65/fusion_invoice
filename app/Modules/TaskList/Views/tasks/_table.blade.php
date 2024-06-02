<table class="table table-hover table-striped table-sm text-nowrap">

    <thead>
    <tr>
        @if(isset($bulk_action) && $bulk_action == true)
            <th>{!! Sortable::link('id', trans('fi.id')) !!}</th>
            <th>{!! Sortable::link('clients.name', trans('fi.client') , 'task') !!}</th>
            <th>{!! Sortable::link('title', trans('fi.title')) !!}</th>
            <th>{!! Sortable::link('description', trans('fi.description')) !!}</th>
            <th>{!! Sortable::link('due_date', trans('fi.due_date')) !!}</th>
            <th>{!! Sortable::link('assignee', trans('fi.assignee')) !!}</th>
            <th>{!! Sortable::link('completed_at', trans('fi.completed')) !!}</th>
            <th>{!! trans('fi.status') !!}</th>
            <th class="text-right">{!! trans('fi.options') !!}</th>
        @else
            @if(isset($client_view) && $client_view == true)
                <th>{!! trans('fi.id') !!}</th>
                <th>{!! trans('fi.title') !!}</th>
                <th>{!! trans('fi.description') !!}</th>
                <th>{!! trans('fi.due_date') !!}</th>
                <th>{!! trans('fi.assignee') !!}</th>
                <th>{!! trans('fi.completed') !!}</th>
                <th>{!! trans('fi.status') !!}</th>
                <th class="text-right">{!! trans('fi.options') !!}</th>
            @else
                <th>{!! Sortable::link('id', trans('fi.id')) !!}</th>
                <th>{!!  trans('fi.client') !!}</th>
                <th>{!! Sortable::link('title', trans('fi.title')) !!}</th>
                <th>{!! Sortable::link('description', trans('fi.description')) !!}</th>
                <th>{!! Sortable::link('due_date', trans('fi.due_date')) !!}</th>
                <th>{!! Sortable::link('assignee', trans('fi.assignee')) !!}</th>
                <th>{!! Sortable::link('completed_at', trans('fi.completed')) !!}</th>
                <th>{!! trans('fi.status') !!}</th>
                <th class="text-right">{!! trans('fi.options') !!}</th>
            @endif
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach ($tasks as $task)
        <tr>
            @if(isset($bulk_action) && $bulk_action == true)
                <td class="{{(($task->assignee_id == auth()->user()->id) && ($task->user_id == auth()->user()->id)) ? 'column-task-assigned-to-me' : ''}}">
                    <input type="checkbox" class="bulk-record" data-id="{{ $task->id }}">
                </td>
            @else
                <td>{{ $task->id }}</td>
            @endcan

            @if(! isset($client_view) || $client_view == false)
                @can('clients.view')
                    <td>
                        @if(isset($task->client->id))
                            <a href="{{ route('clients.show', [$task->client->id]) }}"
                               title="{{ trans('fi.view_client') }}">{{ $task->client->name }}</a>
                        @endif
                    </td>
                @else
                    <td>{{ isset($task->client->name) ? $task->client->name : '' }}</td>
                @endcan
            @endif

            <td>
                @if($task->user_id == auth()->user()->id || $task->assignee_id == auth()->user()->id)
                    <a href="{{ route('task.show', $task->id) }}">{{ $task->title  }}</a>
                @else
                    {{ $task->title  }}
                @endif
            </td>
            <td>{!! $task->formatted_description !!}</td>
            <td>{!! $task->formatted_due_date !!}</td>
            <td>{{$task->formatted_assignee}}</td>
            <td>{!! $task->formatted_completed_date !!}</td>
            <td>{{$task->is_complete == 1 ? trans('fi.transition.completed') : trans('fi.open')}}</td>
            @if(isset($bulk_action) && $bulk_action == true)
                <td class="text-right">
                    <div class="btn-group action-menu">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.options') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('task.show', [$task->id]) }}"
                               id="view-task-{{ $task->id }}">
                                <i class="fa fa-search"></i> {{ trans('fi.view') }}
                            </a>
                            @if($task->is_complete == 0)
                                <a href="javascript:void(0)" class="action-complete-with-note dropdown-item"
                                   data-task-title="{{ $task->title}}" data-task-id="{{ $task->id }}"
                                   data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-check text-info"></i> {{ trans('fi.complete-with-note') }}
                                </a>
                                <a href="javascript:void(0)" class="action-complete dropdown-item"
                                   data-task-id="{{ $task->id }}" data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-check"></i> {{ trans('fi.complete') }}
                                </a>
                            @else
                                <a href="javascript:void(0)" class="action-reopen dropdown-item"
                                   data-task-id="{{ $task->id }}" data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-check"></i> {{ trans('fi.reopen_task') }}
                                </a>
                            @endif
                            @if($task->user_id == auth()->user()->id || $task->assignee_id == auth()->user()->id)
                                <a class="dropdown-item" href="{{ route('task.edit', [$task->id]) }}">
                                    <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" data-action="{{ route('task.delete', [$task->id]) }}"
                                   data-redirect-to="{{ request()->fullUrl() }}" data-task-id="{{ $task->id }}"
                                   class="action-delete text-danger dropdown-item">
                                    <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </td>
            @else
                @if($task->user_id == auth()->user()->id || $task->assignee_id == auth()->user()->id)
                    <td class="text-right">
                        <div class="btn-group action-menu">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                {{ trans('fi.options') }} <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('task.show', [$task->id]) }}"
                                   id="view-task-{{ $task->id }}">
                                    <i class="fa fa-search"></i> {{ trans('fi.view') }}</a>
                                </li>
                                @if($task->is_complete == 0)

                                    <a href="javascript:void(0)" class="action-complete-with-note dropdown-item"
                                       data-task-title="{{ $task->title}}" data-task-id="{{ $task->id }}"
                                       data-redirect-to="{{ request()->fullUrl() }}">
                                        <i class="fa fa-check text-info"></i> {{ trans('fi.complete-with-note') }}
                                    </a>

                                    <a href="javascript:void(0)" class="action-complete dropdown-item"
                                       data-task-id="{{ $task->id }}"
                                       data-tab="tasks" data-redirect-to="{{ request()->fullUrl() }}">
                                        <i class="fa fa-check"></i> {{ trans('fi.complete') }}
                                    </a>
                                @else
                                    <a href="javascript:void(0)" class="action-reopen dropdown-item"
                                       data-task-id="{{ $task->id }}"
                                       data-tab="tasks"
                                       data-redirect-to="{{ request()->fullUrl() }}">
                                        <i class="fa fa-check"></i> {{ trans('fi.reopen_task') }}
                                    </a>
                                @endif
                                <a class="dropdown-item" href="{{ route('task.edit', [$task->id]) }}">
                                    <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" data-action="{{ route('task.delete', [$task->id]) }}"
                                   data-redirect-to="{{ request()->fullUrl() }}"
                                   data-task-id="{{ $task->id }}"
                                   data-tab="tasks"
                                   class="action-delete text-danger dropdown-item">
                                    <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                </a>
                            </div>
                        </div>
                    </td>
                @else
                    <td>&nbsp;</td>
                @endif
            @endif
        </tr>
    @endforeach
    </tbody>

</table>