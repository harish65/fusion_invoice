@include('quotes._js_mail')
@include('layouts._js_global')

<style type="text/css">
    iframe {
        border: 1px solid !important;
        height: 158px !important;
        width: 100% !important;
    }
</style>

<div class="modal fade" id="modal-mail-quote">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.email_quote') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label>{{ trans('fi.from') }}</label>
                                {!! Form::select('mail_from', $fromMail,'', ['id' => 'mail_from', 'class' => 'form-control form-control-sm']) !!}
                            </div>
                            <div class="col-6">
                                <label>{{ trans('fi.to') }}</label>
                                {!! $contactDropdownTo !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label>{{ trans('fi.cc') }}</label>
                                {!! $contactDropdownCc !!}
                            </div>
                            <div class="col-6">
                                <label>{{ trans('fi.bcc') }}</label>
                                {!! $contactDropdownBcc !!}
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.subject') }}</label>
                        {!! Form::text('subject', $subject, ['id' => 'subject', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.body') }}</label>
                        <a href="javascript:void(0);" title="{{trans('fi.preview')}}"
                           class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-mail-toggle"
                           data-card-name="quote"
                            id="btn-quote-custom-toggle"><i class="fa fa-eye" data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_preview_template') !!}"></i></a>

                        <div class="custom-template-scroller">
                            <div class="p-0 col-12 custom-quote-sourceCode-display d-block">
                                {!! Form::textarea('body', $body, ['id' => 'body','class' => 'sourceCode form-control form-control-sm', 'placeholder' =>trans('fi.placeholder_type_message'),'rows' => 7]) !!}
                            </div>
                            <div class="p-0 d-none custom-quote-iframe-display col-12">
                                <iframe class="border-1" name="targetCode"
                                        id="quote-targetCode"></iframe>
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        {!! Form::checkbox('attach_pdf', 1, config('fi.quoteAttachPDF'), ['id' => 'attach_pdf', 'class' => 'form-check-input']) !!}
                        <label for="attach_pdf">{{ trans('fi.attach_pdf') }}</label>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default"
                        data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-submit-mail-quote" class="btn btn-sm btn-primary"
                        data-loading-text="{{ trans('fi.sending') }}..."
                        data-original-text="{{ trans('fi.send') }}">{{ trans('fi.send') }}</button>
            </div>
        </div>
    </div>
</div>