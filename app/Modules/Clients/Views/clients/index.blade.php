@extends('layouts.master')

@section('javascript')
    @include('layouts._alertifyjs')

    <script type="text/javascript">
        $(function () {

            $('.client-filter-options').change(function () {
                $('form#filter').submit();
            });

            $('#client-columns-setting').click(function () {
                $('#modal-placeholder').load('{!! route('client.get.filterColumns') !!}');
            });

            $('.delete-client').click(function () {

                $(this).addClass('delete-clients-active');

                $('#modal-placeholder').load('{!! route('clients.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'clients',
                        isReload: false,
                        returnURL:'{{route('clients.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );

            });

            $('#tags-filter-open').click(function () {
                $('#modal-placeholder').load('{!! route('clients.filterTags', ['tags' => json_encode($tags), 'tagsMustMatchAll' => $tagsMustMatchAll, 'firstLoad' => true]) !!}')
            });

            $('#btn-clear-filters').click(function () {
                $('#search').val('');
                $('#tags-filter').val('');
                $('#tags-must-match-all').val(0);
                $('.client-filter-options').prop('selectedIndex', 0);
                $('#filter').submit();
            });

            $('.create-task').click(function () {
                $('#modal-placeholder').load($(this).data('action'));
            });
        });
    </script>
@stop

@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="fa fa-users"></i> {{ trans('fi.clients') }}</h1>
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
                                    {!! Form::text('search', request('search'), ['id' =>'search', 'class' => 'h-auto form-control form-control-sm float-right','autofocus','placeholder' => $searchPlaceholder]) !!}

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

                            <button type="button" class="btn btn-sm btn-default" id="tags-filter-open"
                                    data-tags="{{ json_encode($tags) }}" data-match-all="{{ $tagsMustMatchAll }}">
                                <span id="tags-filter-count">({{ count($tags) }})</span> {{ trans('fi.tags') }} <i
                                        class="fa fa-plus fa-xs"></i>
                                {!! Form::hidden('tags', json_encode($tags), ['id' => 'tags-filter']) !!}
                                {!! Form::hidden('tagsMustMatchAll', $tagsMustMatchAll, ['id' => 'tags-must-match-all']) !!}
                            </button>
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('type', $types, request('type'), ['class' => 'client-filter-options form-control form-control-sm inline']) !!}
                        </li>
                        <li class="nav-item mt-1 mb-1 mr-1">
                            {!! Form::select('status', $statuses, request('status'), ['class' => 'client-filter-options form-control form-control-sm inline']) !!}
                        </li>
                        @can('clients.create')
                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>
                            </li>
                        @endcan

                    </ul>
                    {!! Form::close() !!}

                </div>

                <div class="card-body">

                    <table class="table table-striped table-responsive-xs table-responsive-sm table-sm">

                        <thead>
                        <tr>
                            <th class="client-table-type-indicator-column"></th>
                            <th>{!! Sortable::link('id', trans('fi.id')) !!}</th>
                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{!! Sortable::link('email', trans('fi.email_address')) !!}</th>
                            @if(config('fi.clientColumnSettingsPhoneNumber') == 1 )
                                <th>{!! Sortable::link('phone', trans('fi.phone_number')) !!}</th>
                            @endif

                            <th>{!! Sortable::link('created_at', trans('fi.created')) !!}</th>

                            <th style="text-align: right;">{!! Sortable::link('balance', trans('fi.balance')) !!}</th>
                            <th>{!! Sortable::link('active', trans('fi.active')) !!}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($clients as $client)
                            <tr>

                                @if($client->type=='customer')
                                    <td class="client-table-type-indicator-column"></td>
                                @elseif($client->type=='lead')
                                    <td class="client-table-type-indicator-column"
                                        style="background-color: #f0ad4e;" title="Lead"></td>
                                @elseif($client->type=='prospect')
                                    <td class="client-table-type-indicator-column"
                                        style="background-color: #dd4b39;" title="Prospect"></td>
                                @elseif($client->type=='affiliate')
                                    <td class="client-table-type-indicator-column"
                                        style="background-color: #0080ff;" title="Affiliate"></td>
                                @elseif($client->type=='other')
                                    <td class="client-table-type-indicator-column"
                                        style="background-color: #a3a3a3;" title="Other"></td>
                                @else
                                    <td class="client-table-type-indicator-column"
                                        style="background-color: grey;" title="Unknown"></td>
                                @endif

                                <td>{{ $client->id }}</td>

                                @if($client->active==1)
                                    <td>
                                        <a href="{{ route('clients.show', [$client->id]) }}">{{ $client->name }}</a>
                                    </td>
                                @else
                                    <td style="text-decoration: line-through;"><a
                                                href="{{ route('clients.show', [$client->id]) }}">{{ $client->name }}</a>
                                    </td>
                                @endif

                                <td>{{ $client->email }}</td>
                                @if(config('fi.clientColumnSettingsPhoneNumber') == 1 )
                                    <td>{{ (($client->phone ? $client->phone : ($client->mobile ? $client->mobile : ''))) }}</td>
                                @endif

                                <td>{{ $client->formatted_created_at  }}</td>

                                <td style="text-align: right;">{{ $client->formatted_balance }}</td>
                                <td>{{ ($client->active) ? trans('fi.yes') : trans('fi.no') }}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('clients.show', [$client->id]) }}"
                                               id="view-client-{{ $client->id }}"><i
                                                        class="fa fa-search"></i> {{ trans('fi.view') }}
                                            </a>
                                            @can('clients.update')
                                                <a class="dropdown-item"
                                                   href="{{ route('clients.edit', [$client->id]) }}"
                                                   id="edit-client-{{ $client->id }}"><i
                                                            class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                                </a>
                                            @endcan
                                            @can('quotes.create')
                                                <a href="javascript:void(0)" class="create-quote dropdown-item"
                                                   data-client-id="{{ $client->id }}"><i
                                                            class="fa fa-file-alt"></i> {{ trans('fi.create_quote') }}
                                                </a>
                                            @endcan
                                            @can('invoices.create')
                                                <a href="javascript:void(0)" class="create-invoice dropdown-item"
                                                   data-client-id="{{ $client->id }}"><i
                                                            class="fa fa-file-invoice"></i> {{ trans('fi.create_invoice') }}
                                                </a>
                                            @endcan
                                            <a href="javascript:void(0)" class="create-task dropdown-item"
                                               data-action="{{ route('task.widget.create', ['client'=>$client->id]) }}"><i
                                                        class="fa fa-file-invoice"></i> {{ trans('fi.create_task') }}
                                            </a>
                                            @can('clients.delete')
                                                <div class="dropdown-divider"></div>
                                                <a href="#"
                                                   data-action="{{ route('clients.delete', [$client->id]) }}"
                                                   id="delete-client-{{ $client->id }}"
                                                   class="delete-client text-danger dropdown-item">
                                                    <i class="fa fa-trash"></i>
                                                    {{ trans('fi.delete') }}
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

                <div class="card-footer clearfix">

                    @if(request('type') || request('status') || request('tags') || request('search'))
                        <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $clients->total(),'plural' => $clients->total() > 1 ? 's' : '']) }}
                        <button type="button" class="btn btn-sm btn-link"
                                id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                    @endif

                    <div class="float-right">
                        {!! $clients->appends(request()->except('page'))->render() !!}
                    </div>
                </div>
            </div>

        </div>

    </section>

@stop