@include('layouts._select2')
@include('layouts._daterangepicker')
<script type="text/javascript">
    $(function () {

        initDateRangePicker('quote');

        initDateRangePreSelected('quote');

        var settings = {
            placeholder: '{{ trans('fi.select_client') }}',
            allowClear: true,
            selectOnClose: true,
            width: '100%',
        };

        $('.client-lookup').select2(settings);

        $('.quote_filter_options,#client,#quote_date_range').change(function () {
            $('form#filter').submit();
        });

        $('#quote-columns-setting').click(function () {
            $('#modal-placeholder').load('{!! route('quote.get.filterColumns') !!}')
        });

        $('#btn-bulk-delete').click(function () {
            var ids = [];

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {

                $('#modal-placeholder').load('{!! route('bulk.delete.quotes.modal') !!}', {
                        action: '{{ route('quotes.bulk.delete') }}',
                        modalName: 'quotes',
                        data: ids,
                        returnURL: '{{route('quotes.index')}}'
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

        $('#btn-bulk-print').click(function () {

            var ids = [];
            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });
            if (ids.length > 0) {
                showHideLoaderModal();
                $.get("{{ route('quotes.bulk.save.pdf') }}?ids=" + ids.join()).done(function (response) {
                    showHideLoaderModal();
                    window.open(response).print();
                });
            }
        });

        $('.bulk-change-status').click(function () {
            var ids = [];
            var status = $(this).data('status');

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {

                $('#modal-placeholder').load('{!! route('bulk.status.change.quotes.modal') !!}', {
                        action: '{{ route('quotes.bulk.status') }}',
                        modalName: 'quotes-status-change',
                        data: ids,
                        status: status,
                        returnURL: '{{route('quotes.index')}}'
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

        $('.delete-quote').click(function () {

            $(this).addClass('delete-quotes-active');

            $('#modal-placeholder').load('{!! route('quotes.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'quotes',
                    isReload: false,
                    returnURL: '{{route('quotes.index')}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });

        $('#btn-bulk-pdf').click(function () {

            var ids = [];
            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });
            if (ids.length > 0) {
                window.location = "{{ route('quotes.bulk.pdf') }}?ids=" + ids.join()
            }
        });

        $('#btn-clear-filters').click(function () {
            $('#search,#quote_from_date,#quote_to_date,#client,#quote_date_range').val('');
            $('.quote_filter_options').prop('selectedIndex', 0);
            $('#filter').submit();
        });

        $('.btn-print-quote').click(function () {
            showHideLoaderModal();
            $.get($(this).data('action')).done(function (response) {
                showHideLoaderModal();
                window.open(response).print();
            });
        });

    });
</script>