<div id="project-task-list">
    @if ($tasks->count() == 0)
        <ul class="todo-list">
            <li class="text-center text-info">{{ trans('TimeTracking::lang.task_notice') }}</li>
        </ul>
    @else
        <span class="small">
        <a href="javascript:void(0)" id="btn-bulk-select-all">Select All</a> |
        <a href="javascript:void(0)" id="btn-bulk-deselect-all">Deselect All</a>
    </span>
        <ul class="todo-list ui-sortable task-section-list-table task-section-list-table-sortable">
            @foreach ($tasks as $task)
                <li id="task_id_{{ $task->id }}">
                    <div class="row">
                        <div class="col-sm-2 ">

                        <span class="handle">
                            <i class="fa fa-ellipsis-v"></i>
                            <i class="fa fa-ellipsis-v"></i>
                            <input type="hidden" value="{{ $task->id }}" class="order-id">
                        </span>

                            <input type="checkbox" class="checkbox-bulk-action mr-3" data-task-id="{{ $task->id }}">

                            @if (!$task->activeTimer)
                                <button class="btn btn-sm bg-gray btn-start-timer" data-task-id="{{ $task->id }}" style="width: 109px;"><i
                                            class="fa fa-play"></i>
                                    <strong>{{ trans('TimeTracking::lang.start_timer') }}
                                        <br>{{ $task->formatted_hours }} {{ trans('TimeTracking::lang.hours') }}
                                    </strong>
                                </button>
                            @else
                                <button class="btn btn-sm bg-olive btn-stop-timer"
                                        data-timer-id="{{ $task->activeTimer->id }}" data-task-id="{{ $task->id }}"><i
                                            class="fa fa-pause"></i>
                                    <strong>{{ trans('TimeTracking::lang.pause_timer') }}<br>
                                        <span id="hours_{{ $task->id }}">00</span>:<span
                                                id="minutes_{{ $task->id }}">00</span>:<span
                                                id="seconds_{{ $task->id }}">00</span></strong>
                                </button>
                            @endif
                        </div>

                        <div class="col-sm-8">
                            <div class="row">
                                <span class="font-weight-bold" title="{{trans('fi.name')}}">{{ $task->name }}</span>
                            </div>
                            <div class="row">
                                <p>{{ $task->description }}</p>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="tools" style="font-size: 1.25em;">
                                <a href="javascript:void(0);" class="btn-show-timers" data-task-id="{{ $task->id }}"
                                   data-toggle="tooltip" title="{{ trans('TimeTracking::lang.show_timers') }}">
                                    <i class="fa fa-clock"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn-bill-task" data-task-id="{{ $task->id }}"
                                   data-toggle="tooltip" title="{{ trans('TimeTracking::lang.bill_task') }}">
                                    <i class="fa fa-dollar-sign"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn-edit-task" data-task-id="{{ $task->id }}"
                                   data-toggle="tooltip" title="{{ trans('TimeTracking::lang.edit_task') }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                @can('time_tracking.delete')
                                <a href="javascript:void(0);" class="btn-delete-task text-danger"
                                   data-task-id="{{ $task->id }}"
                                   data-toggle="tooltip" title="{{ trans('TimeTracking::lang.delete_task') }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>