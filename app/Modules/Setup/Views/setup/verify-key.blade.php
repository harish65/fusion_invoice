@extends('setup.master')
@section('javascript')
    @include('layouts._alertifyjs')
@stop
@section('content')

    <section class="content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12">

                    <div class="card card-primary card-outline">

                        <div class="card-header">
                            <h3 class="card-title">{{ trans('fi.verify_key') }}</h3>
                        </div>

                        <div class="card-body">
                            {!! Form::open(['route' => 'setup.postVerify.key', 'class' => 'form-install']) !!}
                            @include('layouts._alerts')

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    {!! Form::text('key', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.enter_key'), 'autocomplete' => 'off']) !!}
                                </div>
                            </div>

                            <button class="btn btn-sm btn-primary" type="submit">{{ trans('fi.verify') }}</button>

                            {!! Form::close() !!}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop