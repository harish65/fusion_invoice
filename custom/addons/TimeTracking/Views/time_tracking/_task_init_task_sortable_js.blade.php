<script type="text/javascript">
    function initTaskSortable() {
        $(".task-section-list-table").sortable({
            connectWith: [".task-section-list-table-sortable"],
            update: function () {
                var $_this = $(this);
                var Lists = $_this.find('.order-id');
                var reOrder = [];

                $.each(Lists, function (key, value) {
                    reOrder.push($(value).val());
                });

                reOrder.length == 0 ? $_this.css('display', 'block') : '';

                if (reOrder.length > 0) {
                    $_this.css('display', '');
                    var form_data = objectToFormData({task_ids: reOrder});
                    $.ajax({
                        url: '{{ route('timeTracking.tasks.updateDisplayOrder') }}',
                        method: 'post',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function () {
                            $_this.find('p.no-task').html("");
                        },
                    }).fail(function (response) {
                        $.each($.parseJSON(response.responseText).errors, function (id, message) {
                            alertify.error(message[0], 5);
                        });
                    });
                } else {
                    $_this.find('p.no-task').html("{{ trans('fi.no_records_found') }}");
                }

            }
        });
    }
</script>