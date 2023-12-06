<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\Foundation\View\Facades\JavaScriptFacade;
use Corals\Modules\Reservation\Classes\ReservationPayment;
use Corals\Modules\Reservation\Facades\ReservationFacade;
use Corals\Modules\Reservation\Facades\ServiceSchedule;
use Corals\Modules\Reservation\Models\LineItem;
use Corals\Modules\Reservation\Models\Reservation;
use Corals\Modules\Reservation\Models\Service;
use Corals\Modules\Reservation\Services\ReservationService;
use Facades\Corals\Modules\Reservation\Classes\DoctorReservations;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class DoctorReservationsController extends DoctorBaseController
{


    /**
     * @param Request $request
     * @param Service $service
     * @return Factory|View
     */
    public function schedule(Request $request, Service $service)
    {

        JavaScriptFacade::put([
            'public_js_labels' => trans('Reservation::labels.public_js'),
        ]);

        $owner = $service->owner;

        $this->reservationSEO("Book appointment [ $owner->full_name ]");

        return view('views.public_reservation.index')->with(compact('service', 'owner'));
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return mixed
     */
    public function getServiceFormattedSchedule(Request $request, Service $service)
    {
        $fromDate = $request->get('start_date');

        if ($reservationId = ServiceSchedule::getSelectedReservationFromSession()) {
            $reservation = Reservation::query()->where('id', $reservationId)
                ->where('object_id', $service->id)
                ->where('object_type', getMorphAlias(Service::class))
                ->first();
        }

        return array_merge(ServiceSchedule::getFormattedSchedule($service, $fromDate), [
            'serviceObject' => $service,
            'selectedReservationHashedId' => optional($reservation ?? null)->hashed_id
        ]);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return array
     */
    public function getServiceOptionalLineItems(Request $request, Service $service)
    {
        abort_if(!$request->ajax(), 404);

        $optionalLineItems = [];

        $service->optionalLineItems
            ->each(function (LineItem $lineItem) use (&$optionalLineItems, $request) {

                $optionalLineItems[] = \RatesFacade::getLineItemAsArray($lineItem, [
                    'startsAt' => $request->get('startsAt'),
                    'endsAt' => $request->get('endsAt')
                ]);

            });

        return $optionalLineItems;
    }

    /**
     * @param Request $request
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function createReservation(Request $request, ReservationService $reservationService)
    {
        try {
            $user = optional(user());

            //prepend main line item
            $service = Service::find($request->get('service_id'));

            $lineItems = $request->get('line_items');

            $lineItems = Arr::prepend($lineItems, ['code' => $service->mainLineItem()->first()->code]);

            $request->merge(['line_items' => $lineItems]);

            //delete old selected reservation
            if ($selectedReservationHashedId = $request->get('selected_reservation_hashed_id')) {
                $selectedReservation = Reservation::findByHash($selectedReservationHashedId);

                if ($selectedReservation) {
                    $reservationService->destroy($request, $selectedReservation);
                }
            }

            $request->request->remove('selected_reservation_hashed_id');

            $reservation = $reservationService->store($request, Reservation::class, [
                'status' => 'draft',
                'code' => Reservation::getCode('RES'),
                'owner_id' => $user->id,
                'owner_type' => user() ? getMorphAlias(user()) : null
            ]);


            ServiceSchedule::storeReservationIdInSession($reservation->id);

            $message = [
                'selectedReservationId' => $reservation->hashed_id,
            ];
        } catch (\Exception $exception) {
            log_exception($exception, Reservation::class, 'store');
            $message = ['message' => $exception->getMessage(), 'level' => 'error'];
            $code = 400;
        }

        return response()->json($message, $code ?? 200);
    }


    /**
     * @param Request $request
     * @param Reservation $reservation
     * @param ReservationPayment $reservationPayment
     * @return Factory|View
     */
    public function checkoutPage(Request $request, Reservation $reservation, ReservationPayment $reservationPayment)
    {
        $service = $reservation->service;
        $owner = $service->owner;

        $invoice = $reservation->invoice;

        abort_if($reservation->status !== 'draft', 404);

        $this->reservationSEO("Checkout");

        $gateway = null;
        $available_gateways = $reservationPayment->getReservationAvailableGateway();
        $urlPrefix = '';

        return view("views.public_reservation.checkout")
            ->with(
                compact('reservation', 'owner', 'service',
                    'invoice', 'gateway', 'available_gateways', 'urlPrefix')
            );
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkout(Request $request, Reservation $reservation)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required | email',
            'phone' => 'required',
            'terms' => 'required',
            'gateway' => 'required',
            'checkoutToken' => 'required'
        ]);

        try {
            $contactDetails = [
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone')
            ];

            $invoice = $reservation->invoice;

            $gateway = $request->get('gateway');

            $reservationPayment = new ReservationPayment($gateway);

            if ($reservationPayment->doPayment($request, $reservation)) {
                $reservationStatus = \Settings::get('reservations_need_approval', true) ? 'pending' : 'confirmed';
                $reservation->update(['status' => $reservationStatus]);
                $invoice->update(['status' => 'paid']);
            }

            ServiceSchedule::clearSelectedReservationFromSession($reservation->id);


            $reservation->setProperty('contact_details', $contactDetails);
            $invoice->setProperty('billing_address', $contactDetails);


            return redirectTo(url("reserve/checkout/success/$reservation->hashed_id"));

        } catch (\Exception $exception) {
            $message = [
                'level' => 'error',
                'message' => $exception->getMessage()
            ];

            $code = 400;
        }

        return response()->json($message, $code ?? 200);
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function cancel(Request $request, Reservation $reservation, ReservationService $reservationService)
    {
        if (user()) {
            abort_if(
                user()->cant('cancel', $reservation)
                ||
                (
                    $reservation->service_id != optional(ReservationFacade::getUserService(user()))->id
                    && $reservation->owner_id != user()->id
                )
                , 403
            );
        }

        try {

            $reservationService->cancel($reservation);

            $message = [
                'action' => 'redirectTo',
                'url' => url('/'),
                'message' => 'Reservation Successfully Cancelled',
                'level' => 'success'
            ];

        } catch (\Exception $exception) {

            $message = [
                'level' => 'error',
                'message' => $exception->getMessage()
            ];

            $code = 400;
        }

        return response()->json($message, $code ?? 200);

    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function confirmReservation(Request $request, Reservation $reservation, ReservationService $reservationService)
    {
        abort_if(
            user()->cant('confirm', $reservation)
            || $reservation->service_id != optional(DoctorReservations::getUserService(user()))->id
            , 403
        );


        try {

            $reservationService->confirm($reservation);

            $message = [
                'message' => 'Reservation Successfully Confirmed',
                'level' => 'success'
            ];

        } catch (\Exception $exception) {

            $message = [
                'level' => 'error',
                'message' => $exception->getMessage()
            ];
            log_exception($exception);

            $code = 400;
        }

        return response()->json($message, $code ?? 200);
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @return Factory|View
     */
    public function successCheckout(Request $request, Reservation $reservation)
    {

        abort_if(in_array($reservation->status, ['draft']), 404);
        $this->reservationSEO('Success Checkout');


        $publicInvoiceURL = URL::signedRoute('publicInvoice',
            ['invoice' => $reservation->invoice->hashed_id]);

        return view('views.public_reservation.success_checkout')
            ->with(compact('reservation', 'publicInvoiceURL'));
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     */
    public function removeReservation(Request $request, Reservation $reservation, ReservationService $reservationService)
    {
        ServiceSchedule::clearSelectedReservationFromSession($reservation->id);

        $reservationService->destroy($request, $reservation);
    }

}
