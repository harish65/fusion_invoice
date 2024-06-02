<style>
    .tools {
        display: none;
    }

    .timer-row:hover .tools {
        display: block;
    }
</style>

<table class="table table-hover table-striped table-sm text-nowrap mt-5">
    <thead>
    <tr>
        <th></th>
        <th>{{ trans('TimeTracking::lang.start_time') }}</th>
        <th>{{ trans('TimeTracking::lang.stop_time') }}</th>
        <th>{{ trans('TimeTracking::lang.hours') }}</th>
        <th style="width: 4%;"></th>
        <th style="width: 5%;"></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($timers as $timer)
        <tr class="timer-row">
            <td class="{!! ($timer->tense == 'ct' ? 'timer-today' : 
                           ($timer->tense == 'py' ? 'timer-yesterday' : 
                           ($timer->tense == 'po' ? 'timer-in-the-past' : 
                           ($timer->tense == 'ft' ? 'timer-tomorrow' : 
                           'timer-in-the-future')))) !!}"> 
                {{ $timer->formatted_start_at_for_humans }} 
            </td>
            <td>{{ $timer->formatted_start_at }}</td>
            <td>{{ $timer->formatted_end_at }}</td>
            <td>{{ $timer->formatted_hours }}</td>
            <td>
                @if ($timer->formatted_end_at)
                    @can('time_tracking.update')
                    <div class="tools">
                        <a href="javascript:void(0)" class="btn-edit-timer" data-start-at="{{ $timer->start_at }}" data-end-at="{{ $timer->end_at }}" data-timer-id="{{ $timer->id }}" data-toggle="tooltip" title="{{ trans('TimeTracking::lang.edit_timer') }}">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>
                    @endcan
                @endif
            </td>
            <td>
                @if ($timer->formatted_end_at)
                    @can('time_tracking.delete')
                    <div class="tools">
                        <a href="javascript:void(0)" class="btn-delete-timer text-danger" data-timer-id="{{ $timer->id }}" data-toggle="tooltip" title="{{ trans('TimeTracking::lang.delete_timer') }}">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    @endcan
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>