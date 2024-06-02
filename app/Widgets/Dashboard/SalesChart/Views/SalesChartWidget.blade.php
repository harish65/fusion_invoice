@can('sales_chart.view')
    @include('layouts._chartjs')
    <div id="sales-chart-dashboard-totals-widget">
        <div class="card card-primary card-outline" id="collapsed-card-sales-chart">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> {{trans('fi.sales_chart')}}</h3>
                <div class="card-tools pull-right">

                    <button type="button" class="btn btn-tool btn-xs btn-default" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-sliders-h" data-toggle="tooltip" data-placement="auto"
                           title="{!! trans('fi.tt_sales_chart_settings') !!}">
                        </i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                        <a href="{{route('widgets.dashboard.SalesChart.widgetUpdateSalesChartSetting',['invoiceStatus'=> 0])}}"
                           class="sales-chart dropdown-item {{(config('fi.widgetSalesChartSetting') == 0) ?'active' : ''}}"
                           data-sales-chart-setting="sent">
                            <i class="fas fa-share"></i> {{ trans('fi.sent_only') }}
                        </a>

                        <a href="{{route('widgets.dashboard.SalesChart.widgetUpdateSalesChartSetting',['invoiceStatus'=> 1])}}"
                           class="sales-chart dropdown-item {{(config('fi.widgetSalesChartSetting') == 1) ?'active' : ''}}"
                           data-sales-chart-setting="draftAndSent">
                            <i class="fab fa-firstdraft"></i> {{ trans('fi.sent_and_draft') }}
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="{{route('widgets.dashboard.SalesChart.widgetUpdateSalesChartAccumulateSetting',['invoiceAccumulateStatus'=> 0])}}"
                           class=" dropdown-item {{(config('fi.accumulateTotals') == 0) ?'active' : ''}}"
                           data-sales-chart-setting="0">
                            <i class="fas fa-not-equal"></i> {{ trans('fi.do_not_accumulate_totals') }}
                        </a>

                        <a href="{{route('widgets.dashboard.SalesChart.widgetUpdateSalesChartAccumulateSetting',['invoiceAccumulateStatus'=> 1])}}"
                           class=" dropdown-item {{(config('fi.accumulateTotals') == 1) ?'active' : ''}}"
                           data-sales-chart-setting="1">
                            <i class="fas fa-equals"></i> {{ trans('fi.accumulate_totals') }}
                        </a>
                    </div>


                    <button type="button" class="btn btn-tool collapse-toggle-btn" data-widget-name='sales-chart'
                            data-card-widget="collapse">
                        <i class="fas fa-minus" id="collapsed-card-icon-sales-chart"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="collapsed-card-display-sales-chart">
                <div class="chart">
                    <div class="chartjs-size-monitor">
                        <div class="chartjs-size-monitor-expand">
                            <div class=""></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink">
                            <div class=""></div>
                        </div>
                    </div>
                    <canvas id="areaChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 488px;"
                            width="488" height="250" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            var _invoiceData = JSON.parse('{!! json_encode($chartDate['chartDataInvoiceArray']) !!}');
            var _labels = JSON.parse('{!! json_encode($chartDate['labels']) !!}');
            var _paymentData = JSON.parse('{!! json_encode($chartDate['chartDataPaymentArray']) !!}');
            var areaChartCanvas = $('#areaChart').get(0).getContext('2d');
            var areaChartData = {
                labels: _labels,
                responsive: true,
                datasets: [
                    {
                        label: '{!! trans('fi.invoices') !!}',
                        backgroundColor: 'rgba(58,191,212,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(58,191,212,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(58,191,212,1)',
                        data: _invoiceData,
                    },
                    {
                        label: '{!! trans('fi.payments') !!}',
                        backgroundColor: 'rgba(98, 221, 125, .9)',
                        borderColor: '#c1c7d1',
                        pointColor: 'rgba(98, 221, 125, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(98, 221, 125,1)',
                        data: _paymentData,
                    },
                ]
            };
            var areaChartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: true,
                    labels: {
                        fontColor: "#a8a8a8"
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            fontColor: "#a8a8a8"
                        },
                        scaleLabel: {
                            display: false,
                            labelString: '{{trans('fi.value')}}'
                        },
                        gridLines: {
                            display: false,
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            fontColor: "#a8a8a8"
                        },
                        scaleLabel: {
                            display: false,
                            labelString: '{{trans('fi.time_period')}}'
                        },
                        gridLines: {
                            display: false,
                        }
                    }]
                }
            };

            new Chart(areaChartCanvas, {
                type: 'line',
                data: areaChartData,
                options: areaChartOptions,

            });

            Chart.plugins.register({
                afterDraw: function (chart) {
                    if (chart.data.datasets[0].data.every(item => item === 0) && chart.data.datasets[1].data.every(item => item === 0)) {
                        let ctx = chart.chart.ctx;
                        let width = chart.chart.width;
                        let height = chart.chart.height;

                        chart.clear();
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = "30px sans-serif";
                        ctx.fillText('{{trans('fi.no_data_to_display')}}', width / 2, height / 2);
                        ctx.restore();
                    }
                }
            });
        });
    </script>
@endcan
