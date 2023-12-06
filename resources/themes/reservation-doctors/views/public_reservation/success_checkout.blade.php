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
                                aria-current="page">@lang('reservation-doctors::labels.doctors.success_checkout')</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.doctors.success_checkout')</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content success-page-cont">
        <div class="container-fluid">

            <div class="row justify-content-center">
                <div class="col-lg-6">

                    <!-- Success Card -->
                    <div class="card success-card">
                        <div class="card-body">
                            <div class="success-cont">

                                <i class="fas fa-check"></i>
                                <h3>@lang('reservation-doctors::labels.doctors.appointment_booked_successfully')</h3>
                                <h5> @lang('reservation-doctors::labels.doctors.reservation_code')
                                    :[ {{$reservation->code}}
                                    ]</h5>

                                {!! \Facades\Corals\Modules\Reservation\Classes\DoctorReservations::successCheckoutMessage($reservation) !!}

                                <a href="{{$publicInvoiceURL}}" target="_blank"
                                   class="btn btn-primary view-inv-btn">@lang('reservation-doctors::labels.doctors.view_invoice')</a>

                            </div>
                        </div>
                    </div>
                    <!-- /Success Card -->

                </div>
            </div>

        </div>
    </div>
@endsection