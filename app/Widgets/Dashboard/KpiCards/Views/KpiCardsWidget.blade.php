@include('_js_dashboard_collapse_toggle')
@can('kpi_cards.view')
    @if($kpiCardsSettings == true)

        <div id="invoice-dashboard-totals-widget">

            <div class="card card-primary card-outline" id="collapsed-card-kpi-cards">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-briefcase"></i> {{trans('fi.kpi_cards')}}</h3>

                    <div class="card-tools pull-right">
                        <button type="button" class="btn btn-tool collapse-toggle-btn" data-widget-name='kpi-cards' data-card-widget="collapse">
                            <i class="fas fa-minus" id="collapsed-card-icon-kpi-cards"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" id="collapsed-card-display-kpi-cards">
                    <div class="row">
                        @if(config('fi.dashboardDraftInvoices') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-draft-invoices">
                                    <div class="inner">
                                        <h2>{{ $invoicesTotalDraft }}</h2>

                                        <p>{{ trans('fi.draft_invoices') }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-edit"></i>
                                    </div>
                                    @can('invoices.view')
                                        <a href="{{ route('invoices.index') }}?status=draft"
                                           class="small-box-footer kpi-card-small-box-footer text-right pr-1">
                                            {{ trans('fi.view_draft_invoices') }} <i
                                                    class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                        @if(config('fi.dashboardSentInvoices') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-sent-invoices">
                                    <div class="inner">
                                        <h2>{{ $invoicesTotalSent }}</h2>

                                        <p>{{ trans('fi.sent_invoices') }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-share"></i>
                                    </div>
                                    @can('invoices.view')
                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('invoices.index') }}?status=sent">
                                            {{ trans('fi.view_sent_invoices') }} <i
                                                    class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                        @if(config('fi.dashboardOverdueInvoices') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-overdue-invoices">
                                    <div class="inner">
                                        <h2>{{ $invoicesTotalOverdue }}</h2>

                                        <p>{{ trans('fi.overdue_invoices') }}</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-alert"></i></div>
                                    @can('invoices.view')
                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('invoices.index') }}?status=overdue">
                                            {{ trans('fi.view_overdue_invoices') }} <i
                                                    class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                        @can('payments.view')
                            @if(config('fi.dashboardPaymentsCollectedInvoices') == 1)
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="small-box kpi-card-payments-collected">
                                        <div class="inner">
                                            <h2>{{ $invoicesTotalPaid }}</h2>

                                            <p>{{ trans('fi.payments_collected') }}</p>
                                        </div>
                                        <div class="icon"><i class="ion ion-cash"></i></div>

                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('payments.index') }}">
                                            {{ trans('fi.view_payments') }} <i class="fa fa-arrow-circle-right"></i>
                                        </a>

                                    </div>
                                </div>
                            @endif
                        @endcan

                        @if(config('fi.dashboardDraftQuotes') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-draft-quotes">
                                    <div class="inner">
                                        <h2>{{ $quotesTotalDraft }}</h2>

                                        <p>{{ trans('fi.draft_quotes') }}</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-edit"></i></div>
                                    @can('quotes.view')
                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('quotes.index') }}?status=draft">
                                            {{ trans('fi.view_draft_quotes') }} <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                        @if(config('fi.dashboardSentQuotes') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-sent-quotes">
                                    <div class="inner">
                                        <h2>{{ $quotesTotalSent }}</h2>

                                        <p>{{ trans('fi.sent_quotes') }}</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-share"></i></div>
                                    @can('quotes.view')
                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('quotes.index') }}?status=sent">
                                            {{ trans('fi.view_sent_quotes') }} <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                        @if(config('fi.dashboardRejectedQuotes') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-rejected-quotes">
                                    <div class="inner">
                                        <h2>{{ $quotesTotalRejected }}</h2>

                                        <p>{{ trans('fi.rejected_quotes') }}</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-thumbsdown"></i></div>
                                    @can('quotes.view')
                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('quotes.index') }}?status=rejected">
                                            {{ trans('fi.view_rejected_quotes') }} <i
                                                    class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                        @if(config('fi.dashboardApprovedQuotes') == 1)
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="small-box kpi-card-approved-quotes">
                                    <div class="inner">
                                        <h2>{{ $quotesTotalApproved }}</h2>

                                        <p>{{ trans('fi.approved_quotes') }}</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-thumbsup"></i></div>
                                    @can('quotes.view')
                                        <a class="small-box-footer kpi-card-small-box-footer text-right pr-1"
                                           href="{{ route('quotes.index') }}?status=approved">
                                            {{ trans('fi.view_approved_quotes') }} <i
                                                    class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @endif
@endcan