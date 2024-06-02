@include('recurring_invoices._js_edit_from')

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">{{ trans('fi.from') }}</h3>

        <div class="card-tools pull-right">
            <button class="btn btn-sm btn-default" id="btn-change-company-profile">
                <i class="fa fa-exchange"></i> {{ trans('fi.change') }}
            </button>
        </div>
    </div>
    <div class="card-body">
        <strong>{{ $recurringInvoice->companyProfile->company }}</strong><br>
        {!! $recurringInvoice->companyProfile->formatted_address !!}<br>
        {{ trans('fi.phone') }}: {{ $recurringInvoice->companyProfile->phone }}<br>
        {{ trans('fi.email') }}: {{ $recurringInvoice->user->email }}
    </div>
</div>