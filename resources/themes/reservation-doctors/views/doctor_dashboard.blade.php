@push('partial_css')
    <style>
        .fc-event-time, .fc-event-title {
            color: #72afd2 !important;
        }

        .fc-event-title {
            font-weight: 700 !important;
        }
    </style>
@endpush

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-4">@lang('reservation-doctors::labels.doctors.patient_appointment')</h4>
        <div class="appointment-tab">

            <!-- Appointment Tab -->
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded">
                <li class="nav-item">
                    <a class="nav-link active" href="#upcoming-appointments"
                       data-toggle="tab">@lang('reservation-doctors::labels.doctors.upcoming')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#today-appointments"
                       data-toggle="tab">@lang('reservation-doctors::labels.doctors.today')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#previous-appointments"
                       data-toggle="tab">@lang('reservation-doctors::labels.partial.previous')</a>
                </li>
            </ul>
            <!-- /Appointment Tab -->

            <div class="tab-content">

                <!-- Upcoming Appointment Tab -->
                <div class="tab-pane show active" id="upcoming-appointments">
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                    <tr>
                                        <th>@lang('reservation-doctors::labels.doctors.patient_name')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.appt_date')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.reservation_code')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.status')</th>
                                        <th class="text-center">@lang('reservation-doctors::labels.doctors.paid_amount')</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @include('views.partials.reservations_records_table',['reservations'=>$upcomingReservations=Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getDoctorUpcomingReservations()])
                                    </tbody>
                                </table>
                            </div>

                            <div class="m-2">
                                {!! $upcomingReservations->links() !!}
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /Upcoming Appointment Tab -->

                <!-- Today Appointment Tab -->
                <div class="tab-pane" id="today-appointments">
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                    <tr>
                                        <th>@lang('reservation-doctors::labels.doctors.patient_name')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.appt_date')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.reservation_code')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.status')</th>
                                        <th class="text-center">@lang('reservation-doctors::labels.doctors.paid_amount')</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @include('views.partials.reservations_records_table',['reservations'=>$todayReservations=Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getDoctorTodayReservations()])

                                    </tbody>
                                </table>

                            </div>

                            <div class="m-2">
                                {!! $todayReservations->links() !!}
                            </div>

                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="previous-appointments">
                    <div class="card card-table mb-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                    <tr>
                                        <th>@lang('reservation-doctors::labels.doctors.patient_name')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.appt_date')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.reservation_code')</th>
                                        <th>@lang('reservation-doctors::labels.doctors.status')</th>
                                        <th class="text-center">@lang('reservation-doctors::labels.doctors.paid_amount')</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @include('views.partials.reservations_records_table',['reservations'=>$todayReservations=Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getDoctorPreviousReservations()])

                                    </tbody>
                                </table>

                            </div>

                            <div class="m-2">
                                {!! $todayReservations->links() !!}
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('Reservation::services.partials.service_events',[
    'service'=> ReservationFacade::getUserService(user()),
   'column'=>12
])