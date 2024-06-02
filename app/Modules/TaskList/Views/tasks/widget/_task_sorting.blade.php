@if(count($tasks) > 0)

    @foreach($tasks as $task)
        <li class="{{ $task->is_complete ? 'done' : ''}} task-complete">
                <span class="handle ui-sortable-handle">
                  <i class="fas fa-ellipsis-v"></i>
                  <i class="fas fa-ellipsis-v"></i>
                      <input type="hidden" value="{{ $task->id }}" class="order-id">
                </span>
            <div class="icheck-primary d-inline ml-2"
                 title="{{trans('fi.ctrl_plus_click')}}">
                <input type="checkbox" id="task_status_{{ $task->id }}"
                       class="task-status" data-task-title="{{ $task->title}}"
                       data-task-id="{{ $task->id }}"
                       {{ $task->is_complete ? ' checked' : '' }}
                       data-link="{{ route('task.complete', ['id' => $task->id , 'complete' => $task->is_complete ? '0' : '1']) }}">
                <label for="task_status_{{ $task->id }}"></label>
            </div>
            <i class="fa initials">
                {!! ($task->assignee_id) ? $task->assignee->getAvatar(26) : null !!}
            </i>
            <span class="text">{{ $task->title }}</span>
            @if($task->attachments->count() > 0 || $task->notes->count() > 0 || $task->client || $task->due_date)
                @if($task->attachments->count() > 0)
                    <small class="badge badge-default">
                        <i class="fa fa-paperclip"> </i> {{ $task->attachments->count() }}
                    </small>
                @endif
                @if($task->notes->count() > 0)
                    <small class="badge badge-default"><i
                                class="fa fa-comments"> </i> {{ $task->notes->count() }}
                    </small>
                @endif
                @if($task->client)
                    <small class="task-list-smaller-font"> {!! $task->client ? '<a href="'.route('clients.show',$task->client).'">'.$task->client->name.'</a>' : '' !!}</small>
                @endif
                @if($task->due_date)
                    <small class="task-list-smaller-font {!! ($task->overdue && !$task->is_complete ? 'text-danger' : ($task->dueToday && !$task->is_complete ? 'text-success' : 'task-not-due'))!!}">{{ $task->formatted_due_date }} </small> @endif
                @if($task->created_at)
                    <small class="task-list-smaller-font float-right d-block task-action-date d-block pt-1"
                           title="{!! $task->created_at!!}" style="color: #999;">
                        <i class="fa fa-clock"></i>
                        {{ $task->_formatted_as_created_at}} </small>
                @endif
            @endif
            <div class="tools pr-2">
                @if(!$task->is_complete)
                    <button class="btn btn-xs btn-outline-primary btn-edit-task task-action-btn task-edit-btn d-done"
                            data-link="{{ route('task.widget.edit', ['id' => $task->id]) }}">
                        <i class="fas fa-edit"
                           title="{{ trans('fi.edit') }}"></i>
                    </button>
                @endif

                <button class="btn btn-xs btn-outline-danger btn-delete-task task-action-btn task-delete-btn d-done"
                        data-action="{{ route('task.delete', ['id' => $task->id]) }}">
                    <i class="fas fa-trash"
                       title="{{ trans('fi.delete') }}"></i>
                </button>

            </div>
        </li>
    @endforeach
    <p class="no-task"></p>
@else
    <p class="no-task">{{ trans('fi.no_records_found') }}</p>
@endif
