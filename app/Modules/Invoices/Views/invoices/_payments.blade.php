<script type="text/javascript">
    $(function () {
        let paymentsCount = '{{count($payments)}}';
        if (paymentsCount) {
            $('.payment-count').html(paymentsCount);
        }
        else {
            $('.payment-count').html('');
        }
        $('.btn-edit-payment').click(function () {
            $('#modal-placeholder').load($(this).data('url'));
        });

        $('.btn-delete-payment').click(function () {
            var $_this = $(this);
            $_this.addClass('delete-invoices-payments-active');

            $('#modal-placeholder').load('{!! route('invoices.payment.delete.modal') !!}', {
                    action: '{{ route('invoices.payments.delete', [$invoiceId]) }}',
                    modalName: 'invoices-payments',
                    paymentInvoiceId: $_this.data('payment-invoice-id'),
                    isReload: true,
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

<table class="table table-hover table-striped table-responsive-sm table-responsive-xs">

    <thead>
    <tr>
        <th>{{ trans('fi.payment_date') }}</th>
        <th>{{ trans('fi.amount') }}</th>
        <th>{{ trans('fi.payment_method') }}</th>
        <th>{{ trans('fi.note') }}</th>
        @if(Gate::check('payments.update') || Gate::check('payments.delete'))
            <th>{{ trans('fi.options') }}</th>
        @endif
    </tr>
    </thead>

    <tbody>
    @foreach ($payments as $paymentInvoice)
        <tr>
            <td>{{ $paymentInvoice->payment->formatted_paid_at }}</td>
            <td>{{ $paymentInvoice->formatted_invoice_amount_paid }}</td>
            <td>
                @if ($paymentInvoice->payment->paymentMethod)
                    {{ $paymentInvoice->payment->paymentMethod->name }}
                @elseif($paymentInvoice->payment->credit_memo_id)
                    {{trans('fi.credit_memo')}}
                @endif
            </td>
            <td>{{ $paymentInvoice->payment->note }}</td>
            @if(Gate::check('payments.update') || Gate::check('payments.delete'))
                <td>
                    @can('payments.update')
                        <a class="btn btn-xs btn-primary btn-edit-payment-note" href="javascript:void(0);"
                           title="{{ trans('fi.edit_payment_note_form') }}" data-invoice-id="{{ $paymentInvoice->invoice_id }}"
                           data-payment-invoice-id="{{ $paymentInvoice->id }}">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endcan
                    @can('payments.delete')
                        <a class="btn btn-xs btn-danger btn-delete-payment" href="javascript:void(0);"
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
