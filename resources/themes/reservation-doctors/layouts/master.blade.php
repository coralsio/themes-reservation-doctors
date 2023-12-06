<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    {!! \SEO::generate() !!}
    <title>@yield('title') | {{ \Settings::get('site_name', 'Corals') }}</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ \Settings::get('site_favicon') }}" rel="icon">

    @include('partials.scripts.header')
</head>
<body>
@php \Actions::do_action('after_body_open') @endphp


<div class="main-wrapper">

@include('partials.header')

    @yield('before_content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    @yield('after_content')
    @include('partials.footer')
    @include('partials.scripts.footer')
</div>
</body>
</html>
