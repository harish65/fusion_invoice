@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.item_sales') }}
@stop
@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.item_sales') }}</h1>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>


    <table class="alternate" style=" font-size: 12px;">
        @if(count($results['records']) > 0)
            @foreach ($results['records'] as $key=>$items)
                <thead>
                <tr>
                    <td colspan="9" text-align="center"><h2 class="theme-color">{{ $key }}</h2></td>
                </tr>
                <tr>
                    <th class="theme-color" style="width: 10%; text-align: left;">{{ trans('fi.date') }}</th>
                    <th class="theme-color" style="width: 10%; text-align: left;">{{ trans('fi.invoice') }}</th>
                    <th class="theme-color" style="width: 15%; text-align: left;">{{ trans('fi.client') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.price') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.quantity') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.subtotal') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.discount') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.tax') }}</th>
                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.total') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($items['items'] as $item)
                    <tr>
                        <td class="theme-color">{{ $item['date'] }}</td>
                        <td class="theme-color">{{ $item['invoice_number'] }}</td>
                        <td class="theme-color">{{ $item['client_name'] }}</td>
                        <td class="amount theme-color">{{ $item['price'] }}</td>
                        <td class="amount theme-color">{{ $item['quantity'] }}</td>
                        <td class="amount theme-color">{{ $item['subtotal'] }}</td>
                        <td class="amount theme-color">{{ $item['discount'] }}</td>
                        <td class="amount theme-color">{{ $item['tax'] }}</td>
                        <td class="amount theme-color">{{ $item['total'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="amount theme-color"colspan="4"><strong>{{ trans('fi.total') }}</strong></td>
                    <td class="amount theme-color"><strong>{{ $items['totals']['quantity'] }}</strong></td>
                    <td class="amount theme-color"><strong>{{ $items['totals']['subtotal'] }}</strong></td>
                    <td class="amount theme-color"><strong>{{ $items['totals']['discount'] }}</strong></td>
                    <td class="amount theme-color"><strong>{{ $items['totals']['tax'] }}</strong></td>
                    <td class="amount theme-color"><strong>{{ $items['totals']['total'] }}</strong></td>
                </tr>
                </tbody>
            @endforeach
            <tr>
                <td class="amount theme-color" colspan="8"><strong>{{ trans('fi.total') }}</strong></td>
                <td class="amount theme-color"><strong>{{ $results['grand_total'] }}</strong></td>
            </tr>
        @else
            <tr><td><h4 class="theme-color">{{ trans('fi.no_records_found') }}</h4></td></tr>
        @endif
    </table>
@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>