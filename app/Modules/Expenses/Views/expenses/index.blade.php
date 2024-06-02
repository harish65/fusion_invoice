@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('.btn-bill-expense').click(function () {
                $('#modal-placeholder').load("{{ route('expenseBill.create') }}", {
                    id: $(this).data('expense-id'),
                    redirectTo: '{{ request()->fullUrl() }}'
                });
            });

            $('.expense_filter_options').change(function () {
                $('form#filter').submit();
            });

            $('#btn-bulk-delete').click(function () {

                var ids = [];

                $('.bulk-record:checked').each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length > 0) {

                    $('#modal-placeholder').load('{!! route('bulk.delete.expenses.modal') !!}', {
                            action: '{{ route('expenses.bulk.delete') }}',
                            modalName: 'expenses',
                            data: ids,
                            returnURL: '{{route('expenses.index')}}'
                        },
                        function (response, status, xhr) {
                            if (status == "error") {
                                var response = JSON.parse(response);
                                alertify.error(response.message);
                            }
                        }
                    );
                }
            });

            $('.delete-expenses').click(function () {
                $(this).addClass('delete-expenses-active');

                $('#modal-placeholder').load('{!! route('expenses.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'expenses',
                        isReload: false,
                        returnURL:'{{route('expenses.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );

            });

            $('.btn-copy-expense').click(function () {
                $.post("{{ route('expenseCopy.store') }}", {
                    expense_id: $(this).data('id')
                }).done(function (response) {
                    window.location = '{{ url('expenses') }}' + '/' + response.id + '/edit';
                }).fail(function (response) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                });
            });

            $('#btn-clear-filters').click(function () {
                $('#search').val('');
                $('.expense_filter_options').prop('selectedIndex', 0);
                $('#filter').submit();
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1 class="d-inline"><i class="fa fa-file-invoice-dollar"> </i> {{ trans('fi.expenses') }}</h1>
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


                    {!! Form::open(['method' => 'GET', 'id' => 'filter', 'class' => 'form-inline m-0']) !!}

                    <ul class="nav nav-pills">
                        <li class="nav-item mr-1">
                            @if (isset($searchPlaceholder))
                                <div class="input-group mt-1 mb-1">
                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control form-control-sm inline ','autofocus','placeholder' => $searchPlaceholder]) !!}
                                    <div class="input-group-append">
                                        <button type="submit" id="search-btn" class="btn btn-sm btn-default"><i
                                                    class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            @endif
                        </li>
                    </ul>
                    <ul class="nav nav-pills ml-auto">
                        @can('expenses.delete')
                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger bulk-actions"
                                   id="btn-bulk-delete"><i class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>
                            </li>
                        @endcan

                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'expense_filter_options form-control form-control-sm inline ']) !!}
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('status', $statuses, request('status'), ['class' => 'expense_filter_options form-control form-control-sm inline ']) !!}
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('category', $categories, request('category'), ['class' => 'expense_filter_options form-control form-control-sm inline ']) !!}
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('vendor', $vendors, request('vendor'), ['class' => 'expense_filter_options form-control form-control-sm inline ']) !!}
                        </li>

                        @can('expenses.create')
                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                    {!! Form::close() !!}


                </div>

                <div class="card-body no-padding">
                    <table class="table table-sm table-hover table-striped table-responsive-xs table-responsive-sm">
                        <thead>
                        <tr>
                            @can('expenses.delete')
                                <th>
                                    <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
                                </th>
                            @endcan
                            <th>{!! Sortable::link('id', trans('fi.id')) !!}</th>
                            <th>{!! Sortable::link('vendor', trans('fi.vendor')) !!}</th>
                            <th>{!! Sortable::link('expense_date', trans('fi.date')) !!}</th>
                            <th>{!! Sortable::link('expense_categories.name', trans('fi.category')) !!}</th>
                            <th>{!! Sortable::link('description', trans('fi.description')) !!}</th>
                            <th>{!! Sortable::link('amount', trans('fi.amount')) !!}</th>
                            <th>{{ trans('fi.attachments') }}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($expenses as $expense)
                            <tr>
                                @can('expenses.delete')
                                    <td><input type="checkbox" class="bulk-record" data-id="{{ $expense->id }}"></td>
                                @endcan
                                <td>
                                    @can('expenses.update')
                                        <a href="{{ route('expenses.edit', [$expense->id]) }}"
                                           title="{{ trans('fi.edit') }}">{{ $expense->id }}</a></td>
                                @else
                                    {{ $expense->id }}
                                @endcan
                                <td>
                                @if (in_array(auth()->user()->user_type, ['admin']))
                                    @if ($expense->vendor_id)
                                        <a href="{{ route('expenses.vendors.edit', [$expense->vendor_id]) }}">
                                    @endif
                                    {{ $expense->vendor_name }}
                                </a>
                                @else
                                    {{ $expense->vendor_name }}
                                @endif
                                </td>
                                <td>{{ $expense->formatted_expense_date  }}</td>
                                <td>
                                    {{ $expense->category_name }}
                                    @if ($expense->vendor_name)
                                        <br><span class="text-muted">{{ $expense->vendor_name }}</span>
                                    @endif
                                </td>
                                <td>{!! $expense->formatted_description !!}</td>
                                <td>
                                    {{ $expense->formatted_amount }}
                                    @if ($expense->is_billable)
                                        @if ($expense->has_been_billed)
                                            @can('invoices.update')
                                                <br><a href="{{ route('invoices.edit', [$expense->invoice_id]) }}"><span
                                                            class="label label-success">{{ trans('fi.billed') }}</span></a>
                                            @else
                                                <br><span class="badge badge-success">{{ trans('fi.billed') }}</span>
                                            @endcan
                                        @else
                                            <br><span class="badge badge-danger">{{ trans('fi.not_billed') }}</span>
                                        @endif
                                    @else
                                        <br><span class="badge badge-default">{{ trans('fi.not_billable') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach ($expense->attachments as $attachment)
                                        <a href="{{ $attachment->download_url }}"><i
                                                    class="fa fa-file-o"></i> {{ $attachment->filename }}</a><br>
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @can('expenses.update')
                                                @if ($expense->is_billable and !$expense->has_been_billed)
                                                    <a href="javascript:void(0)" class="btn-bill-expense dropdown-item"
                                                       data-expense-id="{{ $expense->id }}">
                                                        <i class="fa fa-money-check-alt"></i> {{ trans('fi.bill_this_expense') }}
                                                    </a>
                                                @endif
                                                <a class="dropdown-item"
                                                   href="{{ route('expenses.edit', [$expense->id]) }}"><i
                                                            class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                            @endcan
                                            @can('expenses.create')
                                                <a href="#" class="btn-copy-expense dropdown-item"
                                                   data-id="{{ $expense->id }}"><i
                                                            class="fa fa-copy"></i> {{ trans('fi.copy') }}</a>
                                            @endcan
                                            @can('expenses.delete')
                                                <div class="dropdown-divider"></div>
                                                <a href="#"
                                                   data-action="{{ route('expenses.delete',[$expense->id]) }}"
                                                   class="delete-expenses text-danger dropdown-item">
                                                    <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="card-footer">

                    <div class="row">

                        <div class="col-sm-12 col-md-5">
                            @if(request('company_profile') || request('status') || request('category') || request('vendor') || request('search'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $expenses->total(),'plural' => $expenses->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-right">
                                {!! $expenses->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

@stop