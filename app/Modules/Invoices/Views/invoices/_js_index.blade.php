@include('layouts._select2')
@include('layouts._daterangepicker')
<script type="text/javascript">
    $(function () {
        initDateRangePicker('invoice');

        initDateRangePreSelected('invoice');

        var settings = {
            placeholder: '{{ trans('fi.select_client') }}',
            allowClear: true,
            selectOnClose: true,
            width: '100%',

        };

        $('.client-lookup').select2(settings);

        $('.invoice_filter_options,#client,#invoice_date_range').change(function () {
            $('form#filter').submit();
        });

        $('#columns-filter-open').click(function () {
            $('#modal-placeholder').load('{!! route('invoice.get.filterColumns') !!}')
        });

        $('#tags-filter-open').click(function () {
            $('#modal-placeholder').load('{!! route('invoice.filterTags', ['tags' => json_encode($tags), 'tagsMustMatchAll' => $tagsMustMatchAll, 'firstLoad' => true]) !!}')
        });

        $('#btn-bulk-delete').click(function () {

            var ids = [];

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {

                $('#modal-placeholder').load('{!! route('bulk.delete.invoices.modal') !!}', {
                        action: '{{ route('invoices.bulk.delete') }}',
                        modalName: 'invoices',
                        data: ids,
                        returnURL: '{{route('invoices.index')}}'
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

        $('.bulk-change-status').click(function () {
            var ids = [];
            var status = $(this).data('status');

            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (ids.length > 0) {

                $('#modal-placeholder').load('{!! route('bulk.status.change.invoices.modal') !!}', {
                        action: '{{ route('invoices.bulk.status') }}',
                        modalName: 'invoices-status-change',
                        data: ids,
                        status: status,
                        returnURL: '{{route('invoices.index')}}'
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

        $('#btn-bulk-pdf').click(function () {

            var ids = [];
            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });
            if (ids.length > 0) {
                window.location = "{{ route('invoices.bulk.pdf') }}?ids=" + ids.join()
            }
        });

        $('#btn-bulk-print').click(function () {

            var ids = [];
            $('.bulk-record:checked').each(function () {
                ids.push($(this).data('id'));
            });
            if (ids.length > 0) {
                showHideLoaderModal();
                $.get("{{ route('invoices.bulk.save.pdf') }}?ids=" + ids.join()).done(function (response) {
                    showHideLoaderModal();
                    window.open(response).print();
                });
            }
        });

        $('.delete-invoice').click(function () {

            $(this).addClass('delete-invoices-active');

            $('#modal-placeholder').load('{!! route('invoices.delete.modal') !!}', {
                    action: $(this).data('action'),
                    modalName: 'invoices',
                    isReload: false,
                    returnURL: '{{route('invoices.index')}}'
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );

        });

        $('.send-overdue-reminder').click(function () {

            var $_this = $(this);
            $.ajax({
                url: $_this.data('action'),
                method: 'get',
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (data) {
                    showHideLoaderModal();

                    if (data.success) {
                        alertify.success(data.message, 5);
                    } else {
                        alertify.error(data.message, 5);
                    }
                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.error_sending_reminder') }}', 5);
                }
            });

        });

        $('.send-upcoming-notice').click(function () {

            var $_this = $(this);
            $.ajax({
                url: $_this.data('action'),
                method: 'get',
                beforeSend: function () {
                    showHideLoaderModal();
                },
                success: function (data) {
                    showHideLoaderModal();

                    if (data.success) {
                        alertify.success(data.message, 5);
                    } else {
                        alertify.error(data.message, 5);
                    }
                },
                error: function () {
                    showHideLoaderModal();
                    alertify.error('{{ trans('fi.error_sending_reminder') }}', 5);
                }
            });

        });

        $('.apply-to-invoices').click(function () {
            var url = '{{ route("payments.prepareCreditApplication", ":creditMemo") }}';
            url = url.replace(':creditMemo', $(this).data('invoice-id'));
            var redirect_url = $(this).data('redirect-to');
            $('#modal-placeholder').load(url, {
                redirect_to: redirect_url
            });
        });

        $('.apply-credit-memo').click(function () {
            var url = '{{ route("payments.prepareInvoiceSettlementWithCreditMemo", ":invoice") }}';
            url = url.replace(':invoice', $(this).data('invoice-id'));
            var redirect_url = $(this).data('redirect-to');
            $('#modal-placeholder').load(url, {
                redirect_to: redirect_url
            });
        });

        $('.apply-pre-payment').click(function () {
            var url = '{{ route("payments.prepareInvoiceSettlementWithPrePayment", ":invoice") }}';
            url = url.replace(':invoice', $(this).data('invoice-id'));
            var redirect_url = $(this).data('redirect-to');
            $('#modal-placeholder').load(url, {
                redirect_to: redirect_url
            });
        });

        $('#btn-clear-filters').click(function () {
            $('#search,#client,#tags-filter,#invoice_date_range,#invoice_from_date,#invoice_to_date').val('');
            $('#tags-must-match-all').val(0);
            $('.invoice_filter_options').prop('selectedIndex', 0);
            $('#filter').submit();
        });

        $('.btn-copy-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('invoiceCopy.create') }}', {
                invoice_id: $(this).data('invoice-id')
            });
        });

        $('.btn-copy-recurring-invoice').click(function () {
            $('#modal-placeholder').load('{{ route('invoiceToRecurringInvoiceCopy.create') }}', {
                invoice_id: $(this).data('invoice-id')
            });
        });

        $('.btn-print-invoice').click(function () {
            showHideLoaderModal();
            $.get($(this).data('action')).done(function (response) {
                showHideLoaderModal();
                window.open(response).print();
            });
        });

        $('.btn-un-mail-invoice,.btn-mail-invoice,.btn-invoice-status-change-to-draft').click(function () {
            $.post($(this).data('action')).done(function (response) {
                if (response.success == true) {
                    alertify.success(response.message, 5);
                    window.location.reload();
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

        $('.btn-invoice-status-change-to-cancel').click(function () {
            $.post($(this).data('action')).done(function (response) {
                alertify.success(response.message, 5);
                window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
            });
        });

        $('.btn-print-pdf-and-mark-as-mailed-invoice').click(function () {
            showHideLoaderModal();
            $.get($(this).data('action')).done(function (response) {
                showHideLoaderModal();
                window.open(response).print();
            });
        });

        $('.btn-edit-invoice-sent-and-paid').click(function () {

            $.post('{{ route('allow.editing.invoices.in.status') }}', {
                status: $(this).data('status'),
                invoice_id: $(this).data('invoice')
            }).done(function (response) {
                if (response.success == true) {
                    alertify.success(response.message);
                } else {
                    alertify.error(response.message);
                }
            }).fail(function (xhr) {
                let errors = JSON.parse(xhr.responseText).errors;
                $.each(errors, function (name, data) {
                    alertify.error(data[0], 5);
                });
            });

        });

    });
</script>