@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-tax-rate').click(function () {

                $(this).addClass('delete-tax-rates-active');
                $('#modal-placeholder').load('{!! route('tax.rates.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'tax-rates',
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
                    <h1>{{ trans('fi.tax_rates') }}</h1>
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

                                <a href="{{ route('taxRates.create') }}" class="btn btn-sm btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

                            </li>

                        </ul>

                    </div>

                </div>
                <div class="card-body">

                    <table class="table table-sm table-hover table-striped">

                        <thead>
                        <tr>
                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{!! Sortable::link('percent', trans('fi.percent')) !!}</th>
                            <th>{!! Sortable::link('is_compound', trans('fi.compound')) !!}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($taxRates as $taxRate)
                            <tr>
                                <td>{{ $taxRate->name }}</td>
                                <td>{{ $taxRate->formatted_percent }}</td>
                                <td>{{ $taxRate->formatted_is_compound }}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('taxRates.edit', [$taxRate->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#" data-action="{{ route('taxRates.delete',[$taxRate->id]) }}"
                                               class="delete-tax-rate text-danger dropdown-item">
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

                        {!! $taxRates->appends(request()->except('page'))->render() !!}

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop