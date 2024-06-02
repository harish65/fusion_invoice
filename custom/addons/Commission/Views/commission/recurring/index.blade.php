@extends('layouts.master')
@section('javascript')
    @include('commission._js_addon_global')

    <script type="text/javascript">

        $(function () {

            $('.commission_filter_options ').change(function () {
                $('form#filter').submit();
            });

            $('#btn-clear-filters').click(function () {
                $('#search').val('');
                $('.commission_filter_options').prop('selectedIndex', 0);
                $('#filter').submit();
            });

            $('#btn-bulk-delete').click(function () {

                var ids = [];

                $('.bulk-record:checked').each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length > 0) {

                    alertify.confirm("{!! trans('fi.bulk_delete_record_warning') !!}", function () {
                        $.ajax({
                            url: "{{ route('recurring.invoices.commission.bulk.delete') }}",
                            method: 'post',
                            data: {ids: ids},
                            beforeSend: function () {
                                showHideLoaderModal();
                            },
                            success: function () {
                                showHideLoaderModal();
                                window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
                            }
                        });
                    }, function () {
                        alertify.alert().destroy();
                    }).setHeader(confirmHeader).set({transition: 'zoom', defaultFocus: 'cancel'});

                }
            });

        });
    </script>
@stop
@section('content')
    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('Commission::lang.commission') }}
                        <small>{{ trans('Commission::lang.recurring_invoice_commission') }}</small>
                    </h1>
                </div>
                <div class="col-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="card card-primary card-outline">

                <div class="card-header">

                    {!! Form::open(['method' => 'GET', 'id' => 'filter', 'class' => 'form-inline']) !!}
                    <ul class="nav nav-pills">
                        <li class="nav-item mr-1">
                            <div class="input-group mt-1 mb-1">
                                @if (isset($searchPlaceholder))
                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control form-control-sm inline','autofocus','placeholder' => $searchPlaceholder]) !!}

                                    <div class="input-group-append">
                                        <button type="submit" id="search-btn" class="btn btn-sm btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </li>
                    </ul>
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item mt-1 mb-1 mr-1">
                            @can('commission.delete')
                                <a href="javascript:void(0)" class="btn btn-danger bulk-actions btn-sm"
                                   id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                            @endcan
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'commission_filter_options form-control form-control-sm inline']) !!}
                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('status', $filterStatuses, request('status','active'), ['class' => 'commission_filter_options form-control form-control-sm inline']) !!}
                        </li>

                    </ul>

                    {!! Form::close() !!}

                </div>

                <div class="card-body no-padding">

                    @include('commission.recurring._list')

                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 mt-3">
                            @if(request('company_profile') || (request('status') && request('status') != 'all') || request('search'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $recurringInvoiceItemCommissions->total(),'plural' => $recurringInvoiceItemCommissions->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>

                        <div class="col-sm-12 col-md-7">
                            <div class="float-right mt-3">
                                {!! $recurringInvoiceItemCommissions->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>
@stop
