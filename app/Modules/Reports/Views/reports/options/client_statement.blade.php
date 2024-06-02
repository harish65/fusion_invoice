@extends('layouts.master')

<style> .is-dark {filter: invert(83%);}  .custom-invoice-padding{padding-top: 4px !important;}</style>

@section('javascript')

    @include('layouts._daterangepicker')
    @include('layouts._select2')

    <script type="text/javascript">
        $(function () {

            initDateRangePicker('client_statement');

            initDateRangePreSelected('client_statement');

            if (document.querySelectorAll('.dark-mode').length == 1) {
                const iframes = document.querySelectorAll('iframe');

                for (i = 0; i < iframes.length; i++) {
                    iframes[i].classList.toggle('is-dark');
                }
            }

            $('#invoice-tags').select2({tags: true, createTag: function(params) {
                    return null;
                },
                tokenSeparators: [",", " "]});
            // Define the select settings
            var settings = {
                placeholder: '{{ trans('fi.select_client') }}',
                allowClear: true,
                width: '100%',
            };

            // Make all existing items select
            $('.client-lookup').select2(settings);

            $(document).on('change', '.client-lookup', function () {
                if ($('.client-lookup').val() != null) {
                    var uri = window.location.toString();
                    if (uri.indexOf("?") > 0) {
                        var clean_uri = uri.substring(0, uri.indexOf("?"));
                        window.history.replaceState({}, document.title, clean_uri);
                        location.reload();
                    }
                }
            });

            $('#btn-run-report').click(function () {

                var $this = $(this);
                $this.html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + $this.data('loading-text')).attr("disabled", true);

                var from_date = $('#client_statement_from_date').val();
                var to_date = $('#client_statement_to_date').val();
                var client_id = $('#client_name').val();
                var company_profile_id = $('#company_profile_id').val();
                var invoice_status = $('#invoice-status').val();
                var invoice_tags = ($('#invoice-tags').val() && $('#invoice-tags').val().length) ? ($('#invoice-tags').val().toString()) : '';

                $.post("{{ route('reports.clientStatement.validate') }}", {
                    from_date: from_date,
                    to_date: to_date,
                    client_id: client_id,
                    company_profile_id: company_profile_id,
                    invoice_status: invoice_status,
                    invoice_tags: invoice_tags
                }).done(function () {
                    clearErrors();
                    $('#form-validation-placeholder').html('');
                    output_type = $("input[name=output_type]:checked").val();
                    query_string = "?from_date=" + from_date + "&to_date=" + to_date + "&client_id=" + client_id;
                    if (company_profile_id) {
                        query_string += ("&company_profile_id=" + company_profile_id);
                    }
                    if (invoice_status) {
                        query_string += ("&invoice_status=" + invoice_status);
                    }
                    if(invoice_tags)
                    {
                        query_string += ("&invoice_tags=" + invoice_tags);
                    }
                    if (output_type == 'preview') {
                        $('#report-preview').show();
                        $('#preview-results').attr('src', "{{ route('reports.clientStatement.html') }}" + query_string);
                    }
                    else if (output_type == 'pdf') {
                        window.open("{{ route('reports.clientStatement.pdf') }}" + query_string, '_blank');
                    }
                    else if (output_type == 'csv') {
                        window.open("{{ route('reports.clientStatement.csv') }}" + query_string, '_blank');
                    }
                    $this.html($this.data('original-text')).attr("disabled", false);
                }).fail(function (response) {
                    $this.html($this.data('original-text')).attr("disabled", false);
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
                    <h1><i class="fa fa-bar-chart"> </i> {{ trans('fi.client_statement') }}</h1>
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

                <div class="col-12">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('fi.options') }}</h3>

                            <div class="card-tools">
                                <button class="btn btn-sm btn-primary" data-loading-text="{{ trans('fi.preparing') }}"
                                        data-original-text="{{ trans('fi.run_report') }}"
                                        id="btn-run-report">{{ trans('fi.run_report') }}</button>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ trans('fi.company_profile') }}:</label>
                                        {!! Form::select('company_profile_id', $companyProfiles, null, ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm'])  !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ trans('fi.client') }}:</label>

                                    <div class="form-group">
                                        {!! Form::select('client_name', $clients, request('client'), ['id' => 'client_name', 'class' => 'form-control form-control-sm client-lookup', 'autocomplete' => 'off']) !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ trans('fi.status') }}:</label>

                                    <div class="form-group">
                                        {!! Form::select('status', $filterStatuses, request('status'), ['class' => 'invoice_filter_options form-control form-control-sm inline', 'id' => 'invoice-status']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>{{ trans('fi.invoice_tags') }}:</label>
                                    {!! Form::select('tags[]', $tags, null, ['class' => 'form-control form-control-sm client-tags','multiple' => true, 'id' => 'invoice-tags', 'style' => 'width:100%']) !!}
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.date_range') }}:</label>
                                        {!! Form::hidden('from_date', null, ['id' => 'client_statement_from_date']) !!}
                                        {!! Form::hidden('to_date', null, ['id' => 'client_statement_to_date']) !!}
                                        {!! Form::text('date_range', null, ['id' => 'client_statement_date_range', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly','placeholder'=>trans('fi.filter_by_date')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.output_type') }}:</label><br>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="output_type"
                                                   value="preview" checked="checked" id="preview">
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
                                    scrolling="yes"
                                    onload="resizeIframe(this, 500);"></iframe>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

@stop