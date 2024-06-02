@extends('layouts.master')

@section('javascript')

    @include('layouts._select2')
    @include('clients._js_lookup')
    @include('time_tracking._task_list_refresh_js')
    @include('time_tracking._task_init_task_sortable_js')
    @include('time_tracking._project_edit_totals_refresh')
    @include('layouts._formdata')

    <script type="text/javascript">
        $(function () {

            var timers = [];

            $('#btn-add-task').click(function () {
                $('#modal-placeholder').load('{{ route('timeTracking.tasks.create') }}', {
                    project_id: '{{ $project->id }}'
                });
            });

            initTaskSortable();

            $(document).on('click', '.btn-delete-task', function () {
                let $this = $(this);
                var $warning = "{!! trans('TimeTracking::lang.confirm_delete_task') !!}";
                var ids = [];
                ids.push($this.data('task-id'));
                $this.addClass('delete-projects-tasks-active');

                $('#modal-placeholder').load('{!! route('timeTracking.tasks.delete.modal') !!}', {
                        action: "{!! route('timeTracking.tasks.delete') !!}",
                        ids: ids,
                        modalName: 'projects-tasks',
                        message: $warning,
                        isReload: false,
                        returnURL: '{!! route('timeTracking.projects.edit', [$project->id]) !!}',
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $('#btn-bulk-delete-tasks').click(function () {
                var $warning = "{!! trans('TimeTracking::lang.confirm_delete_task') !!}";
                var ids = [];
                $('.checkbox-bulk-action:checked').each(function () {
                    ids.push($(this).data('task-id'));
                });

                if (ids.length > 0) {
                    $('#modal-placeholder').load('{!! route('timeTracking.tasks.delete.modal') !!}', {
                            action: "{!! route('timeTracking.tasks.delete') !!}",
                            ids: ids,
                            modalName: 'projects-tasks',
                            message: $warning,
                            isReload: false,
                            returnURL: '{!! route('timeTracking.projects.edit', [$project->id]) !!}',
                        },
                        function (response, status, xhr) {
                            if (status == "error") {
                                var response = JSON.parse(response);
                                alertify.error(response.message);
                            }
                        }
                    );
                }
            });

            $(document).on('click', '#btn-bulk-select-all', function () {
                $('.checkbox-bulk-action').prop('checked', true);
            });
            $(document).on('click', '#btn-bulk-deselect-all', function () {
                $('.checkbox-bulk-action').prop('checked', false);
            });

            function submitTaskDelete(ids) {
                $.post('{{ route('timeTracking.tasks.delete') }}', {
                    ids: ids
                }).done(function () {
                    refreshTaskList();
                    refreshTotals();
                    initTaskSortable();
                });
            }

            $('#btn-bulk-bill-tasks').click(function () {
                var ids = [];
                $('.checkbox-bulk-action:checked').each(function () {
                    ids.push($(this).data('task-id'));
                });
                if (ids.length > 0) {
                    submitTaskBill(ids);
                } else {
                    alertify.error('{{ trans('TimeTracking::lang.select_task') }}', 5);
                }
            });

            $(document).on('click', '.btn-bill-task', function () {
                var ids = [];
                ids.push($(this).data('task-id'));

                if (ids.length > 0) {
                    submitTaskBill(ids);
                }
            });

            function submitTaskBill(ids) {
                $('#modal-placeholder').load('{{ route('timeTracking.bill.create') }}', {
                    projectId: '{{ $project->id }}',
                    taskIds: JSON.stringify(ids)
                });
            }

            function ajaxTimeTrackerIndex() {
                $.get('{{ route('timeTracking.timers.ajax.index') }}').done(function (res) {
                    $('.quiz-sticky').html(res)
                });
            }

            $(document).on('click', '.btn-start-timer', function () {
                taskId = $(this).data('task-id');
                $.post('{{ route('timeTracking.timers.start') }}', {
                    task_id: taskId,
                    project_id: '{{$project->id}}'
                }).done(function () {
                    refreshTaskList();
                    startTimer(taskId);
                    initTaskSortable();
                    ajaxTimeTrackerIndex();
                });
            });

            $(document).on('click', '.btn-stop-timer', function () {
                clearInterval(timers[$(this).data('task-id')]);
                $.post('{{ route('timeTracking.timers.stop') }}', {
                    timer_id: $(this).data('timer-id'),
                    project_id: '{{$project->id}}',
                    task_id: $(this).data('task-id')
                }).done(function () {
                    refreshTaskList();
                    refreshTotals();
                    initTaskSortable();
                    ajaxTimeTrackerIndex();
                });
            });

            $(document).on('click', '.btn-edit-task', function () {
                $('#modal-placeholder').load('{{ route('timeTracking.tasks.edit') }}', {
                    id: $(this).data('task-id')
                });
            });

            $(document).on('click', '.btn-show-timers', function () {
                $('#modal-placeholder').load('{{ route('timeTracking.timers.show') }}', {
                    time_tracking_task_id: $(this).data('task-id')
                });
            });

            $('#btn-save-settings').click(function () {

                $.post('{{ route('timeTracking.projects.update', [$project->id]) }}', {
                    name: $('#project_name').val(),
                    company_profile_id: $('#company_profile_id').val(),
                    client_id: $('#client_name').val(),
                    hourly_rate: $('#hourly_rate').val(),
                    status: $('#status').val()
                }).done(function () {
                    alertify.success('{{ trans('fi.settings_successfully_saved') }}', 5);
                    setInterval(function () {
                        window.location.reload();
                    }, 1000);

                }).fail(function (response) {
                    if (response.status == 400) {
                        showAlertifyErrors($.parseJSON(response.responseText).errors);
                    } else {
                        alertify.error('{{ trans('fi.unknown_error') }}', 5);
                    }
                });
            });

            $(document).off("click", "#btn-add-preset-task-apply").on('click', '#btn-add-preset-task-apply', function () {
                $('#modal-addon-timeTracking-preset-tasks-apply').load('{!! route('timeTracking.projects.preset.task.apply.modal') !!}', {
                        'project_id': '{{$project->id}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $(document).off("click", "#btn-add-preset-task").on('click', '#btn-add-preset-task', function () {

                $('#modal-addon-timeTracking-preset-tasks').load('{!! route('timeTracking.projects.preset.task.modal') !!}', {
                        'project_id': '{{$project->id}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            function startTimer(taskId) {
                $.post('{{ route('timeTracking.timers.seconds') }}', {
                    task_id: taskId
                }).done(function (sec) {
                    setTimerInterval(taskId, sec);
                });
            }

            function setTimerInterval(taskId, sec) {
                timerInterval = setInterval(function () {
                    $("#seconds_" + taskId).html(pad(++sec % 60));
                    $("#minutes_" + taskId).html(pad(parseInt(sec / 60 % 60, 10)));
                    $('#hours_' + taskId).html(pad(parseInt(sec / 60 / 60, 10)));
                }, 1000);

                timers[taskId] = timerInterval;
            }

            @foreach ($tasks as $task)
            @if ($task->activeTimer)
            startTimer({{ $task->id }});
            @endif
            @endforeach

            $('.delete-project').click(function () {
                let $_this = $(this);
                var $warning = "{!! trans('TimeTracking::lang.confirm_delete_project') !!}";

                $_this.addClass('delete-projects-active');

                $('#modal-placeholder').load('{!! route('timeTracking.projects.delete.modal') !!}', {
                        action: '{{ route('timeTracking.projects.delete', [$project->id]) }}',
                        modalName: 'projects',
                        isReload: true,
                        message: $warning,
                        returnURL: '{{route('timeTracking.projects.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );

            });
        });
    </script>

@stop

@section('content')
    <div id="modal-addon-project-task-timer"></div>
    <div id="modal-addon-timeTracking-preset-task-items"></div>
    <div id="modal-addon-timeTracking-preset-tasks-apply"></div>
    <div id="modal-addon-timeTracking-preset-tasks"></div>


    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="d-inline">{{ trans('TimeTracking::lang.time_tracking') }}</h1>
                    <h5 class="d-inline text-info pl-2">{{ $project->name }} </h5>
                </div>
                <div class="col-sm-6 pr-0">
                    <div class="float-sm-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                {{ trans('fi.other') }} <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                <a href="javascript:void(0);" id="btn-add-preset-task-apply" class="dropdown-item">
                                    <i class="fa fa-plus"></i> {{ trans('TimeTracking::lang.add_preset_task') }}
                                </a>
                                <a href="javascript:void(0);" id="btn-add-preset-task" class="dropdown-item">
                                    <i class="fa fa-tasks"></i> {{ trans('TimeTracking::lang.maintain_preset_task') }}
                                </a>
                                @can('time_tracking.delete')
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="delete-project text-danger dropdown-item">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <a href="{{ route('timeTracking.projects.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-backward"></i> {{ trans('fi.back') }}
                        </a>
                        <button class="btn btn-sm btn-primary" id="btn-save-settings">
                            <i class="fa fa-save"></i> {{ trans('fi.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

        </div>

        <div class="row">

            <div class="col-lg-10">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list"></i> {{ trans('TimeTracking::lang.tasks') }}</h3>

                        <div class="card-tools">
                            <button class="btn btn-sm btn-primary" id="btn-add-task">
                                <i class="fa fa-plus"></i> {{ trans('TimeTracking::lang.add_task') }}
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('TimeTracking::lang.bulk_actions') }} <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <a href="javascript:void(0)" id="btn-bulk-bill-tasks" class="dropdown-item"><i
                                                class="fa fa-dollar-sign"></i> {{ trans('TimeTracking::lang.bill_tasks') }}
                                    </a>
                                    @can('time_tracking.delete')
                                        <div class="dropdown-divider"></div>
                                        <a href="javascript:void(0)" id="btn-bulk-delete-tasks"
                                           class="text-danger dropdown-item"><i
                                                    class="fa fa-trash"></i> {{ trans('TimeTracking::lang.delete_tasks') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        @include('time_tracking._task_list')

                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list"></i> {{ trans('TimeTracking::lang.billed_tasks') }}
                        </h3>
                    </div>

                    <div class="card-body table-responsive">

                        <table class="table table-hover table-striped table-sm text-nowrap ">
                            <thead>
                            <tr>
                                <th>{{ trans('TimeTracking::lang.task') }}</th>
                                <th class="text-right">{{ trans('TimeTracking::lang.hours') }}</th>
                                <th>{{ trans('fi.invoice') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($tasksBilled as $task)
                                <tr>
                                    <td>{{ $task->name }}</td>
                                    <td class="text-right">{{ $task->formatted_hours }}</td>
                                    <td>
                                        <a href="{{ route('invoices.edit', [$task->invoice_id]) }}">{{ $task->invoice->number }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="col-lg-2">

                <div id="div-totals">
                    @include('time_tracking._project_edit_totals')
                </div>

                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">{{ trans('fi.options') }}</h3>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label>{{ trans('TimeTracking::lang.project_name') }}:</label>
                            {!! Form::text('project_name', $project->name, ['id' => 'project_name', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.company_profile') }}:</label>
                            {!! Form::select('company_profile_id', $companyProfiles, $project->company_profile_id, ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>* {{ trans('fi.client') }}:</label>
                            {!! Form::select('client_id', $clients, $project->client_id, ['id' => 'client_name', 'class' => 'form-control form-control-sm client-lookup form-control-sm', 'autocomplete' => 'off', 'style'=>"width: 100%;"]) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('TimeTracking::lang.hourly_rate') }}:</label>
                            {!! Form::text('hourly_rate', $project->hourly_rate, ['id' => 'hourly_rate', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.status') }}:</label>
                            {!! Form::select('status', $statuses, $project->status, ['id' => 'status', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop