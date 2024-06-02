@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

    @isset($expenseVendor)
        {!! Form::model($expenseVendor, ['route' => ['expenses.vendors.update', $expenseVendor->id]]) !!}
    @else
        {!! Form::open(['route' => 'expenses.vendors.store']) !!}
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

                        {{ trans('fi.expense_vendor_form') }}

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

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.vendor') }}: </label>
                                        {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm','placeholder' => 'Name']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.email') }}: </label>
                                        {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control form-control-sm','placeholder' => 'Email']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('fi.mobile') }}: </label>
                                        {!! Form::text('mobile', null, ['id' => 'mobile', 'class' => 'form-control form-control-sm','placeholder' => 'Mobile Number']) !!}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>* {{ trans('fi.category') }}: </label>
                                        {!! Form::select('category_id', $expenseCategory, (($editMode) ? $expenseVendor->category_id : null), ['id' => 'category_name', 'class' => 'form-control form-control-sm category-lookup']) !!}
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label>{{ trans('fi.contact_names') }}: </label>
                                        {!! Form::text('contact_names', null, ['id' => 'contact_names', 'class' => 'form-control form-control-sm','placeholder' => 'Contact Names']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label>{{ trans('fi.address') }}: </label>
                                        {!! Form::textarea('address', null, ['id' => 'address', 'class' => 'form-control form-control-sm', 'rows' => 2,'placeholder' => 'Address']) !!}
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label>{{ trans('fi.note') }}: </label>
                                        {!! Form::textarea('notes', null, ['id' => 'notes', 'class' => 'form-control form-control-sm', 'rows' => 4,'placeholder' => 'Note']) !!}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {!! Form::close() !!}
@stop