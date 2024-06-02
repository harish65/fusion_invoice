@include('payments._js_fetch_invoices')
<div class="modal fade" id="modal-fetch-invoices" data-keyboard="false" data-backdrop="static">
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

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa {{getCurrencyClass(config('fi.baseCurrency'))}}"></i>
                                        </div>
                                    </div>
                                    <input type="text" name="amount" value="{{$formatted_amount}}"
                                           data-currency="{{ $currency_code }}" data-amount="{{$amount}}" id="amount"
                                           disabled
                                           class="form-control form-control-sm disabled"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @foreach(session()->getOldInput() as $key=>$value)
                                <input type="hidden" name="{{$key}}" value="{{$value}}"/>
                            @endforeach
                            <div class="invoice-table">
                                <table class="table table-hover table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('fi.invoice') }}</th>
                                        <th>{{ trans('fi.date') }}</th>
                                        <th>{{ trans('fi.due') }}</th>
                                        <th>{{ trans('fi.summary') }}</th>
                                        <th>{{ trans('fi.total') }}</th>
                                        <th>{{ trans('fi.balance') }}</th>
                                        <th>{{ trans('fi.paid_amount') }}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->number }}</td>
                                            <td class="hidden-xs">{{ $invoice->formatted_invoice_date }}</td>
                                            <td class="hidden-md hidden-sm hidden-xs"
                                                @if ($invoice->isOverdue) style="color: #ff0000; font-weight: bold;" @endif>{{ $invoice->formatted_due_at }}</td>
                                            <td class="hidden-sm hidden-xs">{{ $invoice->summary }}</td>
                                            <td class="hidden-sm hidden-xs">{{ $invoice->amount->formatted_total }}</td>
                                            <td class="hidden-sm hidden-xs">{{ $invoice->amount->formatted_balance }}</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fa {{getCurrencyClass($invoice->currency_code)}}"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text"
                                                           id="{{'paid_amount_' . $invoice->id}}"
                                                           name="{{'paid_amount[' . $invoice->id. ']'}}"
                                                           data-amount="{{$invoice->amount->balance}}"
                                                           data-currency="{{$invoice->currency_code}}"
                                                           data-id="{{ $invoice->id }}"
                                                           class="form-control form-control-sm"
                                                           disabled autocomplete="off"/>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="checkbox" id="{{'invoice_selection_' . $invoice->id}}"
                                                       name="{{'invoice_selection[' . $invoice->id .']'}}"
                                                       value="{{ $invoice->id }}"
                                                       title="{{($invoice->currency_code == $currency_code) ? '' : trans('fi.currency_not_match')}}"
                                                       {{($invoice->currency_code == $currency_code) ? '' : 'disabled'}}
                                                       data-currency="{{$invoice->currency_code}}"
                                                       data-amount="{{$invoice->amount->balance}}"
                                                       data-id="{{ $invoice->id }}" class="check check-aligned mt-2"/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 offset-md-6">
                            <div class="form-group float-right">
                                <label>{{ trans('fi.remaining_payment_balance') }}:</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fa {{getCurrencyClass(config('fi.baseCurrency'))}}"></i>
                                        </div>
                                    </div>
                                    {!! Form::text('remaining_balance', $formatted_amount , ['class' => 'form-control form-control-sm disabled', 'id' => 'remaining_balance','readonly'=>true, 'data-amount' => sprintf("%.2f", $amount) ]) !!}
                                </div>
                                <div class="mt-1">
                                    {{ trans('fi.remaining_payment_balance_apply_later_info') }}
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
