@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('.filter_options').change(function () {
                $('form#filter').submit();
            });

            $('.project-delete').click(function () {
                let $_this = $(this);
                var $warning = "{{ trans('TimeTracking::lang.confirm_delete_project') }}";

                $_this.addClass('delete-projects-active');

                $('#modal-placeholder').load('{!! route('timeTracking.projects.delete.modal') !!}', {
                        action: $_this.data('action'),
                        modalName: 'projects',
                        isReload: false,
                        message: $warning,
                        returnURL: '{{route('timeTracking.projects.index')}}'
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $('.time-tracker-popup-modal').click(function () {
                $(this).addClass('disabled');
                $('#modal-placeholder').load('{!! route('timeTracking.timers.setting.modal') !!}',
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    });
            });

        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('TimeTracking::lang.time_tracking') }}
                        <small>{{ trans('TimeTracking::lang.projects') }}</small>
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

                        {!! Form::open(['method' => 'GET', 'id' => 'filter']) !!}

                        <ul class="nav nav-pills ml-auto btn-tool">

                            <li class="nav-item mt-1 mb-1 mr-1">
                                <a href="javascript:void(0)" title="{{ trans('fi.settings') }}"
                                   class="btn btn-sm btn-default time-tracker-popup-modal">
                                    <i class="fas fa-sliders-h"></i>
                                </a>
                            </li>

                            <li class="nav-item mt-1 mb-1 mr-1">
                                {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'filter_options form-control form-control-sm']) !!}
                            </li>
                            <li class="nav-item mt-1 mb-1 mr-1">
                                {!! Form::select('status', $statuses, request('status'), ['class' => 'filter_options form-control form-control-sm']) !!}
                            </li>

                            @can('time_tracking.create')
                                <li class="nav-item mt-1 mb-1 mr-1">
                                    <a href="{{ route('timeTracking.projects.create') }}"
                                       class="btn btn-sm btn-primary"><i
                                                class="fa fa-plus"></i> {{ trans('TimeTracking::lang.create_project') }}
                                    </a>
                                </li>
                            @endcan

                        </ul>
                        {!! Form::close() !!}
                    </div>
                </div>

                <div class="card-body no-padding table-responsive">

                    <table class="table table-hover table-striped table-sm">

                        <thead>
                        <tr>
                            <th>{{ trans('fi.status') }}</th>
                            <th>{{ trans('fi.created') }}</th>
                            <th>{{ trans('TimeTracking::lang.project') }}</th>
                            <th>{{ trans('fi.client') }}</th>
                            <th class="text-right">{{ trans('TimeTracking::lang.unbilled_hours') }}</th>
                            <th class="text-right">{{ trans('TimeTracking::lang.billed_hours') }}</th>
                            <th class="text-right">{{ trans('TimeTracking::lang.total_hours') }}</th>
                            <th class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ trans('fi.' . $project->status) }}</td>
                                <td>{{ $project->formatted_created_at}}</td>
                                <td>
                                    @can('time_tracking.update')
                                        <a href="{{ route('timeTracking.projects.edit', [$project->id]) }}">{{ $project->name }}</a>
                                    @else
                                        {{ $project->name }}
                                    @endcan
                                </td>
                                <td>
                                    @can('clients.view')
                                        <a href="{{ route('clients.show', [$project->client->id]) }}">{{ $project->client->name }}</a>
                                    @else
                                        {{ $project->client->name }}
                                    @endcan
                                </td>
                                <td class="text-right">{{ $project->unbilled_hours }}</td>
                                <td class="text-right">{{ $project->billed_hours }}</td>
                                <td class="text-right">{{ $project->hours }}</td>
                                <td class="text-right">
                                    @if(Gate::check('time_tracking.update') || Gate::check('time_tracking.delete'))
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @can('time_tracking.update')
                                                    <a class="dropdown-item"
                                                       href="{{ route('timeTracking.projects.edit', [$project->id]) }}">
                                                        <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                                    </a>
                                                @endcan
                                                @can('time_tracking.delete')
                                                    <div class="dropdown-divider"></div>
                                                    <a href="javascript:void(0);"
                                                       class="project-delete text-danger dropdown-item"
                                                       data-action="{{ route('timeTracking.projects.delete', [$project->id]) }}">
                                                        <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
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

                    <div class="text-right">
                        {!! $projects->appends(request()->except('page'))->render() !!}
                    </div>
                </div>

            </div>


        </div>

    </section>

@stop