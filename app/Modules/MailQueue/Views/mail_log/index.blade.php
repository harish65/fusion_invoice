@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
        $(function () {
            $('.btn-show-content').click(function () {
                $('#modal-placeholder').load('{{ route('mailLog.content') }}', {
                    id: $(this).data('id')
                });
            });

            $('.delete-email-log').click(function () {

                $(this).addClass('delete-mail-log-active');

                $('#modal-placeholder').load('{!! route('mailLog.delete.modal') !!}', {
                        action: $(this).data('action'),
                        modalName: 'mail-log',
                        isReload: false,
                        returnURL: null
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
                    <h1>{{ trans('fi.mail_log') }}</h1>
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
                    <table class="table table-sm table-hover table-striped table-responsive-sm table-responsive-xs">

                        <thead>

                            <tr>
                                <th>{!! Sortable::link('created_at', trans('fi.date')) !!}</th>
                                <th>{!! Sortable::link('to', trans('fi.to')) !!}</th>
                                <th>{!! Sortable::link('subject', trans('fi.subject')) !!}</th>
                                <th>{!! Sortable::link('from', trans('fi.from')) !!}</th>
                                <th>{!! Sortable::link('cc', trans('fi.cc')) !!}</th>
                                <th>{!! Sortable::link('bcc', trans('fi.bcc')) !!}</th>
                                <th>{!! Sortable::link('sent', trans('fi.sent')) !!}</th>
                                <th>{{ trans('fi.options') }}</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($mails as $mail)
                                <tr>
                                    <td>{{ $mail->formatted_created_at }}</td>
                                    <td>{{ $mail->formatted_to }}</td>
                                    <td><a href="javascript:void(0)" class="btn-show-content" data-id="{{ $mail->id }}">{{ $mail->subject }}</a></td>
                                    <td>{{ $mail->formatted_from }}</td>
                                    <td>{{ $mail->formatted_cc }}</td>
                                    <td>{{ $mail->formatted_bcc }}</td>
                                    <td align="center">{!! $mail->formatted_sent !!}</td>
                                    <td class="text-right">
                                        <a href="#" data-action="{{ route('mailLog.delete', [$mail->id])}}"
                                           class="delete-email-log btn btn-sm btn-danger" title="{{ trans('fi.delete') }}">
                                            <i class="fa fa-trash"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="card-footer clearfix">

                    <div class="float-right mt-3">

                        {!! $mails->appends(request()->except('page'))->render() !!}
                        
                    </div>

                </div>

            </div>

        </div>

    </section>
@stop