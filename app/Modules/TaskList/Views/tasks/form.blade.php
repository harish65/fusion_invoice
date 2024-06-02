@extends('layouts.master')

@section('head')
    @include('layouts._select2')
@stop

@section('javascript')
    <script type="text/javascript">
        $(function () {
            // Setup the ui

            $('#title').focus();

                    @if(config('fi.includeTimeInTaskDueDate') == 1 )
            var dateFormatElement = dateTimeFormat;
                    @else
            var dateFormatElement = dateFormat;
            @endif

            $('#task-due-date').datetimepicker({
                @if(!$editMode)
                defaultDate: new Date(),
                date: new Date(),
                @endif
                icons: {time: 'far fa-clock'},
                format: dateFormatElement,
                autoclose: true
            });

            $('.select2-select-box').select2();

            // Submit the form
            let formData = new FormData();
            $('.task-save-confirm').click(function () {
                let $form = $('#task-form');
                var date = $("input[name='due_date']").val();
                formData.append("due_date_timestamp", moment(date).format('YYYY-MM-DD HH:mm:ss'));
                formData.append("due_date", date);
                formData.append("title", $('#title').val());
                formData.append("description", $('#description').val());
                formData.append("assignee_id", $('#task-assignee-select').val());
                formData.append("client_id", $('#task-client-select').val());
                formData.append("task_section_id", $('#task-section-select').val());
                formData.append("is_complete", $(this).data('complete') ?? 0);
                formData.append("completion_note", $('#completion_note').val() ?? 0);
                formData.append("is_recurring", ($('#is_recurring').is(':checked') == true) ? 1 : 0);
                formData.append("recurring_period", $('#recurring_period').val());
                formData.append("recurring_frequency", $('#recurring_frequency').val());

                $.ajax({
                    url: $form.attr('action'),
                    method: 'post',
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (response) {
                    alertify.success(response.message, 5);
                    $('#task-modal').modal('hide');
                    $('#search-btn').trigger('click');
                    let url = "{{route('task.show',['id' => ':id'])}}"
                    url = url.replace(':id', response.task_id);
                    window.location = url;
                }).fail(function (xhr) {
                    let errors = JSON.parse(xhr.responseText).errors;
                    $.each(errors, function (name, data) {
                        alertify.error(data[0], 5);
                    });
                });
            });

            $('#task-assignee-select').change(function () {
                let $userId = '{{ auth()->user()->id }}';
                if ($(this).val() != $userId) {
                    $("#task-section-select").val(1).change().attr('readonly', true);
                } else {
                    $("#task-section-select").val(2).change().attr('readonly', false);
                }
            });

            $('#is_recurring').click(function () {
                if ($(this).is(':checked')) {
                    $("#recurring_task_settings").show();
                } else {
                    $("#recurring_task_settings").hide();
                }
            });

            $('.action-complete-with-note').on('click', function () {
                let id = $(this).data('task-id');
                var url = '{{ route("task.complete-with-note.modal",[ ":id"] ) }}';
                url = url.replace(':id', id);
                $('#modal-placeholder').load(url,{widget : 0});
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="pull-left d-inline">
                        @if ($editMode == true)
                            {{ trans('fi.tasks') }} #{{ $task->id }}
                        @else
                            {{ trans('fi.task_form') }}
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        @if ($editMode)
                            <a href="{{ $returnUrl }}" class="btn btn-sm btn-default"><i class="fa fa-backward"></i> {{ trans('fi.back') }}</a> @endif
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary task-save-confirm">
                                <i class="fa fa-save" data-complete="0"></i>
                                {{ trans('fi.save') }}
                            </button>
                            @if ($editMode)
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="#" class="task-save-confirm dropdown-item" data-complete="1">
                                        <i class="fa fa-check"></i> {{ trans('fi.save-and-complete') }}
                                    </a>
                                    <a href="#" class="action-complete-with-note dropdown-item" data-task-title="{{ $task->title}}"
                                       data-task-id="{{ $task->id }}" data-redirect-to="{{ request()->fullUrl() }}">
                                        <i class="fa fa-check text-info"></i> {{ trans('fi.complete-with-note') }}
                                    </a>
                                    <a href="#" class="task-save-confirm dropdown-item" data-complete="0">
                                        <i class="fa fa-check text-danger"></i> {{ trans('fi.save-and-unComplete') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">
                            {!! Form::open(['route' => ($editMode) ? ['task.update', $task->id] : 'task.store', 'method' => 'post', 'role' => 'form', 'id' => 'task-form', 'files' => true]) !!}
                            {!! Form::hidden('user_id', auth()->user()->id) !!}

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>{{ trans('fi.title') }}</label>
                                    {!! Form::text('title', $task->title ?? null, ['id' => 'title', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.title'), 'autocomplete' => 'off']) !!}
                                </div>
                                <div class="form-group col-md-12">
                                    <label>{{ trans('fi.description') }}</label>
                                    {!! Form::textarea('description', $task->description ?? null, ['id' => 'description', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.description'), 'rows' => 3]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ trans('fi.due_date') }}</label>

                                    <div class="input-group date">
                                        <div class="input-group date" id="task-due-date" data-target-input="nearest">
                                            {!! Form::text('due_date', $task->due_date_epoch ?? null, ['class' => 'form-control form-control-sm datetimepicker-input', 'data-target' => '#task-due-date', 'placeholder' => trans('fi.due_date'), 'data-toggle'=>"datetimepicker"]) !!}
                                            <div class="input-group-append" data-target="#task-due-date"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ trans('fi.task_section') }}</label>
                                    {!! Form::select('task_section_id', $taskSections, isset($task) ? $task->task_section_id : 2, ['class' => 'form-control form-control-sm', 'id' => 'task-section-select', 'placeholder' => trans('fi.select_section')]) !!}
                                </div>
                                <div class="form-group select2-form-control col-md-6">
                                    <label>{{ trans('fi.assignee') }}</label>
                                    {!! Form::select('assignee_id', $users, isset($task) ? $task->assignee_id : auth()->user()->id, ['class' => 'form-control form-control-sm select2-select-box', 'id' => 'task-assignee-select']) !!}
                                </div>
                                <div class="form-group select2-form-control col-md-6">
                                    <label>{{ trans('fi.client') }}</label>
                                    {!! Form::select('client_id', $clients, isset($task) ? $task->client_id : $client, ['class' => 'form-control form-control-sm select2-select-box', 'id' => 'task-client-select', 'placeholder' => trans('fi.select_client')]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        {!! Form::checkbox('is_recurring', 1, (isset($task) && $task->is_recurring == 1) ? true : false, ['id' => 'is_recurring','class'=>'custom-control-input']) !!}
                                        <label for="is_recurring"
                                               class="custom-control-label">{{ trans('fi.is_recurring_task') }}</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-12" id="recurring_task_settings"
                                     style="display: {{ isset($task) && $task->is_recurring == 1 ? 'block' : 'none' }}">
                                    <label>{{ trans('fi.every') }}</label>

                                    <div class="row">
                                        <div class="col-md-1">
                                            {!! Form::select('recurring_frequency', array_combine(range(1, 90), range(1, 90)), '1', ['id' => 'recurring_frequency', 'class' => 'form-control form-control-sm']) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::select('recurring_period', $frequencies, (isset($task) && $task->recurring_period != null) ? $task->recurring_period : null, ['id' => 'recurring_period', 'class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>

                                </div>
                                @if($editMode && isset($task))
                                    <div class="form-group col-md-12" id="completion_note_task_settings"
                                         style="display: {{ isset($task) && $task->completion_note != '' ? 'block' : 'none' }}">
                                        <label>{{ trans('fi.completion-note') }}</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                {!! Form::textarea('completion_note', ($editMode) ? $task->completion_note  : null, ['class' => 'form-control form-control-sm', 'id' => 'completion_note', 'rows' => 2]) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {!! Form::close() !!}
                        </div>

                    </div>
                    @if($editMode && isset($task))
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" role="tablist">
                                    @can('notes.view')
                                        <li class="nav-item">
                                            <a href="#tab-notes" data-toggle="tab" class="active nav-link">
                                                {{ trans('fi.notes') }} {!! $task->notes->count() > 0 ? '<span class="badge badge-primary notes-count">'.$task->notes->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('attachments.view')
                                        <li class="nav-item">
                                            <a href="#tab-attachments" data-toggle="tab" class="nav-link">
                                                {{ trans('fi.attachments') }} {!! $task->attachments->count() > 0 ? '<span class="badge badge-primary attachment-count">'.$task->attachments->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    @can('notes.view')
                                        <div class="tab-pane active" id="tab-notes">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('notes._js_timeline', ['object' => $task, 'model' => 'FI\Modules\TaskList\Models\Task', 'hideHeader' => true, 'showPrivateCheckbox' => 1, 'showPrivate' => 1])
                                                    <div id="note-timeline-container"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                    @can('attachments.view')
                                        <div class="tab-pane" id="tab-attachments">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('attachments._table', ['object' => $task, 'model' => 'FI\Modules\TaskList\Models\Task', 'modelId' => $task->id])
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </section>

@stop