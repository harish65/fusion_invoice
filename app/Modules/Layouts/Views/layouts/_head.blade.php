<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="{{ asset('assets/dist/css/fonts.google.css?v='.config('fi.version')) }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css?v='.config('fi.version')) }}">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet"
      href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css?v='.config('fi.version')) }}">
<!-- iCheck -->
<link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css?v='.config('fi.version')) }}">
<!-- Theme style -->
<link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css?v='.config('fi.version')) }}">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css?v='.config('fi.version')) }}">
<!-- IonIcons -->
<link href="{{ asset('assets/plugins/ionicons-1.5.2/css/ionicons.min.css?v='.config('fi.version')) }}" rel="stylesheet" type="text/css"/>
<!-- Custom CSS -->
<link href="{{ asset('assets/custom.css?v='.config('fi.version')) }}" rel="stylesheet" type="text/css"/>

@if (file_exists(base_path('custom/custom.css')))
    <link href="{{ asset('custom/custom.css?v='.config('fi.version')) }}" rel="stylesheet" type="text/css"/>
@endif

<!-- jQuery -->
<script src="{{ asset('assets/plugins/jquery/jquery.min.js?v='.config('fi.version')) }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.js?v='.config('fi.version')) }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js?v='.config('fi.version')) }}"></script>
<!-- Summernote -->
<script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js?v='.config('fi.version')) }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js?v='.config('fi.version')) }}"></script>


<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte.js?v='.config('fi.version')) }}"></script>

<script type="text/javascript">
    var dateTimeFormat = '{{ $dateTimeFormat }}';
    var dateFormat = '{{ $dateFormat }}';
    $(function () {

        $('.navbar .dropdown-item').on('click', function (e) {


            if (!$(this).hasClass('notification-item') && !$(this).hasClass('ajax-request')) {
                var $el = $(this);
                var $parent = $el;
                $(this).parent("li").toggleClass('open');

                if ($(window).width() < 499) {
                    $el.next().css({"top": $el[0].offsetTop + 40, "left": 0});
                }

                if ($parent.parent().children("ul").length) {
                    e.preventDefault();
                    e.stopPropagation();
                    if ($parent.hasClass('show')) {
                        $parent.removeClass('show');
                        $el.next().removeClass('show');
                        $el.next().css({"top": -999, "left": -999});
                    } else {
                        if ($(window).width() < 500) {
                            $el.next().find('.sm-submenu-close').addClass('d-block');
                            $parent.parent().find('.show').removeClass('show');
                            $parent.addClass('show');
                            $el.next().addClass('show');
                            $el.next().css({
                                "top": $el[0].offsetTop + 40, "left": 0
                            });
                        } else {
                            $parent.parent().find('.show').removeClass('show');
                            $parent.addClass('show');
                            $el.next().addClass('show');
                            $el.next().find('.sm-submenu-close').removeClass('d-block').addClass('d-none')
                            $el.next().css({
                                "top": $el[0].offsetTop,
                                "width": 'auto',
                                "left": 'auto'
                            });
                        }
                    }
                }
            }
        });

        $('.main-menu li a').on('click', function () {
            if ($(this).attr('href') != '#') {
                location.href = $(this).attr('href');
            }
        });

        $('.navbar .dropdown').on('hidden.bs.dropdown', function () {
            $(this).find('li.dropdown').removeClass('show open');
            $(this).find('ul.dropdown-menu').removeClass('show open');
        });

        $('.toggleClasses').on('click', function () {
            if($(this).hasClass('fa-angle-left')){
                $(this).removeClass('fa-angle-left').addClass('fa-angle-right')
            }else{
                $(this).removeClass('fa-angle-right').addClass('fa-angle-left')
            }
        });
    });

</script>