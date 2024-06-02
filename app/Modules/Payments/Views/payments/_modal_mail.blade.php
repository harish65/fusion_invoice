@include('payments._js_mail')
@include('layouts._js_global')

<style type="text/css">
    iframe {
        border: 1px solid !important;
        height: 158px !important;
        width: 100% !important;
    }
</style>
<div class="modal fade" id="modal-mail-payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.email_payment_receipt') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">
                    <div class="form-group">
                        <label>{{ trans('fi.from') }}</label>
                        {!! Form::select('mail_from', $fromMail,'', ['id' => 'mail_from', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.to') }}</label>
                        {!! $contactDropdownTo !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.cc') }}</label>
                        {!! $contactDropdownCc !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.bcc') }}</label>
                        {!! $contactDropdownBcc !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.subject') }}</label>
                        {!! Form::text('subject', $subject, ['id' => 'subject', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.body') }}</label>
                        <a href="javascript:void(0);" title="{{trans('fi.preview')}}"
                           class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-mail-toggle"
                           data-card-name="payment"
                           id="btn-payment-custom-toggle"><i class="fa fa-eye"></i></a>
                    </div>

                    <div class="form-group">
                        <div class="custom-template-scroller">
                            <div class="p-0 col-12 custom-payment-sourceCode-display d-block">
                                {!! Form::textarea('body', $body, ['id' => 'body','class' => 'sourceCode form-control form-control-sm', 'placeholder' =>trans('fi.placeholder_type_message'),'rows' => 7]) !!}
                            </div>
                            <div class="p-0 d-none custom-payment-iframe-display col-12">
                                <iframe class="border-1" name="targetCode"
                                        id="payment-targetCode"></iframe>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('attach_pdf', 1, config('fi.paymentAttachInvoice'), ['id' => 'attach_pdf', 'checked' => config('fi.paymentAttachInvoice') == 1 ? true :false]) !!}
                                {{ trans('fi.attach_invoice_pdf') }}
                            </label>
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-submit-mail-payment" class="btn btn-sm btn-primary"
                        data-loading-text="{{ trans('fi.sending') }}..." data-original-text="{{ trans('fi.send') }}">{{ trans('fi.send') }}</button>
            </div>
        </div>
    </div>
</div>