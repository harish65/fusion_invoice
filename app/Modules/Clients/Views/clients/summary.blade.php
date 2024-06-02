<div class="row">
    @if(isset($invoicePaymentSummary[$currency]['totalInvoiced']) && !empty($invoicePaymentSummary[$currency]['totalInvoiced']))
        <div class="col-sm-8">
            <strong class="pull-right">{{ trans('fi.total_invoiced') }}:</strong>
        </div>
        <div class="col-sm-4 text-right">
            {{ $invoicePaymentSummary[$currency]['totalInvoiced'] ?? "-" }}
        </div>
    @endif

    @if(isset($invoicePaymentSummary[$currency]['totalPaidInvoices']) && !empty($invoicePaymentSummary[$currency]['totalPaidInvoices']))
        <div class="col-sm-8">
            <strong class="pull-right">{{ trans('fi.total_paid_invoices') }}:</strong>
        </div>
        <div class="col-sm-4 text-right">
            {{ $invoicePaymentSummary[$currency]['totalPaidInvoices'] ?? "-" }}
        </div>
    @endif
    @if(isset($invoicePaymentSummary[$currency]['totalOpenInvoices']) && !empty($invoicePaymentSummary[$currency]['totalOpenInvoices']))
        <div class="col-sm-8">
            <strong class="pull-right">{{ trans('fi.open_invoices') }}:</strong>
        </div>
        <div class="col-sm-4 text-right">
            {{ $invoicePaymentSummary[$currency]['totalOpenInvoices'] ?? "-" }}
        </div>
    @endif
    @if(isset($invoicePaymentSummary[$currency]['totalOpenCredits']) && !empty($invoicePaymentSummary[$currency]['totalOpenCredits']))
        <div class="col-sm-8">
            <strong class="pull-right">{{ trans('fi.open_credits') }}:</strong>
        </div>
        <div class="col-sm-4 text-right">
            {{ $invoicePaymentSummary[$currency]['totalOpenCredits'] ?? "-" }}
        </div>
    @endif
    @if(isset($invoicePaymentSummary[$currency]['totalUnappliedPayments']) && !empty($invoicePaymentSummary[$currency]['totalUnappliedPayments']))
        <div class="col-sm-8">
            <strong class="pull-right">{{ trans('fi.unapplied_payments') }}:</strong>
        </div>
        <div class="col-sm-4 text-right">
            {{ $invoicePaymentSummary[$currency]['totalUnappliedPayments'] ?? "-" }}
        </div>
    @endif
    @if(isset($invoicePaymentSummary[$currency]['totalBalance']) && !empty($invoicePaymentSummary[$currency]['totalBalance']))
        <div class="col-sm-8">
            <strong class="pull-right">{{ trans('fi.balance') }}:</strong>
        </div>
        <div class="col-sm-4 text-right">
            {{ $invoicePaymentSummary[$currency]['totalBalance'] ?? "-" }}
        </div>
    @endif
</div>