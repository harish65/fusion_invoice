@extends('client_center.layouts.public')

@section('javascript')
    @include('layouts._alertifyjs')
    <script type="text/javascript">
        $(function () {
            $('#view-notes').hide();
            $('.btn-notes').click(function () {
                $('#view-doc').toggle();
                $('#view-notes').toggle();
                $('#' + $(this).data('button-toggle')).show();
                $(this).hide();
            });

            $('.quote-approve').click(function () {
                var $_this = $(this);
                $_this.addClass('disabled quote-disabled');
                $('#modal-placeholder').load('{!! route('clientCenter.public.quote.approve.and.reject.modal') !!}', {
                        action: $_this.data('action'),
                        modalName: 'quotes',
                        message: "{!! trans('fi.confirm_approve_quote') !!}",
                        isReload: true,
                    },
                    function (response, status, xhr) {
                        if (status == "error") {
                            var response = JSON.parse(response);
                            alertify.error(response.message);
                        }
                    }
                );
            });

            $('.quote-reject').click(function () {

                var $_this = $(this);
                $_this.addClass('disabled quote-disabled');
                $('#modal-placeholder').load('{!! route('clientCenter.public.quote.approve.and.reject.modal') !!}', {
                        action: $_this.data('action'),
                        modalName: 'quotes',
                        message: "{!! trans('fi.confirm_reject_quote') !!}",
                        isReload: true,
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
    @if(config('fi.secure_link') == 1)
        <style>
            .expire-link-msg-css {
                position: absolute;
                top: 50px;
                right: 14px;
            }
        </style>
        <div class="expire-link-msg-css col-md-3 col-sm-12">
            <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                <span class="font-style-color">{!! trans('fi.quote_link_expire', ['days' => config('fi.secure_link_expire_day')]) !!}</span><span class="text-danger">*</span>
            </marquee>
        </div>
    @endif
    <section class="content iframe-content">

        <div class="container-fluid">

            @include('layouts._alerts')
            <div class="row">
                <div class="col-12 ">
                    <div class="offset-md-1 col-md-10">
                        <div class="card card-primary card-outline mt-2">
                            <div class="card-header">
                                <div class="card-tools">
                                    <ul class="nav nav-pills ml-auto">
                                        <li class="nav-item mt-1 mb-1 mr-1">
                                            <a href="{{ route('clientCenter.public.quote.pdf', [$quote->url_key]) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary"><i class="fa fa-print"></i>
                                                <span>{{ trans('fi.pdf') }}</span>
                                            </a>
                                        </li>
                                        @if (auth()->check())
                                            <li class="nav-item mt-1 mb-1 mr-1">
                                                <a href="javascript:void(0)" id="btn-notes"
                                                   data-button-toggle="btn-notes-back"
                                                   class="btn btn-sm btn-primary btn-notes">
                                                    <i class="fa fa-comments"></i> {{ trans('fi.notes') }}
                                                </a>
                                                <a href="javascript:void(0)" id="btn-notes-back"
                                                   data-button-toggle="btn-notes" class="btn btn-sm btn-primary btn-notes"
                                                   style="display: none;">
                                                    <i class="fa fa-backward"></i> {{ trans('fi.back_to_quote') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (count($quote->attachments))
                                            <li class="nav-item mr-1">
                                                <div class="btn-group mt-1">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                                            data-toggle="dropdown" aria-expanded="true">
                                                        <i class="fa fa-file-alt"></i> {{ trans('fi.attachments') }}
                                                        <span class="caret"></span>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @foreach ($quote->attachments as $attachment)
                                                            <a class="dropdown-item"
                                                               href="{{ $attachment->download_url }}">{{ $attachment->filename }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </li>
                                        @endif

                                        @if (in_array($quote->status_text, ['draft', 'sent']))
                                            <li class="nav-item mt-1 mb-1 mr-1">
                                                <a href="#"
                                                   data-action="{{ route('clientCenter.public.quote.approve', [$quote->url_key, $token]) }}"
                                                   class="btn  btn-sm btn-success quote-approve">
                                                    <i class="fa fa-thumbs-up"></i> {{ trans('fi.approve') }}
                                                </a>
                                                <a href="#"
                                                   data-action="{{ route('clientCenter.public.quote.reject', [$quote->url_key, $token]) }}"
                                                   class="btn btn-sm btn-danger quote-reject">
                                                    <i class="fa fa-thumbs-down"></i> {{ trans('fi.reject') }}
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">

                                <div id="view-doc">
                                    <iframe src="{{ route('clientCenter.public.quote.html', [$urlKey]) }}" width="100%"
                                            style="min-height: 750px;" frameborder="0"
                                            onload="resizeIframeSection(this, 800);">

                                    </iframe>
                                </div>

                                @if (auth()->check())
                                    <div id="view-notes">
                                        <div class="col-sm-12 table-responsive" style="overflow-x: visible;">
                                            <div class="card card-primary card-outline">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            @include('notes._js_timeline', ['object' => $quote, 'model' => 'FI\Modules\Quotes\Models\Quote', 'hideHeader' => true, 'showPrivateCheckbox' => 0, 'showPrivate' => 1])
                                                            <div id="note-timeline-container"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@stop