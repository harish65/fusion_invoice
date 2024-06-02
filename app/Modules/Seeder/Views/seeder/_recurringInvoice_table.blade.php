<table class="table table-hover table-striped table-sm text-nowrap">
    <thead>
    <tr>
        @foreach($tableHeaders as $tableHeader)
            <th class="{{in_array($tableHeader,['total','balance']) ? 'text-right' : ''}}">
                {{ trans('fi.'.$tableHeader) }}
            </th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @foreach ($seeds as $recurringInvoice)
        <tr>
            <td>
                <a target="_blank" href="{{ route('recurringInvoices.edit', [$recurringInvoice->id]) }}"
                   title="{{ trans('fi.edit') }}">{{ $recurringInvoice->id }}
                </a>
            </td>
            <td>
                <a target="_blank" href="{{ route('clients.show', [$recurringInvoice->client->id]) }}" title="{{ trans('fi.view_client') }}">{{ $recurringInvoice->client->name }}</a>
            </td>
            <td>{{ $recurringInvoice->short_summary }}</td>
            <td>{{ $recurringInvoice->formatted_next_date }}</td>
            <td>{{ $recurringInvoice->formatted_stop_date }}</td>
            <td>{{ $recurringInvoice->recurring_frequency . ' ' . $frequencies[$recurringInvoice->recurring_period] }}</td>
            <td class="text-right">{{ $recurringInvoice->amount->formatted_total }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
