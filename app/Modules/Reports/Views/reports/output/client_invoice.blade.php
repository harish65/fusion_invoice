@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.client_invoice') }}
@stop
@section('content')
    <h1 style="margin-bottom: 0;width: 100%;float: left;" class="theme-color">{{ trans('fi.client_invoice') }}</h1>
    @foreach ($results as $key => $result)
        @if($key  != 'includeLineItemDetail')
            <h2 style="margin-top: 0; margin-bottom: 0;" class="theme-color">{!! $result['client_name'] !!}</h2>
            <h3 style="margin-top: 0;" class="theme-color">
                {{ $result['from_date'] }} - {{ $result['to_date'] }}
            </h3>
            <br>
            @if(count($result['records']) > 0)
                <table class="alternate" style=" font-size: 12px;">
                    @if($results['includeLineItemDetail'] == 1)
                        @foreach ($result['records'] as $recordsKey => $records)
                            @foreach ($records as $recordKey => $record)
                                <thead>
                                @if(array_search($recordKey,array_keys($records)) == 0)
                                    <tr>
                                        <td colspan="8" text-align="center">
                                            <h2 class="theme-color">{{ trans('fi.currency') }}: {{ $recordsKey }}</h2>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="8" text-align="center">
                                        <h3 class="{{ $record['type'] == 'credit_memo' ? 'text-danger' : '' }} theme-color"
                                            title="{{ $record['type'] == 'credit_memo' ? trans('fi.credit_memo') : '' }}">
                                            {{$record['number']}} {{ '( '. $record['formatted_invoice_date'] .' )'}}
                                        </h3>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="amount theme-color"
                                        style="width: 10%; text-align: left;">{{ trans('fi.product') }}</th>
                                    <th class="amount theme-color"
                                        style="width: 15%;text-align: left;">{{ trans('fi.description') }}</th>
                                    <th class="amount theme-color" style="width: 5%;">{{ trans('fi.price') }}</th>
                                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.quantity') }}</th>
                                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.subtotal') }}</th>
                                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.discount') }}</th>
                                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.tax') }}</th>
                                    <th class="amount theme-color" style="width: 10%;">{{ trans('fi.total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($record['items']) && $record['items'] > 0)

                                    @foreach ($record['items'] as $item)
                                        <tr>
                                            <td class="theme-color">{{ $item['product'] }}</td>
                                            <td class="theme-color">{{ $item['description'] }}</td>
                                            <td class="amount theme-color">{{ $item['price'] }}</td>
                                            <td class="amount theme-color">{{ $item['quantity'] }}</td>
                                            <td class="amount theme-color">{{ $item['subtotal'] }}</td>
                                            <td class="amount theme-color">{{ $item['discount'] }}</td>
                                            <td class="amount theme-color">{{ $item['tax'] }}</td>
                                            <td class="amount theme-color">{{ $item['total'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="amount theme-color" colspan="3">
                                            <strong>{{ trans('fi.total') }}</strong></td>
                                        <td class="amount theme-color">
                                            <strong>{{ $record['items_totals']['quantity'] }}</strong></td>
                                        <td class="amount theme-color">
                                            <strong>{{ $record['items_totals']['subtotal'] }}</strong></td>
                                        <td class="amount theme-color">
                                            <strong>{{ $record['items_totals']['discount'] }}</strong></td>
                                        <td class="amount theme-color">
                                            <strong>{{ $record['items_totals']['tax'] }}</strong></td>
                                        <td class="amount theme-color">
                                            <strong>{{ $record['items_totals']['total'] }}</strong></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><h4 class="theme-color" colspan="8"
                                            text-align="center">{{ trans('fi.no_records_found') }}</h4></td>
                                    </tr>
                                @endif
                                </tbody>
                            @endforeach
                        @endforeach
                    @else
                        @foreach ($result['records'] as $key => $records)
                            <thead>
                            @if(count($result['records']) > 1)
                                <tr>
                                    <td colspan="5" text-align="center">
                                        <h2 class="theme-color">{{ trans('fi.currency') }}: {{ $key }}</h2>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th class="theme-color">{{ trans('fi.date') }}</th>
                                <th class="theme-color">{{ trans('fi.invoice') }}</th>
                                <th class="amount theme-color">{{ trans('fi.total') }}</th>
                                <th class="amount theme-color">{{ trans('fi.paid') }}</th>
                                <th class="amount theme-color">{{ trans('fi.balance') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($records as $record)
                                <tr>
                                    <td class="theme-color">{{ $record['formatted_invoice_date'] }}</td>
                                    <td class="{{ $record['type'] == 'credit_memo' ? 'text-danger' : '' }} theme-color"
                                        title="{{ $record['type'] == 'credit_memo' ? trans('fi.credit_memo') : '' }}">{{ $record['number'] }}</td>
                                    <td class="amount theme-color">{{ $record['formatted_total'] }}</td>
                                    <td class="amount theme-color">{{ $record['formatted_paid'] }}</td>
                                    <td class="amount theme-color">{{ $record['formatted_balance'] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2"></td>
                                <td class="amount theme-color" style="font-weight: bold;">{{ $result['total'][$key] }}</td>
                                <td class="amount theme-color" style="font-weight: bold;">{{ $result['paid'][$key] }}</td>
                                <td class="amount theme-color" style="font-weight: bold;">{{ $result['balance'][$key] }}</td>
                            </tr>
                            @endforeach

                            </tbody>
                            @endif
                </table>
            @else
                <h5 class="theme-color" colspan="8" text-align="center" style="padding-top: 0;padding-bottom: 20;margin-top: 0;">
                    {{ trans('fi.no_records_found') }}</h5></td>
            @endif
        @endif
    @endforeach
@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>