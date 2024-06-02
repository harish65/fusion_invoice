<table class="table table-hover table-striped table-sm">
    <thead>
    <tr>
        @can('invoices.update')
            <th>
                <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
            </th>
        @endcan
        <th>{!! Sortable::link('recurring_invoices.id', trans('fi.recurring_invoice')) !!}</th>
        <th>{!! Sortable::link('clients.name', trans('fi.client')) !!}</th>
        <th>{!! Sortable::link('username', trans('Commission::lang.user')) !!}</th>
        <th>{!! Sortable::link('commission_type', trans('Commission::lang.commission_type')) !!}</th>
        <th>{!! Sortable::link('recurring_invoice_items.name', trans('Commission::lang.product')) !!}</th>
        <th>{!! Sortable::link('recurring_invoice_item_amounts.subtotal', trans('Commission::lang.sub_total')) !!}</th>
        <th>{!! Sortable::link('recurring_invoice_item_commissions.amount', trans('Commission::lang.commission')) !!}</th>
        <th>{!! Sortable::link('recurring_invoice_item_commissions.stop_date', trans('Commission::lang.stop_date')) !!}</th>
        <th>{{ trans('Commission::lang.note') }}</th>
        <th class="text-right">{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($recurringInvoiceItemCommissions as $commission)
        <tr>
            @can('commission.update')
                <td class="m-1">
                    <input type="checkbox" class="bulk-record" data-id="{{ $commission->id }}">
                </td>
            @endcan
            <td>
                @can('recurring_invoices.update')
                    <a href="{{ route('recurringInvoices.edit', [$commission->invoiceItem->recurring_invoice_id]) }}"
                       title="{{ trans('fi.edit') }}">{{ $commission->invoiceItem->recurring_invoice_id }}</a>
                @else
                    {{ $commission->invoiceItem->recurring_invoice_id }}
                @endcan
            </td>
            <td>
                @can('clients.view')
                    <a href="{{ route('clients.show', [$commission->invoiceItem->recurringInvoice->client->id]) }}"
                       title="{{ trans('fi.view_client') }}">
                        {{ $commission->invoiceItem->recurringInvoice->client->name }}
                    </a>
                @else
                    {{ $commission->invoiceItem->recurringInvoice->client->name}}
                @endcan

            </td>
            <td>
                @can('users.update')
                    <a href="{{ route('users.edit', [$commission->user->id, $commission->user->user_type]) }}">
                        {{ $commission->user->name}}
                    </a>
                @else
                    {{ $commission->user->name}}
                @endcan
            </td>
            <td>{{ $commission->type->name}}</td>
            <td>{{ $commission->invoiceItem->name}}</td>
            <td>{{ $commission->invoiceItem->amount->formatted_subtotal}}</td>
            <td>{{ $commission->formatted_amount }}</td>
            <td>{{ $commission->invoiceItem->recurringInvoice->stop_date}}</td>
            <td>{{ $commission->note }}</td>
            <td align="right">
                @if(Gate::check('commission.update') || Gate::check('commission.delete'))
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.options') }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            @can('commission.update')
                                <a class="edit-commission dropdown-item"
                                   href="{{ route('recurring.invoice.commission.edit', [$commission->id,$commission->invoiceItem->recurringInvoice->id]) }}">
                                    <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                </a>
                            @endcan
                            @can('commission.delete')
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="commission-delete dropdown-item text-danger"
                                   data-action="{{ route('recurring.invoice.commission.delete', [$commission->id]) }}">
                                    <i class="fa fa-trash"></i> {{ trans('fi.delete') }}
                                </a>
                            @endcan
                        </ul>
                    </div>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
