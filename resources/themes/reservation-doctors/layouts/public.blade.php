<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    <title>@yield('title') | {{ \Settings::get('site_name', 'Corals') }}</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link href="{{ \Settings::get('site_favicon') }}" rel="icon">

    @include('partials.scripts.header')
</head>
<body>
@php \Actions::do_action('after_body_open') @endphp
<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Header -->
@include('partials.header')
<!-- /Header -->

@yield('before_content')

<!-- Page Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                    <div class="profile-sidebar">
                        <div class="widget-profile pro-widget-content">
                            <div class="profile-info-widget">
                                <a href="#" class="booking-doc-img">
                                    <img src="{{user()->picture}}" alt="User Image">
                                </a>
                                <div class="profile-det-info">
                                    <h3>{{user()->full_name}}</h3>

                                    {{--                                    <div class="patient-details">--}}
                                    {{--                                        <h5 class="mb-0">BDS, MDS - Oral & Maxillofacial Surgery</h5>--}}
                                    {{--                                    </div>--}}
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-widget">
                            <nav class="dashboard-menu">
                                <ul>
                                    @foreach(\Corals\Menu\Facades\Menus::getMenu('doctors_sidebar') as $menu)

                                        @continue(!$menu->user_can_access)
                                        @php $isActiveMenu = \Request::is(explode(',',$menu->active_menu_url)) ;@endphp

                                        <li class="{{$isActiveMenu ? 'active':''}}">
                                            <a href="{{url($menu->url)}}">
                                                <i class="{{$menu->icon}}"></i>
                                                <span>{{$menu->name}}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-md-7 col-lg-8 col-xl-9">
                    @yield('content')
                </div>

            </div>
        </div>

    </div>
@yield('after_content')

<!-- /Page Content -->

@include('partials.scripts.footer')
<!-- Footer -->
    <!-- /Footer -->

</div>
<!-- /Main Wrapper -->
@include('partials.footer')


</body>
</html>
