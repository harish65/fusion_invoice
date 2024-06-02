@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-currencies').click(function () {

                $(this).addClass('delete-currencies-active');

                $('#modal-placeholder').load('{!! route('currencies.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'currencies',
                        isReload: false,
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );

            });
        });
    </script>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('fi.currencies') }}</h1>
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

                    <div class="card-tools">

                        <ul class="nav nav-pills ml-auto">

                            <li class="nav-item mt-1 mb-1 mr-1">

                                <a href="{{ route('currencies.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-striped table-sm table-responsive-xs table-responsive-sm">

                        <thead>
                        <tr>
                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{!! Sortable::link('code', trans('fi.code')) !!}</th>
                            <th>{!! Sortable::link('symbol', trans('fi.symbol')) !!}</th>
                            <th>{!! Sortable::link('placement', trans('fi.symbol_placement')) !!}</th>
                            <th>{!! Sortable::link('decimal', trans('fi.decimal_point')) !!}</th>
                            <th>{!! Sortable::link('thousands', trans('fi.thousands_separator')) !!}</th>
                            <th class="text-right"> {{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($currencies as $currency)
                            <tr style="font-weight: {{ $currency->code == config('fi.baseCurrency') ? 600 : 'normal' }}">
                                <td class="position-relative align-items-center">
                                    @if($currency->code == config('fi.baseCurrency'))
                                        <i class="circleIcon position-absolute fa fa-circle"></i>&nbsp;
                                    @endif
                                    <a href="{{ route('currencies.edit', [$currency->id]) }}">
                                        {{ $currency->name }}
                                    </a>
                                </td>
                                <td>{{ $currency->code }}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>{{ $currency->formatted_placement }}</td>
                                <td>{!! html_entity_decode($currency->decimal) !!} </td>
                                <td>{!! html_entity_decode($currency->thousands) !!} </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('currencies.edit', [$currency->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="delete-currencies text-danger dropdown-item"
                                               data-action="{{ route('currencies.delete',[$currency->id])}}">
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

                    <div class="float-right mt-3">

                        {!! $currencies->appends(request()->except('page'))->render() !!}

                    </div>

                </div>

            </div>

        </div>

    </section>
@stop

<style>
    .circleIcon { font-size: 8px;top: 29%;left: 0px;}
    .custom-invoice-padding {padding-top: 4px !important;}
</style>