<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_default_invoice_template') }}">{{ trans('fi.default_invoice_template') }}
                : </label>
            {!! Form::select('setting[invoiceTemplate]', $invoiceTemplates, config('fi.invoiceTemplate'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_default_doc_scheme') }}">{{ trans('fi.default_document_number_scheme') }}
                : </label>
            {!! Form::select('setting[invoiceGroup]', $documentNumberSchemes, config('fi.invoiceGroup'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_due_after') }}">{{ trans('fi.invoices_due_after') }}: </label>
            {!! Form::text('setting[invoicesDueAfter]', config('fi.invoicesDueAfter'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_default_status_filter') }}">{{ trans('fi.default_status_filter') }}
                : </label>
            {!! Form::select('setting[invoiceStatusFilter]', $invoiceStatuses, config('fi.invoiceStatusFilter'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_default_terms') }}">{{ trans('fi.default_terms') }}: </label>
            {!! Form::textarea('setting[invoiceTerms]', config('fi.invoiceTerms'), ['class' => 'form-control form-control-sm', 'rows' => 5]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_default_footer') }}">{{ trans('fi.default_footer') }}: </label>
            {!! Form::textarea('setting[invoiceFooter]', config('fi.invoiceFooter'), ['class' => 'form-control form-control-sm', 'rows' => 5]) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_automatic_email_on_recur') }}">{{ trans('fi.automatic_email_on_recur') }}
                : </label>
            {!! Form::select('setting[automaticEmailOnRecur]', ['0' => trans('fi.no'), '1' => trans('fi.yes')], config('fi.automaticEmailOnRecur'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_automatic_email_payment_receipts') }}">{{ trans('fi.automatic_email_payment_receipts') }}
                : </label>
            {!! Form::select('setting[automaticEmailPaymentReceipts]', ['0' => trans('fi.no'), '1' => trans('fi.yes')], config('fi.automaticEmailPaymentReceipts'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_online_payment_method') }}">{{ trans('fi.online_payment_method') }}
                : </label>
            {!! Form::select('setting[onlinePaymentMethod]', $paymentMethods, config('fi.onlinePaymentMethod'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_qr_code_on_invoice_quote') }}">{{ trans('fi.qr_code_on_invoice_quote') }}
                : </label>
            {!! Form::select('setting[qrCodeOnInvoiceQuote]', $yesNoArray, config('fi.qrCodeOnInvoiceQuote'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_allow_invoice_delete') }}">{{ trans('fi.allow_invoice_delete') }}
                : </label>
            {!! Form::select('setting[allowInvoiceDelete]', $yesNoArray, config('fi.allowInvoiceDelete'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_show_invoices_from') }}">{{trans('fi.show_invoices_from')}}
                : </label>
            {!! Form::select('setting[showInvoicesFrom]', $showInvoicesFrom, config('fi.showInvoicesFrom'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::checkbox('setting[secure_link]', 1, config('fi.secure_link'), ['class' => 'all-selected' ,'id' => 'secure_link']) !!}
            <label data-toggle="tooltip" data-placement="auto" for="secure_link"
                   title="{{ trans('fi.tt_secure_link_and_expire_link_days') }}">{{ trans('fi.secure_link_and_expire_link_days') }}
                : </label>
            <div class="row mb-3">
                <label class="col-sm-6 col-form-label">{{ trans('fi.secure_link_expire_days') }}: </label>
                <div class="col-sm-6">
                    {!! Form::text('setting[secure_link_expire_day]',config('fi.secure_link_expire_day'), ['class' => 'form-control form-control-sm','id' => 'secure_link_expire_day','placeholder'=>trans('fi.enter_days')]) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_invoices_if_emailed_while_draft') }}">{{ trans('fi.if_invoice_is_emailed_while_draft') }}
                : </label>
            {!! Form::select('setting[resetInvoiceDateEmailDraft]', $invoiceWhenDraftOptions, config('fi.resetInvoiceDateEmailDraft'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_allow_edit_invoices_status') }}">{{ trans('fi.allow_edit_invoices_status') }}
                : </label>
            {!! Form::select('setting[allowEditInvoiceStatus]', ['draft'=>trans('fi.draft'),'draft_and_sent'=>trans('fi.draft_and_sent'),'draft_or_sent_and_paid'=>trans('fi.draft_or_sent_and_paid'),], config('fi.allowEditInvoiceStatus'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{!! trans('fi.tt_gen_allow_line_item_discounts') !!}">
                {{ trans('fi.allow_line_item_discounts') }}: </label>
            {!! Form::select('setting[allowLineItemDiscounts]', $yesNoArray, config('fi.allowLineItemDiscounts'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label data-toggle="tooltip" data-placement="auto"
                   title="{{ trans('fi.tt_ss_include_draft_invoices_unpaid_and_overdue') }}">{{ trans('fi.include_draft_invoices_unpaid_and_overdue') }}
                : </label>
            {!! Form::select('setting[includeDraftInvoicesUnpaidAndOverdue]', $yesNoArray, config('fi.includeDraftInvoicesUnpaidAndOverdue'), ['class' => 'form-control form-control-sm']) !!}
        </div>
    </div>
</div>