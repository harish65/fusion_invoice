@extends('layouts.master')

@section('javascript')
    @include('payments._js_index')
@stop

@section('content')

    <div id="modal-confirm-payment-invoices"></div>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1 class="d-inline"><i class="fa fa-credit-card pull-left"> </i> {{ trans('fi.payments') }}</h1>
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
                    <ul class="nav nav-pills">
                        <li class="nav-item mr-1">
                            @if (isset($searchPlaceholder))
                                <div class="input-group mt-1 mb-1">
                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control form-control-sm inline','autofocus','placeholder' => $searchPlaceholder]) !!}
                                    <div class="input-group-append">
                                        <button type="submit" id="search-btn" class="btn btn-sm btn-default"><i
                                                    class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <ul class="nav nav-pills ml-auto">

                        @can('payments.delete')
                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger bulk-actions"
                                   id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                            </li>
                        @endcan


                        @can('payments.create')

                            <li class="nav-item mt-1 mb-1 mr-1">

                                <a href="javascript:void(0)" class="btn btn-sm btn-primary create-payment btn-action-modal">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>

                            </li>

                        @endcan
                    </ul>
                    {!! Form::close() !!}


                </div>
                <div class="card-body table-responsive no-padding">
                    @include('payments._table', ['bulk_action' => true])
                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-sm-12 col-md-5 mt-3">
                            @if(request('search'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $payments->total(),'plural' => $payments->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-right mt-3">
                                {!! $payments->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

@stop