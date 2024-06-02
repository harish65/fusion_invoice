@extends('layouts.master')

@section('javascript')

    @include('layouts._formdata')
    @include('layouts._select2')
    @include('item_lookups._js_item_lookups')
    @if (config('commission_enabled'))
        @include('commission._js_addon_global')
    @endif

@stop

@section('content')

    <div id="div-recurring-invoice-edit">

        @include('recurring_invoices._edit')

    </div>

@stop