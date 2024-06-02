<div class="card card-primary card-outline">
    <div class="card-body">
        <span class="float-left"><strong>{{ trans('fi.subtotal') }}</strong></span>
        <span class="float-right">{{ $invoice->amount->formatted_subtotal }}</span>

        <div class="clearfix"></div>

        @if ($invoice->discount > 0)
            <span class="float-left"><strong>{{ trans('fi.discount') }}</strong></span>
            <span class="float-right">{{ $invoice->amount->formatted_discount }}</span>

            <div class="clearfix"></div>
        @endif

        <span class="float-left"><strong>{{ trans('fi.tax') }}</strong></span>
        <span class="float-right">{{ $invoice->amount->formatted_tax }}</span>

        <div class="clearfix"></div>

        @if ($invoice->total_convenience_charges > 0)
            <span class="float-left"><strong>{{ trans('fi.conven_charges') }}</strong></span>
            <span class="float-right">{{ $invoice->formatted_total_convenience_charges }}</span>

            <div class="clearfix"></div>
        @endif

        <span class="float-left"><strong>{{ trans('fi.total') }}</strong></span>
        <span class="float-right">{{ $invoice->amount->formatted_total }}</span>

        <div class="clearfix"></div>

        <span class="float-left"><strong>{{ ($invoice->type=='credit_memo') ? trans('fi.applied') : trans('fi.paid')}}</strong></span>
        <span class="float-right">{{ $invoice->amount->formatted_paid }}</span>

        <div class="clearfix"></div>

        <span class="float-left"><strong>{{ trans('fi.balance') }}</strong></span>
        <span class="float-right">{{ $invoice->amount->formatted_balance }}</span>

        <div class="clearfix"></div>
    </div>
</div>
