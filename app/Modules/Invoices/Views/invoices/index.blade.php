@extends('layouts.master')
<style>
    .basic-setting-scroller {
        list-style: none;
        padding: 0;
        max-width: 406px;
    }

    .basic-setting-scroller::-webkit-scrollbar {
        height: 7px;
        width: 6px;
        cursor: e-resize;
    }

    .basic-setting-scroller::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background-color: #fff;
        -webkit-box-shadow: inset 0 0 6px rgb(206 212 218);
    }

    .scroller-li {
        display: inline-grid;
        border: 1px solid #bdc1c5;
        border-radius: 4px;
    }

    .custom-invoice-padding {
        padding-top: 4px !important;
    }
</style>
@section('javascript')
    @include('invoices._js_index')
@stop

@section('content')

    <div id="modal-invoice-client-create"></div>
    <div id="modal-confirm-payment-invoices"></div>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="fa fa-file-invoice"></i> {{ trans('fi.invoices') }}</h1>
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
                            @can('invoices.update')
                                <a href="javascript:void(0);" class="btn btn-sm btn-default btn-action-modal"
                                   id="columns-filter-open">
                                    <i class="fas fa-sliders-h p-1" data-toggle="tooltip" data-placement="auto"
                                       title="{!! trans('fi.column_settings') !!}">
                                    </i>
                                </a>
                            @endcan
                        </li>
                    </ul>

                    <ul class="nav nav-pills nav-search pt-1">
                        @if (isset($searchPlaceholder))
                            <li class="nav-item mr-1">
                                <div class="input-group mb-1">
                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control form-control-sm inline','autofocus','placeholder' => $searchPlaceholder]) !!}
                                    <div class="input-group-append">
                                        <button type="submit" id="search-btn" class="btn btn-sm btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                            </li>
                        @endif
                    </ul>
                    <ul class="nav nav-pills  ml-auto">
                        <li class="nav-item mt-1 mb-1 mr-1">
                            @can('invoices.update')
                                <div class="btn-group bulk-actions">
                                    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('fi.change_status') }} <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu">
                                        @foreach ($bulkStatuses as $key => $status)
                                            <a href="javascript:void(0);"
                                               class="bulk-change-status btn-sm dropdown-item"
                                               data-status="{{ $key }}">{{ $status }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endcan

                            <div class="btn-group bulk-actions ">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('fi.action') }} <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    @can('invoices.view')
                                        <a href="javascript:void(0);" class="dropdown-item bulk-actions"
                                           id="btn-bulk-pdf"><i class="fa fa-file-pdf"></i> {{ trans('fi.pdf') }}</a>

                                        <a href="javascript:void(0);" class="dropdown-item bulk-actions"
                                           id="btn-bulk-print"><i class="fa fa-print"></i> {{ trans('fi.print') }}</a>
                                    @endcan
                                    @if(config('fi.allowInvoiceDelete') == 1)
                                        @can('invoices.delete')
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="text-danger dropdown-item bulk-actions"
                                               id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                            </a>
                                        @endcan
                                    @endif

                                </div>
                            </div>

                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::hidden('from_date', null, ['id' => 'invoice_from_date']) !!}
                                {!! Form::hidden('to_date', null, ['id' => 'invoice_to_date']) !!}
                                {!! Form::text('date_range', null, ['id' => 'invoice_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly' ,'placeholder'=>trans('fi.filter_by_date')]) !!}
                            </div>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::select('client', $clients, request('client'), ['id' => 'client', 'class' => 'form-control client-lookup form-control-sm scroller-setting', 'autocomplete' => 'off']) !!}
                            </div>
                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            <button type="button" class="btn btn-sm btn-default float-left scroller-setting"
                                    id="tags-filter-open" data-tags="{{ json_encode($tags) }}"
                                    data-match-all="{{ $tagsMustMatchAll }}">
                                <span id="tags-filter-count">({{ count($tags) }})</span> {{ trans('fi.tags') }}
                                <i class="fa fa-plus fa-xs"></i>
                                {!! Form::hidden('tags', json_encode($tags), ['id' => 'tags-filter']) !!}
                                {!! Form::hidden('tagsMustMatchAll', $tagsMustMatchAll, ['id' => 'tags-must-match-all']) !!}
                            </button>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'invoice_filter_options form-control form-control-sm inline scroller-setting','id'=>'scroller-setting-company_profile','data-name'=>'company_profile']) !!}
                            {!! Form::select('status', $filterStatuses, request('status'), ['class' => 'invoice_filter_options form-control form-control-sm inline scroller-setting','id'=>'scroller-setting-status','data-name'=>'status']) !!}
                        </li>

                        @can('invoices.create')
                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary create-invoice btn-action-modal">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
                            </li>
                        @endcan
                    </ul>

                    {!! Form::close() !!}
                </div>

                <div class="card-body table-responsive">
                    @include('invoices._table', ['bulk_action' => true])
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 mt-3">
                            @if(request('from_date') || request('to_date') || request('client') || request('company_profile') || (request('status') && request('status') != 'all') || (request('tags') && request('tags') != '[]') || request('search'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $invoices->total(),'plural' => $invoices->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-right mt-3">
                                {!! $invoices->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
