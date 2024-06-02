<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>{{ config('fi.headerTitleText') }}</title>

    @include('layouts._head')

    @include('layouts._js_global')

    @yield('head')

    @yield('javascript')

</head>
<body class="{{ $skinClass }} sidebar-mini fixed">

<div class="wrapper">

    <header class="main-header">

        <nav class="navbar navbar-expand bg-{{$topBarColor}}  navbar-{{ $navClass }}">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars text-{{$topBarColorText}}"></i>
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

    </header>

    <aside class="main-sidebar sidebar-dark-primary">

        <a href="{{ route('clientCenter.dashboard')}}" class=" bg-{{$topBarColor }}  brand-link shadow-lg ">
            <img src="{{ asset('assets/dist/img/logo.png') }}" alt="{{ config('fi.headerTitleText') }}"
                 class="brand-image img-circle elevation-5">
            <span class="text-{{$topBarSymbolColorText }} ? text-{{$topBarSymbolColorText }} : brand-text font-weight-dark">{{ config('fi.headerTitleText') }}</span>
        </a>

        <section class="sidebar">

            @yield('sidebar')

        </section>

    </aside>

    <div class="content-wrapper">
        @yield('content')
    </div>

</div>

<div id="modal-placeholder"></div>

<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

</body>

</html>