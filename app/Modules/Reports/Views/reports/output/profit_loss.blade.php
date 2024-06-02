@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.profit_and_loss') }}
@stop
@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.profit_and_loss') }}</h1>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>
    <br>
    <table class="alternate" style=" font-size: 12px;">
        <thead>
        <tr>
            <th></th>
            <th class="amount theme-color">{{ trans('fi.total') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="theme-color" style="font-weight: bold;">{{ trans('fi.income') }}</td>
            <td class="amount theme-color" style="font-weight: bold;">{{ $results['income'] }}</td>
        </tr>
        <tr>
            <td class="theme-color" style="font-weight: bold;">{{ trans('fi.expenses') }}</td>
            <td></td>
        </tr>
        @foreach ($results['expenses'] as $category => $amount)
            <tr>
                <td class="theme-color" style="text-indent: 15px;">{{ $category }}</td>
                <td class="amount theme-color">{{ $amount }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="theme-color" style="font-weight: bold;">{{ trans('fi.total_expenses') }}</td>
            <td class="amount theme-color" style="font-weight: bold;">{{ $results['total_expenses'] }}</td>
        </tr>
        <tr>
            <td class="theme-color" style="font-weight: bold;">{{ trans('fi.net_income') }}</td>
            <td class="amount theme-color" style="font-weight: bold;">{{ $results['net_income'] }}</td>
        </tr>
        </tbody>
    </table>

@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>