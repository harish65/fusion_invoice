@extends('layouts.master')

@section('javascript')

    @include('layouts._colorpicker')
    @include('layouts._select2')

@stop

@section('content')
    <style>
        .user-settings-overlay {
            width: 100%;
            padding: 0px !important;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.07);
            z-index: 99999;
            border-radius: 5px;
        }
        .user-settings-overlay-true {
            pointer-events: none;
            padding-top: 7.5px !important;
            opacity: 0.5;
        }
    </style>
    <script type="text/javascript">
        $(function () {
            function userSettings(userId) {
                showHideLoaderModal();
                var url = '{{ route("settings.user.specific.dashboard.settings", ["id" => ":id"]) }}';
                url = url.replace(':id', userId);
                window.location.replace(url);
            }

            $('.user-settings').change(function (evt) {
                if (($('.user-settings').val()).length > 0) {
                    userSettings($('.user-settings').val());
                } else {
                    window.location.replace('{{route('settings.user.specific.dashboard.index')}}');
                }
            });

            $('#user-settings').select2({
                placeholder: '{{ trans('fi.select_user') }}',
                allowClear: true,
                dropdownAutoWidth: false,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $(document).ready(function () {
                $('#dashboard-select-all-columns').prop('checked', false);
                var isAllChecked = eachColumnSelectedOrNot();
                if (isAllChecked == 1) {
                    $('#dashboard-select-all-columns').prop('checked', true);
                } else {
                    $('#dashboard-select-all-columns').prop('checked', false);
                }
            });
            $('#dashboard-select-all-columns').click(function () {
                if ($(this).prop('checked')) {
                    $('.dashboard-column-chk').prop('checked', true);

                } else {
                    $('.dashboard-column-chk').prop('checked', false);
                }
            });
            $('.dashboard-column-chk').click(function () {

                $('#dashboard-select-all-columns').prop('checked', false);

                if ($(this).prop('checked')) {
                    var isAllChecked = eachColumnSelectedOrNot();
                    if (isAllChecked == 1) {
                        $('#dashboard-select-all-columns').prop('checked', true);
                    }
                } else {
                    $('#dashboard-select-all-columns').prop('checked', false);
                }
            });

            function eachColumnSelectedOrNot() {
                var isAllChecked = 1;

                $('.dashboard-column-chk').each(function () {
                    if (!this.checked)
                        isAllChecked = 0;
                });
                return isAllChecked;
            }

            $('.widgetColumnWidth').change(function (e) {
                if ($(this).data('user-id') != null) {
                    $.post('{{route('user.width.setting')}}', {
                        name: $(this).data('width-name'),
                        value: $(this).val(),
                        id: $(this).data('user-id')
                    });
                }
            })


            $('.user-settings-assign').click(function () {
                var parentUserId = $('.user-settings-assign').data('parent-id');
                $('#modal-placeholder').load('{!! route('get.users') !!}',
                    {
                        userParentId: parentUserId
                    });
            });

            $('.user-default-settings-set').click(function () {
                var url = "{{ route('users.default-setting',['id'=>':id']) }}";
                var id = $('.user-settings-assign').data('parent-id');
                url = url.replace(':id', id);

                $.get(url, function (responce) {
                    if (responce.success === true) {
                        alertify.success(responce.message);
                        setTimeout(
                            function () {
                                window.location.reload();
                            }, 1000);
                    }
                });
            });

            $("form").one('submit', function myFormSubmitCallback(evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $(this).serializeFormJSON();
                var customFields = {};
                var missingValues = {};
                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('users-field-name');
                    var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                    if (fieldName !== undefined) {
                        if ('file' === inputType) {
                            customFields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                            return true;
                        }
                        if ('select' === inputType) {
                            if ($(this).find('option:selected').length == 0) {
                                customFields[fieldName] = '';
                                return true;
                            }
                        }
                        if ('checkbox' === inputType) {
                            customFields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                            return true;
                        }
                        if ('radio' === inputType) {
                            if ($(this).prop('checked') == true) {
                                customFields[fieldName] = $(this).val();
                                selectCustomRadioButtonValue = $(this).val();
                            }
                            if ($(this).prop('checked') == false && selectCustomRadioButtonValue == null) {
                                customFields[fieldName] = 'null';
                            }
                            return customFields[fieldName];
                        }
                        customFields[$(this).data('users-field-name')] = $(this).val();
                    }
                });
                $.each(customFields, function (customKey, valueCustom) {
                    $.each(formAllData, function (formKey, formValue) {
                        var filter_column = formKey.substring(0, formKey.lastIndexOf("]") + 1);
                        var result = filter_column.startsWith("custom", 0);
                        if (result == true) {
                            if ('custom[' + customKey + ']' != filter_column) {
                                return missingValues['custom[' + customKey + ']'] = valueCustom;
                            }
                        } else {
                            return missingValues['custom[' + customKey + ']'] = valueCustom;
                        }
                    });
                });
                $.each(missingValues, function (key, value) {
                    var input = $("<input>").attr("type", "hidden").attr("class", "missingValue").attr("name", key).val(value);
                    $("form").append($(input));
                });
                $(this).submit();
            });
            $('#name').focus();
        });
    </script>

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 data-toggle="tooltip" data-placement="auto"
                        title="{!! trans('fi.tt_dashboard_settings') !!}">
                        <i class="fas fa-address-card pr-2"></i>
                        {{ trans('fi.user_specific_dashboards') }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="form-group  float-right">
                        {!! Form::select('user', $users, isset($user->id) ? $user->id:null, ['id' => 'user-settings', 'class' => 'form-control form-control-sm user-settings']) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if(isset($user))

        {!! Form::model($user, ['route' => ['settings.user.specific.dashboard.update', $user->id] , 'id' => 'userModelEditMode','enctype' => 'multipart/form-data']) !!}

        {!!  Form::hidden('user_id',$user->id,['id' => 'user_id']) !!}
        {!!  Form::hidden('custom_module', 'user',['id' => 'custom_module']) !!}
    @endif
    <section class="content content-settings {{ isset($user) ? '' :'user-settings-overlay' }}">
        <div class="container-fluid {{ isset($user) ? '' :'user-settings-overlay-true' }}">
            @include('layouts._alerts')
            @if(isset($user->id))
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="float-sm-right">
                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false"
                                        title="{!! trans('fi.tt_users_settings') !!}">
                                    {{trans('fi.action')}}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="javascript:void(0)"
                                       class="user-default-settings-set dropdown-item">
                                        {{ trans('fi.save_configuration_default') }}
                                    </a>

                                    <a href="javascript:void(0)"
                                       class="user-settings-assign dropdown-item"
                                       data-parent-id="{{isset($user->id) ? $user->id : null}}">
                                        {{ trans('fi.assign_configuration_other') }}
                                    </a>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12">

                    @foreach ($dashboardWidgets as $widget)

                    @section('translateWidgetNamesSection')
                        @switch(strtolower($widget))
                            @case('clientactivity')
                            {{ $widgetIcon = '<i class="fa fa-child"></i>' }}
                            {{ $widgetHdr = trans('fi.recent_client_activity') }}
                            {{ $widgetTooltip = trans('fi.tt_db_recent_client_activity') }}
                            @break
                            @case('tasks')
                            {{ $widgetIcon = '<i class="fa fa-list"></i>' }}
                            {{ $widgetHdr = trans('fi.task_list') }}
                            {{ $widgetTooltip = trans('fi.tt_db_task_list') }}
                            @break
                            @case('clienttimeline')
                            {{ $widgetIcon = '<i class="fa fa-list"></i>' }}
                            {{ $widgetHdr = trans('fi.client_timeline') }}
                            {{ $widgetTooltip = trans('fi.tt_db_timeline') }}
                            @break
                            @case('saleschart')
                            {{ $widgetIcon = '<i class="fas fa-chart-line"></i>' }}
                            {{ $widgetHdr = trans('fi.sales_chart') }}
                            {{ $widgetTooltip = trans('fi.tt_db_sales_chart') }}
                            @break
                            @case('kpicards')
                            {{ $widgetIcon = '<i class="fa fa-briefcase"></i>' }}
                            {{ $widgetHdr = trans('fi.kpi_cards') }}
                            {{ $widgetTooltip = trans('fi.tt_db_kpi_cards') }}
                            @break
                            @case('openinvoiceaging')
                            {{ $widgetIcon = ' <i class="far fa-chart-bar"></i>' }}
                            {{ $widgetHdr = trans('fi.open_invoice_aging') }}
                            {{ $widgetTooltip = trans('fi.tt_db_open_invoice_aging') }}
                            @break
                            @default
                            {{ $widgetIcon = '' }}
                            {{ $widgetHdr = $widget }}
                            {{ $widgetTooltip = '' }}
                        @endswitch
                    @endsection

                    <div class="card">
                        <div class="card-header">
                            @if (strtolower($widget) == 'kpicards')
                                <h3 class="card-title" data-toggle="tooltip" data-placement="auto"
                                    title="{{ $widgetTooltip }}"> {!! $widgetIcon !!} {{ $widgetHdr }}</h3>
                                <div class="form-group filter-column-item float-right m-0">
                                    <label class="m-0">
                                        {!! Form::checkbox('select_all_columns', null,null, ['class' => 'all-selected' ,'id' => 'dashboard-select-all-columns']) !!} {{trans('fi.check-all')}}
                                    </label>
                                </div>
                            @else
                                <h3 class="card-title" data-toggle="tooltip" data-placement="auto"
                                    title="{{ $widgetTooltip }}"> {!! $widgetIcon !!} {{ $widgetHdr }}</h3>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if (strtolower($widget) == 'kpicards')
                                    @foreach($kpiCardsSettings as $key => $kpiCardsSetting )
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {!! Form::checkbox('setting[dashboard'.$kpiCardsSetting.']', 1,isset($userSettings['dashboard'.$kpiCardsSetting]) ? $userSettings['dashboard'.$kpiCardsSetting] : 0 , ['class'=>'dashboard-column-chk','id' => 'dashboard_'.$key]) !!}
                                                <label for='{{'dashboard_'.$key}}'>{{ trans('fi.'.$key) }} </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ trans('fi.enabled') }}: </label>
                                            {!! Form::select('setting[widgetEnabled' . $widget . ']', $yesNoArray, isset($userSettings['widgetEnabled'.$widget]) ? $userSettings['widgetEnabled'.$widget] : 0, ['id' => 'widgetEnabled' . $widget, 'class' => 'form-control form-control-sm widgetEnabled']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ trans('fi.column_width') }}: </label>
                                            {!! Form::select('setting[widgetColumnWidth' . $widget . ']', ['dynamic_width'=>trans('fi.dynamic_width'),'full_width'=>trans('fi.full_width')], isset($userSettings['widgetColumnWidth'.$widget]) ? $userSettings['widgetColumnWidth'.$widget] : 'dynamic_width', ['id' => 'widgetColumnWidth' . $widget, 'class' => 'form-control form-control-sm widgetColumnWidth','data-width-name' => $widget , 'data-user-id' => isset($user->id) ? $user->id : null]) !!}
                                        </div>
                                    </div>
                                    @if (strtolower($widget) == 'tasks')
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>{{ trans('fi.include_time_in_due_date') }}
                                                    : </label>
                                                {!! Form::select('setting[includeTimeInTaskDueDate]', $yesNoArray, isset($userSettings['includeTimeInTaskDueDate']) ? $userSettings['includeTimeInTaskDueDate'] : 0, ['class' => 'form-control form-control-sm']) !!}
                                            </div>
                                        </div>
                                    @endif
                                    @if(strtolower($widget) == 'saleschart')
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>{{ trans('fi.accumulate_totals') }}: </label>
                                                {!! Form::select('setting[accumulateTotals]', $yesNoArray, isset($userSettings['accumulateTotals']) ? $userSettings['accumulateTotals'] : 0, ['class' => 'form-control form-control-sm']) !!}
                                            </div>
                                        </div>
                                    @endif

                                @endif

                            </div>
                        </div>
                    </div>

                    @if (view()->exists($widget . 'WidgetSettings'))
                        @include($widget . 'WidgetSettings')
                    @endif

                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {!! Form::close() !!}
@stop