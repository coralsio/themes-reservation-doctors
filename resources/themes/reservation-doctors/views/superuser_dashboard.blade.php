<div class="row">
    <div class="col-md-12">
        <div class="card dash-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-3">
                        <div class="dash-widget dct-border-rht">
                            <div class="circle-bar circle-bar1">
                                <div class="circle-graph1" data-percent="75">
                                    <img src="{{$theme->url('img/icon-01.png')}}"
                                         class="img-fluid" alt="patient">
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6>@lang('reservation-doctors::labels.doctors.total_patient')</h6>
                                <h3>{{Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getTotalPatients()}}</h3>
                                <p class="text-muted">@lang('reservation-doctors::labels.doctors.till_today')</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-3">
                        <div class="dash-widget dct-border-rht">
                            <div class="circle-bar circle-bar2">
                                <div class="circle-graph2" data-percent="65">
                                    <img src="{{$theme->url('img/icon-02.png')}}"
                                         class="img-fluid" alt="Patient">
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6>@lang('reservation-doctors::labels.doctors.today_patients')</h6>
                                <h3>{{Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getTodayPatients()}}</h3>
                                <p class="text-muted">{{today()->format('d, M Y')}}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-3">
                        <div class="dash-widget">
                            <div class="circle-bar circle-bar3">
                                <div class="circle-graph3" data-percent="50">
                                    <img src="{{ $theme->url('img/icon-03.png') }}"
                                         class="img-fluid" alt="Patient">
                                </div>
                            </div>
                            <div class="dash-widget-info">
                                <h6>@lang('reservation-doctors::labels.doctors.appointments')</h6>
                                <h3>{{Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getTotalReservations()}}</h3>
                                <p class="text-muted">{{today()->format('d, M Y')}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-3">
                        <div class="dash-widget dct-border-rht">
                            <div class="circle-bar circle-bar1">
                                <i class="fa fa-folder-open fa-fw" style="font-size: 50px"></i>
                            </div>
                            <div class="dash-widget-info">
                                <h6>@lang('reservation-doctors::labels.partial.categories')</h6>
                                <h3>{{Facades\Corals\Modules\Reservation\Classes\DoctorReservations::reservationCategoriesCount()}}</h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card dash-card">
            <div class="card-body">
                @widget('monthly_revenue')
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card dash-card">
            <div class="card-body">
                @widget('top_services_categories')
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dash-card">
            <div class="card-body">
                @widget('daily_reservations')
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dash-card">
            <div class="card-body">
                @widget('this_week_reservations_by_status')
            </div>
        </div>
    </div>
</div>
