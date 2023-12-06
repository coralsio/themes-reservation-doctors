<?php


use Corals\Theme\Facades\Theme;

\Filters::add_filter('dashboard_url', function ($dashboard_url) {
    if (!isSuperUser()) {
        return 'my-dashboard';
    }
    return $dashboard_url;
}, 12);

\Filters::add_filter('dashboard_content', function ($dashboard_content) {
    $theme = Theme::find('reservation-doctors');

    Theme::addThemeViews($theme);

    $dashboard_content .= '<hr/>';

    $dashboard_content .= view('views.superuser_dashboard', compact('theme'))->render();

    return $dashboard_content;
}, 20);


