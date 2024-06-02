@if(!isset($enableTimerPopup))
    <style>
        .quiz-sticky {
            position: fixed;
            top: 90%;
            right: 1px;
            opacity: 0.95;
            z-index: 99;
        }
        .btn-dashboard-remove-timer {
            top: -3px;
            right: -27px;
        }
    </style>
    <div class="position-fixed quiz-sticky"></div>

    <script type="text/javascript">
        var ajaxTimers = [];
        var $document = $(document.body);

        ajaxTimeTrackerIndex();

        function setTimerInterval(taskId, sec) {
            timerInterval = setInterval(function () {
                $document.find('#dashboard_hours_' + taskId).html(pad(parseInt(sec / 60 / 60, 10)));
                $document.find('#dashboard_minutes_' + taskId).html(pad(parseInt(sec / 60 % 60, 10)));
                $document.find('#dashboard_seconds_' + taskId).html(pad(++sec % 60));
            }, 1000);

            clearInterval(ajaxTimers[taskId]);

            ajaxTimers[taskId] = timerInterval;

        }

        $(document).off('click', '.btn-dashboard-stop-timer').on('click', '.btn-dashboard-stop-timer', function () {
            clearInterval(ajaxTimers[$(this).data('task-id')]);
            $.post('{{ route('timeTracking.timers.stop') }}', {
                timer_id: $(this).data('timer-id'),
                task_id: $(this).data('task-id'),
                project_id: $(this).data('project-id'),
                remove: 0,
            }).done(function () {
                ajaxTimeTrackerIndex();
            });
        });


        $(document).off('click', '.btn-dashboard-remove-timer').on('click', '.btn-dashboard-remove-timer', function () {
            clearInterval(ajaxTimers[$(this).data('task-id')]);
            $.post('{{ route('timeTracking.timers.stop') }}', {
                timer_id: $(this).data('timer-id'),
                task_id: $(this).data('task-id'),
                project_id: $(this).data('project-id'),
                remove: 1,
            }).done(function () {
                ajaxTimeTrackerIndex();
            });
        });

        function startTimer(taskId) {
            $.post('{{ route('timeTracking.timers.seconds') }}', {
                task_id: taskId
            }).done(function (sec) {
                setTimerInterval(taskId, sec);
            });
        }

        function ajaxTimeTrackerIndex() {
            $.get('{{ route('timeTracking.timers.ajax.index') }}').done(function (res) {
                $('.quiz-sticky').html(res)
            });
        }


        $(document).off('click', '.btn-dashboard-start-timer').on('click', '.btn-dashboard-start-timer', function () {
            taskId = $(this).data('task-id');
            $.post('{{ route('timeTracking.timers.start') }}', {
                task_id: taskId,
                project_id: $(this).data('project-id'),
                timer_id: $(this).data('timer-id'),
            }).done(function () {
                startTimer(taskId);
                ajaxTimeTrackerIndex();
            });
        });
    </script>
@endif