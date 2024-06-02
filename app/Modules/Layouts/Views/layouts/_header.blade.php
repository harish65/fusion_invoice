<nav class="main-header  navbar navbar-expand bg-{{$topBarColor}}  {{($topBarColor == 'default' && $navClass == 'dark') ? 'navbar-dark':'navbar-light'}} {{(($topBarColor != 'default' && $navClass == 'dark') ? 'border-0':'')}}">
    <!-- Left navbar links -->
    <ul class="navbar-nav">

        <li class="nav-item">
            <a class="nav-link " id="toggleClasses" data-widget="pushmenu" href="#" role="button">
                <i class="toggleClasses fas fa-angle-left text-{{$topBarColorText}}"></i>
            </a>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <ul class="nav navbar-nav">
            <li>
                <a class="nav-link" href="https://www.fusioninvoice.com/docs" title="{{ trans('fi.documentation') }}"
                   target="_blank">
                    <i class="fa fa-question-circle text-{{$topBarColorText}}"></i>
                </a>
            </li>
            <li class="nav-item dropdown notifications-menu notifications-menu-id">
                <a href="#" class="nav-link" data-toggle="dropdown" aria-expanded="true">
                    <i class="fas fa-bell text-{{$topBarColorText}}">
                        @if(count($notifications))
                            <span class="badge badge-{{isset($topBarLogoutColorText) ? $topBarLogoutColorText :'danger'}} navbar-badge"
                                  style="background: {{isset($topBarLogoutColorText) ? $topBarLogoutColorText : '#dc3545'}}; color : {{($topBarLogoutColorText == '#82FFFF') ? 'black' :'' }}">{{count($notifications)}}</span>
                        @endif
                    </i>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right notifications-menu"
                     style="max-height: 430px;overflow-x: hidden;overflow-y: auto;right: -75px;">
                    @if(count($notifications))
                        <div class="row">
                            <div class="col-md-12">
                                <span class="dropdown-item dropdown-header">
                                    <a href="javascript:void(0)" data-url="{{route('dashboard.index')}}"
                                       title="{{ trans('fi.clear-all') }}"
                                       class="btn btn-xs btn-danger float-left clear-all-notifications"><i
                                                class="fa fa-trash"></i>
                                    </a>
                                    {{trans('fi.total_notifications',['total' => count($notifications)])}}
                                </span>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        @foreach($notifications as $notification)
                            {!! $notification->notification_detail['link'] !!}
                            <div class="dropdown-divider"></div>
                        @endforeach

                    @else
                        <span class="dropdown-footer">{{trans('fi.no_notifications')}}</span>
                    @endif
                </div>
            </li>
            @if (in_array(auth()->user()->user_type, ['admin']))

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle custom-invoice-padding" id="dropdown2"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false" title="{{ trans('fi.system') }}">
                        <i class="fa fa-cog text-{{$topBarColorText}}"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="dropdown2"
                        style="min-width:210px;">

                        <li class="dropdown-submenu dropdown-hover">
                            <a class="dropdown-item dropdown-toggle sm-dropdown-menu-show" id="dropdown2-1"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                <i class="fas fa-cogs pr-2"></i>
                                {{ trans('fi.configuration') }}
                            </a>
                            <ul class="submenu submenu-left dropdown-menu sm-dropdown-menu"
                                aria-labelledby="dropdown2-1">
                                <li>
                                    <button type="button" class="sm-submenu-close d-none pr-2 close" aria-hidden="true">
                                        ×
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                                        <i class="fas fa-sliders-h pr-2"></i>{{ trans('fi.system_settings') }}
                                    </a>
                                </li>

                                <li class="dropdown-divider"></li>

                                <li class="dropdown-submenu">
                                    <a id="dropdownSub1-1" role="button" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false" class="dropdown-item dropdown-toggle">
                                        <i class="fas fa-tachometer-alt pr-2"></i>{{ trans('fi.dashboards') }}
                                    </a>
                                    <ul aria-labelledby="dropdownSub1-1" class="dropdown-menu border-0 shadow">
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('settings.system.default.dashboard.index') }}">
                                                <i class="fas fa-desktop pr-2"></i>{{ trans('fi.system_default_dashboard') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{  route('settings.user.specific.dashboard.index') }}">
                                                <i class="fas fa-address-card pr-2"></i>{{ trans('fi.user_specific_dashboards') }}
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('company.profiles.index') }}">
                                        <i class="far fa-building pr-2"></i>{{ trans('fi.company_profiles') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('documentNumberSchemes.index') }}">
                                        <i class="fa fa-list-ol pr-2"></i>{{ trans('fi.document_number_schemes')}}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('paymentMethods.index') }}">
                                        <i class="fa fa-money-check-alt pr-2"></i>{{ trans('fi.payment_methods') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('taxRates.index') }}">
                                        <i class="fas fa-percentage pr-2"></i>{{ trans('fi.tax_rates') }}</a>
                                </li>

                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('itemLookups.index') }}">
                                        <i class="fa fa-dolly-flatbed pr-2"></i>{{ trans('fi.item_lookups') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('item.categories.index') }}">
                                        <i class="fa fa-layer-group pr-2"></i>{{ trans('fi.item_categories') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('expenses.vendors.index') }}">
                                        <i class="fa fa-store-alt pr-2"></i>{{ trans('fi.expense_vendors') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('expenses.categories.index') }}">
                                        <i class="fa fa-list-ul pr-2"></i>{{ trans('fi.expense_categories') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('currencies.index') }}">
                                        <i class="fa fa-money-bill-wave pr-2"></i>{{ trans('fi.currencies') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                @foreach (config('fi.menus.navigation_header') as $menu)
                                    @if (view()->exists($menu))
                                        @include($menu)
                                    @endif
                                @endforeach
                            </ul>
                        </li>

                        <li class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                <i class="far fa-address-card pr-2"></i>{{ trans('fi.user_accounts') }}
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li class="dropdown-submenu dropdown-hover">
                            <a class="dropdown-item dropdown-toggle sm-dropdown-menu-show" id="dropdown3-1"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false" href="#"><i class="fas fa-drafting-compass pr-2"></i>
                                {{ trans('fi.customizations') }}
                            </a>
                            <ul class="submenu submenu-left dropdown-menu sm-dropdown-menu"
                                aria-labelledby="dropdown3-1">
                                <li>
                                    <button type="button" class="sm-submenu-close d-none d-none pr-2 close"
                                            aria-hidden="true">×
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('customFields.index') }}">
                                        <i class="fas fa-table pr-2"></i>{{ trans('fi.custom_fields') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('addons.index') }}">
                                        <i class="fas fa-puzzle-piece pr-2"></i>{{ trans('fi.addons') }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown-divider"></li>

                        <li class="dropdown-submenu dropdown-hover">
                            <a class="dropdown-item dropdown-toggle sm-dropdown-menu-show" id="dropdown4-1"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false" href="#"><i class="fas fa-toolbox pr-2"></i>
                                {{ trans('fi.utilities_and_logs') }}
                            </a>
                            <ul class="submenu submenu-left dropdown-menu sm-dropdown-menu"
                                aria-labelledby="dropdown4-1">
                                <li>
                                    <button type="button" class="sm-submenu-close d-none pr-2 close" aria-hidden="true">
                                        ×
                                    </button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('import.index') }}">
                                        <i class="fa fa-file-import pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_import') !!}">
                                        </i>
                                        {{ trans('fi.import_data') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('export.index') }}">
                                        <i class="fa fa-file-export fa-flip-horizontal pl-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_export') !!}">
                                        </i>
                                        {{ trans('fi.export_data') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item tags-modal-request" data-modal="tags" href="#">
                                        <i class="fa fa-tasks pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_rename_tags') !!}">
                                        </i>
                                        {{ trans('fi.rename_tags') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('mailLog.index') }}">
                                        <i class="fa fa-mail-bulk pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_mail_log') !!}">
                                        </i>
                                        {{ trans('fi.view') }} {{ trans('fi.mail_log') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('systemLog.index') }}">
                                        <i class="fa fa-file pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_system_log') !!}">
                                        </i>
                                        {{ trans('fi.view') }} {{ trans('fi.system_log') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item modal-request" data-action="tags_delete"
                                       data-url="{{ route('tags.delete') }}"
                                       data-message="{!! trans('fi.orphan_tags_delete_confirm') !!}"
                                       data-method="post"
                                       data-delete="{!! trans('fi.clean_up') !!}"
                                       id="btn-delete-orphan-tags" href="javascript:void(0);">
                                        <i class="fa fa-tag pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_tag_cleanup') !!}">
                                        </i>
                                        {{ trans('fi.delete_tags') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item modal-request"
                                       data-action="pdf_cleanup"
                                       data-url="{{ route('settings.pdf.cleanup') }}"
                                       data-message="{!! trans('fi.pdf_cleanup_confirm') !!}"
                                       data-method="get"
                                       data-delete="{!! trans('fi.clean_up') !!}"
                                       id="btn-pdf-cleanup" href="javascript:void(0);">
                                        <i class="fa fa-file-pdf pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_pdf_cleanup') !!}">
                                        </i>
                                        {{ trans('fi.pdf_cleanup') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item modal-request"
                                       data-action="invoices_recalculate"
                                       data-url="{{route('invoices.recalculate')}}"
                                       data-message="{!! trans('fi.recalculate_help_text')!!}"
                                       data-method="post"
                                       data-delete="{!! trans('fi.recalculate') !!}"
                                       id="btn-recalculate-invoices" href="javascript:void(0);">
                                        <i class="fa fa-file-invoice-dollar pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_recalc_invoices') !!}">
                                        </i>
                                        {{ trans('fi.recalculate_invoices') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item modal-request"
                                       data-action="quotes_recalculate"
                                       data-url="{{ route('quotes.recalculate') }}"
                                       data-message="{!! trans('fi.recalculate_help_text')!!}"
                                       data-method="post"
                                       data-delete="{!! trans('fi.recalculate') !!}"
                                       id="btn-recalculate-quotes" href="javascript:void(0);">
                                        <i class="fa fa-file-invoice pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_recalc_quotes') !!}">
                                        </i>
                                        {{ trans('fi.recalculate_quotes') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item modal-request"
                                       data-action="cache_clean"
                                       data-method="get"
                                       data-url="{{ route('settings.cache.cleanup') }}"
                                       data-message="{!! trans('fi.clear_cache_confirm')!!}"
                                       data-method="get"
                                       data-delete="{!! trans('fi.clear_cache') !!}"
                                       id="btn-clear-cache" href="javascript:void(0);">
                                        <i class="fa fa-file-invoice-dollar pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_clear_cache') !!}">
                                        </i>
                                        {{ trans('fi.clear_cache') }}
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                @if(!config('app.demo'))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('data.seeder') }}"
                                            target="_blank">
                                            <i class="fa fa-database pr-2"
                                                data-toggle="tooltip" data-placement="auto"
                                                title="{!! trans('fi.tt_utilities_database_seeder') !!}">
                                            </i>
                                            {{ trans('fi.database_seeder') }}
                                        </a>
                                    </li>
                                @else
                                    <li class="seed-it">
                                        <a class="dropdown-item"  href="javaScript:Void(0)">
                                            <i class="fa fa-database pr-2"
                                                data-toggle="tooltip" data-placement="auto"
                                                title="{!! trans('fi.tt_utilities_database_seeder') !!}">
                                            </i>{{ trans('fi.database_seeder') }}
                                        </a> 
                                    </li> 
                                @endif
                                    
                                @if (!config('app.demo'))
                                    <li class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('settings.backup.database') }}"
                                           target="_blank">
                                            <i class="fa fa-database pr-2"
                                               data-toggle="tooltip" data-placement="auto"
                                               title="{!! trans('fi.tt_utilities_download_database') !!}">
                                            </i>
                                            {{ trans('fi.download_database_backup') }}
                                        </a>
                                    </li>
                                @endif


                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('tasks.run') }}">
                                        <i class="fa fa-tasks pr-2"
                                           data-toggle="tooltip" data-placement="auto"
                                           title="{!! trans('fi.tt_utilities_run_daily_tasks') !!}">
                                        </i>
                                        {{ trans('fi.run_daily_tasks') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown-divider"></li>
                        @foreach (config('fi.menus.system') as $menu)
                            @if (view()->exists($menu))
                                @include($menu)
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endif

            <li class="logout-btn nav-item">
                <a class="logout-color nav-link
                text-{{isset($topBarLogoutColorText) ? $topBarLogoutColorText :'danger'}}"
                   href="{{ route('session.logout') }}"
                   title="{{ trans('fi.sign_out') }}"><i
                            class="fa fa-power-off shadow-lg"></i></a>
            </li>

        </ul>

    </ul>
</nav>
</header>
<style type="text/css">
    @media all and (min-width: 500px) {
        .dropdown-menu .submenu-left {
            right: 100%;
            left: auto;
        }
    }

    @media all and (max-width: 499px ) {
        .dropdown-menu .submenu-left {
            left: auto;
        }

        .sm-dropdown-menu {
            top: 40px !important;
        }
    }
</style>
<script>

    $('.sm-submenu-close').click(function (e) {
        e.stopPropagation();
        e.preventDefault();
        let $_parent = $(this).parent().closest('ul').parent();
        $_parent.removeClass('show open');
        $_parent.find('.sm-dropdown-menu-show ').removeClass('show open');
        $(this).closest('.sm-dropdown-menu').removeClass('show').addClass('d-none');
    });

    $('.sm-dropdown-menu-show').mouseenter(function (e) {
        $('.sm-dropdown-menu').hide().removeClass('d-block');
        $(this).parent().find('.sm-dropdown-menu').show().removeClass('d-none')
    });

    $('.modal-request').click(function () {
        $('#modal-placeholder').load('{!! route('application.clean') !!}', {
            message: $(this).data('message'),
            action: $(this).data('action'),
            url: $(this).data('url'),
            delete: $(this).data('delete'),
            method: $(this).data('method'),
        });
    });

    $('.tags-modal-request').click(function () {
        $('#modal-placeholder').load('{!! route('tags.edit.modal') !!}', {
            modalName: $(this).data('modal'),
        });
    });

    $(function () {
        $(".action-menu").on("show.bs.dropdown", function () {
            var $btnDropDown = $(this).find(".dropdown-toggle");
            var $listHolder = $(this).find(".dropdown-menu");
            $(this).css("position", "static");
            $listHolder.css({
                "top": ($btnDropDown.offset().top + $btnDropDown.outerHeight(true)) + "px",
                "left": $btnDropDown.offset().left + "px"
            });
            $listHolder.data("open", true);
        });
        $(".action-menu").on("hidden.bs.dropdown", function () {
            var $listHolder = $(this).find(".dropdown-menu");
            $listHolder.data("open", false);
        });
        $('.seed-it').click(function(){
            alertify.error('{{ trans("fi.seeding_functionality_not_available_on_demo") }}');
        })
    });
    
</script>