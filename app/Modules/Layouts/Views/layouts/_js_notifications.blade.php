<script type="text/javascript">
    $(function () {
        $(document).on('click', '.notification-item', function () {
            var url = '{{ route("notifications.markViewed", ":notification") }}';
            url = url.replace(':notification', $(this).data('notification-id'));
            var redirect_url = $(this).data('url');
            $.post(url, function () {
                window.location = redirect_url;
            });
        });

        $(document).on('click', '.clear-all-notifications', function () {
            var url = '{{route('notifications.clearAll')}}';
            var redirect_url = $(this).data('url');

            $.post(url, function (response) {
                if (response.success == true) {
                    alertify.success(response.message, 5);
                    window.location = redirect_url;
                } else {
                    alertify.error(response.message, 5);
                }
            }).fail(function (response) {
                if (response.status == 401) {
                    alertify.error($.parseJSON(response.responseText).message);
                } else {
                    alertify.error('{{ trans('fi.unknown_error') }}', 5);
                }
            });
        });
    });
</script>
