@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-item-price-formula').click(function () {

                $(this).addClass('delete-item-price-formula-active');

                $('#modal-placeholder').load('{!! route('item.priceFormula.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'item-price-formula',
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
                    <h1>{{ trans('PricingFormula::lang.item_price_formulas') }}</h1>
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

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        <div class="card-header">

                            <div class="card-tools">
                                <a href="{{ route('item.priceFormula.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
                            </div>
                        </div>

                        <div class="card-body table-responsive">

                            <table class="table table-hover table-striped table-sm text-nowrap">

                                <thead>
                                <tr>
                                    <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                                    <th>{!! trans('PricingFormula::lang.formula') !!}</th>
                                    <th class="text-right">{{ trans('fi.options') }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach ($itemPriceFormulas as $itemPriceFormula)
                                    <tr>
                                        <td>
                                            <a href="{{ route('item.priceFormula.edit', [$itemPriceFormula->id]) }}">{{ $itemPriceFormula->name }}</a>
                                        </td>
                                        <td>{{ $itemPriceFormula->formula }}</td>
                                        <td class="text-right">

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                        data-toggle="dropdown">
                                                    {{ trans('fi.options') }} <span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item"
                                                       href="{{ route('item.priceFormula.edit', [$itemPriceFormula->id]) }}"><i
                                                                class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="#"
                                                       data-action="{{ route('item.priceFormula.delete',[$itemPriceFormula->id]) }}"
                                                       class="text-danger dropdown-item delete-item-price-formula">
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

                    </div>

                    <div class="pull-right">
                        {!! $itemPriceFormulas->appends(request()->except('page'))->render() !!}
                    </div>

                </div>

            </div>

        </div>

    </section>

@stop