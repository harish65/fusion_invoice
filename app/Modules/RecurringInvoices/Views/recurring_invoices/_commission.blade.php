<div class="tab-pane" id="tab-commission">
    <div class="card card-default">
        <div class="card-header">
            <div class="card-tools pull-right">
                @can('commission.update')
                <button name="add_commission" class="btn btn-sm btn-primary create-recurring-commission"
                        style="float: right;"
                        data-id="{{$recurringInvoice->id}}"
                        data-action="{{ route('recurring.invoice.commission.create', [$recurringInvoice->id]) }}"
                        data-type="invoice"><i class="fa fa-plus"></i> {{ trans('Commission::lang.add') }}
                </button>
                @endcan
            </div>
        </div>
        <input type="hidden" value="{{ route('recurring.invoice.commission.load', [$recurringInvoice->id]) }}"
               class="commission_uri">
        @can('commission.view')
        <script>
            $(function () {
                loadInvoiceCommission();
            });
        </script>
        <div class="card-body">
            <div class="renderCommission"></div>
        </div>
        @endcan
    </div>
</div>

