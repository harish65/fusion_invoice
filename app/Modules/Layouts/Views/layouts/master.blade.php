<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('fi.headerTitleText') }}</title>

    @include('layouts._head')

    @include('layouts._js_global')

    @yield('head')

    @yield('javascript')

    @include('layouts._alertifyjs')

    @include('layouts._js_notifications')

</head>
<body class="{{ $skinClass }} position-relative  sidebar-mini fixed">

<div class="wrapper">

    @include('layouts._header')

    <aside class="main-sidebar sidebar-dark-primary">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard.index')}}" class=" bg-{{$topBarColor }}  brand-link shadow-lg ">
            <img src="{{ asset('assets/dist/img/logo.png') }}" alt="{{ config('fi.headerTitleText') }}"
                 class="brand-image img-circle elevation-5">
            <span class="brand-text font-weight-dark">{{ config('fi.headerTitleText') }}</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            @if (config('fi.displayProfileImage'))
                <a href="javascript:void(0);" class="d-block quick-profile-setting">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            {!! auth()->user()->getAvatar(40, true) !!}
                        </div>
                        <div class="info">
                            {!! ucfirst(auth()->user()->name) !!}
                        </div>
                    </div>
                </a>
            @endif
        <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard.index') }}"
                           class="{{ $urlSegment1 == 'dashboard' ? 'nav-link active' : 'nav-link' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>{{ trans('fi.dashboard') }}</p>
                        </a>
                    </li>
                    @can('clients.view')
                        <li class="nav-item nav-item-hover">
                            <a href="{{ route('clients.index', ['status' => 'active']) }}"
                               class="link1 {{ $urlSegment1 == 'clients' ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>{{ trans('fi.clients') }}</p>
                            </a>
                            <a href="@can('clients.create') {{ route('clients.create') }} @endcan"
                               class="link2 btn-link {{ $urlSegment1 == 'clients' ? 'active' : '' }}">
                                @can('clients.create')<i class="fas fa-plus fa-sm text-white "></i>@endcan
                            </a>
                        </li>
                    @endcan
                    @can('quotes.view')
                        <li class="nav-item nav-item-hover">
                            <a href="{{ route('quotes.index', ['status' => config('fi.quoteStatusFilter')]) }}"
                               class="link1 {{ $urlSegment1 == 'quotes' ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon far fa-file-alt"></i>
                                <p>{{ trans('fi.quotes') }}</p>
                            </a>
                            <a href="javascript:void(0)"
                               class="@can('quotes.create') create-quote @endcan link2 btn-link btn-action-modal {{ $urlSegment1 == 'quotes' ? 'active' : '' }}">
                                @can('quotes.create')<i class="fas fa-plus fa-sm text-white"></i>@endcan
                            </a>
                        </li>
                    @endcan
                    @can('invoices.view')
                        <li class="nav-item nav-item-hover">
                            <a href="{{ route('invoices.index', ['status' => config('fi.invoiceStatusFilter')]) }}"
                               class="link1 {{ $urlSegment1 == 'invoices' ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>{{ trans('fi.invoices') }}</p>
                            </a>
                            <a href="javascript:void(0)"
                               class="link2 btn-link btn-action-modal @can('invoices.create') create-invoice @endcan {{ $urlSegment1 == 'invoices' ? 'active' : '' }}">
                                @can('invoices.create')<i class="fas fa-plus fa-sm text-white"></i>@endcan
                            </a>
                        </li>
                    @endcan
                    @can('recurring_invoices.view')
                        <li class="nav-item nav-item-hover">
                            <a href="{{ route('recurringInvoices.index') }}"
                               class="link1 {{ $urlSegment1 == 'recurring_invoices' ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fas fa-sync"></i>
                                <p>{{ trans('fi.recurring_invoices') }}</p>
                            </a>
                            <a href="javascript:void(0)"
                               class="link2 btn-link @can('recurring_invoices.create') create-recurring-invoice btn-action-modal @endcan {{ $urlSegment1 == 'recurring_invoices' ? 'active' : '' }}">
                                @can('recurring_invoices.create')<i class="fas fa-plus fa-sm text-white"></i>@endcan
                            </a>
                        </li>
                    @endcan
                    @can('payments.view')
                        <li class="nav-item nav-item-hover">
                            <a href="{{ route('payments.index') }}"
                               class="link1 {{ $urlSegment1 == 'payments' ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon far fa-credit-card"></i>
                                <p>{{ trans('fi.payments') }}</p>
                            </a>
                            <a href="javascript:void(0)"
                               class="link2 btn-link @can('payments.create') create-payment btn-action-modal @endcan {{ $urlSegment1 == 'payments' ? 'active' : '' }}">
                                @can('payments.create')
                                    <i class="fas fa-plus fa-sm text-white"></i>
                                @endcan
                            </a>
                        </li>
                    @endcan
                    @can('expenses.view')
                        <li class="nav-item nav-item-hover">
                            <a href="{{ route('expenses.index') }}"
                               class="link1 {{ $urlSegment1 == 'expenses' ? 'nav-link active' : 'nav-link' }}">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>{{ trans('fi.expenses') }}</p>
                            </a>
                            <a href="@can('expenses.create'){{ route('expenses.create') }}@endcan"
                               class="link2 btn-link {{ $urlSegment1 == 'expenses' ? 'active' : '' }}">
                                @can('expenses.create')<i class="fas fa-plus fa-sm text-white"></i>@endcan
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item nav-item-hover">
                        <a href="{{ route('task.index') }}"
                           class="link1 {{ $urlSegment1 == 'task' ? 'nav-link active' : 'nav-link' }}">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>{{ trans('fi.tasks') }}</p>
                        </a>
                        <a href="{{ route('task.create') }}"
                           class="link2 btn-link {{ $urlSegment1 == 'task' ? 'active' : '' }}">
                            <i class="fas fa-plus fa-sm text-white"></i>
                        </a>
                    </li>

                    <li class="nav-item {{ ($urlSegment1 == 'report' || $urlSegment2 == 'reports') ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ ($urlSegment1 == 'report' || $urlSegment2 == 'reports') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>
                                {{ trans('fi.reports') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('client_statement.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.clientStatement') }}"
                                       class="small nav-link {{ $urlSegment2 == 'client_statement' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.client_statement') }}</p>
                                    </a>
                                </li>
                            @endcan

                            @can('expense_list.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.expenseList') }}"
                                       class="small nav-link {{ $urlSegment2 == 'expense_list' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.expense_list') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('item_sales.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.itemSales') }}"
                                       class="small nav-link {{ $urlSegment2 == 'item_sales' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.item_sales') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('payments_collected.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.paymentsCollected') }}"
                                       class="small nav-link {{ $urlSegment2 == 'payments_collected' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.payments_collected') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('profit_and_loss.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.profitLoss') }}"
                                       class="small nav-link {{ $urlSegment2 == 'profit_loss' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.profit_and_loss') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('revenue_by_client.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.revenueByClient') }}"
                                       class="small nav-link {{ $urlSegment2 == 'revenue_by_client' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.revenue_by_client') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('tax_summary.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.taxSummary') }}"
                                       class="small nav-link {{ $urlSegment2 == 'tax_summary' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.tax_summary') }}</p>
                                    </a>
                                </li>
                            @endcan
                            @can('recurring_invoice_list.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.recurringInvoiceList') }}"
                                       class="small nav-link {{ $urlSegment2 == 'recurring_invoice_list' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon text-info"></i>

                                        <p>{{ trans('fi.recurring_invoice_list') }}</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{ route('reports.clientInvoice') }}"
                                   class="small nav-link {{ $urlSegment2 == 'client_invoice' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon text-info"></i>

                                    <p>{{ trans('fi.client_invoice') }}</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('reports.creditAndPrepayments') }}"
                                   class="small nav-link {{ $urlSegment2 == 'credit_and_pre_payments' ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon text-info"></i>

                                    <p>{{ trans('fi.credit-memo-and-prepayments') }}</p>
                                </a>
                            </li>
                            @foreach (config('fi.menus.reports') as $report)
                                @if (view()->exists($report))
                                    @include($report)
                                @endif
                            @endforeach
                        </ul>
                    </li>
                    @foreach (config('fi.menus.navigation') as $menu)
                        @if (view()->exists($menu))
                            @include($menu)
                        @endif
                    @endforeach
                    @include('layouts._mru')
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    <div class="content-wrapper position-relative">
        @yield('content')
        @if (config('time_tracking_enabled') && config('fi.floatingTimeTrackingAddon') == 1)
            @include('time_tracking.widget._task_list')
        @endif
    </div>
