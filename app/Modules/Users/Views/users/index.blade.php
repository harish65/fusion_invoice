@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {

            $('.user_filter_options').change(function () {
                $('form#filter').submit();
            });

            $('.delete-user').click(function () {

                var $_this = $(this);

                $_this.addClass('delete-users-active');

                var warning = null;

                if ($_this.data('user-type') === 'client') {
                    warning = '{!! trans('fi.delete_client_user_warning') !!}';
                } else {
                    warning = '{!! trans('fi.delete_user_warning') !!}';
                }

                $('#modal-placeholder').load('{!! route('user.delete.modal') !!}', {
                        action: $_this.data('action'),
                        modalName: 'users',
                        isReload: false,
                        message: warning,
                        returnURL: '{{route('users.index')}}'
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

    <div id="modal-user-create"></div>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_users_about') !!}">
                        <i class="fa fa-user"></i> {{ trans('fi.users') }}</h1>
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

                        {!! Form::open(['method' => 'GET', 'id' => 'filter']) !!}

                        <ul class="nav nav-pills ml-auto">
                            <div class="row">
                                <div class="form-group form-inline">
                                    <label class="pr-3" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_user_types_about') !!}">
                                        {{ trans('fi.user_type') }}: </label>
                                    <li class="nav-item mt-1 mb-1 mr-1 pr-5">
                                        {!! Form::select('userType', ['' => trans('fi.select-user-type')] + $allUserTypes, request('userType'), ['class' => 'user_filter_options form-control form-control-sm']) !!}
                                    </li>

                                    <li class="nav-item mt-1 mb-1 mr-1">

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                {{ trans('fi.new') }} <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @foreach($userTypes as $key => $value)
                                                    <a class="dropdown-item"
                                                       href="{{ route('users.create', [$key]) }}">{{ $value }}</a>
                                                @endforeach
                                            </div>
                                        </div>

                                    </li>
                                </div>
                            </div>
                        </ul>

                        {!! Form::close() !!}

                    </div>

                </div>

                <div class="card-body no-padding">

                    <table class="table table-hover table-striped table-sm table-responsive-xs table-responsive-sm">

                        <thead>
                        <tr>
                            <th>{!! Sortable::link('name', trans('fi.name')) !!}</th>
                            <th>{!! Sortable::link('email', trans('fi.email')) !!}</th>
                            <th>{{ trans('fi.type') }}</th>
                            <th>{{ trans('fi.last_login_at') }}</th>
                            <th>{{ trans('fi.status') }}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($users as $key => $user)
                            <tr>
                                <td>
                                    <a href="{{ route('users.edit', [$user->id, $user->user_type]) }}">{{ $user->name }}</a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ trans('fi.' . $user->user_type) }}</td>
                                <td>{{ $user->formatted_last_login_at }}</td>
                                <td>{!! $user->formatted_status !!}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default btn-sm dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('users.edit', [$user->id, $user->user_type]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a>
                                            <a class="dropdown-item"
                                               href="{{ route('users.password.edit', [$user->id]) }}"><i
                                                        class="fa fa-lock"></i> {{ trans('fi.reset_password') }}</a>
                                            @if($user->id !== auth()->user()->id)
                                                <div class="dropdown-divider"></div>
                                                <a href="#" data-action="{{ route('users.delete', [$user->id])}}"
                                                   data-inactive-action="{{ route('users.update-status', [$user->id])}}"
                                                   data-user-type="{{$user->user_type}}" class="delete-user text-danger
                                                   dropdown-item">
                                                    <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            @if(request('company_profile') || (request('status') && request('status') != 'all') || (request('tags') && request('tags') != '[]') || request('search'))
                                <i class="fa fa-filter"></i> {{ trans('fi.n_records_match', ['label' => $invoices->total(),'plural' => $invoices->total() > 1 ? 's' : '']) }}
                                <button type="button" class="btn btn-sm btn-link"
                                        id="btn-clear-filters">{{ trans('fi.clear') }}</button>
                            @endif
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="float-right">
                                {!! $users->appends(request()->except('page'))->render() !!}
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>

@stop