@extends('layouts.master')
@section('head')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.css?v='.config('fi.version')) }}">
@stop
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
        @include('layouts._alerts')
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body table-responsive">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="list-group div-scroll mb-2">
                                @foreach($files as $file)
                                    <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                                       class="list-group-item @if ($current_file == $file) llv-active @endif">
                                        {{$file}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-10">
                            @if ($logs === null)
                                <div>
                                    {{ trans('fi.log_limit_message',['size' => number_format(config('logviewer.max_file_size') / 1048576, 1)]) }}
                                </div>
                            @else
                                <table id="table-log" class="table table-hover table-striped table-sm text-nowrap"
                                       data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
                                    <thead>
                                    <tr>
                                        @if ($standardFormat)
                                            <th>{{ trans('fi.log_level') }}</th>
                                            <th>{{ trans('fi.log_context') }}</th>
                                            <th>{{ trans('fi.log_date') }}</th>
                                        @else
                                            <th>{{ trans('fi.log_line_number') }}</th>
                                        @endif
                                        <th>{{ trans('fi.log_content') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($logs as $key => $log)
                                        <tr data-display="stack{{{$key}}}">
                                            @if ($standardFormat)
                                                <td class="nowrap text-{{{$log['level_class']}}}">
                                                    <span class="fa fa-{{{$log['level_img']}}}"
                                                          aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
                                                </td>
                                                <td class="text">{{$log['context']}}</td>
                                            @endif
                                            <td class="date">{{{$log['date']}}}</td>
                                            <td class="text" style="width: 50%">
                                                @if ($log['stack'])
                                                    <button class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2 btn-log-action"
                                                            type="button" data-toggle="tooltip" data-placement="auto"
                                                            title="" data-original-title="{{ trans('fi.log_detail') }}">
                                                        <span class="fa fa-search"></span>
                                                    </button>
                                                @endif
                                                {{{$log['text']}}}
                                                @if (isset($log['in_file']))
                                                    <br/>{{{$log['in_file']}}}
                                                @endif
                                                @if ($log['stack'])
                                                    <div class="log-default-string" id="stack{{$key}}"
                                                         style=" white-space: pre-wrap;">{{ Str::limit(trim($log['stack']),70) }}
                                                    </div>
                                                    <div class="collapse btn-action complete log-complete-string"
                                                         id="stack{{$key}}"
                                                         style=" white-space:pre-wrap;">{{trim($log['stack'])}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr
                                    @endforeach

                                    </tbody>
                                </table>
                            @endif
                            <div class="p-2">
                                @if($current_file)
                                    <a class="btn-sm btn-primary mr-2"
                                       href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
                                        <span class="fa fa-download"></span> {{ trans('fi.log_download_file') }}
                                    </a>
                                    <a href="javascript:void(0)" class=" btn-sm btn-danger system-log-clear">
                                        <i class="fa fa-eraser"></i> {{trans('fi.clear')}}</a>
                                    @if(count($files) > 1)
                                        <a id="delete-all-log"
                                           href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
                                            <span class="fa fa-trash-alt"></span> {{ trans('fi.log_delete_all') }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@section('footerJS')

    <script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js?v='.config('fi.version')) }}"></script>
    <script src="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.js?v='.config('fi.version')) }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.table-container tr').on('click', function () {
                $('#' + $(this).data('display')).toggle();
            });
            $('#table-log').DataTable({
                "order": [$('#table-log').data('orderingIndex'), 'desc'],
                "stateSave": true,
                "stateSaveCallback": function (settings, data) {
                    window.localStorage.setItem("datatable", JSON.stringify(data));
                },
                "stateLoadCallback": function (settings) {
                    var data = JSON.parse(window.localStorage.getItem("datatable"));
                    if (data) data.start = 0;
                    return data;
                }
            });
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
        $('.btn-log-action').click(function () {
            $(this).siblings('.log-complete-string').toggle();
            $(this).siblings('.log-default-string').toggle();
        });
    </script>
@stop
@stop