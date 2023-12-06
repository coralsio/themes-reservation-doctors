@extends('layouts.master')


@section('title',$title)

@section('before_content')
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                        href="{{url('/')}}">@lang('reservation-doctors::labels.doctors.home')</a></li>
                            <li class="breadcrumb-item active"
                                aria-current="page">@lang('reservation-doctors::labels.doctors.search')</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-4 col-xl-3 theiaStickySidebar">
            @include('views.public_reservation.partials.doctors_list_filters')
        </div>

        <div class="col-md-12 col-lg-8 col-xl-9">

            @forelse($doctors as $doctor)

                @php($service = ReservationFacade::getUserService($doctor))
                <div class="card">
                    <div class="card-body">
                        <div class="doctor-widget">
                            <div class="doc-info-left">
                                <div class="doctor-img">
                                    <a href="{{url("doctor/$doctor->hashed_id")}}">
                                        <img src="{{$doctor->picture}}" class="img-fluid"
                                             alt="User Image">
                                    </a>
                                </div>
                                <div class="doc-info-cont">
                                    <h4 class="doc-name"><a
                                                href="{{url("doctor/$doctor->hashed_id")}}">{{$doctor->full_name}}</a>
                                    </h4>

                                    @php($category= $service->categories()->first())
                                    <p class="doc-speciality">{{$service->caption}}</p>
                                    <h5 class="doc-department"><img
                                                src="{{$category->thumbnail}}"
                                                class="img-fluid" alt="Speciality">{{$category->name}}</h5>
                                    <div class="rating">
                                        @include('views.partials.doctor_ratings',[
                                                     'reviewRating'=>Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceAverageRating($service,true)[0],
                                                        'reviewsCount'=>Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceCountRating($service)[0]
                                              ])
                                    </div>
                                    <div class="clinic-details">
                                        <p class="doc-location"><i
                                                    class="fas fa-map-marker-alt"></i> {{$service->getProperty('address')}}
                                        </p>
                                    </div>
                                    <div class="clinic-services">
                                        @foreach($service->optionalLineItems as $lineItem)
                                            <span class="m-1">{{$lineItem->name}}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="doc-info-right">
                                <div class="clini-infos">
                                </div>
                                <div class="clinic-booking">
                                    <a class="view-pro-btn"
                                       href="{{url("doctor/$doctor->hashed_id")}}">@lang('reservation-doctors::labels.doctors.view_profile')</a>
                                    <a class="apt-btn"
                                       href="{{url("reserve/schedule/".$service->hashed_id)}}">@lang('reservation-doctors::labels.doctors.book_appointment')  </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            @empty
                <div class="card">
                    <div class="card-body">
                        @lang('reservation-doctors::labels.doctors.no_results_found')
                    </div>
                </div>
            @endforelse

            {!! $doctors->links() !!}

        </div>
    </div>
@endsection

