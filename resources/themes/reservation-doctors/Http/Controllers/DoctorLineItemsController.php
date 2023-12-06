<?php

namespace Corals\Modules\Reservation\Http\Controllers;

use Corals\Modules\Reservation\Models\LineItem;
use Corals\Modules\Reservation\Services\LineItemService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorLineItemsController extends DoctorBaseController
{
    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {

        $this->setViewSharedData([
            'title' => 'Line Items'
        ]);

        $lineItems = LineItem::query()
            ->where(function ($query) {
                $query->where('res_line_items.owner_id', user()->id)
                    ->where('res_line_items.owner_type', getMorphAlias(user()));
            })->latest()
            ->paginate(10);

        return view('views.line_items.index')->withLineItems($lineItems);
    }

    /**
     * @param Request $request
     * @param LineItem $lineItem
     * @return mixed
     */
    public function edit(Request $request, LineItem $lineItem)
    {
        $this->setViewSharedData([
            'title' => "Edit [$lineItem->code] Line item",
            'resource_url' => url('doctor/line-items'),
            'title_singular' => trans('Corals::labels.update_title', ['title' => $lineItem->getIdentifier('code')])
        ]);


        $url = url("doctor/line-items/$lineItem->hashed_id/update");
        $method = 'PUT';

        return view('views.line_items.create_edit')
            ->withLineItem($lineItem)
            ->withurl($url)
            ->withMethod($method);
    }

    /**
     * @param Request $request
     * @param LineItem $lineItem
     * @param LineItemService $lineItemService
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, LineItem $lineItem, LineItemService $lineItemService)
    {
        $this->validate($request, [
            'rate_value' => 'required|numeric|min:0',
            'name' => 'required',
            'status' => 'required',
            'rate_type' => 'required',
            'min_qty' => 'numeric|min:0',
            'max_qty' => 'numeric|min:0',
            'description' => 'required',
            'code' => 'required|unique:res_line_items,code,' . $lineItem->id,

        ]);

        try {
            $lineItemService->update($request, $lineItem);

            flash(trans('Corals::messages.success.updated', ['item' => 'Line Item']))->success();
        } catch (\Exception $exception) {
            log_exception($exception, LineItem::class, 'update');
        }

        return redirectTo('doctor/line-items');
    }

    public function create(Request $request, LineItem $lineItem)
    {
        $this->setViewSharedData([
            'title' => "Create Line item",
            'resource_url' => url('doctor/line-items')
        ]);


        $url = url("doctor/line-items/store");
        $method = 'post';

        return view('views.line_items.create_edit')
            ->withLineItem($lineItem)
            ->withurl($url)
            ->withMethod($method);
    }

    /**
     * @param Request $request
     * @param LineItemService $lineItemService
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, LineItemService $lineItemService)
    {

        $this->validate($request, [
            'rate_value' => 'required|numeric|min:0',
            'name' => 'required',
            'status' => 'required',
            'rate_type' => 'required',
            'min_qty' => 'numeric|min:0',
            'max_qty' => 'numeric|min:0',
            'description' => 'required',
            'code' => 'required|unique:res_line_items,code'
        ]);

        try {

            $lineItemService->store($request, LineItem::class, [
                'owner_id' => user()->id,
                'owner_type' => getMorphAlias(user())
            ]);

            flash(trans('Corals::messages.success.updated', ['item' => 'Line Item']))->success();
        } catch (\Exception $exception) {
            log_exception($exception, LineItem::class, 'update');
        }

        return redirectTo('doctor/line-items');
    }

}
