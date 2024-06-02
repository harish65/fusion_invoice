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

</head>
<body class="light-mode sidebar-collapse">

<div class="wrapper">

    <nav class="navbar navbar-expand navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="{{ auth()->check() ? route('dashboard.index') : '#' }}" class="brand-link nav-link">
                    <img src="{{ asset('assets/dist/img/logo.png') }}" alt="{{ config('fi.headerTitleText') }}"
                         class="brand-image img-circle elevation-5">
                    <span class="brand-text font-weight-dark">{{ config('fi.headerTitleText') }}</span>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            @yield('header')

        </ul>
    </nav>
    <div class="content-wrapper">
        @yield('content')
    </div>
    <div id="modal-placeholder"></div>
    <div id="note-modal-placeholder"></div>
</div>

<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

</body>
</html>