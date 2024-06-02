@extends('layouts.master')

@section('head')
    @include('layouts._select2')
    @include('expenses._js_vendor_lookup')
    @include('expenses._js_category_lookup')
    @include('clients._js_lookup')
@stop

@section('javascript')
    <script type="text/javascript">
        $(function () {

            $('.btn-submite-data').click(function () {
                $('.expense_from').submit();
            });
            $('#vendor_name').change(function () {
                let name = $(this).val();
                var url = '{{ route("expenses.fetch.vendor_category",[ ":name"] ) }}';
                url = url.replace(':name', name);
                $.get(url, function (response) {
                    if (response != null && response != '') {
                        $('#category_name').val(response).trigger('change');
                    } else {
                        $('#category_name').val('').trigger('change');
                    }
                });
            });

            $('#expense_date').datetimepicker({autoclose: true, format: dateFormat});

            @if ($editMode == true)
            $('#btn-copy-expense').click(function () {
                $.post("{{ route('expenseCopy.store') }}", {
                    expense_id: "{{ isset($expense->id) ? $expense->id : '' }}"
                }).done(function (response) {
                    window.location = '{{ url('expenses') }}' + '/' + response.id + '/edit';
                }).fail(function (response) {
                    showAlertifyErrors($.parseJSON(response.responseText).errors);
                });
            });
            @endif

            $("form").one('submit', function myFormSubmitCallback(evt) {
                evt.stopPropagation();
                evt.preventDefault();
                var formAllData = $(this).serializeFormJSON();
                var customFields = {};
                var missingValues = {};

                var selectCustomRadioButtonValue = null;
                $('#custom-body-table').find('.custom-file-input,.custom-form-field,.form-check-input').each(function () {
                    var fieldName = $(this).data('expenses-field-name');
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
                        customFields[$(this).data('expenses-field-name')] = $(this).val();
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

            $('#btn-delete-custom-img').click(function () {
                var url = "{{ route('expenses.deleteImage', [isset($expense->id) ? $expense->id : '','field_name' => '']) }}";
                $.post(url + '/' + $(this).data('field-name')).done(function () {
                    $('.custom_img').html('');
                });
            });
        });

    </script>
@stop

@section('content')

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
                        @if ($editMode == true)
                            {{ trans('fi.expense') }} #{{ $expense->id }}
                        @else
                            {{ trans('fi.expense_form') }}
                        @endif
                    </h1>

                </div>
                <div class="col-sm-6">
                    <div class="text-right">

                        @if ($editMode)
                            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-default"><i
                                        class="fa fa-backward"></i> {{ trans('fi.back') }}</a>
                        @endif
                        @if ($editMode == true)
                            @can('expenses.create')
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                            data-toggle="dropdown">
                                        {{ trans('fi.other') }} <span class="caret"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                                        <a href="#" id="btn-copy-expense" class="dropdown-item">
                                            <i class="fa fa-copy"></i> {{ trans('fi.copy') }}
                                        </a>
                                    </div>
                                </div>
                            @endcan
                        @endif
                        <button class="btn btn-sm btn-primary btn-submite-data"><i
                                    class="fa fa-save"></i> {{ trans('fi.save') }}
                        </button>
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
                            @if ($editMode == true)
                                {!! Form::model($expense, ['route' => ['expenses.update', $expense->id], 'files' => true,'class'=>'expense_from']) !!}

                                {!!  Form::hidden('expense_id', $expense->id,['id' => 'expense_id']) !!}
                                {!!  Form::hidden('custom_module', 'expense',['id' => 'custom_module']) !!}
                            @else
                                {!! Form::open(['route' => 'expenses.store', 'files' => true ,'class'=>'expense_from', 'enctype'=>'multipart/form-data']) !!}
                            @endif

                            {!! Form::hidden('user_id', auth()->user()->id) !!}

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>* {{ trans('fi.company_profile') }}: </label>
                                        {!! Form::select('company_profile_id', $companyProfiles, (($editMode) ? $expense->company_profile_id : config('fi.defaultCompanyProfile')), ['id' => 'company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.vendor') }}: </label>
                                        {!! Form::select('vendor_name', $vendors, (($editMode) ? $expense->vendor_name : null), ['id' => 'vendor_name', 'class' => 'form-control form-control-sm vendor-lookup']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>* {{ trans('fi.category') }}: </label>
                                        {!! Form::select('category_name', $expenseCategory, (($editMode) ? $expense->category_name : null), ['id' => 'category_name', 'class' => 'form-control form-control-sm category-lookup']) !!}
                                    </div>
                                </div>

                                <div class="date col-md-3">
                                    <label>* {{ trans('fi.date') }}: </label>
                                    <div class="input-group date" id='expense_date' data-target-input="nearest">
                                        {!! Form::text('expense_date', (($editMode) ? $expense->formatted_expense_date : $currentDate), ['class' => 'form-control form-control-sm', 'data-toggle' => 'datetimepicker','autocomplete' => 'off','data-target' => '#expense_date']) !!}
                                        <div class="input-group-append"
                                             data-target='#expense_date' data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>* {{ trans('fi.amount') }}: </label>
                                        {!! Form::text('amount', (($editMode) ? $expense->formatted_numeric_amount : null), ['id' => 'amount', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.tax') }}: </label>
                                        {!! Form::text('tax', (($editMode) ? $expense->formatted_numeric_tax : null), ['id' => 'tax', 'class' => 'form-control form-control-sm']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.client') }}: </label>
                                        {!! Form::select('client_id', $clients, null, ['id' => 'client_name', 'class' => 'form-control form-control-sm client-lookup']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('fi.description') }}: </label>
                                        {!! Form::textarea('description', null, ['id' => 'description', 'class' => 'form-control form-control-sm', 'rows' => 5]) !!}
                                    </div>
                                </div>
                            </div>
                            <div id="custom-body-table">
                                @if ($customFields)
                                    @include('custom_fields._custom_fields_unbound', ['object' => isset($expense) ? $expense : []])
                                @endif
                            </div>
                                @if (!$editMode)
                                    @if (!config('app.demo'))
                                        @can('attachments.create')
                                            <div class="form-group">
                                                <label>{{ trans('fi.attach_files') }}: </label>
                                                {!! Form::file('attachments[]', ['id' => 'attachments', 'class' => 'form-control form-control-sm h-100', 'multiple' => 'multiple']) !!}
                                            </div>
                                        @endcan
                                    @endif
                                @endif
                            {!! Form::close() !!}
                            @if ($editMode)
                                @can('attachments.view')
                                    @include('attachments._table', ['object' => $expense, 'model' => 'FI\Modules\Expenses\Models\Expense', 'modelId' => $expense->id])
                                @endcan
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop