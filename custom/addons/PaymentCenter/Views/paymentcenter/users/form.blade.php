@extends('layouts.master')

@section('content')
    @include('layouts._colorpicker')
    <script type="text/javascript">
        $(function () {
            $('#name').focus();
            $('.fi-colorpicker').colorpicker();
            $('.fi-colorpicker').on('colorpickerChange', function (event) {
                $('.colorpicker-element .fa-square').css('color', event.color.toString());
            });
        });
    </script>

    @if ($editMode == true)
        {!! Form::model($user, ['route' => ['paymentCenter.users.update', $user->id]]) !!}
    @else
        {!! Form::open(['route' => 'paymentCenter.users.store']) !!}
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
                        {{ trans('PaymentCenter::lang.payment_center_user_form') }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        <a href="{{ route('paymentCenter.users.index') }}" class="btn btn-sm btn-default">
                            {{ trans('fi.cancel') }}
                        </a>
                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}
                        </button>
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
                                        <label>{{ trans('PaymentCenter::lang.name') }}</label>
                                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('PaymentCenter::lang.email') }}</label>
                                        {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ trans('fi.status') }}: </label>
                                        {!! Form::select('status', $status, null, ['id' => 'status', 'class' => 'form-control form-control-sm', isset($user->id) && auth()->user()->id == $user->id ? 'disabled' : '']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ trans(('fi.initials_bg_color')) }}: </label>

                                        <div class="input-group colorpicker-element">
                                            {!! Form::text('initials_bg_color', null, ['class' => 'form-control form-control-sm fi-colorpicker initials-bg-color', 'readonly' => true]) !!}
                                            <div class="input-group-append">
                                                                        <span class="input-group-text"><i
                                                                                    class="fas fa-square"
                                                                                    style="{{ isset($user->initials_bg_color) && $user->initials_bg_color != '' ? 'color:'.$user->initials_bg_color : '' }}"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @if (!$editMode)
                                <div class="form-group">
                                    <label>{{ trans('fi.password') }}</label>
                                    {!! Form::password('password', ['id' => 'password', 'class' => 'form-control form-control-sm']) !!}
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('fi.password_confirmation') }}</label>
                                    {!! Form::password('password_confirmation', ['id' => 'password_confirmation',
                                    'class' => 'form-control form-control-sm']) !!}
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}

@stop
