@extends('layouts.master')

<style> .is-dark {filter: invert(83%);}  .custom-invoice-padding{padding-top: 4px !important;}</style>

@section('javascript')

    @include('layouts._daterangepicker')

    <script type="text/javascript">
        $(function () {

            initDateRangePicker('timesheet');

            initDateRangePreSelected('timesheet');

            if (document.querySelectorAll('.dark-mode').length == 1) {
                const iframes = document.querySelectorAll('iframe');

                for (i = 0; i < iframes.length; i++) {
                    iframes[i].classList.toggle('is-dark');
                }
            }

            $('#btn-run-report').click(function () {

                var from_date = $('#timesheet_from_date').val();
                var to_date = $('#timesheet_to_date').val();
                var company_profile_id = $('#company_profile_id').val();
                var status = $('#status').val();

                $.post("{{ route('timeTracking.reports.timesheet.validate') }}", {
                    from_date: from_date,
                    to_date: to_date,
                    company_profile_id: company_profile_id,
                    status: status
                }).done(function () {
                    clearErrors();
                    $('#form-validation-placeholder').html('');
                    output_type = $("input[name=output_type]:checked").val();
                    query_string = "?from_date=" + from_date + "&to_date=" + to_date + "&company_profile_id=" + company_profile_id + "&status=" + status;
                    if (output_type == 'preview') {
                        $('#report-preview').show();
                        $('#preview-results').attr('src', "{{ route('timeTracking.reports.timesheet.html') }}" + query_string);
                    }
                    else if (output_type == 'pdf') {
                        window.location = "{{ route('timeTracking.reports.timesheet.pdf') }}" + query_string;
                    }
                    else if (output_type == 'csv') {
                        window.location = "{{ route('timeTracking.reports.timesheet.csv') }}" + query_string;
                    }

                }).fail(function (response) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                });
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="fa fa-bar-chart"> </i> {{ trans('TimeTracking::lang.timesheet') }}</h1>
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
                                <button class="btn btn-primary" id="btn-run-report">{{ trans('fi.run_report') }}</button>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.company_profile') }}:</label>
                                        {!! Form::select('company_profile_id', $companyProfiles, null, ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm'])  !!}
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
                                        {!! Form::hidden('from_date', null, ['id' => 'timesheet_from_date']) !!}
                                        {!! Form::hidden('to_date', null, ['id' => 'timesheet_to_date']) !!}
                                        {!! Form::text('date_range', null, ['id' => 'timesheet_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly','placeholder'=>trans('fi.filter_by_date')]) !!}
                                    </div>
                                </div>

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
                                    scrolling="no"
                                    onload="resizeIframe(this, 500);"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@stop