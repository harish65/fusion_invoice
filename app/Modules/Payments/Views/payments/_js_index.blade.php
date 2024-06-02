<script type="text/javascript">

    $(function () {

        @can('payments.delete')
        $('#btn-bulk-delete').click(function () {

            var ids = [];

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {

                $('#modal-placeholder').load('{!! route('bulk.delete.payments.modal') !!}', {
                        action: '{{ route('payments.bulk.delete') }}',
                        modalName: 'payments',
                        data: ids,
                        returnURL: '{{route('payments.index')}}'
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

        $('.delete-payment').click(function () {

            $(this).addClass('delete-payments-active');

            $('#modal-placeholder').load('{!! route('payments.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'payments',
                    isReload: false,
                    returnURL: '{{route('payments.index')}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });
        @endcan

        $('#btn-clear-filters').click(function () {
            $('#search').val('');
            $('#filter').submit();
        });

    });

</script>