@extends('layouts.master')

@section('content')
    <script type="text/javascript">
        $(function () {

            $('.delete-document-numbers-scheme').click(function () {

                $(this).addClass('delete-document-number-schemes-active');

                $('#modal-placeholder').load('{!! route('document.number.schemes.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'document-number-schemes',
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
                    <h1>{{ trans('fi.document_number_schemes') }}</h1>
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

                                <a href="{{ route('documentNumberSchemes.create') }}" class="btn btn-sm btn-primary"><i
                                            class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

                            </li>

                        </ul>

                    </div>

                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover table-striped table-responsive-xs table-responsive-sm">

                        <thead>
                        <tr>
                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{!! Sortable::link('type', trans('fi.type')) !!}</th>
                            <th>{!! Sortable::link('format', trans('fi.format')) !!}</th>
                            <th>{!! Sortable::link('next_id', trans('fi.next_number')) !!}</th>
                            <th>{!! Sortable::link('left_pad', trans('fi.left_pad')) !!}</th>
                            <th>{!! Sortable::link('reset_number', trans('fi.reset_number')) !!}</th>
                            <th class="text-right"> {{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($documentNumberSchemes as $documentNumberScheme)
                            <tr>
                                <td>
                                    <a href="{{ route('documentNumberSchemes.edit', [$documentNumberScheme->id]) }}">
                                        {{ $documentNumberScheme->name }}
                                    </a>
                                </td>
                                <td>{{ $documentNumberScheme->type }}</td>
                                <td>{{ $documentNumberScheme->format }}</td>
                                <td>{{ $documentNumberScheme->next_id }}</td>
                                <td>{{ $documentNumberScheme->left_pad }}</td>
                                <td>{{ $resetNumberOptions[$documentNumberScheme->reset_number] }}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('documentNumberSchemes.edit', [$documentNumberScheme->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="#"
                                               data-action="{{ route('documentNumberSchemes.delete', [$documentNumberScheme->id])}}"
                                               class="delete-document-numbers-scheme text-danger dropdown-item">
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

                        {!! $documentNumberSchemes->appends(request()->except('page'))->render() !!}

                    </div>

                </div>


            </div>

        </div>

    </section>

@stop