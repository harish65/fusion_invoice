@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.payments_collected') }}
@stop
@section('content')
    @if(count($results['records']) > 0)
        <h1 class="theme-color"
            style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.payments_collected') }}</h1>
        <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

        <h3 class="theme-color"
            style="margin-top: 0;">{{ 'Currency Format: ' . trans($results['currency_format']) }} </h3>

        @foreach ($results['records'] as $paymentMethod => $payments)
            <h2 class="theme-color">{{ $paymentMethod }}</h2>

            <table class="alternate" style=" font-size: 12px;">
                <thead>
                <tr>
                    <th class="theme-color" width="10%">{{ trans('fi.date') }}</th>
                    <th class="theme-color" width="10%">{{ trans('fi.invoice') }}</th>
                    <th class="theme-color" width="15%">{{ trans('fi.client') }}</th>
                    <th class="theme-color" width="10%">{{ trans('fi.payment_method') }}</th>
                    <th class="theme-color" width="27%">{{ trans('fi.note') }}</th>
                    <th class="amount theme-color" width="8%">{{ trans('fi.amount') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($payments['payments'] as $payment)
                    <tr>
                        <td class="theme-color">{{ $payment['date'] }}</td>
                        <td class="theme-color">{{ $payment['invoice_number'] }}</td>
                        <td class="theme-color">{{ $payment['client_name'] }}</td>
                        <td class="theme-color">{{ $payment['payment_method'] }}</td>
                        <td class="theme-color">{{ $payment['note'] }}</td>

                        @if ($results['currency_format'] == 'fi.base_currency')
                            <td class="amount theme-color">{{ $payment['amount'] }}</td>
                        @else
                            <td class="amount theme-color">{{ $payment['amount_with_currency'] }}</td>
                        @endif

                    </tr>
                @endforeach

                <tr>
                    <td class="amount theme-color" colspan="5">
                        <strong>{{trans('fi.total').": " . $paymentMethod }}</strong>
                    </td>
                    <td class="amount theme-color"><strong>{{ $payments['totals']['amount'] }}</strong></td>
                </tr>

                </tbody>
            </table>

        @endforeach

        <hr>

        <table>
            <tbody>
            <tr>
                <td class="theme-color" width="20%"></td>
                <td class="theme-color" width="20%"></td>
                <td class="theme-color" width="20%"></td>
                <td class="theme-color" width="20%"></td>
                <td class="amount theme-color" width="10%"><strong>{{trans('fi.grand_total')}}</strong></td>
                <td class="amount theme-color" width="10%"><strong>{{ $results['grandTotal'] }}</strong></td>
            </tr>
            </tbody>
        </table>
    @else
        <h2 style="padding-top: 50;">{{ trans('fi.no_records_found') }}</h2>
    @endif
@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>