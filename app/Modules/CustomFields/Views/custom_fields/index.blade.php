@extends('layouts.master')

@section('content')
    @include('layouts._formdata')
    <script type="text/javascript">
        $(function () {
            var fixHelper = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            };

            $(".custom-fields tbody").sortable({
                helper: fixHelper,
                update: function () {
                    var Lists = $(this).find('.order-id');
                    var reOrder = [];
                    var type = '';

                    $.each(Lists, function (key, value) {
                        reOrder.push($(value).val());
                        type = $(value).data('type')
                    });

                    var form_data = objectToFormData({ids: reOrder, type: type});
                    $.ajax({
                        url: '{{ route('customFields.reorder') }}',
                        method: 'post',
                        data: form_data,
                        processData: false,
                        contentType: false
                    }).done(function () {
                        alertify.success('{{ trans('fi.record_successfully_updated') }}', 5);
                    }).fail(function (response) {
                        $.each($.parseJSON(response.responseText).errors, function (id, message) {
                            alertify.error(message[0], 5);
                        });
                    });

                }
            });

            $('.delete-custom-field').click(function () {

                $(this).addClass('delete-custom-fields-active');

                $('#modal-placeholder').load('{!! route('customFields.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'custom-fields',
                        isReload: false,
                        returnURL: '{{route('quotes.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );

            });

            $('#btn-bulk-delete').click(function () {

                var ids = [];
                $('.custom-field-bulk-record:checked').each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length > 0) {

                    $('#modal-placeholder').load('{!! route('bulk.customFields.delete.modal') !!}', {
                            action: "{{ route('customFields.bulk.delete') }}",
                            modalName: 'custom-fields',
                            data: ids,
                            returnURL: "{!! urlencode(request()->fullUrl()) !!}"
                        },
                        function (response, status, xhr) {
                            if (status == "error") {
                                var response = JSON.parse(response);
                                alertify.error(response.message);
                            }
                        }
                    );
                }
            });


            $('.custom-field-bulk-select-all').click(function () {
                if ($(this).prop('checked')) {
                    $(this).closest('table').find('.custom-field-bulk-record').prop('checked', true);
                    if ($(this).closest('table').find('.custom-field-bulk-record:checked').length > 0) {
                        $('.bulk-actions').show();
                    }
                }
                else {
                    $(this).closest('table').find('.custom-field-bulk-record').prop('checked', false);
                    $('.bulk-actions').hide();
                }
            });

            $('.custom-field-bulk-record').click(function () {
                if (!$(this).prop('checked')) {
                    $(this).closest('table').find('.custom-field-bulk-select-all').prop('checked', false);
                } else {
                    var isAllChecked = 1;

                    $(this).closest('table').find('.custom-field-bulk-record').each(function () {
                        if (!this.checked)
                            isAllChecked = 0;
                    });

                    if (isAllChecked == 1) {
                        $(this).closest('table').find('.custom-field-bulk-select-all').prop('checked', true);
                    }
                }

                if ($(this).closest('table').find('.custom-field-bulk-record:checked').length > 0) {
                    $('.bulk-actions').show();
                }
                else {
                    $('.bulk-actions').hide();
                    $(this).closest('table').find('.custom-field-bulk-select-all').prop('checked', false);
                }
            });

            let customFieldsCreateUrl = '{{ route('customFields.create') }}';
            $('.nav-tabs>.nav-item').click(function () {
                let tableName = $(this).find('.nav-link').data('tableName');
                let customFieldCreateUrlWithTable = customFieldsCreateUrl + '?table=' + tableName;
                $('#btn-create-custom-field').attr('href', customFieldCreateUrlWithTable);
            });

            let selectedTab = '{!! '#nav-tab-' . $selectedTab !!}';
            $(selectedTab).trigger('click');
        });
    </script>
    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_custom_fields_about') !!}">
                        {{ trans('fi.custom_fields') }}</h1>
                </div>

                <div class="col-sm-6">

                    <div class="text-right">

                        <a href="javascript:void(0)" class="btn btn-sm btn-danger bulk-actions" id="btn-bulk-delete"><i
                                    class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                        <a href="{{ route('customFields.create') }}" class="btn btn-sm btn-primary"
                           id="btn-create-custom-field"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

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

                    <div class="card card-primary card-outline card-outline-tabs">

                        <div class="card-header p-0 border-bottom-0">

                            <ul class="nav nav-tabs">
                                <li class="nav-item"><a id="nav-tab-clients" class="active nav-link" data-toggle="tab" href="#tab-clients" data-table-name="clients">{{ trans('fi.clients') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-company_profiles" class="nav-link" data-toggle="tab" href="#tab-company-profiles" data-table-name="company_profiles">{{ trans('fi.company_profiles') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-expenses" class="nav-link" data-toggle="tab" href="#tab-expenses" data-table-name="expenses">{{ trans('fi.expenses') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-invoices" class="nav-link" data-toggle="tab" href="#tab-invoices" data-table-name="invoices">{{ trans('fi.invoices') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-invoice_items" class="nav-link" data-toggle="tab" href="#tab-invoice-items" data-table-name="invoice_items">{{ trans('fi.invoice_items') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-quotes" class="nav-link" data-toggle="tab" href="#tab-quotes" data-table-name="quotes">{{ trans('fi.quotes') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-quote_items" class="nav-link" data-toggle="tab" href="#tab-quote-items" data-table-name="quote_items">{{ trans('fi.quote_items') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-recurring_invoices" class="nav-link" data-toggle="tab" href="#tab-recurring-invoices" data-table-name="recurring_invoices">{{ trans('fi.recurring_invoices') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-recurring-invoice_items" class="nav-link" data-toggle="tab" href="#tab-recurring-invoice-items" data-table-name="recurring_invoice_items">{{ trans('fi.recurring_invoice_items') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-payments" class="nav-link" data-toggle="tab" href="#tab-payments" data-table-name="payments">{{ trans('fi.payments') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-item_lookups" class="nav-link" data-toggle="tab" href="#tab-item-lookups" data-table-name="item_lookups">{{ trans('fi.item_lookups') }}</a></li>
                                <li class="nav-item"><a id="nav-tab-users" class="nav-link" data-toggle="tab" href="#tab-users" data-table-name="users">{{ trans('fi.users') }}</a></li>
                            </ul>

                        </div>
                        <div class="card-body">

                            <div class="tab-content">

                                <div id="tab-clients" class="tab-pane active">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive no-padding ">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">
                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'clients']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'clients']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'clients']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'clients']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'clients')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="clients" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                    class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-company-profiles" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive no-padding ">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'company_profiles']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'company_profiles']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'company_profiles']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'company_profiles']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'company_profiles')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="company_profiles" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-expenses" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'expenses']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'expenses']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'expenses']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'expenses']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'expenses')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="expenses" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-invoices" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'invoices']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'invoices']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'invoices']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'invoices']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'invoices')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="invoices" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-invoice-items" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'invoice_items']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'invoice_items']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'invoice_items']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'invoice_items']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'invoice_items')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="invoice_items" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-quotes" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'quotes']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'quotes']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'quotes']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'quotes']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'quotes')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="quotes" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#"
                                                                                   data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-quote-items" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'quote_items']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'quote_items']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'quote_items']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'quote_items']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'quote_items')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="quote_items" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-recurring-invoices" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'recurring_invoices']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'recurring_invoices']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'recurring_invoices']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'recurring_invoices']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'recurring_invoices')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="recurring_invoices" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-recurring-invoice-items" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'recurring_invoice_items']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'recurring_invoice_items']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'recurring_invoice_items']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'recurring_invoice_items']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'recurring_invoice_items')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="recurring_invoice_items" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-payments" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'payments']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'payments']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'payments']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'payments']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'payments')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="payments" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-item-lookups" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'item_lookups']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'item_lookups']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'item_lookups']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'item_lookups']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'item_lookups')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="item_lookups" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="tab-users" class="tab-pane">

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="card card-primary card-outline">

                                                <div class="card-body table-responsive  no-padding">
                                                    <table class="table table-hover table-striped table-sm text-nowrap custom-fields">

                                                        <thead>
                                                        <tr>
                                                            <th><div class="btn-group"><input type="checkbox" class="custom-field-bulk-select-all"></div></th>
                                                            <th class="display_order">{!! Sortable::link('display_order', trans('fi.display_order'), null, ['table' => 'users']) !!}</th>
                                                            <th>{!! trans('fi.table_name') !!}</th>
                                                            <th>{!! Sortable::link('column_name', trans('fi.column_name'), null, ['table' => 'users']) !!}</th>
                                                            <th>{!! Sortable::link('field_label', trans('fi.field_label'), null, ['table' => 'users']) !!}</th>
                                                            <th>{!! Sortable::link('field_type', trans('fi.field_type'), null, ['table' => 'users']) !!}</th>
                                                            <th>{{ trans('fi.options') }}</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        @foreach ($customFields as $customField)
                                                            @if($customField->tbl_name == 'users')
                                                                <tr>
                                                                    <td><input type="checkbox" class="custom-field-bulk-record" data-id="{{ $customField->id }}"></td>
                                                                    <td>
                                                                        <i class="fa fa-sort"></i>
                                                                        <input type="hidden" value="{{ $customField->id }}" data-type="users" class="order-id">
                                                                    </td>
                                                                    <td>{{ $tableNames[$customField->tbl_name] }}</td>
                                                                    <td>{{ $customField->column_name }}</td>
                                                                    <td>{{ $customField->field_label }}</td>
                                                                    <td>{{ $customField->field_type }}</td>
                                                                    <td>
                                                                        <div class="btn-group action-menu">
                                                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                                                                {{ trans('fi.options') }} <span class="caret"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <a class="dropdown-item" href="{{ route('customFields.edit', [$customField->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                                                                <div class="dropdown-divider"></div>
                                                                                <a href="#" data-action="{{ route('customFields.delete', [$customField->id])}}"
                                                                                   class="delete-custom-field text-danger dropdown-item">
                                                                                    <i class="fa fa-trash"></i> {{trans('fi.delete') }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

@stop