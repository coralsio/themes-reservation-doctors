<footer class="footer">

    <!-- Footer Top -->
    <div class="footer-top">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-about">
                        <div class="footer-logo">
                            <img src="{{ \Settings::get('site_logo') }}" alt="{{ \Settings::get('site_name', 'Corals') }}" width="150">
                        </div>
                        <div class="footer-about-content">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua. </p>
                            <div class="social-icon">
                                <ul>
                                    @foreach(\Settings::get('social_links',[]) as $key=>$link)
                                        <li class="list-inline-item mb-0">

                                            <a class="social-button shape-circle sb-{{ $key }} sb-light-skin"
                                               href="{{ $link }}"
                                               target="_blank"><i class="fab fa-{{ $key }}"></i>
                                            </a>

                                        </li>
                                    @endforeach


                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /Footer Widget -->

                </div>

                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-menu">
                        <h2 class="footer-title">For Patients</h2>
                        <ul>
                            @foreach(Menus::getMenu('frontend_footer','active') as $menu)
                                <li>
                                    <a href="{{ url($menu->url) }}">@if($menu->icon)<i
                                                class="{{ $menu->icon }} fa-fw"></i>@endif {{ $menu->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- /Footer Widget -->

                </div>



                <div class="col-lg-3 col-md-6">

                    <!-- Footer Widget -->
                    <div class="footer-widget footer-contact">
                        <h2 class="footer-title">@lang('reservation-doctors::labels.template.contact.contact_us')</h2>
                        <div class="footer-contact-info">
                            <div class="footer-address">
                                <span><i class="fas fa-map-marker-alt"></i></span>
                                <p> 3556 Beech Street, San Francisco,<br> California, CA 94108 </p>
                            </div>
                            <p>
                                <i class="fas fa-phone-alt"></i>
                                {{ \Settings::get('contact_mobile','+970599593301') }}
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-envelope"></i>
                                {{ \Settings::get('contact_form_email','support@example.com') }}
                            </p>
                        </div>
                    </div>
                    <!-- /Footer Widget -->

                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container-fluid">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="copyright-text">
                            <p class="mb-0">{!! \Settings::get('footer_text','') !!}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</footer>
