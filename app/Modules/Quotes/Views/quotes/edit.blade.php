@extends('layouts.master')

@section('javascript')

    @include('layouts._formdata')
    @include('layouts._select2')
    @include('item_lookups._js_item_lookups')

@stop

@section('content')

    <div id="div-quote-edit">

        @include('quotes._edit')

    </div>

@stop