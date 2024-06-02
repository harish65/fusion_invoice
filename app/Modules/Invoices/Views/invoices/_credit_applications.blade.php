<script type="text/javascript">
    $(function () {
        let creditApplicationsCount = '{{$creditApplications->count()}}';
        if (creditApplicationsCount) {
            $('.credit-application-count').html(creditApplicationsCount);
        } else {
            $('.credit-application-count').html('');
        }

        $('.btn-delete-payment').click(function () {
            var $_this = $(this);
            $_this.addClass('delete-credit-memo-application-active');

            $('#modal-placeholder').load('{!! route('invoices.delete.credit.memo.application.modal') !!}', {
                    action: '{{ route('invoices.payments.delete', [$creditMemoId]) }}',
                    modalName: 'credit-memo-application',
                    isReload: false,
                    id: $_this.data('payment-invoice-id'),
                    message: "{{ trans('fi.delete_record_warning') }}",
                },
                function (response, status, xhr) {
                    if (status == "error") {
                        var response = JSON.parse(response);
                        alertify.error(response.message);
                    }
                }
            );
        });

    });
</script>

<table class="table table-hover table-striped">

    <thead>
    <tr>
        <th width="25%">{{ trans('fi.invoice') }}</th>
        <th width="20%">{{ trans('fi.payment_date') }}</th>
        <th>{{ trans('fi.amount') }}</th>
        <th>{{ trans('fi.note') }}</th>
        @if(Gate::check('payments.update') || Gate::check('payments.delete'))
            <th class="text-right" width="10%">{{ trans('fi.options') }}</th>
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach ($creditApplications as $paymentInvoice)
        <tr>
            <td>{{ $paymentInvoice->invoice->number }}</td>
            <td>{{ $paymentInvoice->formatted_paid_at }}</td>
            <td>{{ $paymentInvoice->formatted_invoice_amount_paid }}</td>
            <td>{{ $paymentInvoice->payment->note }}</td>
            @if(Gate::check('payments.update') || Gate::check('payments.delete'))
                <td class="text-right">
                    @can('payments.delete')
                        <a class="btn btn-sm btn-danger btn-delete-payment" href="javascript:void(0);"
                           title="{{ trans('fi.delete') }}" data-payment-invoice-id="{{ $paymentInvoice->id }}">
                            <i class="fa fa-times"></i>
                        </a>
                    @endcan
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>

</table>
