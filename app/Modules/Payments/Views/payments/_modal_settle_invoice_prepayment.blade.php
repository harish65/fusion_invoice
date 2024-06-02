@include('payments._js_settle_invoice_prepayment')
<div class="modal fade" id="modal-fetch-invoices" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.prepayment_applications_for_invoice') }}
                    #{{ $invoice->number }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form id="fetch-invoices-form">
                    {!! Form::hidden('invoice_id', $invoice->id) !!}
                    {!! Form::hidden('total_paid', 0, ['id' => 'total_paid']) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-left: 7px;">
                                <label>{{ trans('fi.client_name') }}:</label>
                                {!! Form::text('client_name', $clientName , ['class' => 'form-control form-control-sm disabled', 'id' => 'client_name','disabled'=>true, 'size' => 50]) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('fi.invoice_amount') }}:</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa {{getCurrencyClass($invoice->currency_code)}}"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="amount" value="{{$formatted_amount}}"
                                           data-currency="{{$invoice->currency_code}}" data-amount="{{$amount}}" id="amount"
                                           disabled class="form-control form-control-sm disabled"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="invoice-table">
                                <table class="table table-sm table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('fi.date') }}</th>
                                        <th>{{ trans('fi.summary') }}</th>
                                        <th>{{ trans('fi.total') }}</th>
                                        <th>{{ trans('fi.balance') }}</th>
                                        <th>{{ trans('fi.paid_amount') }}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($settlements as $settlement)
                                        <tr>
                                            <td>{{ $settlement->formatted_created_at }}</td>
                                            <td>{{ $settlement->note }}</td>
                                            <td>{{ $settlement->formatted_numeric_amount }}</td>
                                            <td>{{ $settlement->formatted_numeric_remaining_balance }}</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa {{getCurrencyClass($settlement->currency_code)}}"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" id="{{'paid_amount_' . $settlement->id}}"
                                                           name="{{'paid_amount[' . $settlement->id. ']'}}"
                                                           data-currency="{{$settlement->currency_code}}"
                                                           data-amount="{{sprintf("%.2f", $settlement->remaining_balance)}}"
                                                           data-id="{{ $settlement->id }}"
                                                           disabled value="0" class="form-control form-control-sm" autocomplete="off"/>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="checkbox" id="{{'prepayment_selection_' . $settlement->id}}"
                                                       name="{{'prepayment_selection[' . $settlement->id .']'}}"
                                                       value="{{ $settlement->id }}"
                                                       data-amount="{{sprintf("%.2f", $settlement->remaining_balance)}}"
                                                       title="{{($invoice->currency_code == $settlement->currency_code) ? '' : trans('fi.currency_not_match')}}"
                                                       {{($invoice->currency_code == $settlement->currency_code) ? '' : 'disabled'}}
                                                       data-currency="{{$settlement->currency_code}}"
                                                       data-id="{{ $settlement->id }}" class="check check-aligned mt-2"/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-3 offset-sm-9">
                            <div class="form-group">
                                <label>{{ trans('fi.remaining_invoice_amount') }}:</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa {{getCurrencyClass($invoice->currency_code)}}"></i>
                                        </div>
                                    </div>
                                    {!! Form::text('remaining_balance', $formatted_amount , ['class' => 'form-control form-control-sm disabled', 'id' => 'remaining_balance','readonly'=>true, 'data-amount' => sprintf("%.2f", $amount) ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                @can('payments.create')
                <button type="button" id="confirm-payment-invoices" class="btn btn-sm btn-primary"
                        data-text="{{ trans('fi.submit') }}"
                        data-loading-text="{{ trans('fi.please_wait') }}...">{{ trans('fi.submit') }}</button>
                @endcan
            </div>
        </div>
    </div>
</div>
