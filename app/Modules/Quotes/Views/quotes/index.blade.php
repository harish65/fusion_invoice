@extends('layouts.master')

@section('javascript')
    @include('quotes._js_index')
@stop

@section('content')
    <div id="modal-quote-client-create"></div>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="fa fa-file-alt pull-left"></i> {{ trans('fi.quotes') }}</h1>
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

            <div class="card card-primary card-outline">
                <div class="card-header">
                    {!! Form::open(['method' => 'GET', 'id' => 'filter', 'class' => 'form-inline m-0']) !!}

                    <ul class="nav nav-pills align-self-sm-baseline">
                        <li class="nav-item mr-3 pt-1">
                            @can('quotes.update')
                                <a href="javascript:void(0);" class="btn btn-sm btn-default btn-action-modal"
                                id="quote-columns-setting">
                                    <i class="fas fa-sliders-h p-1" data-toggle="tooltip" data-placement="auto"
                                    title="{!! trans('fi.column_settings') !!}">
                                    </i>
                                </a>
                            @endcan
                        </li>    
                    </ul>     
                    
                    <ul class="nav nav-pills">
                        @if (isset($searchPlaceholder))
                            <li class="nav-item">
                                <div class="input-group mt-1 mb-1 mr-1">
                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control float-right form-control-sm','autofocus','placeholder' => $searchPlaceholder]) !!}
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-sm btn-default" id="search-btn">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endif

                    </ul>

                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mt-1 mb-1 mr-1">
                            @can('quotes.update')
                                <div class="btn-group bulk-actions">
                                    <button type="button" class="btn btn-sm btn-default btn-sm dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-expanded="false">
                                        {{ trans('fi.change_status') }} <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu">
                                        @foreach ($bulkStatuses as $key => $status)
                                            <a href="javascript:void(0)"
                                               class="btn-sm bulk-change-status dropdown-item"
                                               data-status="{{ $key }}">{{ $status }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endcan

                            <div class="btn-group bulk-actions">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('fi.action') }} <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    @can('quotes.view')
                                        <a href="javascript:void(0)" class="dropdown-item bulk-actions"
                                           id="btn-bulk-pdf"><i
                                                    class="fa fa-file-pdf"></i> {{ trans('fi.pdf') }}</a>
                                        <a href="javascript:void(0)" class="dropdown-item bulk-actions"
                                           id="btn-bulk-print"><i
                                                    class="fa fa-print"></i> {{ trans('fi.print') }}</a>
                                    @endcan

                                    @can('quotes.delete')
                                        <a href="javascript:void(0)" class="text-danger dropdown-item bulk-actions"
                                           id="btn-bulk-delete"><i
                                                    class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                                    @endcan
                                </div>
                            </div>

                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::hidden('from_date', null, ['id' => 'quote_from_date']) !!}
                                {!! Form::hidden('to_date', null, ['id' => 'quote_to_date']) !!}
                                {!! Form::text('date_range', request('date_range'), ['id' => 'quote_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly','placeholder'=>trans('fi.filter_by_date')]) !!}
                            </div>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::select('client', $clients, request('client'), ['id' => 'client', 'class' => 'form-control client-lookup form-control-sm', 'autocomplete' => 'off']) !!}
                            </div>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'quote_filter_options form-control inline form-control-sm']) !!}
                            {!! Form::select('status', $filterStatuses, request('status'), ['class' => 'quote_filter_options form-control inline form-control-sm']) !!}
                        </li>

                        <li class="nav-item mt-1 mb-1 ">
                            @can('quotes.create')
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary create-quote btn-action-modal">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>
                            @endcan
                        </li>
                    </ul>
                    {!! Form::close() !!}

                </div>

                <div class="card-body table-responsive">
                    @include('quotes._table', ['bulk_action' => true])
                </div>

                <div class="card-footer clearfix">
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            @if((request('from_date') || request('to_date') || request('company_profile') || request('client') || request('status') || request('search')) && request('status') != 'all')
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $quotes->total(),'plural' => $quotes->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-right">
                                {!! $quotes->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>

                </div>


            </div>

        </div>

    </section>

@stop
