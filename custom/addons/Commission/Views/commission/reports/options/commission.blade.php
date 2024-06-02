@extends('layouts.master')

@section('javascript')

    @include('layouts._daterangepicker')

    <script type="text/javascript">
        $(function () {

            initDateRangePicker('commission');

            initDateRangePreSelected('commission');

            $('#btn-run-report').click(function () {

                var from_date = $('#commission_from_date').val();
                var to_date = $('#commission_to_date').val();
                var user = $('#user').val();
                var status = $('#status').val();
                var type = $('#type').val();
                var output_type = $("input[name=output_type]:checked").val();
                var query_string = "?from_date=" + from_date + "&to_date=" + to_date + "&user=" + user + "&status=" + status + "&type=" + type;
                if (output_type == 'preview') {
                    $('#report-preview').show();
                    $('#preview-results').attr('src', "{{ route('invoice.commission.reports.html') }}" + query_string);
                } else if (output_type == 'pdf') {
                    window.location = "{{ route('invoice.commission.reports.pdf') }}" + query_string;
                } else if (output_type == 'csv') {
                    window.location = "{{ route('invoice.commission.reports.csv') }}" + query_string;
                }

            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('Commission::lang.commission') }}</h1>
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

            <div id="form-validation-placeholder"></div>

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('fi.options') }}</h3>

                            <div class="card-tools">
                                <button class="btn btn-primary"
                                        id="btn-run-report">{{ trans('fi.run_report') }}</button>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.users') }}:</label>
                                        {!! Form::select('user_id', $users, null, ['id' => 'user', 'class' => 'form-control form-control-sm'])  !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.status') }}:</label>
                                        {!! Form::select('status', $statuses, null, ['id' => 'status', 'class' => 'form-control form-control-sm'])  !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.date_range') }}:</label>
                                        {!! Form::hidden('from_date', null, ['id' => 'commission_from_date']) !!}
                                        {!! Form::hidden('to_date', null, ['id' => 'commission_to_date']) !!}
                                        {!! Form::text('date_range', null, ['id' => 'commission_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly','placeholder'=>trans('fi.filter_by_date')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('Commission::lang.commission_type') }}:</label>
                                        {!! Form::select('type', $types, null, ['id' => 'type', 'class' => 'form-control form-control-sm'])  !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.output_type') }}:</label><br>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="output_type" value="preview" checked="checked" id="preview">
                                            <label class="form-check-label" for="preview">{{ trans('fi.preview') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="output_type" value="pdf" id="pdf">
                                            <label class="form-check-label" for="pdf">{{ trans('fi.pdf') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="output_type" value="csv" id="csv">
                                            <label class="form-check-label" for="csv">{{ trans('fi.csv') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <div class="row" id="report-preview" style="display: none;">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <iframe src="about:blank" id="preview-results" frameborder="0" style="width: 100%;"
                                    scrolling="no" onload="resizeIframe(this, 500);"></iframe>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

@stop
