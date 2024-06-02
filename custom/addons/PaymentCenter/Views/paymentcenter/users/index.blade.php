@extends('layouts.master')

@section('content')

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1><i class="fa fa-users"></i> {{ trans('PaymentCenter::lang.payment_center_users') }}</h1>
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
                                <a href="{{ route('paymentCenter.users.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>
                            </li>

                        </ul>

                    </div>
                </div>

                <div class="card-body">

                    <table class="table table-striped table-responsive-sm table-responsive-xs table-sm">
                        <thead>
                        <tr>
                            <th>{{ trans('PaymentCenter::lang.name') }}</th>
                            <th>{{ trans('PaymentCenter::lang.email') }}</th>
                            <th>{{ trans('PaymentCenter::lang.status') }}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('paymentCenter.users.edit', [$user->id]) }}">{{ $user->name }}</a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{!! $user->formatted_status !!}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('paymentCenter.users.edit', [$user->id]) }}">
                                                <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                            </a>
                                            <a class="dropdown-item"
                                               href="{{ route('paymentCenter.users.password.edit', [$user->id]) }}">
                                                <i class="fa fa-lock"></i> {{ trans('fi.reset_password') }}
                                            </a>

                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger"
                                               href="{{ route('paymentCenter.users.delete', [$user->id]) }}"
                                               onclick="return confirm('{{ trans('fi.delete_record_warning') }}');">
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
                    <div class="pull-right">
                        {!! $users->appends(request()->except('page'))->render() !!}
                    </div>
                </div>

            </div>

        </div>

    </section>

@stop