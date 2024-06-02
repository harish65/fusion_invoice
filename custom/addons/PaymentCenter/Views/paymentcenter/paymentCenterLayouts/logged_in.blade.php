@extends('paymentcenter.paymentCenterLayouts.master')

@section('sidebar')
    @if (config('fi.displayProfileImage'))
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                {!! auth()->user()->getAvatar(40, true) !!}
            </div>
            <div class="info">
                <a href="#" class="d-block">{!! ucfirst(auth()->user()->name) !!}</a>
            </div>
        </div>
    @endif
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="{{ route('paymentCenter.dashboard') }}" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>{{ trans('fi.dashboard') }}</p>
                </a>
            </li>
        </ul>
    </nav>
@stop

@section('header')
    <ul class="nav navbar-nav">

        <li class="logout-btn nav-item">
            <a class="logout-color nav-link text-{{isset($topBarLogoutColorText) ? $topBarLogoutColorText :'danger'}}"
               style="color: {{isset($topBarLogoutColorText) ? $topBarLogoutColorText : '#dc3545'}}"
               href="{{ route('paymentCenter.logout') }}"
               title="{{ trans('fi.sign_out') }}">
                <i class="fa fa-power-off shadow-lg"></i>
            </a>
        </li>

    </ul>
@stop