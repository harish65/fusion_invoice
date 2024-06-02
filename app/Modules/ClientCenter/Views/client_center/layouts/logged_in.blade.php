@extends('client_center.layouts.master')

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
                <a href="{{ route('clientCenter.dashboard') }}" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>{{ trans('fi.dashboard') }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientCenter.quotes') }}" class="nav-link">
                    <i class="nav-icon fa fa-file-alt"></i> <p>{{ trans('fi.quotes') }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientCenter.invoices') }}" class="nav-link">
                    <i class="nav-icon fa fa-file-invoice"></i>
                    <p>{{ trans('fi.invoices') }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientCenter.payments') }}" class="nav-link">
                    <i class="nav-icon fa fa-credit-card"></i>
                    <p>{{ trans('fi.payments') }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clientCenter.attachments') }}" class="nav-link">
                    <i class="nav-icon fa fa-download"></i>
                    <p>{{ trans('fi.attachments') }}</p>
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
               href="{{ route('session.logout') }}"
               title="{{ trans('fi.sign_out') }}">
                <i class="fa fa-power-off shadow-lg"></i>
            </a>
        </li>

    </ul>
@stop