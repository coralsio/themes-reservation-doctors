<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\Foundation\Http\Controllers\PublicBaseController;
use Corals\Modules\CMS\Traits\SEOTools;

class DoctorBaseController extends PublicBaseController
{
    use SEOTools;

    protected function reservationSEO($title): void
    {
        $this->setSEO((object)[
            'title' => $title,
            'meta_description' => null,
            'url' => request()->url(),
            'type' => 'page'
        ]);

    }
}
