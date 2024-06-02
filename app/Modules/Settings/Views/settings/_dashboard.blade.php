@extends('layouts.master')

@section('javascript')
    @parent
    <script type="text/javascript">
        $(function () {

            $('#btn-submit').click(function () {
                $('#form-settings').submit();
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
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1 class="jc-left" data-toggle="tooltip" data-placement="auto"
                        title="{!! trans('fi.tt_dashboard_settings') !!}">
                        <i class="fas fa-tachometer-alt pr-3"></i>
                        {{ trans('fi.system_default_dashboard') }}
                    </h1>

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

            {!! Form::open(['route' => 'settings.system.default.dashboard.update', 'files' => true, 'id' => 'form-settings']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="mt-1 pt-2 pb-1">
                        <div class="form-group form-inline">
                            <div class="col-md-6 pl-0">
                            </div>
                            <div class="col-md-4">
                                <label class="fw-450 jc-right" data-toggle="tooltip" data-placement="auto"
                                       title="{{ trans('fi.tt_db_default_time_period') }}">
                                    <i class="fas fa-calendar-alt pr-2"></i>
                                    {{ trans('fi.dashboard_widgets_date_options') }}
                                </label>
                            </div>
                            <div class="col-md-2">
                                {!! Form::select('setting[dashboardWidgetsDateOption]', $dashboardWidgetsDateOptions,isset($settings['dashboardWidgetsDateOption']) ? $settings['dashboardWidgetsDateOption'] : 'this_month', ['id'=> 'dashboard-widgets-date-options','class' => 'form-control form-control-sm form-control-sm w-100']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5 offset-md-7 mb-4">
                            <div id="dashboard-widget-dates"
                                 style="display: {{ isset($settings['dashboardWidgetsDateOption']) && $settings['dashboardWidgetsDateOption'] == 'custom_date_range' || old('setting.dashboardWidgetsDateOption') == 'custom_date_range' ? 'block' : 'none' }};">
                                <div class="row jc-right">
                                    <div class="col-md-6">
                                        <div class="input-group date">
                                            <label>{{ trans('fi.from_date') }} (yyyy-mm-dd):</label>
                                            <div class="input-group date" id='dashboard-widgets-from-date-target'
                                                 data-target-input="nearest">
                                                {!! Form::text('setting[dashboardWidgetsFromDate]', isset($settings['dashboardWidgetsFromDate']) ? $settings['dashboardWidgetsFromDate'] : '', ['class' => 'form-control form-control-sm form-control-sm','data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#dashboard-widgets-from-date-target', 'id' => 'dashboard-widgets-from-date']) !!}
                                                <div class="input-group-append"
                                                     data-target='#dashboard-widgets-from-date-target'
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group date">
                                            <label>{{ trans('fi.to_date') }} (yyyy-mm-dd):</label>
                                            <div class="input-group date" id='dashboard-widgets-to-date-target'
                                                 data-target-input="nearest">
                                                {!! Form::text('setting[dashboardWidgetsToDate]', isset($settings['dashboardWidgetsToDate']) ? $settings['dashboardWidgetsToDate'] : '', ['class' => 'form-control form-control-sm form-control-sm','data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#dashboard-widgets-to-date-target', 'id' => 'dashboard-widgets-to-date']) !!}
                                                <div class="input-group-append"
                                                     data-target='#dashboard-widgets-to-date-target'
                                                     data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($dashboardWidgets as $widget)

                    @section('translateWidgetNamesSection')
                        @switch(strtolower($widget))
                            @case('clientactivity')
                            {{ $widgetIcon = '<i class="fa fa-child pr-2"></i>' }}
                            {{ $widgetHdr = trans('fi.recent_client_activity') }}
                            {{ $widgetTooltip = trans('fi.tt_db_recent_client_activity') }}
                            @break
                            @case('tasks')
                            {{ $widgetIcon = '<i class="fa fa-list pr-2"></i>' }}
                            {{ $widgetHdr = trans('fi.task_list') }}
                            {{ $widgetTooltip = trans('fi.tt_db_task_list') }}
                            @break
                            @case('clienttimeline')
                            {{ $widgetIcon = '<i class="fa fa-list pr-2"></i>' }}
                            {{ $widgetHdr = trans('fi.client_timeline') }}
                            {{ $widgetTooltip = trans('fi.tt_db_timeline') }}
                            @break
                            @case('saleschart')
                            {{ $widgetIcon = '<i class="fas fa-chart-line pr-2"></i>' }}
                            {{ $widgetHdr = trans('fi.sales_chart') }}
                            {{ $widgetTooltip = trans('fi.tt_db_sales_chart') }}
                            @break
                            @case('kpicards')
                            {{ $widgetIcon = '<i class="fa fa-briefcase pr-2"></i>' }}
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
                    <script>
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

                    </script>
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
                                                {!! Form::checkbox('setting[dashboard'.$kpiCardsSetting.']', 1,isset($settings['dashboard'.$kpiCardsSetting]) ? $settings['dashboard'.$kpiCardsSetting] : 0 , ['class'=>'dashboard-column-chk','id' => 'dashboard_'.$key]) !!}
                                                <label for='{{'dashboard_'.$key}}'>{{ trans('fi.'.$key) }} </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ trans('fi.enabled') }}: </label>
                                            {!! Form::select('setting[widgetEnabled' . $widget . ']', $yesNoArray,
                                        isset($settings['widgetEnabled' . $widget]) ? $settings['widgetEnabled' . $widget] : 0, ['id' => 'widgetEnabled' . $widget, 'class' => 'form-control form-control-sm form-control-sm']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label data-toggle="tooltip" data-placement="auto"
                                                   title="{{ trans('fi.tt_db_column_width') }}"> {{ trans('fi.column_width') }}
                                                : </label>
                                            {!! Form::select('setting[widgetColumnWidth' . $widget . ']', ['full_width'=>trans('fi.full_width'),'dynamic_width'=>trans('fi.dynamic_width')],
                                            isset($settings['widgetColumnWidth' . $widget]) ? $settings['widgetColumnWidth' . $widget] :  'dynamic_width',
                                            ['id' => 'widgetColumnWidth' . $widget, 'class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>
                                    @if (strtolower($widget) == 'tasks')
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label data-toggle="tooltip" data-placement="auto"
                                                       title="{{ trans('fi.tt_db_task_list_include_time') }}">{{ trans('fi.include_time_in_due_date') }}
                                                    : </label>
                                                {!! Form::select('setting[includeTimeInTaskDueDate]', $yesNoArray, isset($settings['includeTimeInTaskDueDate']) ? $settings['includeTimeInTaskDueDate'] : 0, ['class' => 'form-control form-control-sm']) !!}
                                            </div>
                                        </div>
                                    @endif

                                    @if(strtolower($widget) == 'saleschart')
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label data-toggle="tooltip" data-placement="auto"
                                                       title="{{ trans('fi.tt_db_accumulate_totals') }}">{{ trans('fi.accumulate_totals') }}
                                                    : </label>
                                                {!! Form::select('setting[accumulateTotals]', $yesNoArray, isset($settings['accumulateTotals']) ? $settings['accumulateTotals'] : 0, ['class' => 'form-control form-control-sm']) !!}
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
            {!! Form::close() !!}
        </div>
    </section>
@stop








