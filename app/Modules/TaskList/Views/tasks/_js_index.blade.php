@include('layouts._daterangepicker')

<script type="text/javascript">
    $(function () {
        initDateRangePicker('task');

        initDateRangePreSelected('task');

        $('.task-status,.task-filters,#task_date_range').change(function () {
            $('form#filter').submit();
        });

        $('.action-complete').on('click', function () {
            let returnURL = document.URL;
            let task_id = $(this).data('task-id');
            var url = '{{ route("task.complete",[ ":id", ":complete"] ) }}';
            url = url.replace(':id', task_id);
            url = url.replace(':complete', 1);
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
            $('#modal-placeholder').load(url, {widget: 0});
        });

        $('.action-reopen').on('click', function () {
            let returnURL = document.URL;
            let task_id = $(this).data('task-id');
            var url = '{{ route("task.complete",[ ":id", ":complete"] ) }}';
            url = url.replace(':id', task_id);
            url = url.replace(':complete', 0);
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

        $('.action-delete').on('click', function () {

            var $_this = $(this);
            $(this).addClass('delete-task-active');

            $('#modal-placeholder').load('{!! route('task.delete.modal') !!}', {
                    action: $_this.data('action'),
                    modalName: 'task',
                    tab: $_this.data('tab'),
                    isReload: false,
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

        $('#btn-clear-filters').click(function () {
            $('#task_date_range, #task_from_date, #task_to_date,#search').val('');
            $('.task-status,.task-filters').prop('selectedIndex', 0);
            $('#filter').submit();
        });

    });
</script>