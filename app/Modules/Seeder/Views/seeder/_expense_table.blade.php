<table class="table table-hover table-striped table-sm text-nowrap">
    <thead>
    <tr>
        @foreach($tableHeaders as $tableHeader)
            <th class="{{in_array($tableHeader,['amount']) ? 'text-right' : ''}}">
                {{ trans('fi.'.$tableHeader) }}
            </th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @foreach ($seeds as $expense)
        <tr>
            <td>
                <a target="_blank" href="{{ route('expenses.edit', [$expense->id]) }}"
                   title="{{ trans('fi.edit') }}">{{ $expense->id }}
                </a>
            </td>
            <td>
                <a target="_blank" href="{{ route('expenses.vendors.edit', [$expense->vendor_id]) }}">
                    {{ $expense->vendor->name }}
                </a>
            </td>
            <td>{{ $expense->formatted_expense_date  }}</td>
            <td>
                {{ $expense->category->name }}
                @if ($expense->vendor_id)
                    <br><span class="text-muted">{{ $expense->vendor->name }}</span>
                @endif
            </td>
            <td>{!! $expense->formatted_description !!}</td>
            <td class="text-right">{{ $expense->formatted_amount }}</td>
        </tr>
    @endforeach
    </tbody>
</table>