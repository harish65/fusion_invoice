@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.revenue_by_client') }}
@stop
@section('content')

    <h1 class="theme-color" style="text-align: center;">{{ trans('fi.revenue_by_client') }}</h1>

    <table class="alternate" style=" font-size: 12px;">
        <thead>
        <tr>
            <th class="theme-color">{{ trans('fi.client') }}</th>
            <th class="theme-color">{{ trans('fi.year') }}</th>
            @foreach ($months as $month)
                <th class="amount theme-color">{{ $month }}</th>
            @endforeach
            <th class="amount theme-color">{{ trans('fi.total') }}</th>
        </tr>
        </thead>
        <tbody>
        @if(count($results['clients']) == 0)
            <h5 style="padding-top: 50;padding-bottom: 50;">{{ trans('fi.no_records_found') }}</h5>
        @endif 
        @foreach ($results['clients'] as $client)
            <tr>
                <td class="theme-color">{{ $client['client'] }}</td>
                <td class="theme-color">{{ $client['year'] }}</td>
                @foreach (array_keys($client['months']) as $monthKey)
                    <td class="amount theme-color">{{ revenueByClientCurrencyFormatter($client['months'][$monthKey]) }}</td>
                @endforeach
                <td class="amount theme-color">{{ revenueByClientCurrencyFormatter($client['total']) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="14" class="amount theme-color"><strong>{{ trans('fi.total') }}</strong></td>
            <td class="amount theme-color"><strong>{{ $results['grand_total'] }}</strong></td>
        </tr>
        </tbody>
    </table>

@stop
<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>