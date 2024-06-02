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
    @foreach ($seeds as $task)
        <tr>
            <td>{{ $task->id }}</td>
            <td>
                <a target="_blank" href="{{ route('clients.show', [$task->client->id]) }}"
                   title="{{ trans('fi.view_client') }}">{{ $task->client->name }}</a>
            </td>
            <td>
                <a target="_blank" href="{{ route('task.show', $task->id) }}">{{ $task->formatted_short_title  }}</a>
            </td>
            <td>{!! $task->formatted_description !!}</td>
            <td>{!! $task->formatted_due_date !!}</td>
            <td>{{ $task->formatted_assignee }}</td>
            <td>{!! $task->formatted_completed_date !!}</td>
            <td>{{ $task->is_complete == 1 ? trans('fi.transition.completed') : trans('fi.open') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
