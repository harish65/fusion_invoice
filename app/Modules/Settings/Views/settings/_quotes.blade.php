<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_default_quote_template') !!}">
                {{ trans('fi.default_quote_template') }}: </label>
            {!! Form::select('setting[quoteTemplate]', $quoteTemplates, config('fi.quoteTemplate'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_default_doc_scheme') !!}">
                {{ trans('fi.default_document_number_scheme') }}: </label>
            {!! Form::select('setting[quoteGroup]', $documentNumberSchemes, config('fi.quoteGroup'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_expire_days') !!}">
                {{ trans('fi.quotes_expire_after') }}: </label>
            {!! Form::text('setting[quotesExpireAfter]', config('fi.quotesExpireAfter'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_default_status_filter') !!}">
                {{ trans('fi.default_status_filter') }}: </label>
            {!! Form::select('setting[quoteStatusFilter]', $quoteStatuses, config('fi.quoteStatusFilter'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_auto_convert') !!}">
                {{ trans('fi.convert_quote_when_approved') }}: </label>
            {!! Form::select('setting[convertQuoteWhenApproved]', $yesNoArray, config('fi.convertQuoteWhenApproved'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_when_converted_action') !!}">
                {{ trans('fi.convert_quote_setting') }}: </label>
            {!! Form::select('setting[convertQuoteTerms]', $convertQuoteOptions, config('fi.convertQuoteTerms'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_default_terms') !!}">
                {{ trans('fi.default_terms') }}: </label>
            {!! Form::textarea('setting[quoteTerms]', config('fi.quoteTerms'), ['class' => 'form-control form-control-sm', 'rows' => 5]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_default_footer') !!}">
                {{ trans('fi.default_footer') }}: </label>
            {!! Form::textarea('setting[quoteFooter]', config('fi.quoteFooter'), ['class' => 'form-control form-control-sm', 'rows' => 5]) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto" title="{!! trans('fi.tt_ss_quotes_if_emailed_in_draft') !!}">
                {{ trans('fi.if_quote_is_emailed_while_draft') }}: </label>
            {!! Form::select('setting[resetQuoteDateEmailDraft]', $quoteWhenDraftOptions, config('fi.resetQuoteDateEmailDraft'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3"></div>
</div>