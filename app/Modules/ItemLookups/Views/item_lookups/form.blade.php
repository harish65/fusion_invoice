@extends('layouts.master')

@section('head')
    @include('layouts._select2')
@stop

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();

            // Define the select settings
            var settings = {
                placeholder: '{{ trans('fi.select-item-category') }}',
                allowClear: true,
                tags: true,
                selectOnClose: true
            };

            // Make all existing items select
            $('.category-lookup').select2(settings);

            $("form").one('submit', function myFormSubmitCallback(evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $(this).serializeFormJSON();
                var customFields = {};
                var missingValues = {};

                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('item_lookups-field-name');
                    var inputType = $(this).attr('type') || this.tagName.toLowerCase();
                    if (fieldName !== undefined) {
                        if ('file' === inputType) {
                            customFields[fieldName] = typeof this.files[0] === 'undefined' ? '' : this.files[0];
                            return true;
                        }
                        if ('select' === inputType) {
                            if ($(this).find('option:selected').length == 0) {
                                customFields[fieldName] = '';
                                return true;
                            }
                        }
                        if ('checkbox' === inputType) {
                            customFields[fieldName] = ($(this).is(":checked")) ? 1 : 0;
                            return true;
                        }
                        if ('radio' === inputType) {
                            if ($(this).prop('checked') == true) {
                                customFields[fieldName] = $(this).val();
                                selectCustomRadioButtonValue = $(this).val();
                            }
                            if ($(this).prop('checked') == false && selectCustomRadioButtonValue == null) {
                                customFields[fieldName] = 'null';
                            }
                            return customFields[fieldName];
                        }
                        customFields[$(this).data('item_lookups-field-name')] = $(this).val();
                    }
                });
                $.each(customFields, function (customKey, valueCustom) {
                    $.each(formAllData, function (formKey, formValue) {
                        var filter_column = formKey.substring(0, formKey.lastIndexOf("]") + 1);
                        var result = filter_column.startsWith("custom", 0);
                        if (result == true) {
                            if ('custom[' + customKey + ']' != filter_column) {
                                return missingValues['custom[' + customKey + ']'] = valueCustom;
                            }
                        } else {
                            return missingValues['custom[' + customKey + ']'] = valueCustom;
                        }
                    });
                });
                $.each(missingValues, function (key, value) {
                    var input = $("<input>").attr("type", "hidden").attr("class", "missingValue").attr("name", key).val(value);
                    $("form").append($(input));
                });

                $(this).submit();
            });
        });
    </script>

    @if ($editMode == true)
        {!! Form::model($itemLookup, ['route' => ['itemLookups.update', $itemLookup->id] ,'enctype' => 'multipart/form-data']) !!}

        {!!  Form::hidden('item_lookup_id',$itemLookup->id,['id' => 'item_lookup_id']) !!}
        {!!  Form::hidden('custom_module', 'item_lookup',['id' => 'custom_module']) !!}
    @else
        {!! Form::open(['route' => 'itemLookups.store' , 'enctype' => 'multipart/form-data']) !!}
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

                        {{ trans('fi.item_lookup_form') }}

                    </h1>

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

            <div class="row">

                <div class="col-md-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="">{{ trans('fi.name') }}: </label>
                                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                                    </div>

                                    <div class="form-group">
                                        <label>{{ trans('fi.category') }}: </label>
                                        {!! Form::select('category_name', $itemCategory, null, ['id' => 'category_name', 'class' => 'form-control form-control-sm category-lookup']) !!}
                                    </div>

                                    <div class="form-group">
                                        <label class="">{{ trans('fi.description') }}: </label>
                                        {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control form-control-sm', 'rows' => 3]) !!}
                                    </div>

                                    @if (config('pricing_formula'))
                                        <div class="form-group">
                                            <label class="">{{ trans('PricingFormula::lang.formula') }}: </label>
                                            {!! Form::select('formula_id', $itemPriceFormulas, null, ['id' => 'formula_id', 'class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="">{{ trans('fi.price') }}: </label>
                                        {!! Form::text('price', (($editMode) ? $itemLookup->formatted_numeric_price: null), ['id' => 'price', 'class' => 'form-control form-control-sm']) !!}
                                    </div>

                                    <div class="form-group">
                                        <label class="">{{ trans('fi.quantity') }}: </label>
                                        {!! Form::text('quantity', (($editMode) ? $itemLookup->quantity: null), ['id' => 'quantity', 'class' => 'form-control form-control-sm']) !!}
                                    </div>


                                    <div class="form-group">
                                        <label class="">{{ trans('fi.tax_1') }}: </label>
                                        {!! Form::select('tax_rate_id', ['-1' => trans('fi.system_default')] + $taxRates, $editMode == true ? null : -1, ['class' => 'form-control form-control-sm']) !!}
                                    </div>

                                    <div class="form-group">
                                        <label class="">{{ trans('fi.tax_2') }}: </label>
                                        {!! Form::select('tax_rate_2_id', ['-1' => trans('fi.system_default')] + $taxRates, $editMode == true ? null : -1, ['class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                @if ($customFields)
                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body" id="custom-body-table">
                                @include('custom_fields._custom_fields_unbound', ['object' => $itemLookup])
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop