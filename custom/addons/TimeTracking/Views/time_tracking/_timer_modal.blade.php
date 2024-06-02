@include('time_tracking._task_list_refresh_js')
@include('time_tracking._project_edit_totals_refresh')
@include('layouts._daterangetimepicker')

<script type="text/javascript">
    $(function () {

        $('#modal-show-timers').modal();

        insertUpdateTime();
        $('#task-timer-list').on('click', '.btn-delete-timer', function () {
            let $this = $(this);
            var $warning = "{!! trans('fi.delete_record_warning') !!}";
            var ids = [];
            $this.addClass('delete-projects-tasks-timers-active');

            $('#modal-addon-project-task-timer').load('{!! route('timeTracking.timers.delete.modal') !!}', {
                    action: "{!! route('timeTracking.timers.delete') !!}",
                    id: $this.data('timer-id'),
                    modalName: 'projects-tasks-timers',
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

        $('#task-timer-list').on('click', '.btn-edit-timer', function () {
            let start_at = $(this).data('start-at');
            let end_at = $(this).data('end-at');
            $('#time_tracking_timer_id').val($(this).data('timer-id'));
            $('#date_time_range').daterangepicker({
                    timePicker: true,
                    autoApply: true,
                    startDate: moment(start_at),
                    endDate: moment(end_at),
                        @if (config('fi.use24HourTimeFormat'))
                        timePicker24Hour: true,
                        @endif
                        locale: {
                            @if (config('fi.use24HourTimeFormat'))
                            format: "{{ strtoupper(config('fi.datepickerFormat')) }} H:mm",
                            @else
                            format: "{{ strtoupper(config('fi.datepickerFormat')) }} h:mm A",
                            @endif
                            customRangeLabel: "{{ trans('fi.custom') }}",
                            daysOfWeek: [
                                "{{ trans('fi.day_short_sunday') }}",
                                "{{ trans('fi.day_short_monday') }}",
                                "{{ trans('fi.day_short_tuesday') }}",
                                "{{ trans('fi.day_short_wednesday') }}",
                                "{{ trans('fi.day_short_thursday') }}",
                                "{{ trans('fi.day_short_friday') }}",
                                "{{ trans('fi.day_short_saturday') }}"
                            ],
                            monthNames: [
                                "{{ trans('fi.month_january') }}",
                                "{{ trans('fi.month_february') }}",
                                "{{ trans('fi.month_march') }}",
                                "{{ trans('fi.month_april') }}",
                                "{{ trans('fi.month_may') }}",
                                "{{ trans('fi.month_june') }}",
                                "{{ trans('fi.month_july') }}",
                                "{{ trans('fi.month_august') }}",
                                "{{ trans('fi.month_september') }}",
                                "{{ trans('fi.month_october') }}",
                                "{{ trans('fi.month_november') }}",
                                "{{ trans('fi.month_december') }}"
                            ],
                            firstDay: 1
                        }
                },
                function (start, end) {
                    daterangepicker_update_fields(start, end);
                });
            $('#date_time_range').click();
            insertUpdateTime();
        });

        function daterangepicker_update_fields(start, end) {
            $('#from_date_time').val(start.format('YYYY-MM-DD H:mm:ss'));
            $('#to_date_time').val(end.format('YYYY-MM-DD H:mm:ss'));
        }

        function refreshTimerList() {
            $('#task-timer-list').load('{{ route('timeTracking.timers.refreshList') }}', {
                time_tracking_task_id: '{{ $task->id }}'
            });
        }

        function insertUpdateTime() {

            $('#date_time_range').on('apply.daterangepicker', function () {
                $.ajax({
                    url: "{{ route('timeTracking.timers.store') }}",
                    method: 'post',
                    data: {
                        time_tracking_task_id: '{{ $task->id }}',
                        start_at: $('#from_date_time').val(),
                        end_at: $('#to_date_time').val(),
                        id: $('#time_tracking_timer_id').val()
                    },
                    beforeSend: function () {
                        showHideLoaderModal();
                    },
                    success: function () {
                        refreshTaskList();
                        refreshTimerList();
                        refreshTotals();
                        $('#time_tracking_timer_id, #date_time_range').val('');
                        showHideLoaderModal();
                    },
                    fail: function (response) {
                        showErrors($.parseJSON(response.responseText).errors, '#modal-status-placeholder');
                        showHideLoaderModal();
                    }
                });
            });
        }
    });
</script>

<div class="modal fade" id="modal-show-timers">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('TimeTracking::lang.timers') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body table-responsive">

                <div id="modal-status-placeholder"></div>

                <div class="row">
                    <div class="col-md-6">
                        <label>{{ trans('TimeTracking::lang.add_timer') }}</label>
                        <div class="input-group">
                            {!! Form::text('date_time_range', null, ['id' => 'date_time_range','data-target'=>'#reservationdatetime', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly']) !!}
                            <div class="input-group-append  open-daterangetimepicker" data-target="#reservationdatetime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        {!! Form::hidden('from_date_time', null, ['id' => 'from_date_time']) !!}
                        {!! Form::hidden('to_date_time', null, ['id' => 'to_date_time']) !!}
                        {!! Form::hidden('id', null, ['id' => 'time_tracking_timer_id']) !!}
                    </div>
                </div>

                <div id="task-timer-list" style="max-height: 65vh; height: auto; overflow-y: auto;">
                    @include('time_tracking._timer_list')
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('TimeTracking::lang.close') }}</button>
            </div>
        </div>
    </div>
</div>