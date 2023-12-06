<header class="header">
    <nav class="navbar navbar-expand-lg header-nav">
        <div class="navbar-header">
            <a id="mobile_btn" href="javascript:void(0);">
							<span class="bar-icon">
								<span></span>
								<span></span>
								<span></span>
							</span>
            </a>
            <a href="{{url('/')}}" class="navbar-brand logo">
                <img src="{{ \Settings::get('site_logo') }}" class="img-fluid"
                     alt="{{ \Settings::get('site_name', 'Corals') }}">
            </a>
        </div>
        <div class="main-menu-wrapper">
            {{--            <div class="menu-header">--}}
            {{--                <a href="index.html" class="menu-logo">--}}
            {{--                    <img src="assets/img/logo.png" class="img-fluid" alt="Logo">--}}
            {{--                </a>--}}
            {{--                <a id="menu_close" class="menu-close" href="javascript:void(0);">--}}
            {{--                    <i class="fas fa-times"></i>--}}
            {{--                </a>--}}
            {{--            </div>--}}
            <ul class="main-nav">
                @include('partials.menu.menu_item', ['menus' => Menus::getMenu('frontend_top','active')])

                <li class="dropdown has-arrow d-block d-md-none" id="language-selector">

                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false">
                        {!! \Language::flag(app()->getLocale()) !!} {{\Language::getName(app()->getLocale())}}
                    </a>

                    <div class="dropdown-menu">

                        @foreach (\Language::allowed() as $code => $name)
                            <a class="dropdown-item" href="{{ \Language::getLocaleUrl($code) }}">
                                {!! \Language::flag($code) !!} {!! $name !!}
                            </a>
                        @endforeach
                    </div>
                </li>

                @auth
                    <li class="dropdown has-arrow logged-item d-block d-md-none">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false">
								<span class="user-img">
									<img class="rounded-circle" src="{{user()->picture}}" width="31" alt="Darren Elder">
								</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="user-header">
                                <div class="avatar avatar-sm">
                                    <img src="{{user()->picture}}" alt="User Image"
                                         class="avatar-img rounded-circle">
                                </div>
                                <div class="user-text">
                                    <h6>{{user()->full_name}}</h6>
                                </div>
                            </div>
                            {{--                        <a class="dropdown-item" href="doctor-dashboard.html">Dashboard</a>--}}
                            <a class="dropdown-item"
                               href="{{  user()->getDashboardURL() }}">@lang('reservation-doctors::labels.doctors.dashboard')</a>

                            <a class="dropdown-item"
                               href="{{url('profile')}}">@lang('reservation-doctors::labels.partial.my_profile')</a>
                            <a href="{{ route('logout') }}" data-action="logout"
                               class="dropdown-item">
                                @lang('reservation-doctors::labels.partial.logout')
                            </a></div>
                    </li>
                @else
                    <li class="d-block d-md-none">
                        <a class="header-login"
                           href="{{url('login')}}">@lang('reservation-doctors::labels.auth.login_signup')</a>
                    </li>
                @endauth
            </ul>
        </div>
        <ul class="nav header-navbar-rht">
            <li class="nav-item contact-item">
                <div class="header-contact-img">
                    <i class="far fa-hospital"></i>
                </div>
                <div class="header-contact-detail">
                    <p class="contact-header">@lang('reservation-doctors::labels.template.contact.contact_us')</p>
                    <a class="contact-info-header"
                       href="tel:{{ \Settings::get('contact_mobile','+970599593301') }}">  {{ \Settings::get('contact_mobile','+970599593301') }}</a>
                </div>
            </li>

            <li class="nav-item dropdown has-arrow" id="language-selector">

                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false">
                    {!! \Language::flag(app()->getLocale()) !!} {{\Language::getName(app()->getLocale())}}
                </a>

                <div class="dropdown-menu">

                    @foreach (\Language::allowed() as $code => $name)
                        <a class="dropdown-item" href="{{ \Language::getLocaleUrl($code) }}">
                            {!! \Language::flag($code) !!} {!! $name !!}
                        </a>
                    @endforeach
                </div>
            </li>

            @auth
                <li class="nav-item dropdown has-arrow logged-item">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false">
								<span class="user-img">
									<img class="rounded-circle" src="{{user()->picture}}" width="31" alt="Darren Elder">
								</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="user-header">
                            <div class="avatar avatar-sm">
                                <img src="{{user()->picture}}" alt="User Image"
                                     class="avatar-img rounded-circle">
                            </div>
                            <div class="user-text">
                                <h6>{{user()->full_name}}</h6>
                            </div>
                        </div>
                        {{--                        <a class="dropdown-item" href="doctor-dashboard.html">Dashboard</a>--}}
                        <a class="dropdown-item"
                           href="{{  user()->getDashboardURL() }}">@lang('reservation-doctors::labels.doctors.dashboard')</a>

                        <a class="dropdown-item"
                           href="{{url('profile')}}">@lang('reservation-doctors::labels.partial.my_profile')</a>
                        <a href="{{ route('logout') }}" data-action="logout"
                           class="dropdown-item">
                            @lang('reservation-doctors::labels.partial.logout')
                        </a></div>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link header-login"
                       href="{{url('login')}}">@lang('reservation-doctors::labels.auth.login_signup')</a>
                </li>
            @endauth
        </ul>
    </nav>
</header>
