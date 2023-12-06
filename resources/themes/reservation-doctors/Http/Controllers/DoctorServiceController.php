<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\Modules\Reservation\Facades\ReservationFacade;
use Corals\Modules\Reservation\Models\Service;
use Corals\Modules\Reservation\Services\ServiceService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorServiceController extends DoctorBaseController
{
    /**
     * @param Request $request
     * @param ServiceService $serviceService
     * @return Factory|View
     */
    public function editMyService(Request $request, ServiceService $serviceService)
    {
        $service = ReservationFacade::getUserService(user());

        $this->setViewSharedData([
            'title' => "Edit [$service->code] Line item",
            'title_singular' => trans('Corals::labels.update_title', ['title' => $service->getIdentifier('code')])
        ]);

        $serviceSchedules = $serviceService->getServiceSchedule($service);

        return view('views.services.edit_my_service')
            ->with(compact('service', 'serviceSchedules'));
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param ServiceService $serviceService
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Service $service, ServiceService $serviceService)
    {
        $this->validate($request, [
            'name' => 'required|unique:res_services,name,' . $service->id,
            'status' => 'required',
            'main_line_item' => 'required',
            'slot_in_minutes' => 'nullable|numeric|min:0',
            'caption' => 'required|max:255',
            'category' => 'required'
        ]);


        try {
            $serviceService->update($request, $service);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Service::class, 'update');
        }

        return redirectTo(url('my-dashboard'));
    }

}
