@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function (){
            var leftPopUp = true;
            var rightPopUp = true;

            $('.change-dashboard-widgets-options').click(function (){
                var option = $(this).data('id');

                $.post("{{ route('dashboard.updateWidgetSettings') }}", {
                    dashboardWidgetsDateOption:option,
                    dashboardWidgetsFromDate:$('#dashboard-widgets-from-date').children().val(),
                    dashboardWidgetsToDate:$('#dashboard-widgets-to-date').children().val()
                }, function (){
                    location.reload();
                });
            });

            $('#dashboard-widgets-from-date').datetimepicker({
                format:dateFormat,
                autoclose:true
            });
            $('#dashboard-widgets-to-date').datetimepicker({
                format:dateFormat,
                autoclose:true
            });

            $('.version-check-preference').click(function (){
                $.get("{{ route('dashboard.version.check.preference') }}", function (){
                    $('.version-info').hide();
                });
            });

            $('.dismiss-forever').click(function (){
                $.get("{{ route('dashboard.agreement.check.preference') }}", function (){
                    $('.agreement-info').hide();
                });
            });

            $('.centerSortable').sortable({
                placeholder:'sort-highlight',
                connectWith:[".centerSortable"],
                handle:'.card-header, .nav-tabs',
                forcePlaceholderSize:true,
                forceHelperSize:true,
                revert:'invalid',
                zIndex:999999,
                start:function (event, ui){
                    centerPopUp = false;
                },
                update:function (event, ui){
                    var $_child_widget = $(this).children('.widget-class');
                    var widget = ui.item.attr('data-widget');
                    var widgetPositions = [];
                    var form_data_center;
                    if ($_child_widget.length != 0) {
                        $_child_widget.each(function (index, e){
                            var $_widget = $(this).data('widget');
                            if ($_widget) {
                                widgetPositions[$_widget] = parseInt(index + 2);
                            }
                        });
                    }
                    widgetPositions['widgetPosition'] = 'center';
                    widgetPositions['widgetColumnPosition'] = 'full_width';
                    form_data_center = objectToFormData(widgetPositions);

                    $.ajax({
                        url:'{{ route('dashboard.widget.position') }}',
                        method:'post',
                        data:form_data_center,
                        processData:false,
                        contentType:false,
                        success:function (){
                            if (centerPopUp == true) {
                                alertify.success('{{trans('fi.position_change')}}');
                            }
                            centerPopUp = true;
                        },
                    }).fail(function (response){
                        $.each($.parseJSON(response.responseText).errors, function (id, message){
                            alertify.error(message[0], 5);
                        });
                    });
                }
            });

            $('.leftSortable').sortable({
                placeholder:'sort-highlight',
                connectWith:[".rightSortable", ".leftSortable"],
                handle:'.card-header, .nav-tabs',
                forcePlaceholderSize:true,
                forceHelperSize:true,
                revert:"valid",
                zIndex:999999,
                start:function (event, ui){
                    leftPopUp = false;
                },
                update:function (event, ui){
                    var $_child_widget = $(this).children('.widget-class');
                    var $_center_index = $('.centerSortable').children('.widget-class').length;
                    var widget = ui.item.attr('data-widget');
                    var widgetPositions = [];
                    var form_data_left;
                    if ($_child_widget.length != 0) {
                        $_child_widget.each(function (index, e){
                            var $_widget = $(this).data('widget');
                            if ($_widget) {
                                widgetPositions[$_widget] = (parseInt($_center_index) + parseInt(index + 2));
                            }
                        });
                    }
                    widgetPositions['widgetPosition'] = 'left';
                    widgetPositions['widgetColumnPosition'] = 'dynamic_width';
                    form_data_left = objectToFormData(widgetPositions);

                    $.ajax({
                        url:'{{ route('dashboard.widget.position') }}',
                        method:'post',
                        data:form_data_left,
                        processData:false,
                        contentType:false,
                        success:function (){
                            if (leftPopUp == true) {
                                alertify.success('{{trans('fi.position_change')}}');
                            }
                            leftPopUp = true;
                        },
                    }).fail(function (response){
                        $.each($.parseJSON(response.responseText).errors, function (id, message){
                            alertify.error(message[0], 5);
                        });
                    });
                }
            });

            $('.rightSortable').sortable({
                placeholder:'sort-highlight',
                connectWith:[".rightSortable", ".leftSortable"],
                handle:'.card-header, .nav-tabs',
                forceHelperSize:true,
                revert:"valid",
                forcePlaceholderSize:true,
                zIndex:999999,
                start:function (event, ui){
                    rightPopUp = false;
                },
                update:function (event, ui){
                    var $_child_widget = $(this).children('.widget-class');
                    var $_left_index = $('.leftSortable').children('.widget-class').length;
                    var $_center_index = $('.centerSortable').children('.widget-class').length;
                    var widget = ui.item.attr('data-widget');
                    var widgetPositions = [];
                    var form_data_right;
                    if ($_child_widget.length != 0) {
                        $_child_widget.each(function (index, e){
                            var $_widget = $(this).data('widget');
                            if ($_widget) {
                                widgetPositions[$_widget] = (parseInt($_left_index + $_center_index) + parseInt(index + 2));
                            }
                        });
                    }
                    widgetPositions['widgetPosition'] = 'right';
                    widgetPositions['widgetColumnPosition'] = 'dynamic_width';

                    form_data_right = objectToFormData(widgetPositions);

                    $.ajax({
                        url:'{{ route('dashboard.widget.position') }}',
                        method:'post',
                        data:form_data_right,
                        processData:false,
                        contentType:false,
                        success:function (){
                            if (rightPopUp == true) {
                                alertify.success('{{trans('fi.position_change')}}');
                            }
                            rightPopUp = true;
                        },
                    }).fail(function (response){
                        $.each($.parseJSON(response.responseText).errors, function (id, message){
                            alertify.error(message[0], 5);
                        });
                    });
                }
            });

            $('.centerSortable .card-header').css('cursor', 'move');
            $('.leftSortable .card-header').css('cursor', 'move');
            $('.rightSortable .card-header').css('cursor', 'move');
        });

    </script>
