@include('settings._js_mail')

<div class="modal fade" id="modal-mail-test">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.send_test_email') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label>{{ trans('fi.from') }}</label>
                        {!! Form::select('from', $fromMail,'', ['id' => 'from', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.to') }}</label>
                        {!! Form::select('to', $to, $testMail, ['id' => 'to', 'class' => 'form-control form-control-sm', 'multiple' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.cc') }}</label>
                        {!! Form::select('cc', $cc, config('fi.mailDefaultCc'), ['id' => 'cc', 'class' => 'form-control form-control-sm', 'multiple' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.bcc') }}</label>
                        {!! Form::select('bcc', $bcc, config('fi.mailDefaultBcc'), ['id' => 'bcc', 'class' => 'form-control form-control-sm', 'multiple' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.subject') }}</label>
                        {!! Form::text('subject', $subject, ['id' => 'subject', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                    <div class="form-group">
                        <label>{{ trans('fi.body') }}</label>
                        {!! Form::textarea('body', $body, ['id' => 'body', 'class' => 'form-control form-control-sm', 'rows' => 3]) !!}
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-submit-mail-test" class="btn btn-sm btn-primary" data-loading-text="{{ trans('fi.sending') }}..." data-original-text="{{ trans('fi.send') }}">{{ trans('fi.send') }}</button>
            </div>
        </div>
    </div>
</div>