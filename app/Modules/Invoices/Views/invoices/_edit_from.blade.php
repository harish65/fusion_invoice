@include('invoices._js_edit_from')

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">{{ trans('fi.from') }}</h3>

        <div class="card-tools pull-right">
            <button  {{$invoiceOverlayStatus ? 'disabled' : null}} class="btn btn-sm btn-default" id="btn-change-company-profile">
                <i class="fa fa-exchange"></i> {{ trans('fi.change') }}
            </button>
        </div>
    </div>
    <div class="card-body">
        <strong>{{ $invoice->companyProfile->company }}</strong><br>
        {!! $invoice->companyProfile->formatted_address !!}<br>
        {{ trans('fi.phone') }}: {{ $invoice->companyProfile->phone }}<br>
        @if(isset($invoice->user->fromEmail)){{ trans('fi.email') }}: {{ $invoice->user->fromEmail }}@endif
    </div>
</div>
