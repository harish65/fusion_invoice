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
    @foreach ($seeds as $quote)
        <tr>
            <td>
                <span class="badge badge-{{ $quote->status }}">{{ trans('fi.' . $quote->status) }}</span>
                @if ($quote->viewed)
                    <span class="badge badge-success">{{ trans('fi.viewed') }}</span>
                @endif
            </td>
            <td>
                <a target="_blank" href="{{ route('quotes.edit', [$quote->id]) }}"
                   title="{{ trans('fi.edit') }}">{{ $quote->number }}
                </a>
            </td>
            <td>{{ $quote->formatted_quote_date }}</td>
            <td>{{ $quote->formatted_expires_at }}</td>
            <td>
                <a target="_blank" href="{{ route('clients.show', [$quote->client->id]) }}"
                   title="{{ trans('fi.view_client') }}">{{ $quote->client->name }}
                </a>
            </td>
            <td>{{ $quote->short_summary }}</td>
            <td>{{ $quote->amount->formatted_total }}</td>
            <td>
                @if ($quote->invoice)
                    <a target="_blank"
                       href="{{ route('invoices.edit', [$quote->invoice_id]) }}">{{ trans('fi.yes') }}</a>
                @else
                    {{ trans('fi.no') }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
