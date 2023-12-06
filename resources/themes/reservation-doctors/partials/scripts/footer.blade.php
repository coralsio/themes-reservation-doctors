{!! Theme::js('js/jquery.min.js') !!}
{!! Theme::js('js/popper.min.js') !!}
{!! Theme::js('js/slick.js') !!}
{!! Theme::js('plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') !!}
{!! Theme::js('js/bootstrap.min.js') !!}
{!! Theme::js('js/circle-progress.min.js') !!}
{!! Theme::js('js/script.js') !!}

{!! Theme::js('plugins/jquery-block-ui/jquery.blockUI.min.js') !!}
{!! Theme::js('assets/corals/plugins/lodash/lodash.js') !!}
{!! Theme::js('plugins/select2/dist/js/select2.full.min.js') !!}

{!! Theme::js('plugins/toastr/toastr.min.js') !!}
{!! Theme::js('plugins/Ladda/spin.min.js') !!}
{!! Theme::js('plugins/Ladda/ladda.min.js') !!}
{!! Theme::js('plugins/sweetalert2/dist/sweetalert2.all.min.js') !!}

{!! Theme::js('js/functions.js') !!}
{!! Theme::js('js/main.js') !!}
{!! \Html::script('assets/corals/js/corals_functions.js') !!}
{!! \Html::script('assets/corals/js/corals_main.js') !!}
{!! Assets::js() !!}
@include('Corals::corals_main')

@yield('js')

@php  \Actions::do_action('footer_js') @endphp

<script type="text/javascript">
    window.base_url = '{!! url('/') !!}';
    {!! \Settings::get('custom_js', '') !!}
</script>
