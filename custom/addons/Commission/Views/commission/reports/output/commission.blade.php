@extends('reports.layouts.master')

@section('content')

    @if(count($results['records']) == 0)
        <h4 class="theme-color" colspan="8" text-align="center">
            {{ trans('fi.no_records_found') }}</h4>
    @endif

    @foreach ($results['records'] as $commission_type => $result)
        <h1 style="margin-bottom: 0;">{{ trans('Commission::lang.'.$commission_type) }}</h1>
        <br>
        <table class="alternate">
            <thead>
            <tr>
                <th>{{ trans('fi.invoice') }}</th>
                <th>{{ trans('fi.date') }}</th>
                <th>{{ trans('fi.client') }}</th>
                <th>{{ trans('Commission::lang.user') }}</th>
                <th>{{ trans('Commission::lang.commission_type') }}</th>
                <th>{{ trans('Commission::lang.product') }}</th>
                <th>{{ trans('Commission::lang.note') }}</th>
                <th>{{ trans('Commission::lang.status') }}</th>
                <th class="amount">{{ trans('fi.amount') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($result as $user_id => $user_data)

                @foreach ($user_data as $data)
                    <tr>
                        <td>{{$data['number']}}</td>
                        <td>{{$data['date']}}</td>
                        <td>{{$data['client']}}</td>
                        <td>{{$data['user']}}</td>
                        <td>{{$data['type']}}</td>
                        <td>{{$data['product']}}</td>
                        <td>{{$data['note']}}</td>
                        <td>{{$data['status']}}</td>
                        <td class="amount">{{$data['format_amount']}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="9" class="total">
                        {{ trans('Commission::lang.total') }} : {{$results['total'][$commission_type][$user_id]}}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="9" class="total">{{ trans('fi.grand_total') }}
                    : {{$results['grand_total'][$commission_type]}} </td>
            </tr>
            </tbody>
        </table>
    @endforeach
@stop
