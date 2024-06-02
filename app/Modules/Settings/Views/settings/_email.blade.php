@section('javascript')
    @parent
    <script type="text/javascript">
        $(function () {

            $('input[type="radio"]').each(function (e) {
                if ($(this).prop("checked") == true) {
                    radioBtnSelected($(this).data('radio-btn'), $(this).data('type'));
                }
            });

            $('#mailPassword').val('');

            $('#lbl-save-first').hide();

            $('#mailDriver').change(function () {
                updateEmailOptions();
            });

            function updateEmailOptions() {

                $('.email-option').hide();

                mailDriver = $('#mailDriver').val();

                if (mailDriver == 'smtp') {
                    $('.smtp-option').show();
                } else if (mailDriver == 'sendmail') {
                    $('.sendmail-option').show();
                } else if (mailDriver == 'mail') {
                    $('.phpmail-option').show();
                } else if (mailDriver == 'sendgrid') {
                    $('.sendgrid-option').show();
                }
            }

            updateEmailOptions();

            $('#mailPassword').val("{{ $mailPassword }}");

            $('#testEmailAddress, #mailDriver, #mailHost, #mailPort, #mailUsername, #mailPassword, #mailEncryption, #mailAllowSelfSignedCertificate, #mailSendmail').change(function () {
                $('#btn-test-email').attr('disabled', true);
                $('#lbl-save-first').show();
            });

            $('#btn-test-email').click(function () {
                var testMail = $('#testEmailAddress').val();
                $('#modal-placeholder').load('{{ route('testMail.create') }}', { testMail : testMail}, function (response, status, xhr) {
                    if (status == 'error') {
                        alertify.error('{{ trans('fi.problem_with_email_template') }}');
                    }
                });
            });

            $(".btn-custom-toggle").on('click', function () {
                var appendId = $(this).data('card-name');

                function runCode() {
                    var content = document.getElementById(appendId + '-sourceCode').value;
                    var iframe = document.getElementById(appendId + '-targetCode');
                    iframe = (iframe.contentWindow) ? iframe.contentWindow : (iframe.contentDocument.document) ? iframe.contentDocument.document : iframe.contentDocument;
                    iframe.document.open();
                    iframe.document.write(content);
                    iframe.document.close();
                    return false;
                }

                runCode();

                if (($('.custom-' + appendId + '-sourceCode-display').hasClass('d-block')) === true) {
                    $(this).children().removeClass().addClass('fa fa-code');
                    $('.toggle-' + appendId + '-header').html('{{trans('fi.preview')}}');
                    $('.custom-' + appendId + '-sourceCode-display').removeClass('d-block').addClass('d-none');
                    $('.custom-' + appendId + '-iframe-display').removeClass('d-none').addClass('d-block');
                } else {

                    $(this).children().removeClass().addClass('fa fa-eye');
                    $('.toggle-' + appendId + '-header').html("{{trans('fi.code')}}");
                    $('.custom-' + appendId + '-sourceCode-display').removeClass('d-none').addClass('d-block');
                    $('.custom-' + appendId + '-iframe-display').removeClass('d-block').addClass('d-none');
                }

            });
            $('.radio-btn-check').click(function () {
                radioBtnSelected($(this).data('radio-btn'), $(this).data('type'));
            });

            function radioBtnSelected(radioBTN, btnType) {
                if (btnType == 'custom') {

                    $('#tab-custom-' + radioBTN + '-template').addClass('active');
                    $('#tab-' + radioBTN + '-template').removeClass('active');
                    $('.custom-' + radioBTN + '-active').addClass('active');
                    $('.' + radioBTN + '-active').removeClass('active');
                    $('.custom-check-' + radioBTN + '-template').show();
                    $('.default-check-' + radioBTN + '-template').hide();
                } else {
                    $('#tab-custom-' + radioBTN + '-template').removeClass('active');
                    $('#tab-' + radioBTN + '-template').addClass('active');
                    $('.' + radioBTN + '-active').addClass('active');
                    $('.custom-' + radioBTN + '-active').removeClass('active');
                    $('.custom-check-' + radioBTN + '-template').hide();
                    $('.default-check-' + radioBTN + '-template').show();
                }
            }
        });
    </script>

