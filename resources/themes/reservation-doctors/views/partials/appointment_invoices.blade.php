<div class="card">
    <div class="card-body pt-0">
        <div class="user-tabs">
            <ul class="nav nav-tabs nav-tabs-bottom nav-justified flex-wrap">
                <li class="nav-item">
                    <a class="nav-link active" href="#pat_appointments"
                       data-toggle="tab">@lang('reservation-doctors::labels.doctors.appointments')</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" href="#billing"
                       data-toggle="tab"><span>@lang('reservation-doctors::labels.doctors.billing')</span></a>
                </li>
            </ul>
        </div>
        <div class="tab-content">

            <!-- Appointment Tab -->
            <div id="pat_appointments" class="tab-pane fade show active">
                <div class="card card-table mb-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('reservation-doctors::labels.doctors.doctor')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.appt_date')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.booking_date')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.reservation_code')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.amount')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.status')</th>
                                    <th>@lang('Corals::labels.actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($reservations??[] as $reservation)
                                    @php
                                        $service = $reservation->service;
                                       $doctor = $reservation->service->owner;
                                    @endphp
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="{{url("doctor/".$doctor->hashed_id)}}"
                                                   class="avatar avatar-sm mr-2">
                                                    <img class="avatar-img rounded-circle"
                                                         src="{{$doctor->picture}}"
                                                         alt="User Image">
                                                </a>
                                                <a href="{{url("doctor/".$doctor->hashed_id)}}">{{$doctor->full_name}}
                                                    <span>{{$service->categories()->first()->name}}</span></a>
                                            </h2>
                                        </td>
                                        <td>{{$reservation->starts_at->format('d M Y')}}<span
                                                    class="d-block text-info">{{$reservation->starts_at->format('h.i A')}}</span>
                                        </td>
                                        <td>{{ $reservation->present('created_at') }}</td>

                                        <td>{{ $reservation->code }}</td>

                                        <td>
                                            {{$reservation->invoice ? $reservation->invoice->present('total') : '-' }}
                                        </td>
                                        <td>
                                            {!! $reservation->present('status') !!}
                                        </td>
                                        <td class="text-right">
                                            <div class="table-action">


                                                @can('cancel',$reservation)
                                                    <a href="{{url("reserve/cancel/$reservation->hashed_id")}}"
                                                       data-confirmation="want to to cancel reservation ?"
                                                       data-page_action="site_reload"
                                                       class="btn bg-danger-light  btn-sm" data-action="post">
                                                        <i class="far fa-trash-alt"></i> @lang('reservation-doctors::labels.doctors.cancel')
                                                    </a>
                                                @endcan


                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            @lang('reservation-doctors::labels.doctors.no_results_found')
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {!! $reservations?$reservations->links():'' !!}
                    </div>
                </div>
            </div>
            <!-- /Appointment Tab -->

            <!-- Billing Tab -->
            <div class="tab-pane" id="billing">
                <div class="card card-table mb-0">
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-hover table-center mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('reservation-doctors::labels.doctors.invoice_no')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.doctor')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.reservation_code')</th>
                                    <th>@lang('reservation-doctors::labels.doctors.amount')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($invoices??[] as $invoice)
                                    @php
                                        $reservation = $invoice->invoicable;
                                        $service = $reservation->service;
                                        $doctor = $service->owner;
                                    @endphp
                                    <tr>
                                        <td>
                                            @can('view',$invoice)
                                                <a href="{{$invoice->getShowURL()}}">
                                                    {{$invoice->code}}
                                                </a>
                                            @else
                                                {{$invoice->code}}
                                            @endcan
                                        </td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="{{url("doctor/$doctor->hashed_id")}}"
                                                   class="avatar avatar-sm mr-2">
                                                    <img class="avatar-img rounded-circle"
                                                         src="{{$doctor->picture}}"
                                                         alt="User Image">
                                                </a>
                                                <a href="{{url("doctor/$doctor->hashed_id")}}">{!! $doctor->full_name !!}
                                                    <span>{{$service->categories()->first()->name}}</span></a>
                                            </h2>
                                        </td>
                                        <td>
                                            {{$reservation->code}}
                                        </td>
                                        <td>{{$invoice->present('total')}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            @lang('reservation-doctors::labels.doctors.no_results_found')
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {!! $invoices?$invoices->links():'' !!}
                    </div>
                </div>
            </div>
            <!-- Billing Tab -->

        </div>
    </div>
</div>
