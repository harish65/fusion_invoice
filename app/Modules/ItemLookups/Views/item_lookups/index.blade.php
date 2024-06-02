@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-item-looks').click(function () {

                $(this).addClass('delete-item-lookups-active');
                $('#modal-placeholder').load('{!! route('item.lookups.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'item-lookups',
                        isReload: false,
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                });
            });

            $('#btn-clear-filters').click(function () {
                $('#search').val('');
                $('.item_filter_options').prop('selectedIndex', 0);
                $('#filter').submit();
            });

            $('.item_filter_options').change(function () {
                $('form#filter').submit();
            });
        });
    </script>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('fi.item_lookups') }}</h1>
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
                        @if (isset($searchPlaceholder))

                            <li class="nav-item mr-1">

                                <div class="input-group mt-1 mb-1">

                                    {!! Form::text('search', request('search'), ['id' => 'search', 'class' => 'h-auto form-control inline form-control-sm','placeholder' => $searchPlaceholder]) !!}

                                    <div class="input-group-append">

                                        <button type="submit" class="btn btn-sm btn-default" id="search-btn">

                                            <i class="fas fa-search"></i>

                                        </button>

                                    </div>

                                </div>

                            </li>

                        @endif
                    </ul>
                    <ul class="nav nav-pills ml-auto">


                        <li class="nav-item mt-1 mb-1 mr-1">

                            {!! Form::select('category', $categories, request('category'), ['class' => 'item_filter_options form-control inline form-control-sm']) !!}

                        </li>

                        @can('clients.create')

                            <li class="nav-item mt-1 mb-1 mr-1">

                                <a href="{{ route('itemLookups.create') }}" class="btn btn-sm btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

                            </li>

                        @endcan

                    </ul>

                    {!! Form::close() !!}

                </div>
                <div class="card-body">

                    <table class="table table-striped table-responsive-sm table-responsive-xs table-sm">

                        <thead>

                        <tr>

                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{!! Sortable::link('item_categories.name', trans('fi.category')) !!}</th>
                            <th>{!! Sortable::link('description', trans('fi.description')) !!}</th>
                            <th>{!! Sortable::link('quantity', trans('fi.quantity')) !!}</th>
                            <th>{!! Sortable::link('price', trans('fi.price')) !!}</th>
                            <th>{{ trans('fi.tax_1') }}</th>
                            <th>{{ trans('fi.tax_2') }}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>

                        </tr>

                        </thead>

                        <tbody>

                        @foreach ($itemLookups as $itemLookup)

                            <tr>
                                <td>
                                    <a href="{{ route('itemLookups.edit', [$itemLookup->id]) }}">{{ $itemLookup->name }}</a>
                                </td>
                                <td>{{ $itemLookup->category_name }}</td>
                                <td>{!!  $itemLookup->formatted_description !!}</td>
                                <td>{{ $itemLookup->quantity }}</td>
                                <td>{{ $itemLookup->formatted_price }}</td>
                                <td>{{ $itemLookup->formatted_taxRate }}</td>
                                <td>{{ $itemLookup->formatted_taxRate2 }}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('itemLookups.edit', [$itemLookup->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                            <div class="dropdown-divider"></div>
                                            <a href="#"
                                               data-action="{{ route('itemLookups.delete',[$itemLookup->id]) }}"
                                               class="delete-item-looks text-danger dropdown-item">
                                                <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="card-footer clearfix">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 mt-3">
                            @if(request('category') || request('search'))

                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $itemLookups->total(),'plural' => $itemLookups->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>

                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-right mt-3">
                                {!! $itemLookups->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

@stop