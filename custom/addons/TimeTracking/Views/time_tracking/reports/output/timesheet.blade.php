@extends('reports.layouts.master')

@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('TimeTracking::lang.timesheet') }}</h1>
    <h3 class="theme-color" style="margin: 0;">{{ $results['company_profile'] }}</h3>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>
    <br>

    @foreach ($results['projects'] as $project)
        <h3 class="theme-color">{{ $project['name'] }}</h3>
        <table class="alternate" style=" font-size: 12px;">
            <thead>
            <tr>
                <th class="theme-color">{{ trans('TimeTracking::lang.task') }}</th>
                <th class="theme-color">{{ trans('TimeTracking::lang.start_time') }}</th>
                <th class="theme-color">{{ trans('TimeTracking::lang.stop_time') }}</th>
                <th class="amount theme-color">{{ trans('TimeTracking::lang.unbilled_hours') }}</th>
                <th class="amount theme-color">{{ trans('TimeTracking::lang.billed_hours') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($project['tasks'] as $task)
                @foreach ($task['timers'] as $timer)
                    <tr>
                        <td class="theme-color">{{ $task['name'] }}</td>
                        <td class="theme-color">{{ $timer['start_at'] }}</td>
                        <td class="theme-color">{{ $timer['end_at'] }}</td>
                        <td class="amount theme-color">@if (!$task['billed']){{ $timer['hours'] }}@endif</td>
                        <td class="amount theme-color">@if ($task['billed']){{ $timer['hours'] }}@endif</td>
                    </tr>
                @endforeach
            @endforeach
            <tr>
                <td class="total theme-color" colspan="3">{{ trans('fi.total') }}:</td>
                <td class="total theme-color">{{ $project['hours_unbilled'] }}</td>
                <td class="total theme-color">{{ $project['hours_billed'] }}</td>
            </tr>
            </tbody>
        </table>
    @endforeach

@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>