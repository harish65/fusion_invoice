@extends('layouts.master')

@section('javascript')
    @parent
    <script type="text/javascript">
        $(function () {

            function secureLink() {
                if ($('#secure_link').is(':checked')) {
                    $('#secure_link_expire_day').removeAttr('disabled');
                } else {
                    $('#secure_link_expire_day').attr("disabled", true);
                }
            }

            secureLink();

            $('#secure_link').click(function () {
                secureLink();
            });

            $('#btn-submit').click(function () {
                $('#form-settings').submit();
            });

            $('#feePercentage').change(function () {
                if ($(this).val() == '' || $(this).val() == 0) {
                    $('#feePercentage').val(3);
                }
            });

            $('#setting-tabs a').click(function (e) {
                var tabId = $(e.target).attr("href").substr(1);
                $.post("{{ route('settings.saveTab') }}", {settingTabId: tabId});
            });

            $('#setting-tabs a[href="#{{ session('settingTabId') }}"]').tab('show');

            $('#btn-generate-timeline').click(function () {
                let $_this = $(this);
                $_this.addClass('delete-generate-timeline-active');
                $('#modal-placeholder').load('{!! route('settings.generate.timeline.modal') !!}', {
                        action: "{{ route('tasks.generate_timeline_history') }}",
                        modalName: 'generate-timeline',
                        returnURL: '{{route('settings.index')}}',
                        message: "{!! trans('fi.generating_timeline_confirm') !!}"
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $('#dashboard-widgets-from-date-target').datetimepicker({autoclose: true, format: dateFormat});

            $('#dashboard-widgets-to-date-target').datetimepicker({autoclose: true, format: dateFormat});

            $('#dashboard-widgets-date-options').click(function () {
                if ($(this).val() == 'custom_date_range') {
                    $('#dashboard-widget-dates').show();
                } else {
                    $('#dashboard-widget-dates').hide();
                }
            });

            $('#btn-update-key').click(function () {
                let $_this = $(this);
                $_this.attr('disabled', true);
                var key = $('#key').val();
                $.post('{{ route('settings.key.update') }}', {
                    key: key,
                }).done(function (response) {
                    if (response.success == true) {
                        alertify.success(response.message);
                        $('#update-key-modal').modal('toggle');
                        $('#license_key').val(key);
                    } else {
                        alertify.error(response.message);
                    }
                    $_this.attr('disabled', false);
                }).fail(function (xhr) {
                    let errors = JSON.parse(xhr.responseText).errors;
                    $.each(errors, function (name, data) {
                        alertify.error(data[0], 5);
                    });
                    $_this.attr('disabled', false);
                });
            });

        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1> {{ trans('fi.system_settings') }}</h1>

                </div>

                <div class="col-sm-6">

                    <div class="text-right">
                        <button class="btn btn-sm btn-primary" id="btn-submit">
                            <i class="fa fa-save"></i> {{ trans('fi.save') }}
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @include('layouts._alerts')

            {!! Form::open(['route' => 'settings.update', 'files' => true, 'id' => 'form-settings']) !!}

            <div class="row">
                <div class="col-md-12">

                    <div class="card card-primary card-outline card-outline-tabs">

                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="setting-tabs">
                                <li class="nav-item">
                                    <a data-toggle="tab" class="active nav-link"
                                       href="#tab-general">{{ trans('fi.general') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link"
                                       href="#tab-invoices">{{ trans('fi.invoices') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link"
                                       href="#tab-quotes">{{ trans('fi.quotes') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link" href="#tab-taxes">{{ trans('fi.taxes') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link" href="#tab-email">{{ trans('fi.email') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link" href="#tab-pdf">{{ trans('fi.pdf') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link"
                                       href="#tab-online-payments">{{ trans('fi.online_payments') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" class="nav-link"
                                       href="#tab-system">{{ trans('fi.system') }}</a>
                                </li>
                                @if(
                                    !config('fi.clientTransitionHistoryCreated')
                                    || !config('fi.expenseTransitionHistoryCreated')
                                    || !config('fi.invoiceTransitionHistoryCreated')
                                    || !config('fi.paymentInvoiceTransitionHistoryCreated')
                                    || !config('fi.paymentTransitionHistoryCreated')
                                    || !config('fi.quoteTransitionHistoryCreated')
                                    || !config('fi.noteTransitionHistoryCreated')
                                    || !config('fi.taskTransitionHistoryCreated')
                                )
                                    <li class="nav-item">
                                        <a data-toggle="tab" class="nav-link"
                                           href="#tab-transitions">{{ trans('fi.transitions') }}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div id="tab-general" class="tab-pane active">
                                    @include('settings._general')
                                </div>
                                <div id="tab-invoices" class="tab-pane">
                                    @include('settings._invoices')
                                </div>
                                <div id="tab-quotes" class="tab-pane">
                                    @include('settings._quotes')
                                </div>
                                <div id="tab-taxes" class="tab-pane">
                                    @include('settings._taxes')
                                </div>
                                <div id="tab-email" class="tab-pane">
                                    @include('settings._email')
                                </div>
                                <div id="tab-pdf" class="tab-pane">
                                    @include('settings._pdf')
                                </div>
                                <div id="tab-online-payments" class="tab-pane">
                                    @include('settings._online_payments')
                                </div>
                                <div id="tab-system" class="tab-pane">
                                    @include('settings._system')
                                </div>
                                <div id="tab-transitions" class="tab-pane">
                                    @include('settings._transition')
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            {!! Form::close() !!}
        </div>
    </section>

@stop