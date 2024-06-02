@extends('layouts.master')

@section('javascript')

    @include('layouts._select2')

@stop

@section('content')

    <script type="text/javascript">
        $(function () {

            $('#name').focus();

            $('#country').select2({
                placeholder: "{{ trans('fi.select_country') }}"
            });

            @if ($editMode == true)
            $('#btn-delete-logo').click(function () {
                $.post("{{ route('company.profiles.deleteLogo', [$companyProfile->id]) }}").done(function () {
                    $('#div-logo').html('');
                });
            });
            @endif

            $('#btn-delete-custom-img').click(function () {
                var url = "{{ route('company.profiles.deleteImage', [isset($companyProfile->id) ? $companyProfile->id : '','field_name' => '']) }}";
                $.post(url + '/' + $(this).data('field-name')).done(function () {
                    $('.custom_img').html('');
                });
            });

            $("form").one('submit', function myFormSubmitCallback(evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $(this).serializeFormJSON();
                var customFields = {};
                var missingValues = {};

                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('company_profiles-field-name');
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
                        customFields[$(this).data('company_profiles-field-name')] = $(this).val();
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
        {!! Form::model($companyProfile, ['route' => ['company.profiles.update', $companyProfile->id], 'files' => true]) !!}

        {!!  Form::hidden('company_profile_id', $companyProfile->id,['id' => 'company_profile_id']) !!}
        {!!  Form::hidden('custom_module', 'company_profile',['id' => 'custom_module']) !!}
    @else
        {!! Form::open(['route' => 'company.profiles.store', 'files' => true]) !!}
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

                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_company_profiles_about') !!}">
                        {{ trans('fi.company_profile') }}</h1>

                </div>

                <div class="col-sm-6">

                    <div class="text-right">

                        {!! Form::submit(trans('fi.save'), ['class' => 'btn btn-sm btn-primary']) !!}

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            @include('layouts._alerts')

            <div class="row">

                <div class="col-12">

                    <div class="card card-primary card-outline">

                        <div class="card-body">
                            <div class="form-group">
                                <label>{{ trans('fi.company') }}: </label>
                                {!! Form::text('company', null, ['id' => 'company', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ trans('fi.address') }}: </label>
                                {!! Form::textarea('address', null, ['id' => 'address', 'class' => 'form-control form-control-sm', 'rows' => 4]) !!}
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.city') }}: </label>
                                        {!! Form::text('city', null, ['id' => 'city', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.state') }}: </label>
                                        {!! Form::text('state', null, ['id' => 'state', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.postal_code') }}: </label>
                                        {!! Form::text('zip', null, ['id' => 'zip', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.country') }}: </label>
                                        {!! Form::select('country', $countries, null, ['id' => 'country', 'class' => 'form-control form-control-sm', 'placeholder' => '']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.phone') }}: </label>
                                        {!! Form::text('phone', null, ['id' => 'phone', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.fax') }}: </label>
                                        {!! Form::text('fax', null, ['id' => 'fax', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.mobile') }}: </label>
                                        {!! Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.web') }}: </label>
                                        {!! Form::text('web', null, ['id' => 'web', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.logo') }}: </label>
                                        @if (!config('app.demo'))
                                            <div id="div-logo">
                                                @if ($editMode and $companyProfile->logo)
                                                    <p>{!! $companyProfile->logo(100) !!}</p>
                                                    <a href="javascript:void(0)"
                                                       id="btn-delete-logo">{{ trans('fi.remove_logo') }}</a>
                                                @endif
                                            </div>
                                            {!! Form::file('logo') !!}
                                        @else
                                            Disabled for demo
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.default_invoice_template') }}:</label>
                                        {!! Form::select('invoice_template', $invoiceTemplates, ((isset($companyProfile)) ? $companyProfile->invoice_template : config('fi.invoiceTemplate')), ['id' => 'invoice_template', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.default_quote_template') }}:</label>
                                        {!! Form::select('quote_template', $quoteTemplates, ((isset($companyProfile)) ? $companyProfile->quote_template : config('fi.quoteTemplate')), ['id' => 'invoice_template', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                            </div>
                            <div id="custom-body-table">
                                @if ($customFields)
                                    @include('custom_fields._custom_fields_unbound', ['object' => isset($companyProfile) ? $companyProfile : []])
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop