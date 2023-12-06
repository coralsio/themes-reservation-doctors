{!! Theme::css('css/bootstrap.min.css') !!}

{!! Theme::css('plugins/fontawesome/css/fontawesome.min.css') !!}
{!! Theme::css('plugins/fontawesome/css/all.min.css') !!}

@if(\Language::isRTL())

    {!! Theme::css('css/style_rtl.css') !!}
@else
    {!! Theme::css('css/style.css') !!}

@endif

{!! Theme::css('plugins/select2/dist/css/select2.min.css') !!}

{!! Theme::css('plugins/sweetalert2/dist/sweetalert2.css') !!}
{!! Theme::css('plugins/toastr/toastr.min.css') !!}

{!! Theme::css('plugins/Ladda/ladda-themeless.min.css') !!}

{!! Theme::js('js/html5shiv.min.js') !!}
{!! Theme::js('js/respond.min.js') !!}


<script type="text/javascript">
    window.base_url = '{!! url('/') !!}';
    window.rtl = {{ \Language::isRTL() ? "true":"false" }};

</script>
@yield('css')
@stack('partial_css')

