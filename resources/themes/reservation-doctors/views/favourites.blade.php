@extends('layouts.public')

@section('before_content')
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                        href="{{url('/')}}">@lang('reservation-doctors::labels.doctors.home')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">{{$title}}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('title',$title)

@section('content')
    <div class="row row-grid">

        @forelse($userWishlists as $wishlist)
            @php
                $doctor = $wishlist->wishlistable;
                $service = \ReservationFacade::getUserService($doctor);
            @endphp
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="profile-widget">
                    <div class="doc-img">
                        <a href="{{url('doctor/'.$doctor->hashed_id)}}">
                            <img class="img-fluid" alt="User Image" src="{{$doctor->picture}}">
                        </a>
                        @include('views.partials.wishlist_button',compact('doctor'))
                    </div>
                    <div class="pro-content">
                        <h3 class="title">
                            <a href="{{url('doctor/'.$doctor->hashed_id)}}">{{$doctor->full_name}}</a>
                            <i class="fas fa-check-circle verified"></i>
                        </h3>
                        <p class="speciality">{{$service->caption}}</p>
                                                <div class="rating">
                                                    @include('views.partials.doctor_ratings',[
                                                        'reviewRating'=>Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceAverageRating($service,true)[0],
                                                        'reviewsCount'=>Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceCountRating($service)[0]
                                                        ])
                                                </div>
                        <ul class="available-info">
                            <li>
                                <i class="fas fa-map-marker-alt"></i> {{$service->getProperty('address')}}
                            </li>
                            {{--                            <li>--}}
                            {{--                                <i class="far fa-clock"></i> Available on Fri, 22 Mar--}}
                            {{--                            </li>--}}
                            {{--                            <li>--}}
                            {{--                                <i class="far fa-money-bill-alt"></i> $300 - $1000 <i class="fas fa-info-circle"--}}
                            {{--                                                                                      data-toggle="tooltip"--}}
                            {{--                                                                                      title="Lorem Ipsum"></i>--}}
                            {{--                            </li>--}}
                        </ul>
                        <div class="row row-sm">
                            <div class="col-6">
                                <a href="{{url('doctor/'.$doctor->hashed_id)}}"
                                   class="btn view-btn">@lang('reservation-doctors::labels.doctors.view_profile')</a>
                            </div>
                            <div class="col-6">
                                <a href="{{url('reserve/schedule/'.\ReservationFacade::getUserService($doctor)->hashed_id)}}"
                                   class="btn book-btn">@lang('reservation-doctors::labels.doctors.book_now')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12 col-lg-12 col-xl-12">

                <div class="card">
                    <div class="card-body text-center">
                        <h4>
                            @lang('reservation-doctors::labels.doctors.no_results_found')
                        </h4>
                    </div>
                </div>
            </div>

        @endforelse

    </div>

    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12">
            {!! $userWishlists->links() !!}

        </div>
    </div>
@endsection