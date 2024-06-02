@extends('layouts.master')

@section('javascript')

    @include('layouts._colorpicker')
    @include('layouts._select2')
    <script type="text/javascript">
        $(function () {

            $('#btn-delete-custom-img').click(function () {
                var url = "{{ route('users.deleteImage', [isset($user->id) ? $user->id : '','field_name' => '']) }}";
                $.post(url + '/' + $(this).data('field-name')).done(function () {
                    $('.custom_img').html('');
                });
            });

        });
    </script>
    @include('users._js_initials_colorpicker')

@stop

@section('content')
    @if ($editMode == true)
     {!! Form::model($user, ['route' => ['users.update', $user->id]]) !!}
   @else
     {!! Form::open(['route' => ['users.store']]) !!}
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
                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_users_client_about') !!}">
                        <i class="fa fa-user"></i> {{ trans('fi.client_center_user') }}
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.name') }}: </label>
                                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.email') }}: </label>
                                        {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.initials') }}: </label>
                                        {!! Form::text('initials', null, ['id' => 'initials', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans(('fi.initials_bg_color')) }}: </label>
                                        <div class="input-group fi-colorpicker colorpicker-element">
                                            {!! Form::text('initials_bg_color', null, ['class' => 'form-control form-control-sm initials-bg-color']) !!}
                                            <div class="input-group-append">
                                                <i></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!$editMode)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ trans('fi.password') }}: </label>
                                            {!! Form::password('password', ['id' => 'password', 'class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ trans('fi.password_confirmation') }}: </label>
                                            {!! Form::password('password_confirmation', ['id' => 'password_confirmation',
                                            'class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>
                                </div>
                                {!! Form::hidden('user_type', $userType) !!}
                            @endif

                        </div>

                    </div>

                    @if ($customFields)
                        <div class="card card-primary card-outline">

                            <div class="card-header">
                                <h3 class="card-title">{{ trans('fi.custom_fields') }}</h3>
                            </div>

                            <div class="card-body">

                                @include('custom_fields._custom_fields_unbound', ['object' => isset($user) ? $user : []])

                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </section>
    {!! Form::close() !!}

@stop