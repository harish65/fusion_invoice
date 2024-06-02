@extends('client_center.layouts.logged_in')

@section('content')

    <section class="content-header">
        <h1>{{ trans('fi.payments') }}</h1>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12 col-sm-12">

                <div class="card card-primary card-outline">

                    <div class="card-body no-padding">
                        @include('client_center.payments._table')
                    </div>

                </div>

                <div class="pull-right">
                    {!! $payments->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop