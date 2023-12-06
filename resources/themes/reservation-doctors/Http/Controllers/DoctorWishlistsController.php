<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\User\Models\User;
use Corals\Utility\Wishlist\Classes\WishlistManager;
use Corals\Utility\Wishlist\Http\Controllers\WishlistBaseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorWishlistsController extends WishlistBaseController
{
    /**
     *
     */
    protected function setCommonVariables()
    {
        $this->wishlistableClass = User::class;
    }

    public function setTheme()
    {
        \Theme::set(\Settings::get('active_frontend_theme', config('themes.corals_frontend')));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $userWishlists = (new WishlistManager(User::class))->getUserWishlist()->paginate(10);

        $this->setViewSharedData([
            'title' => 'My Favourites'
        ]);

        return view('views.favourites')->with(compact('userWishlists'));
    }

}
