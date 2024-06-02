@extends('layouts.master')

@section('content')

    <section class="content-header">

        <div class="container-fluid">

            <div class="row">
                <div class="col-6">
                    <h1>{{ trans('fi.system_log') }}</h1>
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
            <a href="javascript:void(0)" class="btn btn-sm btn-danger float-right system-log-clear">
                <i class="fa fa-trash"></i> {{trans('fi.clear')}}
            </a>
            <textarea class="w-100 mt-2" rows="30" readonly="readonly" disabled>{{ $logs }}</textarea>

        </div>

    </section>

    <script>
        $(function () {
            @if($logFileExist == false)
            alertify.error('{{ $errorMessage }}');
            @endif

            $('.system-log-clear').click(function () {
                $('#modal-placeholder').load('{!! route('systemLog.clear.modal') !!}',
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