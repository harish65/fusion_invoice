<table class="table table-hover table-striped table-sm text-nowrap">
    <thead>
    <tr>
        @foreach($tableHeaders as $tableHeader)
            <th>
                {{ trans('fi.'.$tableHeader) }}
            </th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @foreach ($seeds as $invoice)
        <tr>
            <td class="{{(isset($client_view) && $invoice->type=='credit_memo') ? 'column-credit-memo' : ''}}">
                @if(($invoice->status == 'sent') && $invoice->virtual_status != null && (in_array('paid', $invoice->virtual_status )))
                @else
                    @if($invoice->status == 'sent' && (in_array('mailed', $invoice->virtual_status) || in_array('emailed', $invoice->virtual_status)))
                    @else
                        @if($invoice->status != '')
                            <span class="badge badge-{{ $invoice->status }}">{{ trans('fi.' . $invoice->status) }}</span>
                        @endif
                    @endif
                @endif

                @if ($invoice->viewed)
                    <span class="badge badge-viewed">{{ trans('fi.viewed') }}</span>
                @endif

                @if ($invoice->virtual_status != null)
                    @foreach($invoice->virtual_status as $virtual_status)
                        @if($virtual_status != 'all_statuses' && $virtual_status != 'viewed')
                            <span class="badge badge-{{ $virtual_status }}">{{ trans('fi.' . $virtual_status) }}</span>
                        @endif
                    @endforeach
                @endif
            </td>
            <td>
                <a target="_blank" href="{{ route('invoices.edit', [$invoice->id]) }}"
                   title="{{ trans('fi.edit') }}">{{ $invoice->number }}
                </a>
            </td>
            <td class="hidden-xs">{{ $invoice->formatted_invoice_date }}</td>
            <td class="hidden-md hidden-sm hidden-xs"
                @if ($invoice->isOverdue) style="color: red; font-weight: bold;" @endif>{{ $invoice->formatted_due_at }}
            </td>
            <td>
                <a target="_blank" href="{{ route('clients.show', [$invoice->client->id]) }}"
                   title="{{ trans('fi.view_client') }}">{{ $invoice->client->name }}
                </a>
            </td>
            <td>{{ $invoice->short_summary }}</td>
            <td>{{ $invoice->formatted_tags }}</td>
            <td>{{ $invoice->amount->formatted_total }}</td>
            <td>{{ $invoice->amount->formatted_balance }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
