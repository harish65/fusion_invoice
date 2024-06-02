@extends('layouts.master')
@section('javascript')
    @include('export._js_index')
@stop
@section('content')

    <section class="content-header">

        <div class="container-fluid">

            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('fi.export_data') }}</h1>
                </div>
                <div class="col-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>

        </div>
        
    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline card-outline-tabs">

                        <div class="card-header p-0 border-bottom-0">
                        
                            <ul class="nav nav-tabs" id="export-tabs">
                                <li class="active nav-item"><a data-toggle="tab" class="active nav-link" data-export-type="Clients" href="#tab-clients">{{ trans('fi.clients') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="Quotes" href="#tab-quotes">{{ trans('fi.quotes') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="QuoteItems" href="#tab-quote-items">{{ trans('fi.quote_items') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="Invoices" href="#tab-invoices">{{ trans('fi.invoices') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="InvoiceItems" href="#tab-invoice-items">{{ trans('fi.invoice_items') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="Payments" href="#tab-payments">{{ trans('fi.payments') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="Expenses" href="#tab-expenses">{{ trans('fi.expenses') }}</a></li>
                                <li class="nav-item"><a data-toggle="tab" class="nav-link" data-export-type="ItemLookups" href="#tab-item-lookups">{{ trans('fi.item_lookups') }}</a></li>
                            </ul>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div id="tab-pane" class="tab-pane">

                                    </div>
                                </div>
                            </div>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@stop