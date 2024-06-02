@extends('layouts.master')
@section('javascript')
    @include('import._js_index')
@stop
@section('content')

    {!! Form::open(['route' => 'import.upload', 'files' => true]) !!}

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>{{ trans('fi.import_file_selection') }}</h1>

                </div>

                <div class="col-sm-6">

                    <div class="text-right">

                        @if (!config('app.demo'))
                            {!! Form::button('<i class="fa fa-forward"></i>' . '  ' . trans('fi.next'), ['type' => 'submit', 'class' => 'btn btn-sm btn-primary import_next_btn float-right d-none']) !!}
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ trans('fi.what_to_import') }}</label>
                                {!! Form::select('import_type', $importTypes, $importType, ['class' => 'form-control form-control-sm','id'=>'import_type']) !!}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.select_file_to_import') }}</label>
                                        @if (!config('app.demo'))
                                            <div>
                                                {!! Form::file('import_file',['class' => 'import_files']) !!}
                                            </div>
                                        @else
                                            Imports are disabled in the demo.
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <div class="">
                                        {{trans('fi.import_example_csv')}}:

                                        <a href="javascript:void(0)"
                                           data-href="{{ route("import.example", ['import_type' => 'clients']) }}"
                                           id="example_import_link">clients_import.csv</a>
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