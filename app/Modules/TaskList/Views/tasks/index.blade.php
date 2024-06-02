@extends('layouts.master')

@section('javascript')
    @include('tasks._js_index')
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1 class="d-inline"><i class="fa fa-tasks"> </i> {{ trans('fi.tasks') }}</h1>
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

                <div class="card-header">
                    {!! Form::open(['method' => 'GET', 'id' => 'filter', 'class' => 'form-inline m-0']) !!}

                    <ul class="nav nav-pills">
                        <li class="nav-item mr-1">
                            @if (isset($searchPlaceholder))
                                <div class="input-group mt-1 mb-1">
                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control form-control-sm inline','autofocus','placeholder' => $searchPlaceholder]) !!}
                                    <div class="input-group-append">
                                        <button type="submit" id="search-btn" class="btn btn-sm btn-default"><i
                                                    class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <ul class="nav nav-pills ml-auto">


                        @if($me['user_type'] === 'admin')
                            <li class="nav-item mt-1 mb-1 mr-1">
                                {!! Form::select('taskFilters', $taskFilters, request('taskFilters', 'open'),['class' => 'task-filters form-control form-control-sm']) !!}
                            </li>
                        @endif

                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('status', $statuses, request('status', 'open'),['class' => 'task-status form-control form-control-sm']) !!}
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::hidden('from_date', null, ['id' => 'task_from_date']) !!}
                            {!! Form::hidden('to_date', null, ['id' => 'task_to_date']) !!}
                            {!! Form::text('date_range', null, ['id' => 'task_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly' ,'placeholder'=>trans('fi.filter_by_date'),'autocomplete' => 'off']) !!}
                        </li>


                        <li class="nav-item mt-1 mb-1 mr-1">
                            <a href="{{ route('task.create') }}" class="btn btn-sm btn-primary"><i
                                        class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
                        </li>
                    </ul>
                    {!! Form::close() !!}

                </div>


                <div class="card-body table-responsive no-padding">

                    @include('tasks._table', ['bulk_action' => false])

                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-sm-12 col-md-5 mt-3">
                            @if(request('date_range') || (request('taskFilters') && request('taskFilters') != 'all_tasks') || (request('status') && request('status') != 'all') || request('search'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $tasks->total(),'plural' => $tasks->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link" id="btn-clear-filters">
                                    {{ trans('fi.clear') }}
                                </button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dynamic-pages float-right pagination-nav-css">
                                {!! $tasks->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

@stop

<style>
    .column-task-assigned-to-me { border-left: 3px solid #ffb6c1; }

    .custom-invoice-padding { padding-top: 4px !important; }
</style>