@stop

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header"></div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts._alerts')
            @if (Cookie::get('versionAlert') != null)
                <div class="alert alert-success alert-dismissible version-info">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="fas fa-bullhorn"></i> {{ trans('fi.new-version-available') }}</h5>
                    {{ Cookie::get('versionAlert') }}
                    <a href="https://www.fusioninvoice.com/docs/{{date('Y')}}/About-FusionInvoice/Release-Notes"
                       class="font-weight-dark ml-3" target="_blank">{{ trans('fi.view-release-notes') }}</a>
                    <a href="#"
                       class="font-weight-dark version-check-preference ml-3">{{ trans('fi.ignore-this-version') }}</a>
                </div>
            @endif
            @if (session()->has('agreementExpireAlert') && session('agreementExpireAlert') != null)
                <div class="alert alert-warning alert-dismissible agreement-info">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> {{ trans('fi.agreement-expire-alert') }}</h5>
                    {{ session('agreementExpireAlert') }}
                    <a href="https://www.fusioninvoice.com/account" class="font-weight-dark text-dark ml-3"
                       target="_blank">{{ trans('fi.renew-now') }}</a>
                    <a href="#" class="font-weight-dark text-dark dismiss-forever ml-3">{{ trans('fi.dismiss-forever') }}</a>
                </div>
            @endif

            @if (session()->has('agreementExpiredAlert') && session('agreementExpiredAlert') != null)
                <div class="alert alert-danger alert-dismissible agreement-info">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> {{ trans('fi.agreement-expired-alert') }}</h5>
                    {{ session('agreementExpiredAlert') }}
                    <a href="https://www.fusioninvoice.com/account" class="font-weight-dark text-dark ml-3"
                       target="_blank">{{ trans('fi.renew-now') }}</a>
                    <a href="#" class="font-weight-dark text-dark dismiss-forever ml-3">{{ trans('fi.dismiss-forever') }}</a>
                </div>
            @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 pull-right">
                        @can('allow_time_period_change.view')
                            <div class="form-group float-left">
                                <label class="pr-2" data-toggle="tooltip" data-placement="auto"
                                       title="{!! trans('fi.tt_dashboard_date_range') !!}">{{ trans('fi.dashboard_date_range') }}</label>
                            </div>
                            <div class="btn-group float-left">
                                <button type="button" class="btn  btn-default btn-sm btn-box-tool dropdown-toggle"
                                        data-toggle="dropdown">
                                    <i class="far fa-calendar-alt"></i> {{ $dashboardWidgetsDateOptions[config('fi.dashboardWidgetsDateOption')] }}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    @foreach ($dashboardWidgetsDateOptions as $key => $option)
                                        @if ($key != 'custom_date_range')
                                            <a href="#" onclick="return false;"
                                               class="dropdown-item change-dashboard-widgets-options"
                                               data-id="{{ $key }}">{{ $option }}</a>
                                        @else
                                            <a href="#" class="dropdown-item" onclick="return false;"
                                               data-toggle="modal"
                                               data-target="#dashboard-widgets-date-modal">{{ $option }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-12 col-md-12 pull-right">
                        <div class="btn-group font-weight-light pr-1 float-right">
                            @if(config('fi.dashboardWidgetsDateOption') == 'custom_date_range')
                                <small class="text-muted">{{$customDateRange}}</small>
                            @endif
                        </div>
                    </div>
                </div><!-- /.row -->
                <section class="content clearfix">
                    <div class="row">
                        @foreach ($widgets as $widget)
                            @if ($widget == 'KpiCards')
                                <div class="col-md-12 col-sm-12 widget-class" data-widget="{{$widget}}">
                                    @include($widget . 'Widget',[$widget])
                                </div>
                            @endif
                        @endforeach

                        <div class="col-lg-12 p-0 centerSortable" data-position-side="center">
                            @if($widgetPositionCenter != null)
                                @foreach ($widgets as $widget)
                                    @if(in_array($widget,$widgetPositionCenter))
                                        @if (config('fi.widgetEnabled' . $widget))
                                            <div class="col-md-12  col-sm-12 widget-class" data-widget="{{$widget}}">
                                                @include($widget . 'Widget',[$widget])
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="col-lg-7 p-0 leftSortable" data-position-side="left">
                            @if($widgetPositionLeft != null)
                                @foreach ($widgets as $widget)
                                    @if(in_array($widget,$widgetPositionLeft))
                                        @if (config('fi.widgetEnabled' . $widget) )
                                            <div class="col-md-12 col-sm-12 widget-class" data-widget="{{$widget}}">
                                                @include($widget . 'Widget',[$widget])
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="col-lg-5 p-0 rightSortable" data-position-side="right">

                            @if($widgetPositionRight != null)
                                @foreach ($widgets as $widget)
                                    @if(in_array($widget,$widgetPositionRight))
                                        @if (config('fi.widgetEnabled' . $widget))
                                            <div class="col-md-12  col-sm-12 widget-class" data-widget="{{$widget}}">
                                                @include($widget . 'Widget',[$widget])
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section><!-- /.container-fluid -->

    <div class="modal fade" id="dashboard-widgets-date-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">{{ trans('fi.custom_date_range') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>{{ trans('fi.from_date') }}</label>
                        <div class="input-group date">
                            <div class="input-group date" id="dashboard-widgets-from-date" data-target-input="nearest">
                                {!! Form::text('setting_dashboardWidgetsFromDate', config('fi.dashboardWidgetsFromDate') ? \Carbon\Carbon::createFromDate(config('fi.dashboardWidgetsFromDate'))->format(config('fi.dateFormat')) : '', ['class' => 'form-control form-control-sm datetimepicker-input','data-toggle' => 'datetimepicker', 'data-target' => '#dashboard-widgets-from-date', 'id' => 'dashboard-widgets-from-date']) !!}
                                <div class="input-group-append" data-target="#dashboard-widgets-from-date"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.to_date') }}</label>
                        <div class="input-group date">
                            <div class="input-group date" id="dashboard-widgets-to-date" data-target-input="nearest">
                                {!! Form::text('setting_dashboardWidgetsToDate', config('fi.dashboardWidgetsToDate') ?\Carbon\Carbon::createFromDate(config('fi.dashboardWidgetsToDate'))->format(config('fi.dateFormat')): '', ['class' => 'form-control form-control-sm datetimepicker-input','data-toggle' => 'datetimepicker', 'data-target' => '#dashboard-widgets-to-date',  'id' => 'dashboard-widgets-to-date']) !!}
                                <div class="input-group-append" data-target="#dashboard-widgets-to-date"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default"
                            data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                    <button type="button" class="btn btn-sm btn-primary change-dashboard-widgets-options"
                            data-id="custom_date_range" data-dismiss="modal">{{ trans('fi.save') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->
@stop