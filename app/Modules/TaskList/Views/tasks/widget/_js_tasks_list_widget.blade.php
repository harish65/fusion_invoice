@include('layouts._daterangepicker')
<script type="text/javascript">
    $(function () {

        $(document).on('click', '.dynamic-pages > nav > .pagination a', function (event) {
            event.preventDefault();
            var id = $(this).closest('.pagination').closest('.dynamic-pages').data('section-id');
            var name = $(this).closest('.pagination').closest('.dynamic-pages').data('section-name');
            loadTimelineListNew($(this).attr('href').split('page=')[1], id, name);
        });

        function loadTimelineListNew(page = 1, id, name) {
            let $form = $('#tasks-filter-form');
            let data = $form.serializeArray();

            data.push({name: 'page', value: page});
            data.push({name: 'taskSection', value: id});

            $.ajax({
                url: "{{route('task.widget.list')}}",
                method: 'post',
                data: data,
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (response) {
                    showHideLoaderModal();
                    $('#task-list-' + name + '-container').html(response);

                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
            data.pop('taskSection');
        }
    });
</script>