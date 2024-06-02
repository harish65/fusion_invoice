<!DOCTYPE html>
<html class="public-layout">
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('fi.headerTitleText') }}</title>

    @include('layouts._head')

    @include('layouts._js_global')

    @yield('head')

    @yield('javascript')

    @include('layouts._alertifyjs')
    <style>
        .expire-page {
            width: 100% !important;
            float: left !important;
        }
    </style>
</head>
<body class="{{ $skinClass }} sidebar-collapse">

<div class="wrapper">

    <nav class="navbar navbar-expand navbar-{{ $navClass }}">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="{{ auth()->check() ? route('dashboard.index') : '#' }}" class="brand-link nav-link">
                    <img src="{{ asset('assets/dist/img/logo.png') }}" alt="{{ config('fi.headerTitleText') }}"
                         class="brand-image img-circle elevation-5">
                    <span class="brand-text font-weight-dark">{{ config('fi.headerTitleText') }}</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="content-wrapper expire-page">
        <section class="content">
            <div class="error-page" style="margin: 10% auto 0 !important;">
                <h1 class="w-100 m-auto text-center headline text-yellow ">404</h1>

                <div class="error-content">
                    <h3><i class="fa fa-warning text-yellow"></i> {{ trans('fi.your_link_is_expired') }}</h3>
                </div>
            </div>
        </section>
    </div>
    <div id="modal-placeholder"></div>
    <div id="note-modal-placeholder"></div>
</div>

</body>
</html>