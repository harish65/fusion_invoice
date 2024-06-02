@can('payments.update')
<script type="text/javascript">
    $(function () {
        $('.email-payment-receipt').click(function () {
            $('#modal-placeholder').load("{{ route('paymentMail.create') }}", {
                payment_id: $(this).data('payment-id'),
                redirectTo: $(this).data('redirect-to')
            }, function (response, status, req) {
                if (status == 'error') {
                    alertify.error('{{ trans('fi.problem_with_email_template') }}', 5);
                }
            });
        });
    });
</script>
@endcan

<table class="table table-hover table-striped table-sm text-nowrap">

    <thead>
    <tr>
        @if(isset($bulk_action) && $bulk_action == true)
            @can('payments.delete')
            <th>
                <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
            </th>
            @endcan
        @endif
        <th>{!! Sortable::link('paid_at', trans('fi.payment_date'), 'payments') !!}</th>
        @if(!isset($client_view))
            <th>{!! trans('fi.client') !!}</th>
        @endif
        <th>{!! Sortable::link('payment_invoices.invoices.number', trans('fi.invoice'), 'payments') !!}</th>
        <th>{!! Sortable::link('payment_invoices.invoices.invoice_date', trans('fi.invoice_date'), 'payments') !!}</th>
        <th>{!! trans('fi.summary') !!}</th>
        <th>{!! trans('fi.amount') !!}</th>
        <th>{!! trans('fi.open_balance') !!}</th>
        <th>{!! Sortable::link('payment_methods.name', trans('fi.payment_method'), 'payments') !!}</th>
        <th>{!! Sortable::link('note', trans('fi.note'), 'payments') !!}</th>
        <th class="text-right">{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($payments as $payment)


        <tr>
            @if(isset($bulk_action) && $bulk_action == true)
                @can('payments.delete')
                <td><input type="checkbox" class="bulk-record" data-id="{{ $payment->id }}"></td>
                @endcan
            @endif
            <td>{{ $payment->formatted_paid_at }}</td>
            @if(!isset($client_view))
                <td>
                    @if( Gate::check('clients.view'))
                        @if($payment->client_id)
                            <a href="{{ route('clients.show', [$payment->client_id]) }}">{{ $payment->client->name }}</a>
                        @endif
                    @else
                        {{ $payment->client->name }}
                    @endif
                </td>
            @endif
            <td>
                @if( Gate::check('invoices.update'))
                    @if(count($payment->paymentInvoice) == 1 && isset($payment->paymentInvoice->first()->invoice->number))
                        <a href="{{ route('invoices.edit', [$payment->paymentInvoice->first()->invoice_id]) }}">{{ $payment->paymentInvoice->first()->invoice->number }}</a>
                    @elseif(count($payment->paymentInvoice) > 1)
                        <a href="javascript:void(0)"
                           data-action="{{ route('payments.applications',['payment' => $payment->id])}}"
                           class="payment-applications">
                            {{ trans('fi.multiple') }}
                        </a>
                    @endif
                @else
                    @if(count($payment->paymentInvoice) == 1)
                        {{ $payment->paymentInvoice->first()->invoice->number }}
                    @elseif(count($payment->paymentInvoice) > 1)
                        <a href="javascript:void(0)"
                           data-action="{{ route('payments.applications',['payment' => $payment->id])}}"
                           class="payment-applications">
                            {{ trans('fi.multiple') }}
                        </a>
                    @endif
                @endif
            </td>
            <td>
                @if(count($payment->paymentInvoice) == 1 && isset($payment->paymentInvoice->first()->invoice->formatted_invoice_date))
                    {{ $payment->paymentInvoice->first()->invoice->formatted_invoice_date }}
                @elseif(count($payment->paymentInvoice) > 1)
                    {{ trans('fi.multiple') }}
                @endif
            </td>
            <td>
                @if(count($payment->paymentInvoice) == 1 && isset($payment->paymentInvoice->first()->invoice->summary))
                    {{ $payment->paymentInvoice->first()->invoice->summary }}
                @endif
            </td>
            <td>{{ $payment->formatted_amount_with_currency }}</td>
            <td>
                <span class="{{($payment->remaining_balance) ? 'open-balance' : ''}}">{{ $payment->formatted_remaining_balance_with_currency }}</span>
            </td>
            <td>@if ($payment->paymentMethod) {{ $payment->paymentMethod->name }} @endif</td>
            <td>{!! $payment->formatted_notes !!}</td>
                <td class="text-right">
                <div class="btn-group action-menu">
                    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                        {{ trans('fi.options') }} <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @if($payment->amount == $payment->remaining_balance)
                            @can('payments.update')
                            <a href="javascript:void(0)" class="edit-payment dropdown-item"
                               data-action="{{ route('payments.editPayment',['payment' => $payment->id]) }}">
                                <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                            </a>
                            @endcan
                        @endif
                        @if(count($payment->paymentInvoice) == 1)
                            @can('invoices.view')
                            <a href="{{ route('invoices.pdf', [$payment->paymentInvoice->first()->invoice_id]) }}"
                               target="_blank" id="btn-pdf-invoice" class="dropdown-item">
                                <i class="fa fa-print"></i> {{ trans('fi.invoice_pdf') }}
                            </a>
                            @endcan
                        @endif
                        @if(count($payment->paymentInvoice) > 0)
                            @can('invoices.view')
                            <a href="javascript:void(0)"
                               data-action="{{ route('payments.applications',['payment' => $payment->id])}}"
                               class="payment-applications dropdown-item">
                                <i class="fas fa-dollar-sign"></i> {{ trans('fi.payment_applications') }}
                            </a>
                            @endcan
                        @endif
                        @if (config('fi.mailConfigured'))
                            @can('payments.update')
                            <a href="javascript:void(0)" class="email-payment-receipt dropdown-item"
                               data-payment-id="{{ $payment->id }}" data-redirect-to="{{ request()->fullUrl() }}">
                                <i class="fa fa-envelope"></i> {{ trans('fi.email_payment_receipt') }}
                            </a>
                            @endcan
                        @endif
                        @can('payments.view')
                        <a href="{{ route('payments.pdf',$payment->id) }}" class="payment-receipt dropdown-item">
                            <i class="fa fa-file-pdf"></i> {{ trans('fi.payment_receipt') }}
                        </a>
                        @endcan
                        @can('payments.delete')
                        <div class="dropdown-divider"></div>
                        <a href="#" data-action="{{ route('payments.delete', [$payment->id]) }}"
                           class="delete-payment text-danger dropdown-item">
                            <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </td>
        </tr>

    @endforeach
    </tbody>

</table>