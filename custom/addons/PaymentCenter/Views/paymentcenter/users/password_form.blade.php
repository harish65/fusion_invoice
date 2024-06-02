@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#password').focus();
        });
    </script>

    {!! Form::open(['route' => ['paymentCenter.users.password.update', $user->id]]) !!}

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="pull-left d-inline">
                        {{ trans('PaymentCenter::lang.payment_center_reset_password') }}: {{ $user->name }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        <a href="{{ route('paymentCenter.users.index') }}" class="btn btn-sm btn-default">
                            {{ trans('fi.cancel') }}
                        </a>
                        <button class="btn btn-sm btn-primary">
                            <i class="fa fa-save"></i> {{ trans('fi.reset_password') }}
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

                            <div class="form-group">
                                <label>{{ trans('fi.password') }}</label>
                                {!! Form::password('password', ['id' => 'password', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.password_confirmation') }}</label>
                                {!! Form::password('password_confirmation', ['id' => 'password_confirmation', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}

@stop