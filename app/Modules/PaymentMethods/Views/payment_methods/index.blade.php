@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-payment-method').click(function () {

                $(this).addClass('delete-payment-methods-active');

                $('#modal-placeholder').load('{!! route('payment.methods.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'payment-methods',
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
                    <h1>{{ trans('fi.payment_methods') }}</h1>
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

                                <a href="{{ route('paymentMethods.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> {{ trans('fi.new') }}</a>

                            </li>

                        </ul>

                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-sm table-hover table-striped">

                        <thead>
                        <tr>
                            <th>{!! Sortable::link('name', trans('fi.payment_method')) !!}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($paymentMethods as $paymentMethod)
                            <tr>
                                <td>
                                    <a href="{{ route('paymentMethods.edit', [$paymentMethod->id]) }}">
                                        {{ $paymentMethod->name }}
                                    </a>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('paymentMethods.edit', [$paymentMethod->id]) }}"><i class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                            <div class="dropdown-divider"></div>
                                            <a href="#"
                                               data-action="{{ route('paymentMethods.delete',[$paymentMethod->id]) }}"
                                               class="delete-payment-method text-danger dropdown-item">
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

                        {!! $paymentMethods->appends(request()->except('page'))->render() !!}

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop