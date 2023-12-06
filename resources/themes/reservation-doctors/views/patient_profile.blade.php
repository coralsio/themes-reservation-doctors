@extends('layouts.master')

@section('before_content')
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                        href="{{url('/')}}">@lang('reservation-doctors::labels.doctors.home')</a></li>
                            <li class="breadcrumb-item active"
                                aria-current="page">@lang('reservation-doctors::labels.doctors.profile')</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.doctors.profile')</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('title',$title)

@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar dct-dashbd-lft">

                    <!-- Profile Widget -->
                    <div class="card widget-profile pat-widget-profile">
                        <div class="card-body">
                            <div class="pro-widget-content">
                                <div class="profile-info-widget">
                                    <a href="#" class="booking-doc-img">
                                        <img src="{{$patient->picture}}" alt="User Image">
                                    </a>
                                    <div class="profile-det-info">
                                        <h3>{{$patient->full_name}}</h3>

                                        <div class="patient-details">
                                            {{--                                            <h5><b>Patient ID :</b> PT0016</h5>--}}
                                            {{--                                            <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Newyork, United States</h5>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="patient-info">
                                <ul>
                                    @if($patient->phone_number)
                                        <li>Phone <span>{{$patient->phone_number}}</span></li>
                                    @endif
                                    <li>Email <span>{{$patient->email}}</span></li>
{{--                                    <li>Age <span>38 Years, Male</span></li>--}}
                                    {{--                                    <li>Blood Group <span>AB+</span></li>--}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /Profile Widget -->

                    <!-- Last Booking -->
                    @if( ($latestReservations = \Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getPatientReservations($patient,3))->isNotEmpty()  )
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Last Booking</h4>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach($latestReservations as $reservation)

                                    @php
                                        $service = $reservation->service;
                                        $category = $service->categories()->first();
                                    @endphp
                                    <li class="list-group-item">
                                        <div class="media align-items-center">
                                            <div class="mr-3">
                                                <a href="{{url('doctor/'.$reservation->service->owner->hashed_id)}}">
                                                    <img alt="Image placeholder"
                                                         src="{{$reservation->service->owner->picture}}"
                                                         class="avatar  rounded-circle">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <h5 class="d-block mb-0">{{$service->owner->full_name}} </h5>
                                                <span class="d-block text-sm text-muted">{{$category->name}}</span>
                                                <span class="d-block text-sm text-muted">{{$reservation->ends_at->format('d M Y h.i A')}}</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                @endif
                <!-- /Last Booking -->

                </div>

                <div class="col-md-7 col-lg-8 col-xl-9 dct-appoinment">
                    @include('views.partials.appointment_invoices')
                </div>
            </div>

        </div>

    </div>

@endsection
