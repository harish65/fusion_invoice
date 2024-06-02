@extends('layouts.master')

@section('head')
    @include('layouts._datepicker')
@stop

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('#title').focus();
            $('#start_at').datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true});
            $('#due_at').datepicker({format: '{{ config('fi.datepickerFormat') }}', autoclose: true});
        });
    </script>
@stop

@section('content')

    @if ($editMode == true)
        {!! Form::model($task, ['route' => ['simpleTodo.update', $task->id]]) !!}
    @else
        {!! Form::open(['route' => 'simpleTodo.store']) !!}
    @endif

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('SimpleTodo::translations.task_form') }}
        </h1>
        <div class="pull-right">
            {!! Form::submit(trans('fi.save'), ['class' => 'btn btn-primary']) !!}
        </div>
        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-md-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <div class="form-group">
                            <label>{{ trans('SimpleTodo::translations.title') }}: </label>
                            {!! Form::text('title', null, ['id' => 'title', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.description') }}: </label>
                            {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.start_date') }}: </label>
                            {!! Form::text('start_at', (($editMode) ? $task->start_at : ''), ['id' => 'start_at', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.due_date') }}: </label>
                            {!! Form::text('due_at', (($editMode) ? $task->due_at : ''), ['id' => 'due_at', 'class' => 'form-control form-control-sm']) !!}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop