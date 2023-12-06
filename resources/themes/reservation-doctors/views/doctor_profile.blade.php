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
                                aria-current="page">@lang('reservation-doctors::labels.doctors.doctor_profile')</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.doctors.doctor_profile')</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <!-- Doctor Widget -->
            <div class="card">
                <div class="card-body">
                    <div class="doctor-widget">
                        <div class="doc-info-left">
                            <div class="doctor-img">
                                <img src="{{$user->picture}}" class="img-fluid" alt="User Image">
                            </div>
                            <div class="doc-info-cont">
                                <h4 class="doc-name">{{$user->full_name}}</h4>
                                <p class="doc-speciality">{{$service->caption}}</p>

                                @php($category = $service->categories()->first())

                                <p class="doc-department"><img src="{{$category->thumbnail}}" class="img-fluid"
                                                               alt="Speciality">{{$category->name}}</p>

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
                                    @foreach($service->optionalLineItems as $item)
                                        <span class="m-1">{{$item->name}}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="doc-info-right ">
                            <div class="doctor-action justify-content-center">

                                @include('views.partials.wishlist_button',['doctor'=>$user])

                                <a href="{{url('messaging/discussions/create?user='.$user->hashed_id)}}"
                                   class="btn btn-white msg-btn">
                                    <i class="far fa-comment-alt"></i>
                                </a>
                                @if($user->phone_number)
                                    <a href="tel:{{$user->phone_number}}" class="btn btn-white call-btn">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                @endif

                            </div>
                            <div class="clinic-booking">
                                <a class="apt-btn"
                                   href="{{url("reserve/schedule/".$service->hashed_id)}}">@lang('reservation-doctors::labels.doctors.book_appointment')  </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Doctor Widget -->

            <!-- Doctor Details Tab -->
            <div class="card">
                <div class="card-body pt-0">
                    <!-- Tab Menu -->
                    <nav class="user-tabs mb-4">
                        <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                            <li class="nav-item">
                                <a class="nav-link active" href="#doc_overview"
                                   data-toggle="tab">@lang('reservation-doctors::labels.doctors.overview')</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#doc_reviews"
                                   data-toggle="tab">@lang('reservation-doctors::labels.doctors.reviews')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#doc_business_hours"
                                   data-toggle="tab">@lang('reservation-doctors::labels.doctors.business_hours')</a>
                            </li>
                        </ul>
                    </nav>
                    <!-- /Tab Menu -->

                    <!-- Tab Content -->
                    <div class="tab-content pt-0">

                        <!-- Overview Content -->
                        <div role="tabpanel" id="doc_overview" class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-md-12">

                                    <!-- About Details -->
                                    <div class="widget about-widget">
                                        <h4 class="widget-title">@lang('reservation-doctors::labels.doctors.about_me')</h4>
                                        <p>{{$service->description}}</p>
                                    </div>
                                    <!-- /About Details -->


                                    <!-- Services List -->
                                    <div class="service-list">
                                        <h4>@lang('reservation-doctors::labels.doctors.services')</h4>
                                        <ul class="clearfix">
                                            @foreach($service->optionalLineItems as $item)
                                                <li>{{$item->name}}</li>
                                            @endforeach

                                        </ul>
                                    </div>
                                    <!-- /Services List -->


                                </div>
                            </div>
                        </div>
                        <!-- /Overview Content -->

                        <!-- Reviews Content -->
                        <div role="tabpanel" id="doc_reviews" class="tab-pane fade">

                            @include('views.partials.doctor_reviews')

                        </div>
                        <!-- /Reviews Content -->

                        <!-- Business Hours Content -->
                        <div role="tabpanel" id="doc_business_hours" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-6 offset-md-3">

                                    <!-- Business Hours Widget -->
                                    <div class="widget business-widget">
                                        <div class="widget-content">


                                            <div class="listing-hours">
                                                <div class="listing-day current">
                                                    <div class="day">@lang('reservation-doctors::labels.doctors.today')
                                                        <span>{{now()->format('j M Y')}}</span>
                                                    </div>
                                                    <div class="time-items">
                                                            <span class="open-status"><span
                                                                        class="badge bg-{{$todayBusinessHours['is_open'] ? 'success':'danger'}}-light"> {{ $todayBusinessHours['is_open'] ? trans('reservation-doctors::labels.doctors.open_now') : trans('reservation-doctors::labels.doctors.closed_now') }}</span></span>
                                                        <span class="time">{{$todayBusinessHours['label']}}</span>
                                                    </div>
                                                </div>

                                                @foreach($businessHours as $businessHour)

                                                    @if($businessHour['working'])
                                                        <div class="listing-day">
                                                            <div class="day">{{$businessHour['day_name']}}</div>
                                                            <div class="time-items">
                                                                <span class="time">{{$businessHour['label']}}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="listing-day closed">
                                                            <div class="day">{{$businessHour['day_name']}}</div>
                                                            <div class="time-items">
                                                        <span class="time"><span
                                                                    class="badge bg-danger-light">@lang('reservation-doctors::labels.doctors.closed')</span></span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Business Hours Widget -->

                                </div>
                            </div>
                        </div>
                        <!-- /Business Hours Content -->

                    </div>
                </div>
            </div>
            <!-- /Doctor Details Tab -->

        </div>
    </div>
@endsection
