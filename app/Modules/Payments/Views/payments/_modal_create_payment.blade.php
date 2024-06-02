@include('payments._js_create_payment')
@include('layouts._select2')

<div class="modal fade" id="create-payment" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if($payment)
                    <h5 class="modal-title">{{ trans('fi.edit_payment') }}</h5>
                @else
                    <h5 class="modal-title">{{ trans('fi.enter_payment') }}</h5>
                @endif
                <button type="button" class="close modal-create-payment-close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>
                @php
                    $id = ($payment) ? $payment->id : null;
                    $paymentIntent = (old('payment_intent')) ? old('payment_intent') : (($payment) ? 'pre_payment' :
                    'for_invoices');
                    $clientId = (old('client_id')) ? old('client_id') : (($payment) ? $payment->client_id : null);
                    $amount = (old('amount')) ? old('amount') : (($payment) ? sprintf("%.2f", $payment->amount) : null);
                    $paidAt = (old('paid_at')) ? old('paid_at') : (($payment) ? $payment->formatted_paid_at : null);
                    $paymentMethodId = (old('payment_method_id')) ? old('payment_method_id') : (($payment) ?
                    $payment->payment_method_id : null);
                    $note = (old('note')) ? old('note') : (($payment) ? $payment->note : null);
                    $btnText = ($payment) ? trans('fi.save') : trans('fi.apply_to_invoices');
                @endphp
                <form class="form-horizontal" id="create-payment-form">
                    {!! Form::hidden('id', $id) !!}
                    <div class="form-group">
                        <label>{{ trans('fi.payment_intent') }}</label>
                        {!! Form::select('payment_intent', $paymentOptions, $paymentIntent, ['id' => 'payment_intent', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.client') }}</label>
                        {!! Form::select('client_id', $clients, $clientId, ['id' => 'client_id', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off', 'style'=>"width: 100%;"]) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.currency') }}</label>

                        {!! Form::select('currency_code', $currencies, ($payment) ? $payment->currency_code : config('fi.baseCurrency'), ['id' => 'currency_code', 'class' => 'form-control form-control-sm']) !!}

                    </div>
                    <div class="form-group">
                        <label>{{ trans('fi.payment_amount') }}</label>

                        {!! Form::text('amount', (!empty($payment) && !empty($currency)) ? \FI\Support\NumberFormatter::format($payment->amount, $currency) : "", ['id' => 'amount', 'data-amount' => $amount,'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}

                    </div>

                    <div class="input-group date">
                        <label>{{ trans('fi.payment_date') }}</label>
                        <div class="input-group date" id='paid_at' data-target-input="nearest">
                            {!! Form::text('paid_at', $paidAt,  ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#paid_at']) !!}
                            <div class="input-group-append"
                                 data-target='#paid_at' data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.payment_method') }}</label>

                        {!! Form::select('payment_method_id', $paymentMethods, $paymentMethodId, ['id' => 'payment_method_id', 'class' => 'form-control form-control-sm']) !!}

                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.note') }}</label>

                        {!! Form::textarea('note', $note, ['id' => 'note', 'class' => 'form-control form-control-sm', 'rows' => 4]) !!}

                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('email_payment_receipt', 1, null, ['id' => 'email_payment_receipt', 'class' => 'form-check-input']) !!}
                            <label class="form-check-label"
                                   for="email_payment_receipt">{{ trans('fi.email_payment_receipt') }}</label>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default modal-create-payment-close"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                @can('payments.create')
                    <button type="button" id="create-payment-confirm" class="btn btn-sm btn-primary"
                            data-text="{{ $btnText }}"
                            data-loading-text="{{ trans('fi.please_wait') }}..."> {{ $btnText }} </button>
                @endcan
            </div>
        </div>
    </div>
</div>
