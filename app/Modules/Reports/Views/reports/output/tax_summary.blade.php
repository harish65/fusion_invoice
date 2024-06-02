@extends('reports.layouts.master')
@section('title')
    {{ config('fi.headerTitleText') }} | {{ trans('fi.tax_summary') }}
@stop
@section('content')

    <h1 class="theme-color" style="margin-bottom: 0;width: 100%;float: left;">{{ trans('fi.tax_summary') }}</h1>
    <h3 class="theme-color" style="margin-top: 0;">{{ $results['from_date'] }} - {{ $results['to_date'] }}</h3>

    @if(count($results['records']) == 0)
        <h5 style="padding-top: 50;padding-bottom: 50;">{{ trans('fi.no_records_found') }}</h5>
    @else
        <table class="alternate" style=" font-size: 12px;">

            <thead>
            <tr>
                <th class="theme-color" style="width: 50%;">{{ trans('fi.tax_rate') }}</th>
                <th class="amount theme-color" style="width: 25%;">{{ trans('fi.taxable_amount') }}</th>
                <th class="amount theme-color" style="width: 25%;">{{ trans('fi.taxes') }}</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($results['records'] as $taxRate => $result)
                <tr>
                    <td class="theme-color">{{ $taxRate }}</td>
                    <td class="amount theme-color">{{ $result['taxable_amount'] }}</td>
                    <td class="amount theme-color">{{ $result['taxes'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="amount theme-color" colspan="2" >{{ trans('fi.total') }}</td>
                <td class="amount theme-color">{{ $results['total'] }}</td>
            </tr>
            <tr>
                <td class="amount theme-color" colspan="2" >{{ trans('fi.paid') }}</td>
                <td class="amount theme-color">{{ $results['paid'] }}</td>
            </tr>
            <tr>
                <td class="amount theme-color" colspan="2" >{{ trans('fi.remaining') }}</td>
                <td class="amount theme-color">{{ $results['remaining'] }}</td>
            </tr>
            </tbody>

        </table>
    @endif 

@stop

<style>
    {{ config('fi.skin') == 'dark-mode' ? iframeThemeColor() : ''  }}
</style>