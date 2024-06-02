@include('layouts._select2')
@include('layouts._daterangepicker')
<script type="text/javascript">
    $(function () {
        initDateRangePicker('recurring_invoice');

        initDateRangePreSelected('recurring_invoice');

        $('#tags-filter-open').click(function () {
            $('#modal-placeholder').load('{!! route('recurringInvoice.filterTags', ['tags' => json_encode($tags), 'tagsMustMatchAll' => $tagsMustMatchAll, 'firstLoad' => true]) !!}')
        });

        $('#recurring-invoice-columns-setting').click(function () {
            $('#modal-placeholder').load('{!! route('recurring.invoice.get.filterColumns') !!}')
        });

        var settings = {
            placeholder: '{{ trans('fi.select_client') }}',
            allowClear: true,
            selectOnClose: true,
            width: '100%',

        };

        $('.client-lookup').select2(settings);

        $('.recurring_invoice_filter_options,#client,#recurring_invoice_date_range').change(function () {
            $('form#filter').submit();
        });

        $('.delete-recurring-invoice').click(function () {

            $(this).addClass('delete-recurring-invoice-active');

            $('#modal-placeholder').load('{!! route('recurring.invoice.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'recurring-invoice',
                    isReload: false,
                    returnURL:'{{route('recurringInvoices.index')}}'
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

            $('#search,#recurring_invoice_from_date,#recurring_invoice_to_date,#recurring_invoice_date_range,#client,#tags-filter').val('');
            $('#tags-must-match-all').val(0);
            $('.recurring_invoice_filter_options').prop('selectedIndex', 0);
            $('#filter').submit();
        });

        $('.btn-copy-recurring-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('recurringInvoiceCopy.create') }}', {
                recurring_invoice_id: $(this).data('recurring-invoice-id')
            });
        });

        $(document).on('click', '#btn-create-live-invoice', function () {
            showHideLoaderModal();

            $.post('{{route('recurringInvoices.create.live.invoice')}}' , {'id': $(this).data('id')})
                .done(function (response) {
                    showHideLoaderModal();
                    alertify.success(response.message, 5);
                }).fail(function (response) {
                showHideLoaderModal();
                alertify.error($.parseJSON(response.responseText).message, 5);
            });
        });
    });
</script>