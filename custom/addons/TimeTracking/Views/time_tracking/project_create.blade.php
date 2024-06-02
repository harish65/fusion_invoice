@extends('layouts.master')

@section('javascript')
    @include('layouts._select2')
    @include('clients._js_lookup')
@stop

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

    {!! Form::open(['route' => 'timeTracking.projects.store']) !!}

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="pull-left d-inline">{{ trans('TimeTracking::lang.create_project') }}</h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
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
                                <label>* {{ trans('TimeTracking::lang.project_name') }}: </label>
                                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label>* {{ trans('fi.company_profile') }}:</label>
                                    {!! Form::select('company_profile_id', $companyProfiles, config('fi.defaultCompanyProfile'),
                                    ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                                </div>
                                <div class="col-md-4">
                                    <label>* {{ trans('fi.client') }}:</label>
                                    {!! Form::select('client_id', $clients, null, ['id' => 'client_name', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off', 'style'=>"width: 100%;"]) !!}
                                </div>
                                <div class="col-md-4">
                                    <label>* {{ trans('TimeTracking::lang.hourly_rate') }}:</label>
                                    {!! Form::text('hourly_rate', null, ['id' => 'hourly_rate', 'class' => 'form-control form-control-sm']) !!}
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