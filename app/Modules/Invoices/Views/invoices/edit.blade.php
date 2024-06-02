@extends('layouts.master')

@section('javascript')

    @include('layouts._select2')
    @include('item_lookups._js_item_lookups')
    @include('layouts._formdata')
    @if (config('commission_enabled'))
        @include('commission._js_addon_global')
    @endif

@stop

@section('content')

    <div id="div-invoice-edit">

        @include('invoices._edit')

    </div>

@stop