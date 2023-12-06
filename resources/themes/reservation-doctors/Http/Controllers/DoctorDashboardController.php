<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\Modules\Reservation\Facades\ReservationFacade;
use Corals\Modules\Reservation\Facades\ServiceSchedule;
use Corals\Modules\Reservation\Models\Reservation;
use Corals\User\Models\User;
use Facades\Corals\Modules\Reservation\Classes\DoctorReservations;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorDashboardController extends DoctorBaseController
{
    /**
     * @param Request $request
     * @param User $user
     * @return Factory|View
     */
    public function doctorProfile(Request $request, User $user)
    {
        $this->reservationSEO($user->full_name);

        $service = ReservationFacade::getUserService($user);

        list($todayBusinessHours, $businessHours) = ServiceSchedule::getBusinessHours($service);

        return view('views.doctor_profile')->with(compact('user', 'service', 'businessHours', 'todayBusinessHours'));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function dashboard(Request $request)
    {
        $this->setViewSharedData([
            'title' => 'Dashboard'
        ]);

        $role = user()->roles()->first();

        switch ($role->name) {
            case 'doctor':
                $reservations = DoctorReservations::getDoctorReservations();
                $invoices = DoctorReservations::getDoctorInvoices();
                break;
            case 'member':
                $reservations = DoctorReservations::getPatientReservations();
                $invoices = DoctorReservations::getPatientInvoices();
                break;
            default:
                $reservations = null;
                $invoices = null;
        }

        return view('views.dashboard')->with(compact('reservations', 'invoices'));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function myPatients(Request $request)
    {
        tap(user()->roles->pluck('name')->toArray(), function ($userRoles) {
            abort_if(in_array('member', $userRoles), 404);
        });


        $serviceId = ReservationFacade::getUserService(user())->id;

        $reservations = Reservation::query()->where('res_reservations.service_id', $serviceId)
            ->whereNotIn('res_reservations.status', ['cancelled', 'draft'])
            ->groupByRaw("JSON_UNQUOTE(JSON_EXTRACT(properties,'$.contact_details.email'))")
            ->paginate(10);


        $this->setViewSharedData(['title' => 'My patients']);

        return view('views.my_patients')->with(compact('reservations'));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Factory|View
     */
    public function patientProfile(Request $request, User $user)
    {
        if (!user()) {
            abort(403);
        }

        tap(user()->roles->pluck('name')->toArray(), function ($userRoles) use ($user) {
            abort_if(!user() || (in_array('member', $userRoles) && $user->id !== user()->id), 403);
        });


        $this->reservationSEO($user->full_name);

        $reservations = DoctorReservations::getPatientReservations($user);
        $invoices = DoctorReservations::getPatientInvoices($user);

        return view('views.patient_profile')->withPatient($user)->with(compact('reservations', 'invoices'));
    }
}