</div>

<div id="modal-placeholder"></div>
<div id="attachment-modal-placeholder"></div>
<div id="modal-confirm-credit-application"></div>
<div id="note-modal-placeholder"></div>
<div class="modal" id="modal-loader">
    <div class="overlay text-center position-absolute p-3"
         style="left: 50%;top: 50%;-webkit-transform: translateX(-50%) translateY(-50%);transform: translateX(-50%) translateY(-50%);">
        <i class="fa fa-spinner fa-9x"></i>
    </div>
</div>

<script>
    var $document = $(document.body);
    $document.find('.main-sidebar > .sidebar > .mt-2 > ul.nav-sidebar > li.nav-item-hover').each(function () {
        $(this).children().each(function (e) {
            $(this).children().each(function (e) {
                if ($(this).hasClass('active')) {
                    $(this).next('span.nav-item-plus-icon').addClass('d-block');
                }
            });
        });
    });
    $('.quick-profile-setting').click(function () {
        $('#modal-placeholder').load('{!! route('dashboard.user.modal.edit') !!}', {
            userId: '{{auth()->user()->id}}',
            modalName: 'users'
        });
    });
</script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets/plugins/moment/moment.min.js?v='.config('fi.version')) }}"></script>
<script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js?v='.config('fi.version')) }}"></script>
@yield('footerJS')

</body>
</html>
