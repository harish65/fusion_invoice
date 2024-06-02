@extends('layouts.master')

@section('content')

    <script type="text/javascript">

        $(function () {

            $('.delete-item-categories').click(function () {

                $(this).addClass('delete-item-categories-active');
                $('#modal-placeholder').load('{!! route('item.categories.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'item-categories',
                        isReload: false,
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    });
            });
        });
    </script>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('fi.item_categories') }}</h1>
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

                                <a href="{{ route('item.categories.create') }}" class="btn btn-sm btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

                            </li>

                        </ul>

                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-striped table-sm">

                        <thead>

                        <tr>
                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>

                        </thead>

                        <tbody>

                        @foreach ($itemCategories as $itemCategory)

                            <tr>
                                <td>
                                    <a href="{{ route('item.categories.edit', [$itemCategory->id]) }}">{{ $itemCategory->name }}</a>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('item.categories.edit', [$itemCategory->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                            <div class="dropdown-divider"></div>
                                            <a href="#"
                                               data-action="{{ route('item.categories.delete',[$itemCategory->id]) }}"
                                               class="delete-item-categories text-danger dropdown-item">
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

                        {!! $itemCategories->appends(request()->except('page'))->render() !!}

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop