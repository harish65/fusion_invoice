@include('quotes._js_edit')

<style>
    select.discount-type, select.discount-type option {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
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
            <div class="col-12 d-none d-sm-block">
                <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
            </div>
        </div>
        <div class="row mb-2">

            <div class="col-sm-6">

                <h1 class="d-inline">{{ trans('fi.quote') }} #{{ $quote->number }}</h1>

                @if ($quote->viewed)
                    <span class="badge badge-success">{{ trans('fi.viewed') }}</span>
                @endif
                @if($quote->status == 'approved')
                    <span class="badge badge-approved">{{ trans('fi.approved') }}</span>
                @endif
                @if($quote->status == 'canceled')
                    <span class="badge badge-canceled">{{ trans('fi.canceled') }}</span>
                @endif

                @if ($quote->invoice)
                    <span class="badge badge-info">
                        <a href="{{ route('invoices.edit', [$quote->invoice_id]) }}"
                           style="color: inherit;">{{ trans('fi.converted_to_invoice') }} {{ $quote->invoice->number }}</a>
                    </span>
                @endif

            </div>

            <div class="col-sm-6 pr-0">

                <div class="float-sm-right">

                    <div class="btn-group">

                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.action') }} <span class="caret"></span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a class="dropdown-item" href="{{ route('quotes.pdf', [$quote->id]) }}" target="_blank"
                               id="btn-pdf-quote">
                                <i class="fa fa-file-pdf"></i> {{ trans('fi.pdf') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);"
                               data-action="{{ route('quotes.save.pdf', [$quote->id]) }}"
                               id="btn-print-quote">
                                <i class="fa fa-print"></i> {{ trans('fi.print') }}
                            </a>

                            @if (config('fi.mailConfigured'))
                                @can('quotes.update')
                                    <a href="javascript:void(0)" id="btn-email-quote" class="email-quote dropdown-item"
                                       data-quote-id="{{ $quote->id }}"
                                       data-redirect-to="{{ route('quotes.edit', [$quote->id]) }}">
                                        <i class="fa fa-envelope"></i> {{ trans('fi.email') }}
                                    </a>
                                @endcan
                            @endif
                        </div>

                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.other') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-copy-quote">
                                <i class="fa fa-copy"></i> {{ trans('fi.copy') }}
                            </a>
                            @if(in_array($quote->status,['draft', 'sent', 'approved']) && !$quote->invoice)
                                <a class="dropdown-item" href="javascript:void(0)" id="btn-quote-to-invoice">
                                    <i class="fa fa-check"></i> {{ trans('fi.quote_to_invoice') }}
                                </a>
                            @endif
                            <a class="dropdown-item"
                               href="{{ route('clientCenter.public.quote.show', [$quote->url_key, $quote->token]) }}"
                               target="_blank">
                                <i class="fa fa-globe"></i> {{ trans('fi.public') }}
                            </a>

                            <div class="dropdown-divider"></div>
                            @can('quotes.delete')
                                <a class="btn-delete-quote text-danger dropdown-item" href="#">
                                    <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="btn-group">
                        @if ($returnUrl)
                            <a href="{{ $returnUrl }}" class="btn btn-sm btn-default"><i
                                        class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
                        @endif
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary btn-save-quote"
                                data-loading-text="{{ trans('fi.saving') }}"
                                data-original-text="{{ trans('fi.save') }}"><i
                                    class="fa fa-save"></i> {{ trans('fi.save') }}</button>
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="btn-save-quote dropdown-item" data-apply-exchange-rate="1"
                               data-loading-text="{{ trans('fi.saving') }}"
                               data-original-text="{{ trans('fi.save_and_apply_exchange_rate') }}">
                                {{ trans('fi.save_and_apply_exchange_rate') }}
                            </a>
                        </div>
                    </div>

                </div>

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

                <div class="row">

                    <div class="col-md-12">

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">{{ trans('fi.summary') }}</h3>
                            </div>
                            <div class="card-body">
                                {!! Form::text('summary', $quote->summary, ['id' => 'summary', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-6" id="col-from">

                        @include('quotes._edit_from')

                    </div>

                    <div class="col-sm-6" id="col-to">

                        @include('quotes._edit_to')

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-12 table-responsive" style="overflow-x: visible;">

                        <div class="card card-primary card-outline">

                            <div class="card-header">
                                <h3 class="card-title">{{ trans('fi.items') }}</h3>

                                <div class="card-tools">
                                    <button class="btn btn-sm btn-primary" id="btn-add-item"><i
                                                class="fa fa-plus"></i> {{ trans('fi.add_item') }}</button>
                                </div>

                            </div>

                            <div class="card-body overflow-auto">

                                <table id="item-table" data-module-name="quote" data-id="{{$quote->id}}"
                                       class="table table-hover table-borderless table-striped table-sm sortable-item">

                                    <thead>
                                    <tr>
                                        <th></th>

                                        <th class="{{ $allowLineItemDiscounts == true ? 'col-4 cw-33' : 'col-6 cw-50' }}">
                                            {{ trans('fi.product') }} / {{ trans('fi.description') }}
                                        </th>
                                        <th class="col-1 cw-8 text-center">{{ trans('fi.qty') }}</th>
                                        <th class="col-2 cw-16 text-center">{{ trans('fi.price') }}</th>

                                        @if($allowLineItemDiscounts == true)
                                            <th data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_line_item_discount') !!}" 
                                                class="col-2 cw-16 text-center">
                                                {{ trans('fi.discount') }}
                                            </th>
                                        @endif
                                        <th class="col-1 cw-8 text-center">
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

                                    @foreach ($quote->items as $key => $item)

                                        <tr class="item" id="tr-item-{{ $item->id }}">

                                            <td class="handle"><i class="fa fa-sort"></i></td>
                                            <td colspan="{{ $allowLineItemDiscounts == true ? 6 : 5 }}"
                                                class="no-padding col-12">

                                                <table class="table main-table table-hover table-borderless regular-fields mt-10 max-content-custom"
                                                       data-item-custom-id= {{$item->id}}>

                                                    <tr>
                                                        <td class="{{ $allowLineItemDiscounts == true ? 'col-4 cw-33' : 'col-6 cw-50' }} copy-to-clipboard-hover">                                                            
                                                            {!! Form::hidden('quote_id', $quote->id) !!}
                                                            {!! Form::hidden('id', $item->id) !!}
                                                            {!! Form::hidden('item_lookup_id',null,['data-item-lookUp-id' => '']) !!}
                                                            {!! Form::hidden('data_custom_item_delete','yes') !!}
                                                            {!! itemLookUpsDropDown($item, 'item-lookup') !!}
                                                            <i class="float-right p-2 fa fa-copy copy-icon-btn d-none"
                                                               title="{{trans('fi.copy')}}"></i>
                                                            <label class="lbl_item_lookup" style="display: none;">
                                                                <input type="checkbox" class="update_item_lookup"
                                                                       name="save_item_as_lookup"
                                                                       tabindex="999"> {{ trans('fi.save_item_as_lookup') }}
                                                            </label>
                                                            {!! Form::textarea('description', $item->description, ['class' => 'description form-control form-control-sm mt-1', 'rows' => 3]) !!}
                                                        </td>

                                                        <td class="col-1 cw-8">{!! Form::text('quantity', $item->formatted_quantity, ['class' => 'form-control form-control-sm quantity']) !!}</td>
                                                        <td class="col-2 cw-16">{!! Form::text('price', $item->formatted_numeric_price, ['class' => 'form-control form-control-sm price','data-value' => $item->price,'data-currency'=>$quote->currency_code,'readonly' => $item->discount_type && $allowLineItemDiscounts == true ? true :false]) !!}</td>

                                                        @if($allowLineItemDiscounts == true)
                                                            <td class="col-2 cw-16">
                                                                <div class="row">
                                                                    {!! Form::select('discount_type', $discountTypes, $item->discount_type, ['class' => 'form-control form-control-sm discount-type col-5 ml-1']) !!}
                                                                    {!! Form::text('discount', $item->discount, ['class' => $item->discount_type == ''? 'form-control form-control-sm discount col-6 ml-1 d-none' : 'form-control form-control-sm discount col-6 ml-1', 'placeholder' => trans('fi.discount-amount')]) !!}
                                                                </div>
                                                            </td>
                                                        @endif
                                                        <td class="col-1 cw-8">
                                                            {!! Form::select('tax_rate_id', $taxRates, $item->tax_rate_id, ['class' => 'form-control form-control-sm']) !!}
                                                            @if(config('fi.numberOfTaxFields') == '2')
                                                                {!! Form::select('tax_rate_2_id', $taxRates, $item->tax_rate_2_id, ['class' => 'form-control form-control-sm mt-1']) !!}
                                                            @endif
                                                        </td>
                                                        <td class="col-2 item-subtotal cw-16 text-center">{{ $item->amount->formatted_subtotal }}</td>
                                                    </tr>

                                                </table>

                                                @if ($quoteItemCustomFields)
                                                    @include('custom_fields._custom_fields_unbound_quote_items', ['object' => $item, 'customFields' => $quoteItemCustomFields, 'key' => $key])
                                                @endif

                                            </td>

                                            <td>
                                                <a class="btn btn-sm btn-danger btn-delete-quote-item"
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

                <div class="row">

                    <div class="col-12">

                        <div class="card card-primary card-outline card-outline-tabs">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a href="#tab-additional" class="nav-link  active"
                                           data-toggle="tab">{{ trans('fi.additional') }}</a>
                                    </li>
                                    @can('notes.view')
                                        <li class="nav-item">
                                            <a class="nav-link" href="#tab-notes"
                                               data-toggle="tab">{{ trans('fi.notes') }}
                                                {!! $quote->notes->count() > 0 ? '<span class="badge badge-primary notes-count">'.$quote->notes->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                    @can('attachments.view')
                                        <li class="nav-item"><a href="#tab-attachments" class="nav-link"
                                                                data-toggle="tab">
                                                {{ trans('fi.attachments') }} {!! $quote->attachments->count() > 0 ? '<span class="badge badge-primary attachment-count">'.$quote->attachments->count().'</span>' : '' !!}
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab-additional">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ trans('fi.terms_and_conditions') }}</label>
                                                    {!! Form::textarea('terms', $quote->terms, ['id' => 'terms', 'class' => 'form-control form-control-sm', 'rows' => 5]) !!}
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ trans('fi.footer') }}</label>
                                                    {!! Form::textarea('footer', $quote->footer, ['id' => 'footer', 'class' => 'form-control form-control-sm', 'rows' => 5]) !!}
                                                </div>
                                            </div>
                                        </div>

                                        @if ($customFields)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('custom_fields._custom_fields_unbound', ['object' => $quote])
                                                </div>
                                            </div>
                                        @endif

                                    </div>

                                    @can('notes.view')
                                        <div class="tab-pane" id="tab-notes">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('notes._js_timeline', ['object' => $quote, 'model' => 'FI\Modules\Quotes\Models\Quote', 'hideHeader' => true, 'showPrivateCheckbox' => 1, 'showPrivate' => 1])
                                                    <div id="note-timeline-container"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan

                                    @can('attachments.view')
                                        <div class="tab-pane" id="tab-attachments">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @include('attachments._table', ['object' => $quote, 'model' => 'FI\Modules\Quotes\Models\Quote', 'modelId' => $quote->id])
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-2">

                <div id="div-totals">
                    @include('quotes._edit_totals')
                </div>

                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">{{ trans('fi.options') }}</h3>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label>{{ trans('fi.quote') }} #</label>
                            {!! Form::text('number', $quote->number, ['id' => 'number', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="input-group date">
                            <label>{{ trans('fi.date') }}</label>
                            <div class="input-group date" id='quote_date' data-target-input="nearest">
                                {!! Form::text('quote_date', $quote->formatted_quote_date, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#quote_date']) !!}
                                <div class="input-group-append"
                                     data-target='#quote_date' data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>
                        </div>
                        <div class="input-group date">
                            <label>{{ trans('fi.expires') }}</label>
                            <div class="input-group date" id='expires_at' data-target-input="nearest">
                                {!! Form::text('expires_at', $quote->formatted_expires_at, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#expires_at']) !!}
                                <div class="input-group-append"
                                     data-target='#expires_at' data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.discount') }}</label>

                            <div class="input-group">
                                {!! Form::text('discount', $quote->formatted_numeric_discount, ['id' =>
                                'discount', 'class' => 'form-control form-control-sm']) !!}
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.currency') }}</label>
                            {!! Form::select('currency_code', $currencies, $quote->currency_code, ['id' =>
                            'currency_code', 'class' => 'form-control form-control-sm', 'style' => config('fi.baseCurrency') != $quote->currency_code ? 'background:#fff8dc' : '']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.exchange_rate') }}</label>

                            <div class="input-group">
                                {!! Form::text('exchange_rate', $quote->exchange_rate, ['id' =>
                                'exchange_rate', 'class' => 'form-control form-control-sm', 'style' => config('fi.baseCurrency') != $quote->currency_code ? 'background:#fff8dc' : '']) !!}
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-default input-group-text" id="btn-update-exchange-rate"
                                            type="button"
                                            data-toggle="tooltip" data-placement="left"
                                            title="{{ trans('fi.update_exchange_rate') }}"><i
                                                class="fa fa-sync update-exchange"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.status') }}</label>
                            {!! Form::select('status', $statuses, $quote->status,
                            ['id' => 'status', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.template') }}</label>
                            {!! Form::select('template', $templates, $quote->template,
                            ['id' => 'template', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="pull-right">
            <div class="btn-group">
                @if ($returnUrl)
                    <a href="{{ $returnUrl }}" class="btn btn-default">
                        <i class="fa fa-backward"></i> {{ trans('fi.back') }}
                    </a>
                @endif
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary btn-save-quote"
                        data-loading-text="{{ trans('fi.saving') }}"
                        data-original-text="{{ trans('fi.save') }}">
                    <i class="fa fa-save"></i> {{ trans('fi.save') }}
                </button>
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-left" role="menu">
                    <a href="#" class="btn-save-quote dropdown-item" data-apply-exchange-rate="1"
                       data-loading-text="{{ trans('fi.saving') }}"
                       data-original-text="{{ trans('fi.save_and_apply_exchange_rate') }}">
                        {{ trans('fi.save_and_apply_exchange_rate') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        {!!  Form::hidden('quote_id', $quote->id,['id' => 'quotes_id']) !!}
        {!!  Form::hidden('custom_module', 'quote',['id' => 'custom_module']) !!}
        {!!  Form::hidden('custom_items_module', 'quote_item',['id' => 'custom_items_module']) !!}

    </div>
</section>
<div id="modal-quote-client-copy"></div>
