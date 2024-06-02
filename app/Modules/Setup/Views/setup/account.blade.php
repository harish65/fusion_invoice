@extends('setup.master')
@section('javascript')
    @include('layouts._select2')
    @include('layouts._alertifyjs')
@stop
@section('content')

    <script type="text/javascript">
        $(function () {
            $('#country').select2({
                placeholder: "{{ trans('fi.select_country') }}"
            });
        })
    </script>

    <section class="content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-12">

                    <div class="card card-primary card-outline">

                        <div class="card-header">
                            <h3 class="card-title">{{ trans('fi.account_setup') }}</h3>
                        </div>

                        <div class="card-body">
                            {!! Form::open(['route' => 'setup.postAccount', 'class' => 'form-install']) !!}
                            @include('layouts._alerts')

                            <h4>{{ trans('fi.user_account') }}</h4>

                            <div class="row">

                                <div class="col-md-3 form-group">
                                    {!! Form::text('user[name]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.name')]) !!}
                                </div>

                                <div class="col-md-3 form-group">
                                    {!! Form::text('user[email]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.email')]) !!}
                                </div>

                                <div class="col-md-3 form-group">
                                    {!! Form::password('user[password]', ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.password')]) !!}
                                </div>

                                <div class="col-md-3 form-group">
                                    {!! Form::password('user[password_confirmation]', ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.password_confirmation')]) !!}
                                </div>

                            </div>

                            <h4>{{ trans('fi.company_profile') }}</h4>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    {!! Form::text('company_profile[company]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.company')]) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 form-group">
                                    {!! Form::textarea('company_profile[address]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.address'), 'rows' => 4]) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::text('company_profile[city]', null, ['id' => 'city', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.city')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::text('company_profile[state]', null, ['id' => 'state', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.state')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::text('company_profile[zip]', null, ['id' => 'zip', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.postal_code')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::select('company_profile[country]', $countries, null, ['id' => 'country', 'class' => 'form-control form-control-sm', 'placeholder' => trans('fi.country')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-3 form-group">
                                    {!! Form::text('company_profile[phone]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.phone')]) !!}
                                </div>

                                <div class="col-md-3 form-group">
                                    {!! Form::text('company_profile[mobile]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.mobile')]) !!}
                                </div>

                                <div class="col-md-3 form-group">
                                    {!! Form::text('company_profile[fax]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.fax')]) !!}
                                </div>

                                <div class="col-md-3 form-group">
                                    {!! Form::text('company_profile[web]', null, ['class' => 'form-control form-control-sm', 'placeholder' => trans('fi.web')]) !!}
                                </div>

                            </div>

                            <button class="btn btn-sm btn-primary" type="submit">{{ trans('fi.continue') }}</button>

                            {!! Form::close() !!}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop