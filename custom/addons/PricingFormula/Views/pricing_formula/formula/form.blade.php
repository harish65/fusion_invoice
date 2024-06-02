@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();

            $(".copy-to-clipboard").click(function () {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(this).prev('span').text()).select();
                document.execCommand("copy");
                $temp.remove();
                alertify.success('{{ trans('PricingFormula::lang.copied') }}', 5);
            });
        });
    </script>

    @isset($itemPriceFormula)
    {!! Form::model($itemPriceFormula, ['route' => ['item.priceFormula.update', $itemPriceFormula->id]]) !!}
    @else
        {!! Form::open(['route' => 'item.priceFormula.store']) !!}
    @endif

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right">{!!  breadcrumbs() !!}</ol>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="pull-left d-inline">
                        {{ trans('PricingFormula::lang.item_price_formula_form') }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="text-right">
                        <a href="{{ route('item.priceFormula.index') }}"
                           class="btn btn-default"> {{ trans('fi.cancel') }}</a>

                        <button class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>
                    </div>
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

                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ trans('fi.name') }}: </label>
                                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('PricingFormula::lang.formula') }}: </label>
                                {!! Form::textarea('formula', null, ['id' => 'formula', 'class' => 'form-control form-control-sm']) !!}
                                <small class="text-muted">{!! trans('PricingFormula::lang.invoice_price_formula_notes') !!}</small>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>

    {!! Form::close() !!}
@stop