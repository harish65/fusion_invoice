<table class="table table-hover table-striped table-sm text-nowrap">
    <thead>
    <tr>
        @foreach($tableHeaders as $tableHeader)
            <th class="{{in_array($tableHeader,['active','balance']) ? 'text-right' : ''}}">
                {{ trans('fi.'.$tableHeader) }}
            </th>
        @endforeach
    </tr>
    </thead>

    <tbody>
    @foreach ($seeds as $client)
        <tr>
            <td>{{ $client->id }}</td>
            <td style="text-decoration: line-through;">
                <a target="_blank" href="{{ route('clients.show', [$client->id]) }}">{{ $client->name }}</a>
            </td>
            <td>{{ $client->email }}</td>
            <td>
                <span class="badge {{ isset($typeLabels[$client->type]) ? $typeLabels[$client->type] : '' }}">
                   {{ trans('fi.' . $client->type) }}
                </span>
            </td>
            <td>{{ $client->address }}</td>
            @if(config('fi.clientColumnSettingsPhoneNumber') == 1 )
                <td>{{ (($client->phone ? $client->phone : ($client->mobile ? $client->mobile : ''))) }}</td>
            @endif
            <td>{{ $client->formatted_created_at  }}</td>
            <td style="text-align: right;">{{ $client->formatted_balance }}</td>
            <td style="text-align: right;">{{ ($client->active) ? trans('fi.yes') : trans('fi.no') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
