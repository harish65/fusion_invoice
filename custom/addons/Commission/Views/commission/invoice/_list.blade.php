<table class="table table-hover table-striped table-sm">
    <thead>
    <tr>
        @can('invoices.update')
            <th>
                <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
            </th>
        @endcan
        <th>{!! Sortable::link('invoices.number', trans('fi.invoice')) !!}</th>
        <th>{!! Sortable::link('invoices.invoice_date', trans('fi.date')) !!}</th>
        <th>{!! Sortable::link('clients.name', trans('fi.client')) !!}</th>
        <th>{!! Sortable::link('username', trans('Commission::lang.user')) !!}</th>
        <th>{!! Sortable::link('commission_type', trans('Commission::lang.commission_type')) !!}</th>
        <th>{!! Sortable::link('recurring_invoice_items.name', trans('Commission::lang.product')) !!}</th>
        <th>{!! Sortable::link('invoice_item_amounts.subtotal', trans('Commission::lang.sub_total')) !!}</th>
        <th>{!! Sortable::link('invoice_item_commissions.amount', trans('Commission::lang.commission')) !!}</th>
        <th>{{ trans('Commission::lang.note') }}</th>
        <th>{!! Sortable::link('invoice_item_commissions.status', trans('Commission::lang.status')) !!}</th>
        <th class="text-right">{{ trans('fi.options') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($invoiceItemCommission as $commission)
        <tr>
            @can('commission.update')
                <td class="m-1">
                    <input type="checkbox" class="bulk-record" data-id="{{ $commission->id }}">
                </td>
            @endcan
            <td>
                @can('invoices.update')
                    <a href="{{ route('invoices.edit', [$commission->invoiceItem->invoice->id]) }}"
                       title="{{ trans('fi.edit') }}">{{ $commission->invoiceItem->invoice->number }}</a>
                @else
                    {{ $commission->invoiceItem->invoice->number }}
                @endcan
                <span class="badge badge-{{ $commission->invoiceItem->invoice->status }}">{{ trans('fi.' . $commission->invoiceItem->invoice->status) }}</span>
                @if ($commission->invoiceItem->invoice->viewed)
                    <span class="badge badge-success">{{ trans('fi.viewed') }}</span>
                @endif
            </td>
            <td>{{ $commission->invoiceItem->invoice->formatted_invoice_date}}</td>
            <td>
                @can('clients.view')
                    <a href="{{ route('clients.show', [$commission->invoiceItem->invoice->client->id]) }}"
                       title="{{ trans('fi.view_client') }}">
                        {{ $commission->invoiceItem->invoice->client->name }}
                    </a>
                @else
                    {{ $commission->invoiceItem->invoice->client->name}}
                @endcan

            </td>
            <td>
                <a href="{{ route('users.edit', [$commission->user->id, $commission->user->user_type]) }}">
                    {{ $commission->user->name}}
                </a>
            </td>
            <td>{{ $commission->type->name}}</td>
            <td>{{ $commission->invoiceItem->name}}</td>
            <td>{{ $commission->invoiceItem->amount->formatted_subtotal}}</td>
            <td>{{ $commission->formatted_amount }}</td>
            <td>{{ $commission->note }}</td>
            <td>{!! $commission->formatted_status !!}</td>
            <td align="right">
                @if(Gate::check('commission.update') || Gate::check('commission.delete'))
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                            {{ trans('fi.options') }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            @can('commission.update')
                                <a class="edit-commission dropdown-item"
                                   href="{{ route('invoice.commission.edit', [$commission->id,$commission->invoiceItem->invoice->id]) }}">
                                    <i class="fa fa-edit"></i> {{ trans('fi.edit') }}
                                </a>
                            @endcan
                            @can('commission.delete')
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="commission-delete dropdown-item text-danger"
                                   data-action="{{ route('invoice.commission.delete', [$commission->id]) }}">
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
