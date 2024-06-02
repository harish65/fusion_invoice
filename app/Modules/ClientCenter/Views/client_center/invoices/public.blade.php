@extends('client_center.layouts.public')

@section('javascript')

    <script type="text/javascript">
        $(function (){
            $('#view-notes').hide();
            $('.btn-notes').click(function (){
                $('#view-doc').toggle();
                $('#view-notes').toggle();
                $('#' + $(this).data('button-toggle')).show();
                $(this).hide();
            });

            $('.btn-pay').click(function (){
                $(this).addClass('disabled');
                $('#modal-loading').modal();
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
                <span class="font-style-color">{!! trans('fi.invoice_link_expire', ['days' => config('fi.secure_link_expire_day')]) !!}</span><span class="text-danger">*</span>
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
                                            <a href="{{ route('clientCenter.public.invoice.pdf', [$invoice->url_key]) }}"
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
                                                   data-button-toggle="btn-notes"
                                                   class="btn btn-sm btn-primary btn-notes" style="display: none;">
                                                    <i class="fa fa-backward"></i> {{ ($invoice->type == 'credit_memo') ? trans('fi.back_to_credit_memo') :  trans('fi.back_to_invoice') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (count($invoice->attachments))
                                            <li class="nav-item mr-1">
                                                <div class="btn-group mt-1">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                                            data-toggle="dropdown">
                                                        <i class="fa fa-file-alt"></i> {{ trans('fi.attachments') }}
                                                        <span class="caret"></span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right text-right">
                                                        @php $visibility = 0; @endphp
                                                        @foreach ($invoice->attachments as $attachment)
                                                            @if($attachment->client_visibility == 2 && !$invoice->isPayable)
                                                                @php $visibility++; @endphp
                                                                <a class="dropdown-item" href="{{ $attachment->download_url }}">{{ $attachment->filename }}</a>
                                                            @elseif($attachment->client_visibility == 1)
                                                                @php $visibility++; @endphp
                                                                <a class="dropdown-item" href="{{ $attachment->download_url }}">{{ $attachment->filename }}</a>
                                                            @endif
                                                        @endforeach
                                                        @if($visibility == 0)
                                                            <a class="dropdown-item" href="#">{{ trans('fi.no-attachment') }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endif

                                        @if ($invoice->isPayable)
                                            <li class="nav-item mt-1 mb-1 mr-1">
                                                @foreach ($merchantDrivers as $driver)
                                                    <a href="{{ route('merchant.pay.' . strtolower($driver->getName()), [$invoice->url_key]) }}"
                                                       class="btn btn-sm btn-success btn-pay mb-1"><i
                                                                class="fa fa-credit-card"></i> {{ $driver->getSetting('paymentButtonText') }}
                                                    </a>
                                                @endforeach
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">

                                <div id="view-doc">
                                    <iframe width="100%" style="min-height: 750px;" frameborder="0"
                                            src="{{ route('clientCenter.public.invoice.html', [$urlKey]) }}"
                                            onload="resizeIframeSection(this, 800);">
                                    </iframe>
                                </div>

                                @if (auth()->check())
                                    <div id="view-notes">
                                        <div class="col-sm-12 table-responsive" style="overflow-x: visible;">
                                            @include('notes._js_timeline', ['object' => $invoice, 'model' => 'FI\Modules\Invoices\Models\Invoice', 'hideHeader' => true, 'showPrivateCheckbox' => 0, 'showPrivate' => 0])
                                            <div id="note-timeline-container"></div>
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

<div class="modal align-middle" id="modal-loading" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered justify-content-center">
        <div class="task-list-container-loader">
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only"> {{ trans('fi.loading') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
