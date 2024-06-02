@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.client_statement') }}
@stop
@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;" >{{ trans('fi.client_statement') }}</h1>
    <h3 class="theme-color" style="margin-top: 0; margin-bottom: 0;" >{{ $results['client_name'] }}</h3>
    <h3 class="theme-color" style="margin-top: 0;" >{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>
    <br>
    <table class="alternate" style=" font-size: 12px;">
        <thead>
            <tr>
                <th class="theme-color">{{ trans('fi.date') }}</th>
                <th class="theme-color">{{ trans('fi.invoice') }}</th>
                <th class="theme-color">{{ trans('fi.summary') }}</th>
                <th class="amount theme-color">{{ trans('fi.subtotal') }}</th>
                <th class="amount theme-color">{{ trans('fi.discount') }}</th>
                <th class="amount theme-color">{{ trans('fi.tax') }}</th>
                <th class="amount theme-color">{{ trans('fi.total') }}</th>
                <th class="amount theme-color">{{ trans('fi.paid') }}</th>
                <th class="amount theme-color">{{ trans('fi.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(count($results['records']) == 0)
                <tr>
                    <td class="theme-color" colspan="9" text-align="center" style="padding-top: 40;">
                        <h5>{{ trans('fi.no_records_found') }}</h5></td>
                </tr>
            @else
                @foreach ($results['records'] as $key => $records)
                    @if(count($results['records']) > 1)
                    <tr>
                        <td class="theme-color" colspan="9" text-align="center"><h2>{{ trans('fi.currency') }}: {{ $key }}</h2></td>
                    </tr>
                    @endif
                    @foreach ($records as $record)
                        <tr>
                            <td class="theme-color">{{ $record['formatted_invoice_date'] }}</td>
                            <td class="theme-color {{ $record['type'] == 'credit_memo' ? 'text-danger' : '' }}" title="{{ $record['type'] == 'credit_memo' ? trans('fi.credit_memo') : '' }}">{{ $record['number'] }}</td>
                            <td>{{ $record['summary'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_subtotal'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_discount'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_tax'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_total'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_paid'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_balance'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3"></td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $results['subtotal'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $results['discount'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $results['tax'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $results['total'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $results['paid'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $results['balance'][$key] }}</td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    @if(count($results['prepayments']) > 0)
    <h2 class="theme-color" style="margin-top: 0; margin-bottom: 0;">{{ trans('fi.pre_payment') }}</h2>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>
    <br>
    <table class="alternate" style=" font-size: 12px;">
            <thead>
            <tr>
                <th class="theme-color">{{ trans('fi.date') }}</th>
                <th class="amount theme-color">{{ trans('fi.total') }}</th>
                <th class="amount theme-color">{{ trans('fi.balance') }}</th>
            </tr>
            </thead>
            <tbody>

            @foreach ($results['prepayments']['records'] as $key => $records)
                @foreach ($records as $record)
                    <tr>
                        <td class="theme-color">{{ $record['formatted_invoice_date'] }}</td>
                        <td class="amount theme-color">{{ $record['formatted_total'] }}</td>
                        <td class="amount theme-color">{{ $record['formatted_balance'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td class="amount theme-color" style="font-weight: bold;">{{ $results['total'][$key] }}</td>
                    <td class="amount theme-color" style="font-weight: bold;">{{ $results['balance'][$key] }}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>