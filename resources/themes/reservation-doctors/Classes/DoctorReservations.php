<?php

namespace Corals\Modules\Reservation\Classes;


use Corals\Modules\CMS\Models\Category;
use Corals\Modules\Payment\Common\Models\Invoice;
use Corals\Modules\Reservation\Facades\ReservationFacade;
use Corals\Modules\Reservation\Models\Reservation;
use Corals\Modules\Reservation\Models\Service;
use Corals\User\Models\User;
use Corals\Utility\Category\Facades\Category as CategoryManager;
use Corals\Utility\Wishlist\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DoctorReservations
{
    const upcomingReservationStatuses = [
        'pending', 'confirmed'
    ];

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return CategoryManager::getCategoriesByParent(\Settings::get('reservation_service_categories_parent', config('reservation.models.service.category_parent')), 'active', true);
    }

    /**
     * @return int
     */
    public function reservationCategoriesCount()
    {
        return Category::query()
            ->join('utility_categories as parent', 'utility_categories.parent_id', 'parent.id')
            ->where('parent.slug', \Settings::get('reservation_service_categories_parent', config('reservation.models.service.category_parent')))
            ->count();
    }

    /**
     * @param int $limit
     * @return Builder[]|Collection
     */
    public function getDoctorsList($limit = 5)
    {
        return User::query()->whereHas('roles', function ($rolesQuery) {
            $rolesQuery->where('roles.name', 'doctor');
        })->select('users.*')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * @param User $user
     * @return MorphMany
     */
    public function doctorWishlists(User $user)
    {
        return $user->morphMany(Wishlist::class, 'wishlistable');
    }

    /**
     * @param $doctor
     * @return bool
     */
    public function isDoctorInWishlist($doctor): bool
    {

        if (!($user = user())) {
            return false;
        }

        return (bool)$this->doctorWishlists($doctor)
            ->where('user_id', user()->id)
            ->first();
    }


    /**
     * @param User|null $patient
     * @param null $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPatientReservations(User $patient = null, $limit = null)
    {
        if (is_null($patient)) {
            $patient = user();
        }

        return Reservation::query()->where(function ($query) use ($patient) {
            $query->where('res_reservations.owner_id', $patient->id)
                ->where('res_reservations.owner_type', getMorphAlias($patient));
        })->where('res_reservations.status', '<>', 'draft')
            ->when(in_array('doctor', user()->roles()->pluck('name')->toArray()), function ($query) {
                $query->where('object_id', ReservationFacade::getUserService(user())->id)
                    ->where('object_type', getMorphAlias(Service::class));
            })->latest()
            ->with('service.owner')
            ->select('res_reservations.*')
            ->paginate();
    }

    /**
     * @param User|null $doctor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDoctorReservations(User $doctor = null)
    {
        if (is_null($doctor)) {
            $doctor = user();
        }

        return $this->doctorReservationsQuery($doctor)->paginate();
    }

    /**
     * @param $doctor
     * @return Builder
     */
    protected function doctorReservationsQuery($doctor)
    {
        return Reservation::query()->join('res_services', 'res_reservations.service_id', 'res_services.id')
            ->where(function ($query) use ($doctor) {
                $query->where('res_services.owner_id', $doctor->id)
                    ->where('res_services.owner_type', getMorphAlias($doctor));
            })->where('res_reservations.status', '<>', 'draft')
            ->latest()
            ->with('service.owner')
            ->select('res_reservations.*');
    }

    /**
     * @param User $patient
     * @param null $status
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPatientInvoices(User $patient = null, $status = null)
    {
        if (is_null($patient)) {
            $patient = user();
        }

        return Invoice::query()
            ->with('invoicable')
            ->select('invoices.*')
            ->where('invoices.user_id', $patient->id)
            ->when($status, function ($query, $status) {
                $query->where('invoices.status', $status);
            })
            ->paginate();
    }

    public function getDoctorInvoices(User $doctor = null)
    {
        if (is_null($doctor)) {
            $doctor = user();
        }

        return Invoice::query()
            ->with('invoicable')
            ->select('invoices.*')
            ->join('res_reservations', 'invoices.invoicable_id', 'res_reservations.id')
            ->where('invoices.invoicable_type', getMorphAlias(Reservation::class))
            ->join('res_services', 'res_reservations.service_id', 'res_services.id')
            ->where(function ($query) use ($doctor) {
                $query->where('res_services.owner_id', $doctor->id)
                    ->where('res_services.owner_type', getMorphAlias($doctor));
            })->paginate();
    }

    /**
     * @param User|null $doctor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDoctorUpcomingReservations(User $doctor = null)
    {
        if (!$doctor) {
            $doctor = user();
        }

        return $this->doctorReservationsQuery($doctor)
            ->where(function (Builder $query) {
                $query->whereDate('res_reservations.starts_at', '>=', now()->toDateString());
//                $query->whereRaw("DATEDIFF(res_reservations.starts_at,NOW()) <= ?", [\Settings::get('upcoming_reservation_days_limit', 2)]);
            })->whereIn('res_reservations.status', static::upcomingReservationStatuses)
            ->paginate();

    }

    /**
     * @param User|null $doctor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDoctorTodayReservations(User $doctor = null)
    {
        if (!$doctor) {
            $doctor = user();
        }

        return $this->doctorReservationsQuery($doctor)
            ->whereDate('res_reservations.starts_at', today()->toDateString())
            ->whereIn('res_reservations.status', static::upcomingReservationStatuses)
            ->paginate();
    }

    /**
     * @return mixed
     */
    public function getReservationAvailableGateway()
    {
        $availableGateways = \Payments::getAvailableGateways();

        foreach ($availableGateways as $gatewayKey => $gateway_title) {
            $paymentGateway = Payment::create($gatewayKey);
            if (!$paymentGateway->getConfig('support_reservation')) {
                unset($availableGateways[$gatewayKey]);
            }
        }

        return $availableGateways;
    }

    /**
     * @param $request
     * @param $reservation
     * @return bool
     * @throws \Exception
     */
    public function doPayment($request, $reservation): bool
    {
        $invoice = $reservation->invoice;

        $gateway = $request->get('gateway');

        $amount = $invoice->total;

        $order = (Object)[
            'id' => $reservation->id,
            'amount' => $amount,
            'currency' => $invoice->currency,
            'billing' => [
                'billing_address' => [
                    'email' => $request->get('email')
                ]
            ]
        ];

        $checkoutDetails = [
            'token' => $request->get('checkoutToken'),
            'gateway' => $gateway,
        ];


        $user = user() ?? new User;

        $paymentGateway = Payment::create($gateway);

        $paymentGateway->setAuthentication();

        $response = $paymentGateway->createCharge(
            $parameters = $paymentGateway->prepareCreateChargeParameters($order, $user, $checkoutDetails)
        )->send();

        if ($response->isSuccessful()) {

            Transaction::query()->create([
                'code' => Transaction::getCode('RES'),
                'owner_type' => getMorphAlias($user),
                'owner_id' => $user->id ?? 0,
                'sourcable_type' => getMorphAlias($reservation),
                'sourcable_id' => $reservation->id,
                'paid_currency' => $invoice->currency,
                'paid_amount' => $amount,
                'amount' => $amount,
                'transaction_date' => now(),
                'status' => 'completed',
                'type' => 'reservation_payment',
                'reference' => $response->getChargeReference(),
                'notes' => "Payment for reservation #$reservation->id"
            ]);

            return true;
        } else {
            $message = 'pay Gateway Order Failed. ' . $response->getMessage();
            logger($response->getMessage());

            throw new \Exception($message);
        }

    }

    /**
     * @param $paymentGateway
     * @param $reservation
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function createPaymentToken($paymentGateway, $reservation, $params)
    {
        $invoice = $reservation->invoice;
        $amount = $invoice->total;

        $currency = $invoice->currency;
        $description = "Payment fot Reservation#" . $reservation->id;

        $parameters = $paymentGateway->preparePaymentTokenParameters($amount, $currency, $description, $params);
        $paymentGateway->setAuthentication();
        $request = $paymentGateway->purchase($parameters);
        $response = $request->send();


        if ($response->isSuccessful()) {
            return $response->getPaymentTokenReference();
        } else {
            throw new \Exception($response->getDataText());
        }
    }

    /**
     * @param $gateway
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function checkPaymentToken($paymentGateway, $params)
    {
        $parameters = $paymentGateway->prepareCheckPaymentTokenParameters($params);

        if ($paymentGateway->getConfig('require_token_confirm')) {

            $request = $paymentGateway->confirmPaymentToken($parameters);
        } else {
            $request = $paymentGateway->checkPaymentToken($parameters);
        }

        $paymentGateway->setAuthentication();

        $response = $request->send();

        if ($response->isSuccessful()) {
            return $response->getPaymentTokenReference();
        } else {
            throw new \Exception(trans($response->getDataText()));
        }
    }

    /**
     * @param $reservation
     * @return string
     */
    public function successCheckoutMessage($reservation)
    {
        $doctor = $reservation->service->owner;

        $message = trans('reservation-doctors::labels.doctors.checkout_success_message', [
            'doctor' => $doctor->full_name,
            'start_date' => $reservation->starts_at->format('d M Y h:i A'),
            'end_date' => $reservation->ends_at->format('h:i A')
        ]);


        if ($reservation->status == 'pending') {
            $message .= sprintf("<br><strong class='text-danger'>%s</strong>", trans('reservation-doctors::labels.doctors.will_be_reviewed_soon'));
        }
        return "$message</p>";
    }

    /**
     * @return int
     */
    public function getTotalPatients()
    {
        return Reservation::query()
            ->whereNotIn('res_reservations.status', ['cancelled', 'draft'])
            ->whereDate('res_reservations.starts_at', '<=', today()->toDateString())
            ->groupByRaw("JSON_UNQUOTE(JSON_EXTRACT(properties,'$.contact_details.email'))")
            ->pluck('id')
            ->count();
    }

    /**
     * @return int
     */
    public function getTodayPatients()
    {
        return Reservation::query()
            ->whereNotIn('res_reservations.status', ['cancelled', 'draft'])
            ->whereDate('res_reservations.starts_at', '<=', today()->toDateString())
            ->groupByRaw("JSON_UNQUOTE(JSON_EXTRACT(properties,'$.contact_details.email'))")
            ->pluck('id')
            ->count();
    }

    /**
     * @return int
     */
    public function getTotalReservations()
    {
        return Reservation::query()
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->whereDate('res_reservations.starts_at', '<=', today()->toDateString())
            ->count();
    }

    /**
     * @param User|null $doctor
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDoctorPreviousReservations(User $doctor = null)
    {
        if (!$doctor) {
            $doctor = user();
        }

        return $this->doctorReservationsQuery($doctor)
            ->where(function (Builder $query) {
                $query->whereDate('res_reservations.starts_at', '<', now()->toDateString());
//                $query->whereRaw("DATEDIFF(res_reservations.starts_at,NOW()) <= ?", [\Settings::get('upcoming_reservation_days_limit', 2)]);
            })->paginate();

    }
}
