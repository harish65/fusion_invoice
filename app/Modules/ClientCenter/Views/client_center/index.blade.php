@extends('client_center.layouts.logged_in')

@section('content')

    <section class="content-header">
        <h1>{{ trans('fi.dashboard') }}</h1>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('fi.recent_quotes') }}</h3>
                    </div>
                    @if (count($quotes))
                        <div class="card-body no-padding">
                            @include('client_center.quotes._table')
                            <h4 style="text-align: center;"><a href="{{ route('clientCenter.quotes') }}" class="btn btn-sm btn-default">{{ trans('fi.view_all') }}</a></h4>
                        </div>
                    @else
                        <div class="card-body">
                            <p>{{ trans('fi.no_records_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('fi.recent_invoices') }}</h3>
                    </div>
                    @if (count($invoices))
                        <div class="card-body no-padding">
                            @include('client_center.invoices._table')
                            <h4 style="text-align: center;"><a href="{{ route('clientCenter.invoices') }}" class="btn btn-sm btn-default">{{ trans('fi.view_all') }}</a></h4>
                        </div>
                    @else
                        <div class="card-body">
                            <p>{{ trans('fi.no_records_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('fi.recent_payments') }}</h3>
                    </div>
                    @if (count($payments))
                        <div class="card-body no-padding">
                            @include('client_center.payments._table')
                            <h4 style="text-align: center;"><a href="{{ route('clientCenter.payments') }}" class="btn btn-sm btn-default">{{ trans('fi.view_all') }}</a></h4>
                        </div>
                    @else
                        <div class="card-body">
                            <p>{{ trans('fi.no_records_found') }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

@stop