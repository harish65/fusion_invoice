@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.recurring_invoice_list') }}
@stop
@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.recurring_invoice_list') }}</h1>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>
    @if(count($results['records']) > 0)
        @foreach ($results['records'] as $period => $period_wise_data)
            @foreach ($period_wise_data as $frequency => $frequency_wise_data)
                <h2 class="theme-color">{{ trans('fi.every') }} {{ $frequency }} {{ $period }}</h2>
                <table class="alternate" style=" font-size: 12px;">
                    <thead>
                    <tr>
                        <th class="theme-color" style="width: 10%; text-align: left;">{{ trans('fi.id') }}</th>
                        <th class="theme-color" style="width: 26%; text-align: left;">{{ trans('fi.client') }}</th>
                        <th class="theme-color" style="width: 26%; text-align: left;">{{ trans('fi.summary') }}</th>
                        <th class="theme-color" style="width: 14%; text-align: left;">{{ trans('fi.next_date') }}</th>
                        <th class="theme-color" style="width: 14%; text-align: left;">{{ trans('fi.stop_date') }}</th>
                        <th class="amount theme-color" style="width: 10%;">{{ trans('fi.total') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($frequency_wise_data as $item)
                        <tr>
                            <td class="theme-color">{{ $item['id'] }}</td>
                            <td class="theme-color">{{ $item['client_name'] }}</td>
                            <td class="theme-color">{{ $item['summary'] }}</td>
                            <td class="theme-color">{{ $item['next_date'] }}</td>
                            <td class="theme-color">{{ $item['stop_date'] }}</td>
                            <td class="theme-color" text-align="right">{{ $item['total'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="amount theme-color" colspan="4"><strong>{{ trans('fi.total') }}</strong></td>
                        <td class="amount theme-color"><strong>{{ trans('fi.invoices') }} {{ $results['total_invoice'][$period][$frequency] }}</strong></td>
                        <td class="amount theme-color"><strong>{{ $results['total_amount'][$period][$frequency] }}</strong></td>
                    </tr>
                    </tbody>

                </table>
            @endforeach
        @endforeach
        <table class="alternate" style=" font-size: 12px;">
            <tbody>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td class="theme-color" style="width: 20%;"></td>
                <td class="theme-color" style="width: 20%;"></td>
                <td class="theme-color" style="width: 16%;"></td>
                <td style="width: 20%;" class="amount theme-color"><strong>{{ trans('fi.report_total') }}</strong></td>
                <td style="width: 14%;" class="amount theme-color">
                    <strong>{{ trans('fi.invoices') }} {{ $results['grand_total_invoice'] }}</strong>
                </td>
                <td style="width: 10%;" class="amount theme-color"><strong>{{ $results['grand_total_amount'] }}</strong></td>
            </tr>
            </tbody>
        </table>
    @else
        <h4 class="theme-color" style="padding-top: 50;padding-bottom: 50;">{{ trans('fi.no_records_found') }}</h4>
    @endif

@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>