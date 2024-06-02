@extends('layouts.master')

@section('javascript')
    @include('recurring_invoices._js_index')
@stop

@section('content')

    <div id="modal-recurring-invoice-client-create"></div>

    <section class="content-header">

        <div class="container-fluid">

            <div class="row">
                <div class="col-6">
                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_recurring_invoices_about') !!}">
                        <i class="fa fa-sync pull-left"></i> {{ trans('fi.recurring_invoices') }}</h1>
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

            <div class="card card-primary card-outline ">

                <div class="card-header">
                    {!! Form::open(['method' => 'GET', 'id' => 'filter', 'class' => 'form-inline m-0']) !!}
                    <ul class="nav nav-pills align-self-sm-baseline">
                        <li class="nav-item mr-3 pt-1">
                            @can('recurring_invoices.update')
                                <a href="javascript:void(0);"class="btn btn-sm btn-default btn-action-modal"
                                   id="recurring-invoice-columns-setting">
                                   <i class="fas fa-sliders-h p-1" data-toggle="tooltip" data-placement="auto"
                                   title="{!! trans('fi.column_settings') !!}">
                                    </i>
                                </a>
                            @endcan
                        </li>    
                    </ul>                
                    <ul class="nav nav-pills">
                        <li class="nav-item mr-1">
                            @if (isset($searchPlaceholder))
                                <div class="input-group mt-1 mb-1">
                                    {!! Form::text('search', request('search'), ['id' => 'search','class' => 'h-auto form-control inline  align-baseline form-control-sm','autofocus','placeholder' => $searchPlaceholder]) !!}
                                    <div class="input-group-append">
                                        <button type="submit" id="search-btn" class="btn btn-sm btn-default "><i
                                                    class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::hidden('from_date', null, ['id' => 'recurring_invoice_from_date']) !!}
                                {!! Form::hidden('to_date', null, ['id' => 'recurring_invoice_to_date']) !!}
                                {!! Form::text('date_range', null, ['id' => 'recurring_invoice_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly','placeholder'=>trans('fi.filter_by_date')]) !!}
                            </div>
                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::select('client', $clients, request('client'), ['id' => 'client', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off']) !!}
                            </div>
                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">

                            {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'recurring_invoice_filter_options form-control inline form-control-sm']) !!}

                            {!! Form::select('status', $statuses, request('status'), ['class' => 'recurring_invoice_filter_options form-control inline form-control-sm']) !!}

                            <button type="button" class="btn btn-sm btn-default" id="tags-filter-open"
                                    data-tags="{{ json_encode($tags) }}" data-match-all="{{ $tagsMustMatchAll }}">

                                <span id="tags-filter-count">({{ count($tags) }})</span> {{ trans('fi.tags') }} <i
                                        class="fa fa-plus fa-xs"></i>
                                {!! Form::hidden('tags', json_encode($tags), ['id' => 'tags-filter']) !!}
                                {!! Form::hidden('tagsMustMatchAll', $tagsMustMatchAll, ['id' => 'tags-must-match-all']) !!}
                            </button>

                        </li>

                        @can('recurring_invoices.create')

                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary create-recurring-invoice btn-action-modal">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>
                            </li>

                        @endcan

                    </ul>

                    {!! Form::close() !!}
                </div>

                <div class="card-body no-padding">

                    @include('recurring_invoices._table')

                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-sm-12 col-md-5">

                            @if(request('from_date') || request('to_date') || request('client') || request('company_profile') || (request('status') && request('status') != 'all_statuses') || (request('tags') && request('tags') != '[]') || request('search'))

                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $recurringInvoices->total(),'plural' => $recurringInvoices->total() > 1 ? 's' : '']) }}

                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>

                            @endif

                        </div>

                        <div class="col-sm-12 col-md-7">
                            <div class="float-right mt-4">
                                {!! $recurringInvoices->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@stop
