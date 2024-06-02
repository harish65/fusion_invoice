@extends('layouts.master')

@section('content')

    <script type="text/javascript">
        $(function () {

            $('.delete-company-profile').click(function () {

                $(this).addClass('delete-company-profiles-active');

                $('#modal-placeholder').load('{!! route('company.profiles.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'company-profiles',
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

            $(".copy-login-url").click(function () {
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(this).data('action')).select();
                document.execCommand("copy");
                $temp.remove();
                alertify.success('{{ trans('fi.url_copied_clipboard') }}', 5);
            });
        });
    </script>

    <section class="content-header">

        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <h1 data-toggle="tooltip" data-placement="auto"
                        title="{!! trans('fi.tt_company_profiles_about') !!}">
                        {{ trans('fi.company_profiles') }}</h1>

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

                                <a href="{{ route('company.profiles.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> {{ trans('fi.new') }}
                                </a>

                            </li>

                        </ul>

                    </div>

                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover table-striped table-responsive-xs table-responsive-sm">

                        <thead>
                        <tr>
                            <th width="80%">{{ trans('fi.company') }}</th>
                            <th width="10%" class="text-center">{{ trans('fi.login-url') }}</th>
                            <th width="5%" class="text-right">{{ trans('fi.options') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($companyProfiles as $companyProfile)
                            <tr style="font-weight: {{ $companyProfile->is_default == 1 ? 600 : 'normal' }}">

                                <td class="position-relative align-items-center">
                                    @if( $companyProfile->is_default == 1)
                                        <i class="circleIcon position-absolute fa fa-circle"></i>&nbsp;
                                    @endif
                                    <a href="{{ route('company.profiles.edit', [$companyProfile->id]) }}">{{ $companyProfile->company }}</a>
                                </td>
                                <td align="center"><a href="#" class="copy-login-url"
                                                      data-action="{{route('session.login',['profile' => $companyProfile->uuid])}}"
                                                      title="{{ trans('fi.copy_to_clipboard') }}"><i
                                                class="fas fa-copy fa-fw"></i></a></td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                                                data-toggle="dropdown">
                                            {{ trans('fi.options') }} <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                               href="{{ route('company.profiles.edit', [$companyProfile->id]) }}"><i
                                                        class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                            <div class="dropdown-divider"></div>
                                            <a href="#"
                                               data-action="{{ route('company.profiles.delete',[$companyProfile->id]) }}"
                                               class="delete-company-profile text-danger dropdown-item">
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

                    <div class="float-right">

                        {!! $companyProfiles->appends(request()->except('page'))->render() !!}

                    </div>

                </div>

            </div>

        </div>

    </section>

@stop

<style>
    .circleIcon {font-size: 8px;top: 29%;left: 0px;}
    .custom-invoice-padding {padding-top: 4px !important;}
</style>