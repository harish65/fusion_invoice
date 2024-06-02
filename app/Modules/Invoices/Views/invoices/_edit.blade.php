@include('invoices._js_edit')
@include('layouts._select2')
@include('invoices._invoice_overlay')
<style>
    select.discount-type, select.discount-type option {
        font-family: "Font Awesome\ 5 Free";
        font-weight: 900;
        font-size: .70rem !important;
        -moz-osx-font-smoothing: grayscale;
        -webkit-font-smoothing: antialiased;
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        line-height: 1;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-4 mt-1">
                @if($invoiceOverlayStatus)
                    <a href="{{ route('clients.show', [$invoice->client->id]) }}" title="{{ trans('fi.view_client') }}">
                        <strong>{{ $invoice->client->name }}</strong>
                    </a>
                @endif
            </div>
            <div class="col-8 d-none d-sm-block">
                <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-6">

                <h1 class="d-inline {{($invoice->type=='credit_memo') ? 'title-credit-memo' : ''}}">
                    {{ ($invoice->type == 'credit_memo') ? trans('fi.credit_memo') : trans('fi.invoice') }}
                    #{{ $invoice->number }}
                </h1>
                @if ($invoice->viewed)
                    <span class="badge badge-viewed">{{ trans('fi.viewed') }}</span>
                @endif
                @if($invoice->paid_status == true)
                    <span class="badge badge-paid">{{trans('fi.paid') }}</span>
                @endif
                @if($invoice->status=='applied')
                    <span class="badge badge-applied">{{trans('fi.applied') }}</span>
                @endif
                @if($invoice->status == 'canceled')
                    <span class="badge badge-canceled">{{trans('fi.canceled') }}</span>
                @endif
                @if ($invoice->quote()->count())
                    @can('quotes.update')
                        <span class="badge badge-info">
                        <a href="{{ route('quotes.edit', [$invoice->quote->id]) }}"
                           style="color: inherit;">{{ trans('fi.converted_from_quote') }} {{ $invoice->quote->number }}</a>
                    </span>
                    @endcan
                @endif

                @if ($invoice->recurring_invoice_id > 0)
                    <span style="margin-left: 10px;">{{ trans('fi.created_recurring_invoice_id') }}:
                        <a href="{{ route('recurringInvoices.edit', $invoice->recurring_invoice_id) }}">{{ $invoice->recurring_invoice_id }}</a>
                    </span>
                    {!! Form::hidden('number', $invoice->recurring_invoice_id, ['recurring_invoice_id' => 'number']) !!}
                @endif

            </div>
            <div class="col-sm-6 pr-0">
                <div class="float-sm-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.action') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            @if (config('fi.mailConfigured'))
                                <a href="javascript:void(0);" id="btn-email-invoice" class="email-invoice dropdown-item"
                                   data-invoice-id="{{ $invoice->id }}"
                                   data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}">
                                    <i class="fa fa-envelope"></i> {{ trans('fi.email') }}
                                </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('invoices.pdf', [$invoice->id]) }}" target="_blank"
                               id="btn-pdf-invoice">
                                <i class="fa fa-file-pdf"></i> {{ trans('fi.pdf') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);"
                               data-action="{{ route('invoices.save.pdf', [$invoice->id]) }}" id="btn-print-invoice">
                                <i class="fa fa-print"></i> {{ trans('fi.print') }}
                            </a>

                            @if (in_array('mailed',$invoice->virtual_status) == true && $invoice->virtual_status != null)
                                <a class="dropdown-item btn-un-mail-invoice" href="javascript:void(0);"
                                   data-action="{{ route('invoices.remove.dateMailed', [$invoice->id]) }}"
                                   id="btn-un-mail-invoice">
                                    <i class="fa fa-share"></i> {{ trans('fi.unmark_mailed') }}
                                </a>
                            @else
                                <a class="dropdown-item" href="javascript:void(0);"
                                   data-action="{{ route('invoices.save.dateMailed', [$invoice->id]) }}"
                                   id="btn-mail-invoice">
                                    <i class="fa fa-reply"></i> {{ trans('fi.mark_as_mailed') }}
                                </a>
                            @endif

                            @if (in_array('mailed',$invoice->virtual_status) == false)
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);"
                                   data-action="{{ route('invoices.print.pdf.and.mark.as.mailed', [$invoice->id]) }}"
                                   class="btn-print-pdf-and-mark-as-mailed-invoice dropdown-item">
                                    <i class="fa fa-print"></i> {{ trans('fi.print_mark_mailed') }}
                                </a>
                            @endif

                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.other') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            @if($invoice->status != 'draft')
                                @if(config('fi.allowEditInvoiceStatus') == 'draft_and_sent' && $invoice->amount->balance != 0)
                                    <a class="dropdown-item text-warning btn-edit-invoice-sent-and-paid"
                                       href="{{ route('invoices.edit', [$invoice->id,'overlay' => 0]) }}"
                                       data-invoice="{{ $invoice->id }}"
                                       data-status="{{ ($invoice->paid_status == true) ? 'paid' : $invoice->status }}">
                                        <i class="fa fa-edit"></i> {{ trans('fi.allow_edit_status_invoice', ['status' => trans('fi.'.$invoice->status)]) }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif

                                @if(config('fi.allowEditInvoiceStatus') == 'draft_or_sent_and_paid')
                                    <a class="dropdown-item text-warning btn-edit-invoice-sent-and-paid"
                                       href="{{ route('invoices.edit', [$invoice->id,'overlay' => 0]) }}"
                                       data-invoice="{{ $invoice->id }}"
                                       data-status="{{ ($invoice->paid_status == true) ? 'paid' : $invoice->status }}">
                                        <i class="fa fa-edit"></i> {{ trans('fi.allow_edit_status_invoice', ['status' => ($invoice->paid_status == true) ? trans('fi.paid') : ucfirst($invoice->status)]) }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif
                            @endif
                            @can('payments.create')
                                @if ($invoice->isPayable)
                                    <a href="javascript:void(0);" id="btn-enter-payment"
                                       class="enter-payment dropdown-item"
                                       data-invoice-id="{{ $invoice->id }}"
                                       data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                       data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}"><i
                                                class="fa fa-credit-card">
                                        </i> {{ trans('fi.enter_payment') }}
                                    </a>
                                    @if($creditMemoCount > 0)
                                        <a href="javascript:void(0);" id="btn-apply-credit-memo"
                                           class="apply-credit-memo dropdown-item"
                                           data-invoice-id="{{ $invoice->id }}"
                                           data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                           data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}"><i
                                                    class="fa fa-list-alt">
                                            </i> {{ trans('fi.apply_credit_memo') }}
                                        </a>
                                    @endif
                                    @if($prePaymentCount > 0)
                                        <a href="javascript:void(0);" id="btn-apply-pre-payment"
                                           class="apply-pre-payment dropdown-item"
                                           data-invoice-id="{{ $invoice->id }}"
                                           data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                           data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}"><i
                                                    class="fa fa-money-check-alt"></i> {{ trans('fi.apply_pre_payment') }}
                                        </a>
                                    @endif
                                @elseif($invoice->type == 'credit_memo' && abs($invoice->amount->balance) > 0 && $invoiceCount > 0)
                                    <a href="javascript:void(0);" id="btn-apply-to-invoices"
                                       class="apply-to-invoices dropdown-item"
                                       data-invoice-id="{{ $invoice->id }}"
                                       data-invoice-balance="{{ $invoice->amount->formatted_numeric_balance }}"
                                       data-redirect-to="{{ route('invoices.edit', [$invoice->id]) }}"><i
                                                class="far fa-hand-point-right">
                                        </i> {{ trans('fi.apply_to_invoices') }}
                                    </a>
                                @endif
                                @if($invoice->payments->count() == 0 && $invoice->status !== 'draft')
                                    <a class="dropdown-item btn-invoice-status-change-to-draft"
                                       href="javascript:void(0);"
                                       data-action="{{route('invoices.status.changeToDraft',[$invoice->id])}}"
                                       id="btn-invoice-status-change-to-draft">
                                        <i class="fas fa-exchange-alt"></i> {{ trans('fi.change_to_draft') }}
                                    </a>
                                @endif
                            @endcan
                            <a class="dropdown-item" href="javascript:void(0);" id="btn-copy-invoice">
                                <i class="fa fa-copy"></i> {{ trans('fi.copy') }}
                            </a>
                            @if($invoice->type != 'credit_memo')
                                <a href="javascript:void(0);" class="dropdown-item" id="btn-copy-recurring-invoice">
                                    <i class="fa fa-copy"></i> {{ trans('fi.copy_to_recurring_invoice') }}
                                </a>
                            @endif
                            <a class="dropdown-item"
                               href="{{ route('clientCenter.public.invoice.show', [$invoice->url_key, $invoice->token]) }}"
                               target="_blank">
                                <i class="fa fa-globe"></i> {{ trans('fi.public_link') }}
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" id="btn-invoice-view-timeline">
                                <i class="fa fa-clock"></i> {{ trans('fi.timeline') }}
                            </a>
                            @if($invoice->client->active != 0)
                                @if($invoice->type == 'invoice' && $invoice->isOverdue)
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);"
                                       data-action="{{ route('invoices.payment-reminder', [$invoice->id]) }}"
                                       data-invoice-id="{{ $invoice->id }}"
                                       class="send-overdue-reminder dropdown-item">
                                        <i class="fa fa-bell"></i> {{ trans('fi.email_overdue_invoice_reminder') }}
                                    </a>
                                @endif

                                @if($invoice->type == 'invoice' && ! $invoice->isOverdue && $invoice->unPaid_status == true)
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);"
                                       data-action="{{ route('invoices.payment-notice', [$invoice->id]) }}"
                                       data-invoice-id="{{ $invoice->id }}"
                                       class="send-upcoming-notice dropdown-item">
                                        <i class="fa fa-bell"></i> {{ trans('fi.email_upcoming_invoice_notice') }}
                                    </a>
                                @endif

                            @endif
                            @if(config('fi.allowInvoiceDelete') ==  1)
                                @can('invoices.delete')
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="btn-delete-invoice text-danger dropdown-item">
                                        <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div>

                    @if ($returnUrl)
                        <a href="{{$returnUrl}}" class="btn btn-sm empty-invoice-delete btn-default">
                            <i class="fa fa-backward"></i> {{ trans('fi.back') }}
                        </a>
                    @endif

                    <div class="btn-group {{ $invoice->payments->count()  != 0 || $invoice->status !== 'draft' ? 'd-none' : null }} overlay-button">
                        <button type="button" class="btn btn-sm btn-primary btn-save-invoice"
                                data-loading-text="{{ trans('fi.saving') }}"
                                data-original-text="{{ trans('fi.save') }}"><i
                                    class="fa fa-save"></i> {{ trans('fi.save') }}</button>
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="btn-save-invoice dropdown-item"
                               data-loading-text="{{ trans('fi.saving') }}"
                               data-original-text="{{ trans('fi.save_and_apply_exchange_rate') }}"
                               data-apply-exchange-rate="1">
                                {{ trans('fi.save_and_apply_exchange_rate') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                @if ($invoice->virtual_status != null)
                    @foreach($invoice->virtual_status as $virtual_status)
                        {{-- These badges are already shown above. --}}
                        @if(in_array($virtual_status, ["all_statuses","paid","canceled","applied"]) === false)
                            <span class="badge badge-{{ $virtual_status }}">{{ trans('fi.' . $virtual_status) }}</span>
                        @endif
                    @endforeach
                @endif
            </div>

        </div>
    </div>

</section>

<section class="content">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-10">

                @include('layouts._alerts')

                <div id="form-status-placeholder"></div>
                <div class="card card-primary card-outline">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn-summary-and-tags btn btn-xs btn-primary float-right d-none"
                                        data-action="{{ route('invoices.update.summary.and.tags', [$invoice->id]) }}"
                                        id="btn-summary-and-tags">
                                    {{trans('fi.save')}}
                                </button>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>
                                        <div class="card-header pb-1">
                                            <h3 class="card-title">{{ trans('fi.summary') }}</h3>
                                        </div>
                                    </label>
                                    {!! Form::text('summary', $invoice->summary, ['id' => 'summary', 'class' => 'form-control form-control-sm change-summary-and-tags text-summary']) !!}
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        <div class="card-header pb-1">
                                            <h3 class="card-title">{{ trans('fi.tags') }}</h3>
                                        </div>
                                    </label>
                                    {!! Form::select('tags[]', $tags, $selectedTags, ['class' => 'form-control form-control-sm client-tags change-summary-and-tags','multiple' => true, 'id' => 'invoice-tags', 'style' => 'width:100%']) !!}
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-body -->
                </div>

                <div class={{$invoiceOverlayStatus ? "paid-overlay": null }}  {{$creditMemoOverlayStatus ? "paid-overlay": null }}>
                    <div class="row {{ $invoiceOverlayStatus ? 'paid-overlay-true' : null }}  {{$creditMemoOverlayStatus ? "paid-overlay-true": null }}">
                        <div class="col-sm-6 " id="col-from">

                            @include('invoices._edit_from')

                        </div>

                        <div class="col-sm-6 pointer-event-none" id="col-to">

                            @include('invoices._edit_to')

                        </div>

                    </div>

                    <div class="row {{ $invoiceOverlayStatus ? 'paid-overlay-true' : null }}   {{$creditMemoOverlayStatus ? "paid-overlay-true": null }}">

                        <div class="col-sm-12" style="overflow-x: visible;">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">{{ trans('fi.items') }}</h3>

                                    <div class="card-tools pull-right">
                                        <button {{$invoiceOverlayStatus ? 'disabled' : null}} {{$creditMemoOverlayStatus ? 'disabled' : null}} class="btn btn-sm btn-primary"
                                                id="btn-add-item">
                                            <i class="fa fa-plus"></i> {{ trans('fi.add_item') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body overflow-auto">
                                    <table id="item-table" data-module-name="invoice" data-id="{{$invoice->id}}"
                                           class="table table-hover table-borderless table-striped table-sm sortable-item">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th class="{{ $allowLineItemDiscounts == true ? 'col-4 cw-33' : 'col-5 cw-40' }}">
                                                {{ trans('fi.product') }} / {{ trans('fi.description') }}
                                            </th>
                                            <th class="col-1 cw-8 text-center">{{ trans('fi.qty') }}</th>
                                            <th class="col-2 cw-16 text-center">{{ trans('fi.price') }}</th>
                                            @if($allowLineItemDiscounts == true)
                                                <th data-toggle="tooltip" data-placement="auto"
                                                    title="{!! trans('fi.tt_line_item_discount') !!}"
                                                    class="col-2 cw-16 text-center">{{ trans('fi.discount') }}</th>
                                            @endif
                                            <th class="col-2 cw-8 text-center">
                                                {{ trans('fi.tax_1') }}
                                                @if(config('fi.numberOfTaxFields') == '2')
                                                    / {{ trans('fi.tax_2') }}
                                                @endif
                                            </th>
                                            <th class="col-2 cw-16 text-center">{{ trans('fi.total') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($invoice->items as $key =>  $item)
                                            <tr class="item" id="tr-item-{{ $item->id }}">
                                                <td class="handle"><i class="fa fa-sort"></i></td>
                                                <td colspan="{{ $allowLineItemDiscounts == true ? 6 : 5 }}"
                                                    class="no-padding col-12">
                                                    <table class="table main-table table-hover table-borderless regular-fields mt-10 max-content-custom"
                                                           data-item-custom-id="{{$item->id}}">
                                                        <tr>

                                                            <td class="{{ $allowLineItemDiscounts == true ? 'col-4 cw-33' : 'col-5 cw-40' }} copy-to-clipboard-hover">
                                                                {!! Form::hidden('invoice_id', $invoice->id) !!}
                                                                {!! Form::hidden('id', $item->id) !!}
                                                                {!! Form::hidden('item_lookup_id',null,['data-item-lookUp-id' => '']) !!}
                                                                {!! Form::hidden('data_custom_item_delete','yes') !!}
                                                                {!! itemLookUpsDropDown($item, 'item-lookup', 'max-width: 30%;') !!}
                                                                <i class="float-right p-2 fa fa-copy copy-icon-btn d-none"
                                                                   title="{{trans('fi.product_copy')}}"></i>
                                                                <label class="lbl_item_lookup" style="display: none;">
                                                                    <input type="checkbox" class="update_item_lookup"
                                                                           name="save_item_as_lookup" tabindex="999">
                                                                    {{ trans('fi.save_item_as_lookup') }}
                                                                </label>

                                                                {!! Form::textarea('description', $item->description, ['class' => 'description form-control form-control-sm mt-1', 'rows' => 3] ) !!}
                                                            </td>
                                                            <td class="col-1 cw-8">{!! Form::text('quantity', $item->formatted_quantity, ['class' => 'form-control form-control-sm quantity', 'data-field'=>'quantity']) !!}</td>
                                                            <td class="col-2 cw-16">{!! Form::text('price', $item->formatted_numeric_price, ['class' => 'form-control form-control-sm price', 'data-field'=>'price', 'data-value' => $item->price,'data-currency'=>$invoice->currency_code,'readonly' => $item->discount_type && $allowLineItemDiscounts == true ? true :false]) !!}</td>
                                                            @if($allowLineItemDiscounts == true)
                                                                <td class="col-2 cw-16">
                                                                    <div class="row">
                                                                        {!! Form::select('discount_type', $discountTypes, $item->discount_type, ['class' => 'form-control form-control-sm discount-type col-5 ml-1', 'data-field'=>'discount-type']) !!}
                                                                        {!! Form::text('discount', $item->formatted_numeric_discount, ['class' => $item->discount_type == ''? 'form-control form-control-sm discount col-6 ml-1 d-none' : 'form-control form-control-sm discount col-6 ml-1','data-field'=>'discount', 'placeholder' => trans('fi.discount-amount')]) !!}
                                                                    </div>
                                                                </td>
                                                            @endif
                                                            <td class="col-2 cw-8">
                                                                {!! Form::select('tax_rate_id', $taxRates, $item->tax_rate_id, ['class' => 'form-control form-control-sm']) !!}
                                                                @if(config('fi.numberOfTaxFields') == '2')
                                                                    {!! Form::select('tax_rate_2_id', $taxRates, $item->tax_rate_2_id, ['class' => 'form-control form-control-sm mt-1']) !!}
                                                                @endif
                                                            </td>
                                                            <td class="col-2 cw-16 item-subtotal text-center">{{ $item->amount->formatted_subtotal }}</td>
                                                        </tr>
                                                    </table>
                                                    @if ($invoiceItemCustomFields)
                                                        @include('custom_fields._custom_fields_unbound_item_invoice', ['object' => $item, 'customFields' => $invoiceItemCustomFields ,'key' => $key])
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-danger btn-delete-invoice-item {{$invoiceOverlayStatus ? 'disabled' : null}}"
                                                       href="javascript:void(0);" title="{{ trans('fi.delete') }}"
                                                       data-item-id="{{ $item->id }}">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer p-2  footer-btn-add-item" style="display: none;">
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-primary float-right" id="btn-add-item">
                                            <i class="fa fa-plus"></i> {{ trans('fi.add_item') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-12">
                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#tab-additional" class="nav-link  active"
                                           data-toggle="tab">{{ trans('fi.additional') }}</a>
                                    </li>
                                    @can('notes.view')
                                        <li>
                                            <a class="nav-link" href="#tab-notes" data-toggle="tab">
                                                {{ trans('fi.notes') }} {!! $invoice->notes->count() > 0 ? '<span class="badge badge-primary notes-count">'.$invoice->notes->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('attachments.view')
                                        <li class="nav-item">
                                            <a href="#tab-attachments" class="nav-link" data-toggle="tab">
                                                {{ trans('fi.attachments') }} {!! $invoice->attachments->count() > 0 ? '<span class="badge badge-primary attachment-count">'.$invoice->attachments->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                    @if($invoice->type == 'credit_memo')
                                        @can('invoices.view')
                                            <li class="nav-item">
                                                <a href="#tab-credit-applications" data-toggle="tab" class="nav-link">
                                                    {{ trans('fi.credit_applications') }}
                                                    {!! (($invoice->getCreditApplication()->count()) > 0) ? '<span class="badge badge-default credit-application-count">'.$invoice->getCreditApplication()->count().'</span>' : '' !!}
                                                </a>
                                            </li>
                                        @endcan
                                    @else
                                        @can('payments.view')
                                            <li class="nav-item">
                                                <a href="#tab-payments" class="nav-link" data-toggle="tab">
                                                    {{ trans('fi.payments') }} {!! $invoice->payments->count() > 0 ? '<span class="badge badge-default payment-count">'.$invoice->payments->count().'</span>' : '' !!}
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                    @if (config('commission_enabled'))
                                        @can('commission.view')
                                            <li class="nav-item">
                                                <a href="#tab-commission" class="nav-link" data-toggle="tab">
                                                    {{ trans('Commission::lang.commission') }} {!! $invoice->commissions->count() > 0 ? '<span class="badge badge-default commissions-count">'.$invoice->commissions->count().'</span>' : '' !!}
                                                </a>
                                            </li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">

                                    <div class="tab-pane active" id="tab-additional">
                                        <div class="{{$invoiceOverlayStatus ? 'paid-overlay p-2' : null }}">

                                            <div class="row {{ $invoiceOverlayStatus ? 'paid-overlay-true' : null }} ">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('fi.terms_and_conditions') }}</label>
                                                        {!! Form::textarea('terms', $invoice->terms, ['id' => 'terms', 'class' => 'form-control form-control-sm', 'rows' => 5 , $invoiceOverlayStatus ? 'disabled' : null]) !!}
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('fi.footer') }}</label>
                                                        {!! Form::textarea('footer', $invoice->footer, ['id' => 'footer', 'class' => 'form-control form-control-sm', 'rows' => 5 , $invoiceOverlayStatus ? 'disabled' : null]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($customFields)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @include('custom_fields._custom_fields_unbound', ['object' => $invoice])
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @can('notes.view')
                                        <div class="tab-pane" id="tab-notes">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('notes._js_timeline', ['object' => $invoice, 'model' => 'FI\Modules\Invoices\Models\Invoice', 'hideHeader' => true, 'showPrivateCheckbox' => 1, 'showPrivate' => 1])
                                                    <div id="note-timeline-container"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan

                                    @can('attachments.view')
                                        <div class="tab-pane" id="tab-attachments">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('attachments._table', ['object' => $invoice, 'model' => 'FI\Modules\Invoices\Models\Invoice', 'modelId' => $invoice->id])
                                                </div>
                                            </div>
                                        </div>
                                    @endcan

                                    @can('payments.view')
                                        @if($invoice->type == 'invoice')
                                            <div class="tab-pane" id="tab-payments">
                                                @include('invoices._payments', ['payments' => $invoice->payments, 'invoiceId' => $invoice->id])
                                            </div>
                                        @elseif($invoice->type == 'credit_memo')
                                            <div class="tab-pane" id="tab-credit-applications">
                                                @include('invoices._credit_applications', ['creditApplications' => $invoice->getCreditApplication(), 'creditMemoId' => $invoice->id])
                                            </div>
                                        @endif
                                    @endcan

                                    @if (config('commission_enabled'))
                                        @can('commission.view')
                                            @include('invoices._commission')
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2">

                <div id="div-totals">

                    @include('invoices._edit_totals')
                </div>

                <div class={{$invoice->payments->count()  != 0 ?"paid-overlay": null }} {{ $creditMemoOverlayStatus ? 'paid-overlay' : null }}>

                    <div class="card card-primary card-outline {{ $invoiceOverlayStatus? 'paid-overlay-true' : null }} {{ $creditMemoOverlayStatus ? 'paid-overlay-true' : null }}">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('fi.options') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($invoice->type != 'credit_memo')
                                <div class="form-group">
                                    <label class="document-options-label">{{ trans('fi.allow_online_pay_fees') }}
                                        : </label>
                                    {!! Form::select('online_payment_processing_fee', ['yes' => trans('fi.yes'), 'no' => trans('fi.no')], $invoice->online_payment_processing_fee, ['id' => 'online_payment_processing_fee', 'class' => 'form-control form-control-sm']) !!}
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="document-options-label">{{ trans('fi.invoice') }} #</label>
                                {!! Form::text('number', $invoice->number, ['id' => 'number', 'class' =>'form-control form-control-sm']) !!}
                            </div>

                            <div class="input-group date">
                                <label class="document-options-label">{{ trans('fi.date') }}</label>

                                <div class="input-group date document-options-input-group" id='invoice_date'
                                     data-target-input="nearest">
                                    {!! Form::text('invoice_date', $invoice->formatted_invoice_date, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#invoice_date']) !!}
                                    <div class="input-group-append"
                                         data-target='#invoice_date' data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>

                                </div>
                            </div>

                            <div class="input-group date">
                                <label class="document-options-label">{{ trans('fi.due_date') }}</label>

                                <div class="input-group date document-options-input-group" id='due_at'
                                     data-target-input="nearest">
                                    {!! Form::text('due_at', $invoice->formatted_due_at, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#due_at']) !!}
                                    <div class="input-group-append"
                                         data-target='#due_at' data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>

                                </div>
                            </div>

                            @if( $invoice->date_emailed != null)
                                <div class="input-group">
                                    <label class="document-options-label">{{ trans('fi.date_emailed') }}</label>

                                    <div class="input-group document-options-input-group">
                                        {{ $invoice->formatted_date_emailed }}
                                    </div>
                                </div>
                            @endif

                            @if( $invoice->date_mailed != null)
                                <div class="input-group">
                                    <label class="document-options-label">{{ trans('fi.date_mailed') }}</label>

                                    <div class="input-group document-options-input-group">
                                        {{ $invoice->formatted_date_mailed }}
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="document-options-label">{{ trans('fi.discount') }}</label>

                                <div class="input-group">
                                    {!! Form::text('discount', $invoice->formatted_numeric_discount, ['id' =>'discount', 'class' => 'form-control form-control-sm']) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="document-options-label">{{ trans('fi.currency') }}</label>
                                {!! Form::select('currency_code', $currencies, $invoice->currency_code, [ 'disabled' => (($invoice->amount->paid == 0) ? false : (config('fi.allowEditInvoiceStatus') != 'draft' ? (($invoice->status != 'draft') ? true : false) : false)), 'id' => 'currency_code', 'class' => (config('fi.baseCurrency') != $invoice->currency_code) ? 'bg-secondary form-control form-control-sm' :'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label class="document-options-label">{{ trans('fi.exchange_rate') }}</label>

                                <div class="input-group">
                                    {!! Form::text('exchange_rate', $invoice->exchange_rate, ['id' => 'exchange_rate', 'disabled' => (($invoice->amount->paid == 0) ? false : (config('fi.allowEditInvoiceStatus') != 'draft' ? (($invoice->status != 'draft') ? true : false) : false)),  'class' => (config('fi.baseCurrency') != $invoice->currency_code) ? 'bg-secondary form-control form-control-sm' :'form-control form-control-sm']) !!}
                                    <div class="input-group-append">
                                        <button
                                                {{(($invoice->amount->paid == 0) ? '' : (config('fi.allowEditInvoiceStatus') != 'draft' ? (($invoice->status != 'draft') ? 'disabled' : '') : ''))}}
                                                class="btn btn-sm btn-default input-group-text"
                                                id="btn-update-exchange-rate"
                                                type="button"
                                                data-toggle="tooltip" data-placement="left"
                                                title="{{ trans('fi.update_exchange_rate') }}"><i
                                                    class="fas fa-sync update-exchange"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="document-options-label">{{ trans('fi.status') }}</label>
                                {!! Form::select('status', $statuses, $invoice->status,
                                [ 'id' => 'status', 'class' => 'form-control form-control-sm document-options-input-group']) !!}
                            </div>

                            <div class="form-group">
                                <label class="document-options-label">{{ trans('fi.template') }}</label>
                                {!! Form::select('template', $templates, $invoice->template,
                                ['id' => 'template', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pull-right pb-2">
            <div class="btn-group">
                @if ($returnUrl)
                    <a href="{{ $returnUrl }}" class="btn btn-sm btn-default"><i
                                class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
                @endif
            </div>
            <div class="btn-group {{ $invoice->payments->count()  != 0 || $invoice->status !== 'draft' ? 'd-none' : null }} overlay-button">
                <button type="button" class="btn btn-sm btn-primary btn-save-invoice"
                        data-loading-text="{{ trans('fi.saving') }}"
                        data-original-text="{{ trans('fi.save') }}">
                    <i class="fa fa-save"></i> {{ trans('fi.save') }}
                </button>
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-left" role="menu">
                    <a href="#" class="btn-save-invoice dropdown-item" data-loading-text="{{ trans('fi.saving') }}"
                       data-original-text="{{ trans('fi.save_and_apply_exchange_rate') }}"
                       data-apply-exchange-rate="1">
                        {{ trans('fi.save_and_apply_exchange_rate') }}
                    </a>
                </div>
            </div>
        </div>
        {!!  Form::hidden('invoice_id', $invoice->id,['id' => 'invoice_id']) !!}
        {!!  Form::hidden('custom_module', 'invoice',['id' => 'custom_module']) !!}
        {!!  Form::hidden('custom_items_module', 'invoice_item',['id' => 'custom_items_module']) !!}
    </div>

</section>

<div id="modal-invoice-client-copy"></div>
<div id="modal-confirm-payment-invoices"></div>