@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('#btn-delete-task').on('click', function () {
                let $_this = $(this);

                $('#modal-placeholder').load('{!! route('task.delete.modal') !!}', {
                        action: $_this.data('action'),
                        modalName: 'task',
                        tab: $_this.data('tab'),
                        isReload: true,
                        returnURL: document.URL
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $('.action-complete').on('click', function () {
                let returnURL = document.URL;
                let task_id = $(this).data('task-id');
                let complete = $(this).data('complete');
                var url = '{{ route("task.complete",[ ":id", ":complete"] ) }}';
                url = url.replace(':id', task_id);
                url = url.replace(':complete', complete);
                var tab = $(this).data('tab');

                $.post(url).done(function () {
                    if (tab) {
                        var url = new URL(returnURL);
                        url.searchParams.set("tab", tab);
                        window.location.replace(url.href);
                    } else {
                        window.location.replace(returnURL);
                    }
                });
            });

            $('.action-complete-with-note').on('click', function () {
                let id = $(this).data('task-id');
                var url = '{{ route("task.complete-with-note.modal",[ ":id"] ) }}';
                url = url.replace(':id', id);
                $('#modal-placeholder').load(url,{widget : 0});
            });
        });
    </script>

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="d-inline">{{ trans('fi.view_task') }}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ $returnUrl }}" class="btn btn-sm btn-default">
                        <i class="fa fa-backward"></i> {{ trans('fi.back') }}
                    </a>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary task-save-confirm">
                            <i class="fa fa-save" data-complete="0"></i>
                            {{ trans('fi.save') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                data-toggle="dropdown"
                                aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            @if($task->is_complete == 0)
                                <a href="javascript:void(0)" class="action-complete-with-note  dropdown-item"
                                   data-task-title="{{ $task->title}}" data-task-id="{{ $task->id }}"
                                   data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-check text-info"></i> {{ trans('fi.complete-with-note') }}
                                </a>
                                <a href="javascript:void(0)" class="action-complete  dropdown-item" data-complete="1"
                                   data-task-id="{{ $task->id }}" data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-check"></i> {{ trans('fi.complete') }}
                                </a>
                            @endif
                            @if($task->is_complete == 1)
                                <a href="javascript:void(0)" class="action-complete  dropdown-item" data-complete="0"
                                   data-task-id="{{ $task->id }}" data-redirect-to="{{ request()->fullUrl() }}">
                                    <i class="fa fa-check text-danger"></i> {{ trans('fi.save-and-unComplete') }}
                                </a>
                            @endif

                        </div>
                    </div>

                    @if($task->user_id == $me->id)
                        <a id="task-edit-btn" href="{{ route('task.edit', [$task->id]) }}"
                           class="btn btn-sm btn-default">{{ trans('fi.edit') }}</a>
                        <a class="btn btn-sm btn-danger" href="#" data-action="{{ route('task.delete', [$task->id]) }}"
                           id="btn-delete-task"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                    @endif
                </div>
            </div>
        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-12">

                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"
                                    data-edit-link="{{ route('task.edit', [$task->id, 'tab' => 'general']) }}">
                                    <a class="nav-link active" id="general-tab" data-toggle="tab"
                                       href="#tab-details">{{ trans('fi.details') }}
                                    </a>
                                </li>
                                @can('attachments.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('task.edit', [$task->id, 'tab' => 'attachments']) }}">
                                        <a id="attachments-tab" data-toggle="tab" class="nav-link"
                                           href="#tab-attachments">
                                            {{ trans('fi.attachments') }} {!! $task->attachments->count() > 0 ? '<span class="badge badge-primary attachment-count">'.$task->attachments->count().'</span>' : '' !!}
                                        </a>
                                    </li>
                                @endcan
                                @can('notes.view')
                                    <li class="nav-item"
                                        data-edit-link="{{ route('task.edit', [$task->id, 'tab' => 'notes']) }}">
                                        <a id="notes-tab" class="nav-link" data-toggle="tab"
                                           href="#tab-notes">
                                            {{ trans('fi.notes') }} {!! $task->notes->count() > 0 ? '<span class="badge badge-primary notes-count">'.$task->notes->count().'</span>' : '' !!}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">

                                <div id="tab-details" class="tab-pane active">

                                    <div class="row">

                                        <div class="col-md-12">

                                            <table class="table table-sm table-striped">
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.title') }}</label></td>
                                                    <td class="col-md-10">{!! $task->title !!}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.description') }}</label>
                                                    </td>
                                                    <td class="col-md-10">{!! $task->description !!}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.due_date') }}</label></td>
                                                    <td class="col-md-10">{!! $task->formatted_due_date !!}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.task_section') }}</label>
                                                    </td>
                                                    <td class="col-md-10">{!! $task->taskSection->name !!}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-2"><label>{{ trans('fi.assignee') }}</label></td>
                                                    <td class="col-md-10">{!! $task->assignee->formatted_name !!}</td>
                                                </tr>
                                                @can('clients.view')
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.client') }}</label></td>
                                                        <td class="col-md-10">{!! ($task->client) ? "<a href=". route('clients.show', [$task->client->id]) .">". $task->client->name ."</a>" : '' !!}</td>
                                                    </tr>
                                                @endcan
                                                @if($task->user_id != auth()->user()->id)
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.created_by') }}</label>
                                                        </td>
                                                        <td class="col-md-10">{!! ($task->user) ? $task->user->name : '' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-md-2"><label>{{ trans('fi.created_at') }}</label>
                                                        </td>
                                                        <td class="col-md-10">{!! ($task->user) ? $task->formatted_created_at : '' !!}</td>
                                                    </tr>
                                                @endif
                                                @if($task->completion_note)
                                                    <tr>
                                                        <td class="col-md-2">
                                                            <label>{{ trans('fi.completion-note') }}</label>
                                                        </td>
                                                        <td class="col-md-10">{!! $task->completion_note !!}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>

                                    </div>

                                </div>

                                @can('attachments.view')
                                    <div class="tab-pane" id="tab-attachments">
                                        @include('attachments._table', ['object' => $task, 'model' => 'FI\Modules\TaskList\Models\Task', 'modelId' => $task->id])
                                    </div>
                                @endcan

                                @can('notes.view')
                                    <div id="tab-notes" class="tab-pane">
                                        @include('notes._js_timeline', ['object' => $task, 'model' => 'FI\Modules\TaskList\Models\Task', 'hideHeader' => true, 'showPrivateCheckbox' => 0, 'showPrivate' => 1])
                                        <div id="note-timeline-container"></div>
                                    </div>
                                @endcan
                            </div>
                        </div>

                    </div>

                </div>

            </div>

    </section>

@stop
