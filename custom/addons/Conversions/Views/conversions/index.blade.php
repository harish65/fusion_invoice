@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function() {

            $('#btn-convert').click(function() {
                $('#status-placeholder').html('');
                $.post("{{ route('conversions.create') }}", {
                    driver: $('#driver').val(),
                    hostname: $('#hostname').val(),
                    database: $('#database').val(),
                    username: $('#username').val(),
                    password: $('#password').val(),
                    prefix: $('#prefix').val(),
                }).done(function(response) {

                }).fail(function(response) {
                    if (response.status == 400) {
                        showErrors($.parseJSON(response.responseText).errors, '#status-placeholder');
                    } else {
                        alert('{{ trans('fi.unknown_error') }}');
                    }
                });
            });


            $('#btn-truncate-tables').click(function() {
                $.post("{{ route('conversions.truncateTables') }}");
            });

            $('#btn-compare-totals').click(function() {
                $('#status-placeholder').load("{{ route('conversions.compare') }}", {
                    driver: $('#driver').val(),
                    hostname: $('#hostname').val(),
                    database: $('#database').val(),
                    username: $('#username').val(),
                    password: $('#password').val(),
                    prefix: $('#prefix').val(),
                }).done(function(response) {

                }).fail(function(response) {
                    if (response.status == 400) {
                        showErrors($.parseJSON(response.responseText).errors, '#status-placeholder');
                    } else {
                        alert('{{ trans('fi.unknown_error') }}');
                    }
                });
            })

        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>Conversions</h1>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div id="status-placeholder"></div>

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body">

                        <p>
                            The intention of this add-on is to provide a simple way to bring your data into FusionInvoice
                            from the database of another invoicing system. The target database must be accessible through
                            the configuration options below.
                        </p>

                        <div class="form-group">
                            <label>Select the system to convert into FusionInvoice:</label>
                            {!! Form::select('driver', ['' => ''] + $drivers, null, ['class' => 'form-control form-control-sm', 'id' => 'driver']) !!}
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Database Server Hostname:</label>
                                    {!! Form::text('hostname', null, ['class' => 'form-control form-control-sm', 'id' => 'hostname']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Database Name:</label>
                                    {!! Form::text('database', null, ['class' => 'form-control form-control-sm', 'id' => 'database']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Database Username:</label>
                                    {!! Form::text('username', null, ['class' => 'form-control form-control-sm', 'id' => 'username']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Database Password:</label>
                                    {!! Form::password('password', ['class' => 'form-control form-control-sm', 'id' => 'password']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Table Prefix (if any):</label>
                                    {!! Form::text('prefix', null, ['class' => 'form-control form-control-sm', 'id' => 'prefix']) !!}
                                </div>
                            </div>
                        </div>

                        {!! Form::button('Convert', ['class' => 'btn btn-primary', 'id' => 'btn-convert']) !!}

                        {!! Form::button('Compare Totals', ['class' => 'btn btn-primary', 'id' => 'btn-compare-totals']) !!}

                        {!! Form::button('Truncate Tables', ['class' => 'btn btn-primary', 'id' => 'btn-truncate-tables']) !!}

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop