@extends('paymentcenter.paymentCenterLayouts.logged_in')

@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="nav-icon fas fa-tachometer-alt"></i> {{ trans('fi.dashboard') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')
            <div class="card card-primary card-outline">
                <div class="card-header">
                    {!! Form::open(['route' => ['paymentCenter.search'],'class'=>'form-inline m-0' ,'id' =>'payment-center-filter-form']) !!}
                    <ul class="nav nav-pills nav-search pt-1">
                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                Fill in any part of the form to search for open invoices.
                            </div>
                        </li>
                    </ul>
                    <ul class="nav nav-pills  float-right ml-auto">
                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::text('name', request('name'), ['id' => 'name', 'class' => 'form-control form-control-sm' ,'placeholder'=>trans('fi.client_or_company_name')]) !!}
                            </div>
                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::text('phone', request('phone'), ['id' => 'phone', 'class' => 'form-control form-control-sm','placeholder'=>trans('fi.phone_number')]) !!}
                            </div>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::text('invoice_number', request('invoice_number'), ['id' => 'invoice_number', 'class' => 'form-control form-control-sm','placeholder'=>trans('fi.invoice').' '.'#']) !!}
                            </div>
                        </li>

                        <li class="nav-item mt-1 mb-1 mr-1">
                            <div class="form-group">
                                {!! Form::submit('Search', ['class' => 'submit-btn btn btn-sm btn-primary']) !!}
                            </div>
                        </li>

                        <li class="nav-item mt-1 mb-1 ">
                            <a href="javascript:void(0);"
                               class="btn btn-sm btn-primary refresh-payment-center-dashboard text-left"

                               id="reload-payment-center-dashboard"
                               title="Refresh">
                                <i class="fas fa-sync-alt reload-payment-center-dashboard"></i>
                            </a>
                        </li>

                    </ul>
                    {!! Form::close() !!}

                </div>
                @if (isset($invoices))
                    <div class="card-body">

                        <table class="table table-hover table-striped table-sm table-responsive-sm table-responsive-xs">
                            <thead>

                            <tr>
                                <th>{{ trans('fi.status') }}</th>
                                <th>{{ trans('fi.invoice').' '.'#'}}</th>
                                <th>{{ trans('fi.date') }}</th>
                                <th>{{ trans('fi.due') }}</th>
                                <th>{{ trans('fi.client') }}</th>
                                <th>{{ trans('fi.city') }}</th>
                                <th>{{ trans('fi.state') }}</th>
                                <th style="text-align: right;">{{ trans('fi.total') }}</th>
                                <th style="text-align: right;">{{ trans('fi.balance') }}</th>
                                <th style="text-align: center;">{{ trans('fi.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($invoices as $invoice)

                                <tr class="{{ $invoice->type == 'credit_memo' ? 'callout-pink-cm' : null }}">
                                    <td>
                                        <span class="badge badge-{{ $invoice->status }}">{{ trans('fi.' . $invoice->status) }}</span>
                                        @if ($invoice->virtual_status != null)
                                            @foreach($invoice->virtual_status as $virtual_status)
                                                <span class="badge badge-{{ $virtual_status }}">{{ trans('fi.' . $virtual_status) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td><a href="{{ $invoice->public_url }}"
                                           target="_blank">{{ $invoice->number }}</a></td>
                                    <td>{{ $invoice->formatted_created_at }}</td>
                                    <td>{{ $invoice->formatted_due_at }}</td>
                                    <td>{{ $invoice->client->name }}</td>
                                    <td>{{ $invoice->client->city }}</td>
                                    <td>{{ $invoice->client->state }}</td>
                                    <td style="text-align: right;">{{ $invoice->amount->formatted_total }}</td>
                                    <td style="text-align: right;">{{ $invoice->amount->formatted_balance }}</td>
                                    <td align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="{{ $invoice->public_url }}"
                                                   target="_blank">
                                                    <i class="fa fa-eye"> </i> {{ trans( 'fi.view_invoice') }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body" style=" height: 300px;">
                        <div class="text-center">
                            <h3 class="text-black" style=" line-height: 218px;"> {{trans('fi.no_data_to_display')}}</h3>
                        </div>
                    </div>
                @endif
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 mt-3">
                            @if(request('name') || request('phone') || request('invoice_number'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $invoices->total(),'plural' => $invoices->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            @if(isset($invoices) && $invoices != null)
                                <div class="float-right mt-3">
                                    {!! $invoices->appends(request()->except('page'))->render() !!}
                                </div>
                            @endif
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </section>
    <script type="text/javascript">
        $('#reload-payment-center-dashboard').click(function () {
            $('.reload-payment-center-dashboard').addClass('fa-spin');
            setTimeout(function () {
                $('.reload-payment-center-dashboard').removeClass('fa-spin')
            }, 1500);
            $('#name, #phone, #invoice_number').val('');

            $("#payment-center-filter-form").submit();
        });

        $('#btn-clear-filters').click(function () {
            $('#name').val('');
            $('#phone').val('');
            $('#invoice_number').val('');
            $('#payment-center-filter-form').submit();
        });
    </script>
@stop