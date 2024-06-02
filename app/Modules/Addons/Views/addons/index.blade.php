@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.disable-addons').click(function () {
                var $_this = $(this);

                $_this.addClass('delete-disable-addons-active');

                $('#modal-placeholder').load('{!! route('addons.disable.modal') !!}', {
                        action: $_this.data('action'),
                        modalName: 'disable-addons',
                        isReload: false,
                        returnURL: '{{route('addons.index')}}'
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
                    <h1 data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_addons_about') !!}">
                        {{ trans('fi.addons') }}</h1>
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

                <div class="card-body">

                    <table class="table table-sm table-hover table-striped table-responsive-xs table-responsive-sm">

                        <thead>

                        <tr>
                            <th>{{ trans('fi.name') }}</th>
                            <th>{{ trans('fi.author') }}</th>
                            <th>{{ trans('fi.web_address') }}</th>
                            <th>{{ trans('fi.status') }}</th>
                            <th>{{ trans('fi.options') }}</th>
                        </tr>

                        </thead>

                        <tbody>

                        @foreach ($addons as $addon)
                            <tr>
                                <td>{{ $addon->name }}</td>
                                <td>{{ $addon->author_name }}</td>
                                <td>{{ $addon->author_url }}</td>
                                <td>
                                    @if ($addon->enabled)
                                        <span class="text-success">{{ trans('fi.enabled') }}</span>
                                    @else
                                        <span class="text-danger">{{ trans('fi.disabled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($addon->enabled)
                                        <a href="#" data-action="{{ route('addons.uninstall', [$addon->id]) }}"
                                           class="btn btn-sm btn-danger disable-addons">{{ trans('fi.disable') }}</a>
                                        @if ($addon->has_pending_migrations)
                                            <a href="{{ route('addons.upgrade', [$addon->id]) }}" class="btn btn-sm btn-info">{{ trans('fi.complete_upgrade') }}</a>
                                        @endif
                                    @else
                                        <a href="{{ route('addons.install', [$addon->id]) }}" class="btn btn-sm btn-success">{{ trans('fi.install') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

@stop