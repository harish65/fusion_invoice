<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('fi.welcome') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    @if (file_exists(base_path('custom/custom.css')))
        <link href="{{ asset('custom/custom.css') }}" rel="stylesheet" type="text/css"/>
    @endif
</head>
<body class="hold-transition login-page">
<div class="login-box">
@if($logo != '')
    <div class="login-logo">
        <img src="{{$logo}}" alt="{{ config('fi.headerTitleText') }}" class="brand-image card-img-top elevation-5">
    </div>
@else
    <div class="login-logo">
        <img src="{{ asset('assets/dist/img/logo.png') }}" alt="{{ config('fi.headerTitleText') }}"
             class="brand-image img-circle elevation-3" style="opacity: .8">
    </div>
@endif

<!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            @include('layouts._alertifyjs')
            @include('layouts._alerts')
            {!! Form::open(['route' => 'session.attempt', 'onsubmit' => "this.submit_button.disabled = true;"]) !!}
            <div class="input-group mb-3">
                    <input type="email" name="email" id="email" class="form-control form-control-sm" placeholder="{{ trans('fi.email') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control form-control-sm" placeholder="{{ trans('fi.password') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            @if(config('fi.useCaptchInLogin'))
                <div class="form-group">
                    <div class="row mb-10 ml-0 mb-2">
                        <div class="col-xs-5">
                            <div id="captcha-container">{!! captcha_img('math') !!}</div>
                        </div>
                        <div class="col-xs-2 refreshcaptcha ml-1 mt-1">
                            <a href="javascript:void(0)" id="refresh-captcha" title="{{ trans('fi.refresh_captcha') }}"><i
                                        class="fa fa-fw fa-sync"></i></a>
                        </div>
                    </div>

                    <input type="text" name="captcha" class="form-control form-control-sm" placeholder="{{ trans('fi.type_captcha') }}">

                </div>
            @endif
            <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember" name="remember_me" value="1">
                        <label for="remember">
                            {{ trans('fi.remember_me') }}
                        </label>
                        <input type="hidden" name="remember_me" value="0">
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-sm btn-primary btn-block">{{ trans('fi.sign_in') }}</button>
                </div>
                <!-- /.col -->
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.login-card-body -->
    </div>
    <div class="login-logo">
        <span class=" btn float-right p-0 mt-2">
            Powered by
            <a href="https://fusioninvoice.com"><b>Fusion</b>Invoice
            </a>
        </span>

    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte.js') }}"></script>
<script type="text/javascript">
    $(function () {
        $('#email').focus();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#refresh-captcha').click(function () {
            $.post("{{ route('session.refresh_captcha') }}", function (response) {
                $('#captcha-container').html(response);
            });
        });
    });
</script>
</body>
</html>