@stop
<style type="text/css">
    iframe {
        height: 500px !important;
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('fi.email_send_method') }}: </label>
            {!! Form::select('setting[mailDriver]', $emailSendMethods, config('fi.mailDriver'), ['id' => 'mailDriver', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group smtp-option email-option">
            <label>{{ trans('fi.smtp_host_address') }}: </label>
            {!! Form::text('setting[mailHost]', config('fi.mailHost'), ['id' => 'mailHost', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>{{ trans('fi.smtp_host_port') }}: </label>
            {!! Form::text('setting[mailPort]', config('fi.mailPort'), ['id' => 'mailPort', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
</div>

<div class="form-group sendgrid-option email-option">
    <div class="form-group">
        <label>{{ trans('fi.sendgrid_key') }}: </label>
        {!! Form::text('setting[mailSendgridKey]', config('fi.mailSendgridKey'), ['id'=>'mailSendgridKey', 'class' => 'form-control form-control-sm']) !!}
    </div>
</div>

<div class="row smtp-option email-option sendgrid-option">
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>{{ trans('fi.smtp_username') }}: </label>
            {!! Form::text('setting[mailUsername]', config('fi.mailUsername'), ['id' => 'mailUsername', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>{{ trans('fi.smtp_password') }}: </label>
            {!! Form::password('setting[mailPassword]', ['id' => 'mailPassword', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option">
            <label>{{ trans('fi.smtp_encryption') }}: </label>
            {!! Form::select('setting[mailEncryption]', $emailEncryptions, config('fi.mailEncryption'), ['id' => 'mailEncryption', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group smtp-option email-option sendgrid-option">
            <label>{{ trans('fi.allow_self_signed_cert') }}: </label>
            {!! Form::select('setting[mailAllowSelfSignedCertificate]', $yesNoArray, config('fi.mailAllowSelfSignedCertificate'), ['id'=>'mailAllowSelfSignedCertificate', 'class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
</div>

<div class="form-group sendmail-option email-option">
    <div class="form-group">
        <label>{{ trans('fi.sendmail_path') }}: </label>
        {!! Form::text('setting[mailSendmail]', config('fi.mailSendmail'), ['id'=>'mailSendmail', 'class' => 'form-control form-control-sm']) !!}
    </div>
</div>

<div class="row smtp-option sendmail-option phpmail-option email-option sendgrid-option">
    <div class="col-md-4">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option sendgrid-option">
            <label>{{ trans('fi.reply_to_address') }}: </label>
            {!! Form::text('setting[mailReplyToAddress]', config('fi.mailReplyToAddress'), ['class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option sendgrid-option">
            <label>{{ trans('fi.always_cc') }}: </label>
            {!! Form::text('setting[mailDefaultCc]', config('fi.mailDefaultCc'), ['class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group smtp-option sendmail-option phpmail-option email-option sendgrid-option">
            <label>{{ trans('fi.always_bcc') }}: </label>
            {!! Form::text('setting[mailDefaultBcc]', config('fi.mailDefaultBcc'), ['class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('fi.test_email_address') }}: </label>

            <div class="input-group">
                {!! Form::text('setting[testEmailAddress]', config('fi.testEmailAddress'), ['id'=>'testEmailAddress', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
                <span class="input-group-append">
                    <a href="javascript:void(0)" class="btn btn-sm btn-info" id="btn-test-email">
                        <i class="fa fa-envelope"></i> {{ trans('fi.send_test_email') }}
                    </a>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('fi.mail_from_address') }}: </label>
            {!! Form::text('setting[mailFromAddress]', config('fi.mailFromAddress'), ['id'=>'mailFromAddress', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ trans('fi.mail_from_name') }}: </label>
            {!! Form::text('setting[mailFromName]', config('fi.mailFromName'), ['id'=>'mailFromName', 'class' => 'form-control form-control-sm', 'autocomplete' => 'off']) !!}
        </div>
    </div>
</div>

<div class="content-header p-0 pb-2">
    <div class="container-fluid p-0">
        <label>{{ trans('fi.email_templates') }} :</label>
    </div>
</div>

<div class="row">
    <section class="col-lg-12">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="email-templates">
                    <li class="nav-item">
                        <a data-toggle="tab" class="active nav-link"
                           href="#tab-quote_setting_email_template">{{ trans('fi.quotes') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link "
                           href="#tab-invoice_setting_email_template">{{ trans('fi.invoices') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link "
                           href="#tab-credit_memo_setting_email_template">{{ trans('fi.credit_memos') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link "
                           href="#tab-overdue_invoice_setting_email_template">{{ trans('fi.overdue_invoices') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link "
                           href="#tab-payment_receipt_setting_email_template">{{ trans('fi.payment_receipts') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" class="nav-link "
                           href="#tab-upcoming_payment_notice_setting_email_template">{{ trans('fi.upcoming_payment_notices') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content mt-2">

                    <div id="tab-quote_setting_email_template" class="tab-pane active">

                        <a class="btn btn-info btn-xs col-md-1 offset-md-11"
                           href="https://www.fusioninvoice.com/docs/2020/Customization/Email-Templates#quote-email-template"
                           target="_blank">
                            <i class="fa fa-bullhorn"
                               aria-hidden="true"></i> {{ trans('fi.available_fields') }}
                        </a>

                        <div class="form-group">
                            <label>{{ trans('fi.quote_email_subject') }}: </label>
                            {!! Form::text('setting[quoteEmailSubject]', config('fi.quoteEmailSubject'), ['class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.quote_email_body') }}: </label>
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="quote-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="active nav-link  quote-active"
                                               href="#tab-quote-template"><i
                                                        class="fa fa-check default-check-quote-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="nav-link  custom-quote-active"
                                               href="#tab-custom-quote-template"><i
                                                        class="fa fa-check custom-check-quote-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="card-body p-0">
                                    <div class="tab-content mt-2">
                                        <div id="tab-quote-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[quoteUseCustomTemplate]', 'default_mail_template',((config('fi.quoteUseCustomTemplate') == 'default_mail_template') or (config('fi.quoteUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'quote','data-type'=>'default', 'id'=>'quote_use_default_template']) }}
                                                                    {!! Form::label('quote_use_default_template',trans('fi.quote').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'quote_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[quoteEmailBody]', config('fi.quoteEmailBody'), ['class' => 'form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-quote-template" class="tab-pane">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[quoteUseCustomTemplate]', 'custom_mail_template', (config('fi.quoteUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'quote','data-type'=>'custom', 'id'=>'quote_use_custom_template']) }}
                                                                    {!! Form::label('quote_use_custom_template',trans('fi.quote').' '.trans('fi.use_custom_mail_template') , ['class'=>'form-check-label','for' => 'quote_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="quote"
                                                                       id="btn-quote-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-quote-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[quoteCustomMailTemplate]', config('fi.quoteCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light', 'id'=>"quote-sourceCode", 'placeholder' =>trans('fi.placeholder_type_email_template'),'rows' => 23]) !!}
                                                </div>

                                                <div class="p-0 d-none custom-quote-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="quote-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="form-group">
                            <label>{{ trans('fi.quote_approved_email_body') }}: </label>
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="quoteApproved-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="active nav-link  quoteApproved-active"
                                               href="#tab-quoteApproved-template">
                                                <i class="fa fa-check default-check-quoteApproved-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="nav-link custom-quoteApproved-active"
                                               href="#tab-custom-quoteApproved-template">
                                                <i class="fa fa-check custom-check-quoteApproved-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-0">
                                    <div class="tab-content mt-2">
                                        <div id="tab-quoteApproved-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[quoteApprovedUseCustomTemplate]', 'default_mail_template',((config('fi.quoteApprovedUseCustomTemplate') == 'default_mail_template') or (config('fi.quoteApprovedUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'quoteApproved','data-type'=>'default',  'id'=>'quote_approved_use_default_template']) }}
                                                                    {!! Form::label('quote_approved_use_default_template',trans('fi.quote_approved').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'quote_approved_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[quoteApprovedEmailBody]', config('fi.quoteApprovedEmailBody'), ['class' => 'form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-quoteApproved-template" class="tab-pane">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[quoteApprovedUseCustomTemplate]', 'custom_mail_template', (config('fi.quoteApprovedUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check'  ,'data-radio-btn'=>'quoteApproved','data-type'=>'custom', 'id'=>'quote_approved_use_custom_template']) }}
                                                                    {!! Form::label('quote_approved_use_custom_template',trans('fi.quote_approved').' '.trans('fi.custom_mail_template') , ['class'=>'form-check-label','for' => 'quote_approved_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="quoteApproved"
                                                                       id="btn-quoteApproved-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-quoteApproved-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[quoteApprovedCustomMailTemplate]', config('fi.quoteApprovedCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light', 'id'=>"quoteApproved-sourceCode", 'placeholder' =>trans('fi.placeholder_type_email_template'),'rows' => 23]) !!}
                                                </div>

                                                <div class="p-0 d-none custom-quoteApproved-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="quoteApproved-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="form-group">
                            <label>{{ trans('fi.quote_rejected_email_body') }}: </label>
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="quoteRejected-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="active nav-link quoteRejected-active"
                                               href="#tab-quoteRejected-template"><i
                                                        class="fa fa-check default-check-quoteRejected-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="nav-link custom-quoteRejected-active"
                                               href="#tab-custom-quoteRejected-template"><i
                                                        class="fa fa-check custom-check-quoteRejected-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-0">
                                    <div class="tab-content mt-2">
                                        <div id="tab-quoteRejected-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[quoteRejectedUseCustomTemplate]', 'default_mail_template',((config('fi.quoteApprovedUseCustomTemplate') == 'default_mail_template') or (config('fi.quoteRejectedUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' , 'data-radio-btn'=>'quoteRejected','data-type'=>'default','id'=>'quote_rejected_use_default_template']) }}
                                                                    {!! Form::label('quote_rejected_use_default_template',trans('fi.quote_rejected').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'quote_rejected_use_default_template']) !!}
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[quoteRejectedEmailBody]', config('fi.quoteRejectedEmailBody'), ['class' => 'form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-quoteRejected-template" class="tab-pane">

                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[quoteRejectedUseCustomTemplate]', 'custom_mail_template', (config('fi.quoteRejectedUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check'  ,'data-radio-btn'=>'quoteRejected','data-type'=>'custom', 'id'=>'quote_rejected_use_custom_template']) }}
                                                                    {!! Form::label('quote_rejected_use_custom_template',trans('fi.quote_rejected').' '.trans('fi.custom_mail_template') , ['class'=>'form-check-label','for' => 'quote_rejected_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="quoteRejected"
                                                                       id="btn-quoteRejected-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-quoteRejected-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[quoteRejectedCustomMailTemplate]', config('fi.quoteRejectedCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light', 'id'=>"quoteRejected-sourceCode", 'placeholder' =>trans('fi.placeholder_type_email_template'),'rows' => 23]) !!}
                                                </div>

                                                <div class="p-0 d-none custom-quoteRejected-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="quoteRejected-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <br>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                {!! Form::checkbox('setting[quoteAttachPDF]', 1, config('fi.quoteAttachPDF'), ['id' => 'quote_attach_pdf', 'class' => 'custom-control-input']) !!}
                                <label for="quote_attach_pdf"
                                       class="custom-control-label"> {{ trans('fi.attach_quote_pdf') }} </label>
                            </div>
                        </div>

                    </div>

                    <div id="tab-invoice_setting_email_template" class="tab-pane ">
                        <a class="btn btn-info btn-xs col-md-1 offset-md-11"
                           href="https://www.fusioninvoice.com/docs/2020/Customization/Email-Templates#invoice-email-template"
                           target="_blank">
                            <i class="fa fa-bullhorn"
                               aria-hidden="true"></i> {{ trans('fi.available_fields') }}
                        </a>

                        <div class="form-group">
                            <label>{{ trans('fi.invoice_email_subject') }}: </label>
                            {!! Form::text('setting[invoiceEmailSubject]', config('fi.invoiceEmailSubject'), ['class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.invoice_email_body') }}: </label>
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="invoice-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="active nav-link invoice-active"
                                               href="#tab-invoice-template"><i
                                                        class="fa fa-check default-check-invoice-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="nav-link custom-invoice-active"
                                               href="#tab-custom-invoice-template"><i
                                                        class="fa fa-check custom-check-invoice-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="card-body p-0">
                                    <div class="tab-content">

                                        <div id="tab-invoice-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[invoiceUseCustomTemplate]', 'default_mail_template',((config('fi.invoiceUseCustomTemplate') == 'default_mail_template') or (config('fi.invoiceUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'invoice','data-type'=>'default', 'id'=>'invoice_use_default_template']) }}
                                                                    {!! Form::label('invoice_use_default_template',trans('fi.invoice').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'invoice_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[invoiceEmailBody]', config('fi.invoiceEmailBody'), ['class' => 'mt-2 form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-invoice-template" class="tab-pane">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[invoiceUseCustomTemplate]', 'custom_mail_template', (config('fi.invoiceUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'invoice','data-type'=>'custom',  'id'=>'invoice_use_custom_template']) }}
                                                                    {!! Form::label('invoice_use_custom_template',trans('fi.invoice').' '.trans('fi.use_custom_mail_template') , ['class'=>'form-check-label','for' => 'invoice_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="invoice"
                                                                       id="btn-invoice-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body  p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-invoice-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[invoiceCustomMailTemplate]', config('fi.invoiceCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light', 'id'=>"invoice-sourceCode", 'placeholder' => trans('fi.placeholder_type_email_template'), 'rows' => 23]) !!}
                                                </div>

                                                <div class="p-0 d-none custom-invoice-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="invoice-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                {!! Form::checkbox('setting[invoiceAttachPDF]', 1, config('fi.invoiceAttachPDF'), ['id' => 'invoice_attach_pdf', 'class' => 'custom-control-input']) !!}
                                <label for="invoice_attach_pdf"
                                       class="custom-control-label"> {{ trans('fi.attach_invoice_pdf') }} </label>
                            </div>
                        </div>

                    </div>

                    <div id="tab-credit_memo_setting_email_template" class="tab-pane ">
                        <div class="form-group">
                            <label>{{ trans('fi.credit_memo_email_subject') }}: </label>
                            {!! Form::text('setting[creditMemoEmailSubject]', config('fi.creditMemoEmailSubject'), ['class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <label>{{ trans('fi.credit_memo_email_body') }}: </label>

                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="creditMemo-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="active nav-link creditMemo-active"
                                               href="#tab-creditMemo-template"><i
                                                        class="fa fa-check default-check-creditMemo-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab"
                                               class="nav-link custom-creditMemo-active"
                                               href="#tab-custom-creditMemo-template"><i
                                                        class="fa fa-check custom-check-creditMemo-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-0">
                                    <div class="tab-content">
                                        <div id="tab-creditMemo-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[creditMemoUseCustomTemplate]', 'default_mail_template',((config('fi.creditMemoUseCustomTemplate') == 'default_mail_template') or (config('fi.creditMemoUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'creditMemo','data-type'=>'default',  'id'=>'credit_memo_use_default_template']) }}
                                                                    {!! Form::label('credit_memo_use_default_template',trans('fi.credit_memo').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'credit_memo_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[creditMemoEmailBody]', config('fi.creditMemoEmailBody'), ['class' => 'mt-2 form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-creditMemo-template" class="tab-pane">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {{ Form::radio('setting[creditMemoUseCustomTemplate]', 'custom_mail_template', (config('fi.creditMemoUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'creditMemo','data-type'=>'custom', 'id'=>'credit_memo_use_custom_template']) }}
                                                                    {!! Form::label('credit_memo_use_custom_template',trans('fi.credit_memo').' '.trans('fi.use_custom_mail_template') , ['class'=>'form-check-label','for' => 'credit_memo_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="creditMemo"
                                                                       id="btn-creditMemo-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-creditMemo-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[creditMemoCustomMailTemplate]', config('fi.creditMemoCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light',  'id'=>'creditMemo-sourceCode', 'placeholder' => trans('fi.placeholder_type_email_template') ,'rows' => 23]) !!}
                                                </div>

                                                <div class="p-0 d-none custom-creditMemo-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="creditMemo-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-overdue_invoice_setting_email_template" class="tab-pane">
                        <a class="btn btn-info btn-xs col-md-1 offset-md-11"
                           href="https://www.fusioninvoice.com/docs/2020/Customization/Email-Templates#invoice-email-template"
                           target="_blank">
                            <i class="fa fa-bullhorn" aria-hidden="true"></i> {{ trans('fi.available_fields') }}
                        </a>

                        <div class="form-group">
                            <label>{{ trans('fi.overdue_email_subject') }}: </label>
                            {!! Form::text('setting[overdueInvoiceEmailSubject]', config('fi.overdueInvoiceEmailSubject'), ['class' => 'form-control form-control-sm']) !!}
                        </div>

                        <div class="form-group">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="overdueInvoice-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab" class="active nav-link overdueInvoice-active"
                                               href="#tab-overdueInvoice-template">
                                                <i class="fa fa-check default-check-overdueInvoice-template"> </i> {{ trans('fi.default') }}

                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab" class="nav-link custom-overdueInvoice-active"
                                               href="#tab-custom-overdueInvoice-template">
                                                <i class="fa fa-check custom-check-overdueInvoice-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="card-body p-0">
                                    <div class="tab-content">
                                        <div id="tab-overdueInvoice-template" class="mt-2 tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {!! Form::radio('setting[overdueInvoiceUseCustomTemplate]', 'default_mail_template',((config('fi.overdueInvoiceUseCustomTemplate') == 'default_mail_template') or (config('fi.overdueInvoiceUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check'  ,'data-radio-btn'=>'overdueInvoice','data-type'=>'default', 'id'=>'overdue_invoices_use_default_template'])  !!}
                                                                    {!! Form::label('overdue_invoices_use_default_template',trans('fi.overdue_invoices').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'overdue_invoices_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[overdueInvoiceEmailBody]', config('fi.overdueInvoiceEmailBody'), ['class' => 'form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>

                                        <div id="tab-custom-overdueInvoice-template" class="tab-pane">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">

                                                                <div class="form-check form-check-inline">
                                                                    {!! Form::radio('setting[overdueInvoiceUseCustomTemplate]', 'custom_mail_template', (config('fi.overdueInvoiceUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'overdueInvoice','data-type'=>'custom', 'id'=>'overdue_invoices_use_custom_template']) !!}
                                                                    {!! Form::label('overdue_invoices_use_custom_template',trans('fi.overdue_invoices').' '.trans('fi.use_custom_mail_template') , ['class'=>'form-check-label','for' => 'overdue_invoices_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="overdueInvoice"
                                                                       id="btn-overdueInvoice-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-overdueInvoice-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[overdueInvoiceCustomMailTemplate]', config('fi.overdueInvoiceCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light', 'id'=>"overdueInvoice-sourceCode", 'placeholder' => trans('fi.placeholder_type_email_template'),'rows' => 23]) !!}
                                                </div>

                                                <div class="p-0 d-none custom-overdueInvoice-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="overdueInvoice-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('fi.overdue_invoice_reminder_frequency') }}: </label>
                            {!! Form::text('setting[overdueInvoiceReminderFrequency]', config('fi.overdueInvoiceReminderFrequency'), ['class' => 'form-control form-control-sm']) !!}
                            <span class="help-block">{{ trans('fi.overdue_invoice_reminder_frequency_help') }}</span>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                {!! Form::checkbox('setting[overdueAttachInvoice]', 1, config('fi.overdueAttachInvoice'), ['id' => 'overdue_attach_invoice', 'class' => 'custom-control-input']) !!}
                                <label for="overdue_attach_invoice"
                                       class="custom-control-label"> {{ trans('fi.attach_invoice_pdf') }} </label>
                            </div>
                        </div>
                    </div>

                    <div id="tab-payment_receipt_setting_email_template" class="tab-pane ">

                        <div class="form-group">
                            <label>{{ trans('fi.payment_receipt_email_subject') }}: </label>
                            {!! Form::text('setting[paymentReceiptEmailSubject]', config('fi.paymentReceiptEmailSubject'), ['class' => 'form-control form-control-sm']) !!}
                        </div>
                        <div class="form-group">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="paymentReceipt-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab" class="active nav-link paymentReceipt-active"
                                               href="#tab-paymentReceipt-template">
                                                <i class="fa fa-check default-check-paymentReceipt-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab" class="nav-link custom-paymentReceipt-active"
                                               href="#tab-custom-paymentReceipt-template">
                                                <i class="fa fa-check custom-check-paymentReceipt-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="card-body p-0">
                                    <div class="tab-content">
                                        <div id="tab-paymentReceipt-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {!! Form::radio('setting[paymentReceiptUseCustomTemplate]', 'default_mail_template',((config('fi.paymentReceiptUseCustomTemplate') == 'default_mail_template') or (config('fi.paymentReceiptUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'paymentReceipt','data-type'=>'default', 'id'=>'payment_receipt_use_default_template']) !!}
                                                                    {!! Form::label('payment_receipt_use_default_template',trans('fi.payment_receipt').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'payment_receipt_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::select('setting[paymentReceiptBody]', ['default' => trans('fi.default'),'custom' => trans('fi.custom')], config('fi.paymentReceiptBody'), ['class' => 'mt-2 form-control form-control-sm']) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-paymentReceipt-template" class="tab-pane">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {!! Form::radio('setting[paymentReceiptUseCustomTemplate]', 'custom_mail_template', (config('fi.paymentReceiptUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check' ,'data-radio-btn'=>'paymentReceipt','data-type'=>'custom', 'id'=>'payment_receipt_use_custom_template']) !!}
                                                                    {!! Form::label('payment_receipt_use_custom_template',trans('fi.payment_receipt').' '.trans('fi.use_custom_mail_template') , ['class'=>'form-check-label','for' => 'payment_receipt_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="paymentReceipt"
                                                                       id="btn-paymentReceipt-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-paymentReceipt-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[paymentReceiptCustomMailTemplate]', config('fi.paymentReceiptCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light',  'id'=>"paymentReceipt-sourceCode", 'placeholder' => trans('fi.placeholder_type_email_template') ,'rows' => 23]) !!}
                                                </div>
                                                <div class="p-0 d-none custom-paymentReceipt-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="paymentReceipt-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                {!! Form::checkbox('setting[paymentAttachInvoice]', 1, config('fi.paymentAttachInvoice'), ['id' => 'payment_attach_invoice', 'class' => 'custom-control-input']) !!}
                                <label for="payment_attach_invoice"
                                       class="custom-control-label"> {{ trans('fi.attach_invoice_pdf') }} </label>
                            </div>
                        </div>

                    </div>

                    <div id="tab-upcoming_payment_notice_setting_email_template" class="tab-pane ">
                        <a class="btn btn-info btn-xs col-md-1 offset-md-11"
                           href="https://www.fusioninvoice.com/docs/2020/Customization/Email-Templates#invoice-email-template"
                           target="_blank">
                            <i class="fa fa-bullhorn"
                               aria-hidden="true"></i> {{ trans('fi.available_fields') }}
                        </a>
                        <div class="form-group">
                            <label>{{ trans('fi.upcoming_payment_notice_email_subject') }}: </label>
                            {!! Form::text('setting[upcomingPaymentNoticeEmailSubject]', config('fi.upcomingPaymentNoticeEmailSubject'), ['class' => 'form-control form-control-sm']) !!}
                        </div>
                        <div class="form-group">
                            <div class="card card-primary card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs"
                                        id="upcomingPaymentNotice-mail-template">
                                        <li class="nav-item">
                                            <a data-toggle="tab" class="active nav-link upcomingPaymentNotice-active"
                                               href="#tab-upcomingPaymentNotice-template">
                                                <i class="fa fa-check default-check-upcomingPaymentNotice-template"> </i> {{ trans('fi.default') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="tab" class="nav-link custom-upcomingPaymentNotice-active"
                                               href="#tab-custom-upcomingPaymentNotice-template">
                                                <i class="fa fa-check custom-check-upcomingPaymentNotice-template"> </i> {{ trans('fi.custom') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body p-0">
                                    <div class="tab-content">
                                        <div id="tab-upcomingPaymentNotice-template" class="tab-pane active">
                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    {!! Form::radio('setting[upcomingPaymentNoticeUseCustomTemplate]', 'default_mail_template',((config('fi.upcomingPaymentNoticeUseCustomTemplate') == 'default_mail_template') or (config('fi.upcomingPaymentNoticeUseCustomTemplate') == '')) ? true : false, ['class' => 'form-check-input radio-btn-check' , 'data-radio-btn'=>'upcomingPaymentNotice','data-type'=>'default','id'=>'upcoming_payment_notice_use_default_template']) !!}
                                                                    {!! Form::label('upcoming_payment_notice_use_default_template',trans('fi.upcoming_payment_notice').' '.trans('fi.use_default_mail_template') , ['class'=>'form-check-label','for' => 'upcoming_payment_notice_use_default_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                {!! Form::textarea('setting[upcomingPaymentNoticeEmailBody]', config('fi.upcomingPaymentNoticeEmailBody'), ['class' => ' mt-2 form-control form-control-sm bg-light', 'rows' => 5]) !!}
                                            </div>
                                        </div>
                                        <div id="tab-custom-upcomingPaymentNotice-template" class="tab-pane">

                                            <div class="content-header pb-0">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group mb-0">

                                                                <div class="form-check form-check-inline">
                                                                    {!! Form::radio('setting[upcomingPaymentNoticeUseCustomTemplate]', 'custom_mail_template', (config('fi.upcomingPaymentNoticeUseCustomTemplate') == 'custom_mail_template') ? true : false, ['class' => 'form-check-input radio-btn-check' , 'data-radio-btn'=>'upcomingPaymentNotice','data-type'=>'custom','id'=>'upcoming_payment_notice_use_custom_template']) !!}
                                                                    {!! Form::label('upcoming_payment_notice_use_custom_template',trans('fi.upcoming_payment_notice').' '.trans('fi.use_custom_mail_template') , ['class'=>'form-check-label','for' => 'upcoming_payment_notice_use_custom_template']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <ol class="breadcrumb float-right">
                                                                <li class="breadcrumb-item">
                                                                    <a href="javascript:void(0);"
                                                                       class="btn btn-xs btn-default float-right custom-toggle-class btn-custom-toggle"
                                                                       data-card-name="upcomingPaymentNotice"
                                                                       id="btn-upcomingPaymentNotice-custom-toggle">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-2 custom-template-scroller">
                                                <div class="p-0 col-12 custom-upcomingPaymentNotice-sourceCode-display d-block">
                                                    {!! Form::textarea('setting[upcomingPaymentNoticeCustomMailTemplate]', config('fi.upcomingPaymentNoticeCustomMailTemplate'), ['class' => 'sourceCode form-control form-control-sm bg-light',  'id'=>"upcomingPaymentNotice-sourceCode", 'placeholder' =>  trans('fi.placeholder_type_email_template')  ,'rows' => 23]) !!}
                                                </div>
                                                <div class="p-0 d-none custom-upcomingPaymentNotice-iframe-display col-12">
                                                    <iframe class="border-1" name="targetCode"
                                                            id="upcomingPaymentNotice-targetCode"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('fi.upcoming_payment_notice_frequency') }}: </label>
                            {!! Form::text('setting[upcomingPaymentNoticeFrequency]', config('fi.upcomingPaymentNoticeFrequency'), ['class' => 'form-control form-control-sm']) !!}
                            <span class="help-block">{{ trans('fi.upcoming_payment_notice_frequency_help') }}</span>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                {!! Form::checkbox('setting[upcomingPaymentNoticeAttachInvoice]', 1, config('fi.upcomingPaymentNoticeAttachInvoice'), ['id' => 'upcoming_payment_notice_attach_invoice', 'class' => 'custom-control-input']) !!}
                                <label for="upcoming_payment_notice_attach_invoice"
                                       class="custom-control-label"> {{ trans('fi.attach_invoice_pdf') }} </label>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
</div>