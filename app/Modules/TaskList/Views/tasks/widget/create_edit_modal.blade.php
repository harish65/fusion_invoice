@include('layouts._select2')

<script type="text/javascript">
    $(function () {
        // Setup the ui
        $('#task-modal').modal('show');

        $("#task-modal").on('shown.bs.modal', function () {
            $('#title').focus();
        });
        @if(config('fi.includeTimeInTaskDueDate') == 1 )
        var dateFormatElement = dateTimeFormat;
        @else
        var dateFormatElement = dateFormat;
        @endif

        $('#task-due-date-selection').datetimepicker({
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
            $('#create-new-task').prop('disabled',false);
            let $form = $('#task-form');
            @if(!$editMode && !config('app.demo'))
            @can('attachments.create')
            let attachments = document.getElementById('attachments').files.length;
            for (var i = 0; i < attachments; i++) {
                formData.append("attachments[]", document.getElementById('attachments').files[i]);
            }
            @endcan;
            @endif

            var date = $('#task-due-date').val();

            formData.append("due_date_timestamp", moment(date).format('YYYY-MM-DD HH:mm:ss'));
            formData.append("due_date", $('#task-due-date').val());
            formData.append("title", $('#title').val());
            formData.append("description", $('#description').val());
            formData.append("assignee_id", $('#task-assignee-select').val());
            formData.append("client_id", $('#task-client-select').val());
            formData.append("task_section_id", $('#task-section-select').val());
            formData.append("completion_note", $('#completion_note_widget').val());
            formData.append("is_complete", $(this).data('complete'));

            $.ajax({
                url: $form.attr('action'),
                method: 'post',
                data: formData,
                processData: false,
                contentType: false
            }).done(function (response) {
                alertify.success(response.message, 5);
                var tab = '{{ $tab }}';
                if (tab != '') {
                    var url = window.location.href;
                    var pageURL = new URLSearchParams(window.location.search);

                    if (url.indexOf('?') > -1) {
                        if (pageURL.get('tab') == null) {
                            url += '&tab=' + tab
                        }
                    } else {
                        url += '?tab=' + tab
                    }
                    window.location.href = url;
                } else {
                    $('#task-modal').modal('hide');
                    $('#search-btn').trigger('click');
                }

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
            $('#task-modal').modal('hide');
            let id = $(this).data('task-id');
            let redirect = $('.action-complete-with-note').data('redirect-to');
            var url = '{{ route("task.complete-with-note.modal",[ ":id"] ) }}';
            url = url.replace(':id', id);
            $('#modal-placeholder').load(url, redirect);
        });

        $('.create-task-modal-close').click(function (){
           $('.btn-action-modal').removeClass('disabled');
            $('#create-new-task').prop('disabled',false);
        });
    });
</script>

<div class="modal fade" id="task-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.task') }}</h5>
                <button type="button" class="close create-task-modal-close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">


                <div class="row">
                    <div class="col-md-12">
                        {!! Form::open(['route' => ($editMode) ? ['task.widget.update', $task->id] : 'task.widget.store', 'method' => 'post', 'role' => 'form', 'id' => 'task-form', 'files' => true]) !!}
                        <div class="form-group">
                            <label>{{ trans('fi.title') }}</label>
                            {!! Form::text('title', $task->title ?? null, ['id' => 'title', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.title'), 'autocomplete' => 'off']) !!}
                        </div>
                        <div class="form-group">
                            <label>{{ trans('fi.description') }}</label>
                            {!! Form::textarea('description', $task->description ?? null, ['id' => 'description', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.description'), 'rows' => 3]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">

                            <label>{{ trans('fi.due_date') }}</label>
                            <div class="input-group date" id="task-due-date-selection" data-target-input="nearest">
                                {!! Form::text('due_date', isset($task->due_date) && $task->due_date  != null ?  $task->formatted_due_date : null, ['class' => 'form-control form-control-sm',
                                 'id' => 'task-due-date' , 'placeholder' => trans('fi.due_date'), 'data-target' => '#task-due-date-selection', 'data-toggle'=> 'datetimepicker']) !!}
                                <div class="input-group-append" data-target="#task-due-date-selection"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>

                        </div>
                        <div class="form-group">
                            <label>{{ trans('fi.task_section') }}</label>
                            {!! Form::select('task_section_id', $taskSections, isset($task) ? $task->task_section_id : 2, ['class' => 'form-control form-control-sm', 'id' => 'task-section-select', 'placeholder' => trans('fi.select_section')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group select2-form-control">
                            <label>{{ trans('fi.assignee') }}</label>
                            {!! Form::select('assignee_id', $users, isset($task) ? $task->assignee_id : auth()->user()->id, ['class' => 'form-control form-control-sm select2-select-box', 'id' => 'task-assignee-select', 'style'=>'width:350px']) !!}
                        </div>
                        <div class="form-group select2-form-control">
                            <label>{{ trans('fi.client') }}</label>
                            {!! Form::select('client_id', $clients, isset($task) ? $task->client_id : $client, ['class' => 'form-control form-control-sm select2-select-box', 'id' => 'task-client-select', 'placeholder' => trans('fi.select_client'), 'style'=>'width:350px']) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-12" id="completion_note_widget_task_settings"
                         style="display: {{ isset($task) && $task->completion_note != '' ? 'block' : 'none' }}">
                        <label>{{ trans('fi.completion-note') }}</label>
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::textarea('completion_note', ($editMode) ? $task->completion_note  : null, ['class' => 'form-control form-control-sm', 'id' => 'completion_note_widget_widget', 'rows' => 2]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input id="is_recurring" class="custom-control-input" type="checkbox" value="1"
                                       name="is_recurring">
                                <label for="is_recurring"
                                       class="custom-control-label">{{ trans('fi.is_recurring_task') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="recurring_task_settings"
                         style="display: {{ isset($task) && $task->is_recurring == 1 ? 'block' : 'none' }}">
                        <div class="form-group">
                            <label>{{ trans('fi.every') }}</label>

                            <div class="row">
                                <div class="col-md-2">
                                    {!! Form::select('recurring_frequency', array_combine(range(1, 90), range(1, 90)), '1', ['id' => 'recurring_frequency', 'class' => 'form-control form-control-sm']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::select('recurring_period', $frequencies, 3, ['id' => 'recurring_period', 'class' => 'form-control form-control-sm']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        @if (!$editMode)
                            @if (!config('app.demo'))
                                @can('attachments.create')
                                    <div class="form-group">
                                        <label>{{ trans('fi.attach_files') }}: </label>
                                        <div class="form-group">
                                            {!! Form::file('attachments[]', ['id' => 'attachments', 'class' => 'form-control form-control-sm h-100', 'multiple' => 'multiple']) !!}
                                        </div>
                                    </div>
                                @endcan
                            @endif
                        @endif
                        {!! Form::close() !!}
                    </div>
                </div>
                @if ($editMode)
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" role="tablist">
                                        @can('notes.view')
                                            <li class="nav-item">
                                                <a id="notes-tab" data-toggle="tab" href="#tab-notes"
                                                   class="nav-link active">
                                                    {{ trans('fi.notes') }} {!! $task->notes->count() > 0 ? '<span class="badge badge-primary notes-count">'.$task->notes->count().'</span>' : '' !!}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('attachments.view')
                                            <li class="nav-item">
                                                <a id="attachments-tab" data-toggle="tab" href="#tab-attachments"
                                                   class="nav-link">
                                                    {{ trans('fi.attachments') }} {!! $task->attachments->count() > 0 ? '<span class="badge badge-default">'.$task->attachments->count().'</span>' : '' !!}
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <div class="tab-content">
                                        @can('notes.view')
                                            <div id="tab-notes" class="tab-pane active">
                                                @include('notes._js_timeline', ['object' => $task, 'model' => 'FI\Modules\TaskList\Models\Task', 'hideHeader' => true, 'showPrivateCheckbox' => 0, 'showPrivate' => 1])
                                                <div id="note-timeline-container"></div>
                                            </div>
                                        @endcan
                                        @can('attachments.view')
                                            <div id="tab-attachments" class="tab-pane">
                                                @include('attachments._table', ['object' => $task, 'model' => 'FI\Modules\TaskList\Models\Task', 'modelId' => $task->id])
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="modal-footer">
                @if($editMode && isset($task->user) && $task->user->id != auth()->user()->id)
                    <span class="pull-left">{{ trans('fi.task_created_by_and_created_at',['created_by' => $task->user->name,'created_at' => $task->formatted_created_at]) }}</span>
                @endif
                <button type="button" class="btn btn-sm btn-default create-task-modal-close"
                        data-dismiss="modal">{{ trans('fi.cancel') }}
                </button>
                <div class="btn-group">
                    <button type="button" id="task-save-confirm"
                            class=" task-save-confirm btn btn-sm btn-primary"><i
                                class="fa fa-save" data-complete="0"></i> {{ trans('fi.save') }}
                    </button>

                    <button title="{{ trans('fi.complete-with-note') }}" type="button"
                            class="btn btn-sm btn-primary dropdown-toggle"
                            data-toggle="dropdown"
                            aria-expanded="false">
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                        <a href="#" class="task-save-confirm dropdown-item" data-complete="1">
                            <i class="fa fa-check"></i> {{ trans('fi.save-and-complete') }}
                        </a>
                        @if($editMode)
                            <a href="#" class="action-complete-with-note dropdown-item"
                               data-task-title="{{ $task->title}}"
                               data-task-id="{{ $task->id }}" data-redirect-to="{{ route('dashboard.index')}}">
                                <i class="fa fa-check text-info"></i> {{ trans('fi.complete-with-note') }}
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>