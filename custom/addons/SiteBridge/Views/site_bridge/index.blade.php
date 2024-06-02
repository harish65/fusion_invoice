@extends('layouts.master')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>SiteBridge</h1>
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

                <div class="card-body no-padding">
                    {!! Form::open(['route' => 'siteBridge.import']) !!}
                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Database Name: </label>
                                <input type="text" name="sb_database" class="form-control form-control-sm">
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Username: </label>
                                <input type="text" name="sb_username" class="form-control form-control-sm">
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Password: </label>
                                <input type="password" name="sb_password" class="form-control form-control-sm">
                            </div>

                        </div>

                    </div>

                    <input type="submit" class="btn btn-sm btn-primary" value="Submit">
                    {!! Form::close() !!}
                </div>

            </div>

        </div>

    </section>

@stop