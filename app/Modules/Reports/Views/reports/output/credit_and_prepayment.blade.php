@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.credit-memo-and-prepayments') }}
@stop
@section('content')
    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.credit-memo-and-prepayments') }}</h1>
    
    @if(count($credits) + count($pre_payments) == 0)
        <h5 style="padding-top: 100;">{{ trans('fi.no_records_found') }}</h5>
    @endif 
    
    @foreach ($credits as $client => $credit)

        @if(count($credit['records']) > 0)
            <h2 class="theme-color">{{ $client }}</h2>
            <h2 class="theme-color">{{ $credit['from_date'] }} - {{ $credit['to_date'] }}</h2>
            <br>
            <h2 class="text-left theme-color">{{ trans('fi.credit_memos') }}</h2>
            <table class="alternate" style=" font-size: 12px;">
                <thead>
                <tr>
                    <th class="theme-color"width="10%">{{ trans('fi.date') }}</th>
                    <th class="theme-color"width="10%">{{ trans('fi.credit_memo') }}</th>
                    <th class="amount theme-color">{{ trans('fi.total') }}</th>
                    <th class="amount theme-color">{{ trans('fi.paid') }}</th>
                    <th class="amount theme-color">{{ trans('fi.balance') }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($credit['records'] as $key => $records)
                    @if(count($credit['records']) > 1)
                        <tr>
                            <td colspan="5" text-align="center"><h2 class="theme-color">{{ trans('fi.currency') }}: {{ $key }}</h2></td>
                        </tr>
                    @endif
                    @foreach ($records as $record)
                        <tr>
                            <td class="theme-color">{{ $record['formatted_invoice_date'] }}</td>
                            <td class="theme-color">{{ $record['number'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_total'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_paid'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_balance'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $credit['total'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $credit['paid'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $credit['balance'][$key] }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        @endif
    @endforeach

    @foreach ($pre_payments as $client => $pre_payment)

        @if(count($pre_payment['records']) > 0)
            <h2 class="theme-color">{{ $client }}</h2>
            <h2 class="theme-color">{{ $pre_payment['from_date'] }} - {{ $pre_payment['to_date'] }}</h2>
            <br>
            <h2 class="text-left theme-color">{{ trans('fi.pre_payments') }}</h2>
            <table class="alternate" style=" font-size: 12px;">
                <thead>
                <tr>
                    <th class="theme-color" width="10%">{{ trans('fi.date') }}</th>
                    <th class="theme-color" width="10%">&nbsp;</th>
                    <th class="amount theme-color">{{ trans('fi.total') }}</th>
                    <th class="amount theme-color">{{ trans('fi.paid') }}</th>
                    <th class="amount theme-color">{{ trans('fi.balance') }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($pre_payment['records'] as $key => $records)
                    @if(count($pre_payment['records']) > 1)
                        <tr>
                            <td class="theme-color" colspan="5" text-align="center"><h2>{{ trans('fi.currency') }}: {{ $key }}</h2></td>
                        </tr>
                    @endif
                    @foreach ($records as $record)
                        <tr>
                            <td class="theme-color">{{ $record['formatted_invoice_date'] }}</td>
                            <td>&nbsp;</td>
                            <td class="amount theme-color">{{ $record['formatted_total'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_paid'] }}</td>
                            <td class="amount theme-color">{{ $record['formatted_balance'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $pre_payment['total'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $pre_payment['paid'][$key] }}</td>
                        <td class="amount theme-color" style="font-weight: bold;">{{ $pre_payment['balance'][$key] }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        @endif
    @endforeach
@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>