<script type="text/javascript">
    $(function () {
        $('#modal-invoice-list').modal();
    });
</script>
<div class="modal fade" id="modal-invoice-list" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.payment_application') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form id="fetch-invoices-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('fi.client_name') }}:</label>
                                {!! Form::text('client_name', $clientName , ['class' => 'form-control form-control-sm disabled', 'id' => 'client_name','disabled'=>true, 'size' => 50]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('fi.payment_amount') }}:</label>
                                <input type="text" name="amount" value="{{$payment->formatted_numeric_amount}}"
                                       id="amount" disabled class="form-control form-control-sm disabled"/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-hover table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>{{ trans('fi.invoice') }}</th>
                                    <th>{{ trans('fi.date') }}</th>
                                    <th>{{ trans('fi.due') }}</th>
                                    <th>{{ trans('fi.summary') }}</th>
                                    <th>{{ trans('fi.total') }}</th>
                                    <th>{{ trans('fi.paid') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($paymentInvoices as $paymentInvoice)
                                    <tr>
                                        <td>{{ $paymentInvoice->number }}</td>
                                        <td>{{ $paymentInvoice->formatted_invoice_date }}</td>
                                        <td @if ($paymentInvoice->isOverdue) style="color: #ff0000; font-weight: bold;" @endif>{{ $paymentInvoice->formatted_due_at }}</td>
                                        <td>{{ $paymentInvoice->summary }}</td>
                                        <td>{{ $paymentInvoice->amount->formatted_total }}</td>
                                        <td>{{ $paymentInvoice->invoice_amount_paid }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-3 offset-sm-9">
                            <div class="form-group">
                                <label>{{ trans('fi.remaining_payment_balance') }}:</label>
                                {!! Form::text('remaining_balance', $payment->formatted_numeric_remaining_balance , ['class' => 'form-control form-control-sm disabled', 'id' => 'remaining_balance','readonly'=>true]) !!}
                                <div class="mt-1">
                                    {{ trans('fi.remaining_payment_balance_apply_later_info') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">{{ trans('fi.ok') }}</button>
            </div>
        </div>
    </div>
</div>