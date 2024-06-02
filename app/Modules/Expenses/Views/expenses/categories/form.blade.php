@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });
    </script>

    @isset($expenseCategory)
        {!! Form::model($expenseCategory, ['route' => ['expenses.categories.update', $expenseCategory->id]]) !!}
    @else
        {!! Form::open(['route' => 'expenses.categories.store']) !!}
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

                        {{ trans('fi.expense_category_form') }}

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

                            <div class="form-group">
                                <label>{{ trans('fi.category') }}: </label>
                                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control form-control-sm']) !!}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        
    </section>

    {!! Form::close() !!}
@stop