@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('.filter_options').change(function () {
                $('form#filter').submit();
            });

            $('.commission-type-delete').click(function () {

                $(this).addClass('delete-commission-type-active');

                $('#modal-placeholder').load('{!! route('invoice.commission.type.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'commission-type',
                        isReload: false,
                        message: "{{ trans('Commission::lang.confirm_delete_commission_type') }}",
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
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">

            <div class="row">
                <div class="col-6">
                    <h1>
                        {{ trans('Commission::lang.commission') }}
                        <small>{{ trans('Commission::lang.commission_types') }}</small>
                    </h1>
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
                                @can('commission.create')
                                   <a href="{{ route('invoice.commission.type.create') }}" class="btn btn-sm btn-primary"><i
                                               class="fa fa-plus"></i> {{ trans('Commission::lang.create_commission_type') }}</a>
                                @endcan
                            </li>

                        </ul>

                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-striped table-responsive-sm table-sm">
                        <thead>
                        <tr>
                            <th>{{ trans('Commission::lang.name') }}</th>
                            <th>{{ trans('Commission::lang.formula') }}</th>
                            <th>{{ trans('Commission::lang.method') }}</th>
                            <th>{{ trans('fi.created') }}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($commission_types as $commission_type)
                            <tr>
                                <td>
                                    {{ $commission_type->name }}
                                </td>
                                <td>{{ $commission_type->formula}}</td>
                                <td>{{ $commission_type->method}}</td>
                                <td>{{ $commission_type->formatted_created_at}}</td>
                                <td class="text-right">
                                    @if(Gate::check('commission.update') || Gate::check('commission.delete'))
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @can('commission.update')
                                                    <a class="dropdown-item" href="{{ route('invoice.commission.type.edit', [$commission_type->id]) }}">
                                                        <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                                    </a>
                                                @endcan
                                                @can('commission.delete')
                                                    <div class="dropdown-divider"></div>
                                                <a href="javascript:void(0);" class="commission-type-delete dropdown-item text-danger"
                                                       data-action="{{ route('invoice.commission.type.delete', [$commission_type->id]) }}"><i
                                                                class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>

                <div class="card-footer clearfix">
                    <div class="pull-right">
                        {!! $commission_types->appends(request()->except('page'))->render() !!}
                    </div>
                </div>
            </div>
        </div>

    </section>

@stop
