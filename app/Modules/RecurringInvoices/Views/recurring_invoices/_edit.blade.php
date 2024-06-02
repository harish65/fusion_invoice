@include('recurring_invoices._js_edit')
@include('layouts._select2')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12 d-none d-sm-block">
                <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="d-inline">{{ trans('fi.recurring_invoice') }} #{{ $recurringInvoice->id }}</h1>
            </div>
            <div class="col-sm-6 pr-0">
                <div class="float-sm-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.other') }} <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-copy-recurring-invoice">
                                <i class="fa fa-copy"></i> {{ trans('fi.copy') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="btn-create-live-invoice"
                               data-id="{{$recurringInvoice->id}}">
                                <i class="fa fa-retweet" data-toggle="tooltip" data-placement="auto"
                                   title="{{ trans('fi.tt_ri_generate_the_next_live_invoice') }}"></i> {{ trans('fi.create_live_invoice') }}
                            </a>
                            @can('recurring_invoices.delete')
                                <div class="dropdown-divider"></div>
                                <a href="#" class="btn-delete-recurring-invoice text-danger dropdown-item">
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
                        <button type="button" class="btn btn-sm btn-primary btn-save-recurring-invoice"
                                data-loading-text="{{ trans('fi.saving') }}"
                                data-original-text="{{ trans('fi.save') }}"><i
                                    class="fa fa-save"></i> {{ trans('fi.save') }}</button>
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="btn-save-recurring-invoice dropdown-item"
                               data-loading-text="{{ trans('fi.saving') }}"
                               data-original-text="{{ trans('fi.save_and_apply_exchange_rate') }}"
                               data-apply-exchange-rate="1">
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

                <div class="card card-primary card-outline">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>
                                        <div class="card-header" style="padding: 0 0 5px 0">
                                            <h3 class="card-title">{{ trans('fi.summary') }}</h3>
                                        </div>
                                    </label>
                                    {!! Form::text('summary', $recurringInvoice->summary, ['id' => 'summary', 'class' => 'form-control form-control-sm']) !!}
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        <div class="card-header" style="padding: 0 0 5px 0">
                                            <h3 class="card-title">{{ trans('fi.tags') }}</h3>
                                        </div>
                                    </label>
                                    {!! Form::select('tags[]', $tags, $selectedTags, ['class' => 'form-control form-control-sm client-tags','multiple' => true, 'id' => 'invoice-tags', 'style' => 'width:100%']) !!}
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.card-body -->
                </div>

                <div class="row">

                    <div class="col-sm-6" id="col-from">

                        @include('recurring_invoices._edit_from')

                    </div>

                    <div class="col-sm-6" id="col-to">

                        @include('recurring_invoices._edit_to')

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-12 table-responsive" style="overflow-x: visible;">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">{{ trans('fi.items') }}</h3>

                                <div class="card-tools pull-right">
                                    <button class="btn btn-sm btn-primary" id="btn-add-item"><i
                                                class="fa fa-plus"></i> {{ trans('fi.add_item') }}</button>
                                </div>
                            </div>

                            <div class="card-body overflow-auto">
                                <table id="item-table" data-module-name="recurring_invoice"
                                       data-id="{{$recurringInvoice->id}}"
                                       class="table table-hover table-striped table-sm sortable-item table-borderless">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th class="col-5 cw-40">{{ trans('fi.product') }} / {{ trans('fi.description') }}</th>
                                        <th class="col-1 cw-8 text-center">{{ trans('fi.qty') }}</th>
                                        <th class="col-2 cw-16 text-center">{{ trans('fi.price') }}</th>
                                        <th class="col-2 cw-16 text-center">{{ trans('fi.tax_1') }}
                                            @if(config('fi.numberOfTaxFields') == '2')
                                                / {{ trans('fi.tax_2') }}
                                            @endif
                                        </th>
                                        <th class="col-2 text-center cw-16">{{ trans('fi.total') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach ($recurringInvoice->items as $key =>  $item)
                                        <tr class="item" id="tr-item-{{ $item->id }}">
                                            <td class="handle"><i class="fa fa-sort"></i></td>
                                            <td colspan="8" class="no-padding col-12">
                                                <table class="table main-table table-hover table-striped no-padding regular-fields mt-10 max-content-custom"
                                                       data-item-custom-id= {{$item->id}}>
                                                    <tr>
                                                        <td class="col-5 copy-to-clipboard-hover cw-40">
                                                            {!! Form::hidden('recurring_invoice_id', $recurringInvoice->id) !!}
                                                            {!! Form::hidden('id', $item->id) !!}
                                                            {!! Form::hidden('item_lookup_id',null) !!}
                                                            {!! Form::hidden('data_custom_item_delete','yes') !!}
                                                            {!! itemLookUpsDropDown($item, 'item-lookup') !!}
                                                            <i class="float-right p-2 fa fa-copy copy-icon-btn d-none" title="{{trans('fi.copy')}}"></i>
                                                            <label class="lbl_item_lookup" style="display: none;">
                                                                <input type="checkbox" class="update_item_lookup"
                                                                       name="save_item_as_lookup"
                                                                       tabindex="999"> {{ trans('fi.save_item_as_lookup') }}
                                                            </label>
                                                            {!! Form::textarea('description', $item->description, ['class' => 'description form-control form-control-sm mt-1', 'rows' => 3]) !!}
                                                        </td>
                                                        <td class="col-1 cw-8">{!! Form::text('quantity', $item->formatted_quantity, ['class' => 'form-control form-control-sm']) !!}</td>
                                                        <td class="col-2 cw-16">{!! Form::text('price', $item->formatted_numeric_price, ['class' => 'form-control form-control-sm']) !!}</td>
                                                        <td class="col-2 cw-16">
                                                            {!! Form::select('tax_rate_id', $taxRates, $item->tax_rate_id, ['class' => 'form-control form-control-sm']) !!}
                                                            @if(config('fi.numberOfTaxFields') == '2')
                                                                {!! Form::select('tax_rate_2_id', $taxRates, $item->tax_rate_2_id, ['class' => 'form-control form-control-sm']) !!}
                                                            @endif
                                                        </td>
                                                        <td class="col-2 cw-16 text-center">{{ $item->amount->formatted_subtotal }}</td>
                                                    </tr>
                                                </table>

                                                @if ($recurringInvoiceItemCustomFields)
                                                    @include('custom_fields._custom_fields_unbound_recurring_invoice_items', ['object' => $item, 'customFields' => $recurringInvoiceItemCustomFields , 'key' => $key])
                                                @endif
                                            </td>

                                            <td>
                                                <a class="btn btn-sm btn-danger btn-delete-recurring-invoice-item"
                                                   href="javascript:void(0);"
                                                   title="{{ trans('fi.delete') }}"
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
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a href="#tab-additional" class="nav-link  active"
                                               data-toggle="tab">{{ trans('fi.additional') }}</a>
                                        </li>
                                        @if (config('commission_enabled'))
                                            @can('commission.view')
                                                <li class="nav-item">
                                                    <a href="#tab-commission" class="nav-link"
                                                       data-toggle="tab">{{ trans('Commission::lang.commission') }}</a>
                                                </li>
                                            @endcan
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    <div class="tab-pane active" id="tab-additional">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ trans('fi.terms_and_conditions') }}</label>
                                                    {!! Form::textarea('terms', $recurringInvoice->terms, ['id' => 'terms', 'class' => 'form-control form-control-sm', 'rows' => 5]) !!}
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>{{ trans('fi.footer') }}</label>
                                                    {!! Form::textarea('footer', $recurringInvoice->footer, ['id' => 'footer', 'class' => 'form-control form-control-sm', 'rows' => 5]) !!}
                                                </div>
                                            </div>
                                        </div>

                                        @if ($customFields)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @include('custom_fields._custom_fields_unbound', ['object' => $recurringInvoice])
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if (config('commission_enabled'))
                                        @can('commission.view')
                                            @include('recurring_invoices._commission')
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
                    @include('recurring_invoices._edit_totals')
                </div>

                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">{{ trans('fi.options') }}</h3>
                    </div>

                    <div class="card-body">

                        <div class="input-group date">
                            <label>{{ trans('fi.next_date') }}</label>
                            <div class="input-group date" id='next_date' data-target-input="nearest">
                                {!! Form::text('next_date',$recurringInvoice->formatted_next_date, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#next_date']) !!}
                                <div class="input-group-append"
                                     data-target='#next_date' data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group">
                            <label>{{ trans('fi.every') }}</label>
                            {!! Form::select('recurring_frequency', array_combine(range(1, 90), range(1, 90)), $recurringInvoice->recurring_frequency, ['id' => 'recurring_frequency', 'class' => 'form-control form-control-sm']) !!}
                            {!! Form::select('recurring_period', $frequencies, $recurringInvoice->recurring_period, ['id' => 'recurring_period', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="input-group date">
                            <label>{{ trans('fi.stop_date') }}</label>
                            <div class="input-group date" id='stop_date' data-target-input="nearest">
                                {!! Form::text('stop_date', $recurringInvoice->formatted_stop_date, ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#stop_date']) !!}
                                <div class="input-group-append"
                                     data-target='#stop_date' data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group">
                            <label>{{ trans('fi.discount') }}</label>

                            <div class="input-group">
                                {!! Form::text('discount', $recurringInvoice->formatted_numeric_discount, ['id' =>
                                'discount', 'class' => 'form-control form-control-sm']) !!}
                                <div class="input-group-append">
                                    <div class="input-group-text"><i class="fa fa-percentage"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.currency') }}</label>
                            {!! Form::select('currency_code', $currencies, $recurringInvoice->currency_code, ['id' =>
                            'currency_code', 'class' => 'form-control form-control-sm', 'style' => config('fi.baseCurrency') != $recurringInvoice->currency_code ? 'background:#fff8dc' : '']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.exchange_rate') }}</label>

                            <div class="input-group">
                                {!! Form::text('exchange_rate', $recurringInvoice->exchange_rate, ['id' => 'exchange_rate', 'class' => 'form-control form-control-sm', 'style' => config('fi.baseCurrency') != $recurringInvoice->currency_code ? 'background:#fff8dc' : '']) !!}
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-default input-group-text"
                                            id="btn-update-exchange-rate"
                                            type="button"
                                            data-toggle="tooltip" data-placement="left"
                                            title="{{ trans('fi.update_exchange_rate') }}">
                                        <i class="fa fa-sync update-exchange"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.document_number_schemes') }}</label>
                            {!! Form::select('document_number_scheme_id', $documentNumberSchemes, $recurringInvoice->document_number_scheme_id, ['id' => 'document_number_scheme_id', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.template') }}</label>
                            {!! Form::select('template', $templates, $recurringInvoice->template, ['id' => 'template', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {!!  Form::hidden('recurring_invoice_id', $recurringInvoice->id,['id' => 'recurring_invoice_id']) !!}
        {!!  Form::hidden('custom_module', 'recurring_invoice',['id' => 'custom_module']) !!}
        {!!  Form::hidden('custom_items_module', 'recurring_invoice_item',['id' => 'custom_items_module']) !!}

    </div>

</section>
<div id="modal-recurring-invoice-client-copy"></div>