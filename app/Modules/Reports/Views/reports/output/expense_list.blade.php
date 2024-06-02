@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.expense_list') }}
@stop
@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.expense_list') }}</h1>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

    <table class="alternate" style=" font-size: 12px;">
        @if(count($results['records']) > 0)
            @foreach ($results['records'] as $key=>$items)
                <thead>
                @if($key != 'none')
                <tr>
                    <td colspan="7" text-align="center"><h2 class="theme-color">{{ $items['group_name'] }}</h2></td>
                </tr>
                @endif
                <tr>
                    <th class="theme-color" style="width: 10%; text-align: left;">{{ trans('fi.date') }}</th>
                    <th class="theme-color" style="width: 20%; text-align: left;">{{ trans('fi.client') }}</th>
                    <th class="theme-color" style="width: 20%; text-align: left;">{{ trans('fi.category') }}</th>
                    <th class="theme-color" style="width: 15%; text-align: left;">{{ trans('fi.vendor') }}</th>
                    <th class="theme-color" style="width: 5%; text-align: left;">{{ trans('fi.billed') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.amount') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.tax') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.total') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($items['items'] as $expense)
                    <tr>
                        <td class="theme-color">{{ $expense['date'] }}</td>
                        <td class="theme-color">{{ $expense['client'] }}</td>
                        <td class="theme-color">{{ $expense['category'] }}</td>
                        <td class="theme-color">{{ $expense['vendor'] }}</td>
                        <td class="theme-color">{{ ($expense['billed']) ? trans('fi.yes') : trans('fi.no') }}</td>
                        <td class="amount theme-color">{{ $expense['amount'] }}</td>
                        <td class="amount theme-color">{{ $expense['tax'] }}</td>
                        <td class="amount theme-color">{{ $expense['formatted_total'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="amount theme-color" colspan="7"><strong>{{ trans('fi.subtotal') }}</strong></td>
                    <td class="amount theme-color"><strong>{{ $items['totals']['formatted_subtotal'] }}</strong></td>
                </tr>
                </tbody>
            @endforeach
            <tr>
                <td class="amount theme-color"colspan="7"><strong>{{ trans('fi.total') }}</strong></td>
                <td class="amount theme-color"><strong>{{ $results['formatted_total'] }}</strong></td>
            </tr>
        @else
            <tr><td><h4 class="theme-color">{{ trans('fi.no_records_found') }}</h4></td></tr>
        @endif
    </table>

@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>