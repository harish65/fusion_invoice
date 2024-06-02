@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

    @if ($editMode == true)
        {!! Form::model($taxRate, ['route' => ['taxRates.update', $taxRate->id]]) !!}
    @else
        {!! Form::open(['route' => 'taxRates.store']) !!}
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

                    <h1>{{ trans('fi.tax_rate_form') }}</h1>

                </div>
                <div class="col-sm-6">

                    <div class="text-right">

                        <button class="btn btn-sm btn-primary"><i class="fa fa-save"></i> {{ trans('fi.save') }}</button>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            @if ($editMode and $taxRate->in_use)
                <div class="alert alert-warning">{{ trans('fi.cannot_edit_record_in_use') }}</div>
            @endif

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ trans('fi.tax_rate_name') }}: </label>
                                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.tax_rate_percent') }}: </label>
                                @if ($editMode and $taxRate->in_use)
                                    {!! Form::text('percent', (($editMode) ? $taxRate->formatted_numeric_percent : null),
                                    ['id' => 'percent', 'class' => 'form-control form-control-sm', 'readonly' => 'readonly']) !!}
                                @else
                                    {!! Form::text('percent', (($editMode) ? $taxRate->formatted_numeric_percent : null),
                                    ['id' => 'percent', 'class' => 'form-control form-control-sm']) !!}
                                @endif

                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.calculate_as_vat_gst') }}:</label>
                                @if ($editMode and $taxRate->in_use)
                                    {!! Form::select('calculate_vat', ['0' => trans('fi.no'), '1' => trans('fi.yes')],
                                    null, ['class' => 'form-control form-control-sm', 'readonly' => 'readonly', 'disabled' =>
                                    'disabled']) !!}
                                @else
                                    {!! Form::select('calculate_vat', ['0' => trans('fi.no'), '1' => trans('fi.yes')],
                                    null, ['class' => 'form-control form-control-sm']) !!}
                                @endif
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.compound') }}:</label>
                                @if ($editMode and $taxRate->in_use)
                                    {!! Form::select('is_compound', ['0' => trans('fi.no'), '1' => trans('fi.yes')],
                                    null, ['class' => 'form-control form-control-sm', 'readonly' => 'readonly', 'disabled' =>
                                    'disabled']) !!}
                                @else
                                    {!! Form::select('is_compound', ['0' => trans('fi.no'), '1' => trans('fi.yes')],
                                    null, ['class' => 'form-control form-control-sm']) !!}
                                @endif

                                <span class="help-block">{{ trans('fi.compound_tax_note') }}</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        
    </section>

    {!! Form::close() !!}
@stop