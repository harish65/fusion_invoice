@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-expenses-categories').click(function () {

                $(this).addClass('delete-expenses-categories-active');

                $('#modal-placeholder').load('{!! route('expenses.categories.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'expenses-categories',
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
                    <h1>{{ trans('fi.expense_categories') }}</h1>
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

                                <a href="{{ route('expenses.categories.create') }}" class="btn btn-sm btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

                            </li>

                        </ul>

                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-striped table-sm table-responsive-xs table-responsive-sm">

                        <thead>

                        <tr>
                            <th class="col">{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{{ trans('fi.options') }}</th>
                        </tr>

                        </thead>

                        <tbody>

                        @foreach ($expenseCategories as $expenseCategory)
                            <tr>
                                <td class="col-md-8">
                                    <a href="{{ route('expenses.categories.edit', [$expenseCategory->id]) }}">
                                        {{ $expenseCategory->name }}
                                    </a>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('expenses.categories.edit', [$expenseCategory->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#"
                                               data-action="{{ route('expenses.categories.delete',[$expenseCategory->id]) }}"
                                               class="delete-expenses-categories text-danger dropdown-item">
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
                        {!! $expenseCategories->appends(request()->except('page'))->render() !!}
                    </div>

                </div>

            </div>

        </div>

    </section>

@stop
