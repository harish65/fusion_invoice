@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

    @if ($editMode == true)
        {!! Form::model($currency, ['route' => ['currencies.update', $currency->id]]) !!}
    @else
        {!! Form::open(['route' => 'currencies.store']) !!}
    @endif

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1 class="pull-left d-inline">

                        {{ trans('fi.currency_form') }}

                    </h1>

                </div>

                <div class="col-sm-6">

                    <div class="text-right">

                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('fi.name') }}: </label>
                                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                                        <p class="help-block">{{ trans('fi.help_currency_name') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('fi.code') }}: </label>
                                        @if ($editMode and $currency->in_use)
                                            {!! Form::text('code', null, ['id' => 'code', 'class' => 'form-control form-control-sm',
                                            'readonly' => 'readonly']) !!}
                                        @else
                                            {!! Form::text('code', null, ['id' => 'code', 'class' => 'form-control form-control-sm'])
                                            !!}
                                        @endif

                                        <p class="help-block">{{ trans('fi.help_currency_code') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('fi.symbol') }}: </label>
                                        {!! Form::text('symbol', null, ['id' => 'symbol', 'class' => 'form-control form-control-sm']) !!}
                                        <p class="help-block">{{ trans('fi.help_currency_symbol') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('fi.symbol_placement') }}: </label>
                                        {!! Form::select('placement', ['before' => trans('fi.before_amount'), 'after'
                                        => trans('fi.after_amount')], null, ['class' => 'form-control form-control-sm']) !!}
                                        <p class="help-block">{{ trans('fi.help_currency_symbol_placement') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('fi.decimal_point') }}: </label>
                                        <select id="decimal" class="form-control form-control-sm" name="decimal">
                                            @foreach($separators as $key => $separator)
                                                <option value="{{$key}}" {{ isset($currency) && html_entity_decode($currency->decimal) == html_entity_decode($key) ? 'selected' : '' }}>{{ $separator }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('fi.thousands_separator') }}: </label>
                                        <select id="thousands" class="form-control form-control-sm" name="thousands">
                                            @foreach($separators as $key => $separator)
                                                <option value="{{$key}}" {{ isset($currency) && html_entity_decode($currency->thousands) == html_entity_decode($key) ? 'selected' : '' }}>{{ $separator }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input id="system_base_currency" class="custom-control-input" type="checkbox" value="1"
                                                   name="setting[baseCurrency]" {{ isset($currency) && $currency->system_base_currency == true ? 'checked' : '' }}>
                                            <label for="system_base_currency" class="custom-control-label">{{ trans('fi.system_base_currency') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop