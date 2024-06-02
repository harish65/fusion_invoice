@can('open_invoice_aging.view')

    <script>
        $(function () {
            @if ($openInvoiceAging['success'] == false)
            alertify.error('{{$openInvoiceAging['message']}}');
            @endif
        });
    </script>

    <div id="open-invoice-aging-dashboard-totals-widget">

        <div class="card card-primary card-outline" id="collapsed-card-open-invoice-aging">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="far fa-chart-bar"></i> {{trans('fi.open_invoice_aging' )}}
                </h3>

                <div class="card-tools pull-right">
                    <button type="button" class="btn btn-tool btn-xs btn-default" data-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fa fa-sliders-h" data-toggle="tooltip"
                           data-placement="auto"
                           title="{!! trans('fi.tt_open_ar_aging_settings') !!}">
                        </i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" role="menu">

                        <a href="{{route('widgets.dashboard.OpenInvoiceAging.widgetUpdateOpenInvoiceAgingSetting',['invoiceStatus'=>"'sent'"])}}"
                           class="open-invoice-aging dropdown-item {{(config('fi.widgetOpenInvoiceAging') == "'sent'") ?'active' : ''}}"
                           data-open-invoice-aging-setting="sent">
                            <i class="fas fa-share"></i> {{ trans('fi.sent_only') }}
                        </a>

                        <a href="{{route('widgets.dashboard.OpenInvoiceAging.widgetUpdateOpenInvoiceAgingSetting',['invoiceStatus'=>"'sent','draft'"])}}"
                           class="open-invoice-aging dropdown-item {{(config('fi.widgetOpenInvoiceAging') != "'sent'") ?'active' : ''}}"
                           data-open-invoice-aging-setting="draftAndSent">
                            <i class="fab fa-firstdraft"></i> {{ trans('fi.sent_and_draft') }}
                        </a>
                    </div>

                    <button type="button" class="btn btn-tool collapse-toggle-btn" data-widget-name='open-invoice-aging'
                            data-card-widget="collapse">
                        <i class="fas fa-minus" id="collapsed-card-icon-open-invoice-aging"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="collapsed-card-display-open-invoice-aging">
                <div class="row">
                    @if(isset($openInvoiceAging['openInvoiceAging']))
                        @foreach($openInvoiceAging['openInvoiceAging'] as $key => $value)
                            <div class="col-lg-2 col-md-4 col-sm-6 pr-1 pl-0 pt-3">
                                <div class="small-box {{ $value['bg-color'] }} text-center">
                                    <div class="inner p-1">
                                        <strong>{{ $openInvoiceAging['success'] == true ? $value['data'] : ''}}</strong>
                                    </div>
                                    <div class="small-box-footer text-right pr-1">
                                        <span class="text-center d-block open-ar-card-label-dark">{{ trans('fi.'.$key) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

@endcan