<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\Foundation\Search\Search;
use Corals\Modules\Reservation\Models\Service;
use Corals\User\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Settings;

class DoctorPublicController extends DoctorBaseController
{
    /**
     * @param Request $request
     * @return Factory|View
     */
    public function list(Request $request)
    {

        $this->reservationSEO('Doctors List');


        $services = Service::query()
            ->join('users', function ($joinUsers) {
                $joinUsers->on('res_services.owner_id', 'users.id')
                    ->where('res_services.owner_type', getMorphAlias(User::class));
            })->when($request->get('categories'), function (Builder $query, $categories) {
                $this->joinCategories($query, $categories);
            })->when($request->get('open_now'), function ($query) {
                $this->openNowQuery($query);
            })->when($request->get('search_term'), function (Builder $query, $searchTerm) {
                $this->fullTextSearchQuery($query, $searchTerm);
            })->when($request->get('my_favourites') && user(), function (Builder $query) {

                $query->join('utility_wishlists', function ($joinWishlists) {
                    $joinWishlists->on('users.id', 'utility_wishlists.wishlistable_id')
                        ->where('utility_wishlists.wishlistable_type', getMorphAlias(User::class));
                })->where('utility_wishlists.user_id', user()->id);

            })->when($request->get('lat') && $request->get('long'), function (Builder $query) {
                $lat = request('lat');
                $long = request('long');

                $haversine = "(6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(json_extract(res_services.properties,'$.lat'))) 
                    * cos(radians(json_extract(res_services.properties,'$.long')) 
                    - radians(" . $long . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(json_extract(res_services.properties,'$.lat')))) )";

                $radius = request('radius', 50);
                $query->selectRaw("{$haversine} AS distance")
                    ->whereRaw("{$haversine} < ?", [$radius]);

            })->select('users.*');


        $doctors = $this->hydrateToUserWithPagination($services);

        return view('views.public_reservation.doctors_list')->with(compact('doctors'));
    }

    /**
     * @param Builder $query
     * @param $categories
     */
    protected function joinCategories(Builder $query, $categories)
    {
        $query->join('utility_model_has_category', function ($joinModelHasCategory) {
            $joinModelHasCategory->on('res_services.id', 'utility_model_has_category.model_id')
                ->where('utility_model_has_category.model_type', getMorphAlias(Service::class));
        })->join('utility_categories', 'utility_model_has_category.category_id', 'utility_categories.id')
            ->whereIn('utility_categories.slug', $categories);
    }

    /**
     * @param Builder $query
     */
    protected function openNowQuery(Builder $query)
    {
        $query->join('utility_schedules', function ($joinSchedules) {
            $joinSchedules->on('res_services.id', 'utility_schedules.scheduleable_id')
                ->where('utility_schedules.scheduleable_type', getMorphAlias(Service::class));
        })->where('day_of_the_week', today()->shortEnglishDayOfWeek)
            ->where(function ($query) {
                $currentTime = date('h:i:s');
                $query->where('utility_schedules.start_time', '<=', $currentTime)
                    ->Where('utility_schedules.end_time', '>=', $currentTime);
            });
    }

    /**
     * @param $query
     * @param $searchTerm
     */
    protected function fullTextSearchQuery($query, $searchTerm): void
    {
        tap(new Search, function (Search $search) use (&$query, $searchTerm) {
            $config = [
                'title_weight' => Settings::get('ecommerce_search_title_weight'),
                'content_weight' => Settings::get('ecommerce_search_content_weight'),
                'enable_wildcards' => Settings::get('ecommerce_search_enable_wildcards')
            ];

            $query = $search->AddSearchPart($query, $searchTerm, Service::class, $config);
        });
    }

    /**
     * @param Builder $services
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    protected function hydrateToUserWithPagination(Builder $services, $perPage = 10)
    {
        $total = $services->count();

        $servicesPagination = $services->paginate($perPage);

        foreach ($servicesPagination->items() as $item) {
            $items[] = $item->toArray();
        }

        //manual pagination, since we hydrate from service to user
        $paginator = new LengthAwarePaginator(
            $doctorsCollection = User::query()->hydrate($items ?? []),
            $total,
            $perPage
        );

        $paginator->setPath(url('reserve/list'));

        return $paginator;
    }
}

