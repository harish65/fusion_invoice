@include('payments._js_create')
@include('layouts._formdata')

<div class="modal fade" id="modal-enter-payment" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-enter-payment modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.enter_payment_for_invoice') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoice->id }}">

                            <div class="form-group">
                                <label>{{ trans('fi.client') }}</label>

                                {!! Form::select('client_id', [$client->id=>$client->name], $client->id, ['id' => 'client_id', 'class' => 'form-control form-control-sm disabled', 'disabled' => true]) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.invoice') }}</label>

                                {!! Form::select('invoice_id', [$invoice->id => $invoice->number], $invoice->id, ['id' => 'invoice_id', 'class' => 'form-control form-control-sm disabled', 'disabled' => true]) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.invoice_balance') }}</label>

                                {!! Form::text('invoice_balance', $invoice->amount->formatted_balance, ['id' => 'invoice_balance', 'class' => 'form-control form-control-sm disabled', 'disabled' => true, 'data-amount' => $invoice->amount->balance]) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.currency') }}</label>

                                {!! Form::select('currency_code', $currencies, $invoice->currency_code, ['disabled' => true,'id' =>'currency_code', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                            <div class="form-group">
                                <label>{{ trans('fi.note') }}</label>

                                {!! Form::textarea('payment_note', null, ['id' => 'payment_note', 'class' => 'form-control form-control-sm', 'rows' => 4]) !!}
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ trans('fi.amount') }}</label>

                                {!! Form::text('payment_amount', $invoice->amount->formatted_numeric_balance, ['id' => 'payment_amount', 'class' => 'form-control form-control-sm currency-input-validator']) !!}
                            </div>

                            <div class="input-group date">
                                <label>{{ trans('fi.payment_date') }}</label>
                                <div class="input-group date" id="payment_date" data-target-input="nearest">

                                    {!! Form::text('payment_date', $date, ['class' => 'custom-form-field form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off' ,'data-target' => "#payment_date"]) !!}
                                    <div class="input-group-append"
                                         data-target="#payment_date" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.payment_method') }}</label>
                                {!! Form::select('payment_method_id', $paymentMethods, null, ['id' => 'payment_method_id', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            @if (config('fi.mailConfigured') and $client->email)
                                <div class="form-group">
                                    <label
                                            for="email_payment_receipt">{{ trans('fi.email_payment_receipt') }}</label>

                                    <div class="col-sm-8">
                                        {!! Form::checkbox('email_payment_receipt', 1, $client->should_email_payment_receipt, ['id' => 'email_payment_receipt']) !!}
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label>{{ trans('fi.email_payment_receipt') }}</label>

                                    <div class="col-sm-8">
                                        {!! Form::checkbox('dummy', '', '', ['disabled' => 'true', 'title' => trans('fi.email_payment_receipt_notice')]) !!}
                                    </div>
                                </div>
                            @endif

                            <div id="payment-custom-fields">
                                @if ($customFields)
                                    @include('custom_fields._custom_fields_modal')
                                @endif
                            </div>
                            <div class="form-group" style="margin-bottom: 0">
                                <label>{{ trans('fi.remaining_balance') }}</label>

                                {!! Form::text('remaining_balance', 0.00 , ['class' => 'form-control form-control-sm disabled', 'id' => 'remaining_balance','readonly'=>true]) !!}
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                @can('payments.create')
                    <button type="button" id="enter-payment-confirm" class="btn btn-sm btn-primary"
                            data-loading-text="{{ trans('fi.please_wait') }}..." data-original-text="{{ trans('fi.submit') }}">{{ trans('fi.submit') }}</button>
                @endcan
            </div>
        </div>
    </div>
</div>
