@extends('layouts.master')

@section('content')
    <section class="section section-search">
        <div class="container-fluid">
            <div class="banner-wrapper">
                <div class="banner-header text-center">
                    <h1>@lang('reservation-doctors::labels.template.home.search_make_appointment')</h1>
                    <p>@lang('reservation-doctors::labels.template.home.search_make_appointment_header')</p>
                </div>

                <!-- Search -->
                <div class="search-box">
                    <form action="{{url('reserve/list')}}">
                        <div class="form-group search-location">
                            <input type="text" id="_autocomplete" name="address" class="form-control"
                                   placeholder="@lang('reservation-doctors::labels.template.home.search_location')">
                            <span class="form-text">@lang('reservation-doctors::labels.template.home.location_example')</span>
                        </div>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="long" name="long">


                        <div class="form-group search-info">
                            <input type="text" class="form-control"
                                   name="search_term"
                                   placeholder="@lang('reservation-doctors::labels.doctors.search_term_placeholder')">
                            <span class="form-text">@lang('reservation-doctors::labels.doctors.search_term_placeholder')</span>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 46px;-ms-flex: 0 0 46px;
                                                                                flex: 0 0 46px;height: 46px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <!-- /Search -->

            </div>
        </div>
    </section>
    <section class="section section-specialities">
        <div class="container-fluid">
            <div class="section-header text-center">
                <h2>@lang('reservation-doctors::labels.template.home.clinic_specialities')</h2>
                <p class="sub-title">@lang('reservation-doctors::labels.template.home.clinic_specialities_header')</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <!-- Slider -->
                    <div class="specialities-slider slider">

                        <!-- Slider Item -->
                        @foreach(Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getCategories() as $category)
                            <a href="{{url('reserve/list?categories[]='.$category->slug)}}">
                                <div class="speicality-item text-center">
                                    <div class="speicality-img">

                                        <img src="{{$category->thumbnail}}" class="img-fluid"
                                             alt="Speciality">
                                        <span><i class="fa fa-circle" aria-hidden="true"></i></span>
                                    </div>
                                    <p>{{$category->name}}</p>
                                </div>
                            </a>
                    @endforeach
                    <!-- /Slider Item -->


                    </div>
                    <!-- /Slider -->

                </div>
            </div>
        </div>
    </section>
    <section class="section section-doctor">
        <div class="container-fluid">
            <div class="row">

                {!!  \Shortcode::compile( 'block', 'book-our-doctor' )  !!}


                <div class="col-lg-8">
                    <div class="doctor-slider slider">
                        @foreach(Facades\Corals\Modules\Reservation\Classes\DoctorReservations::getDoctorsList() as $doctor)

                            @php($service = \ReservationFacade::getUserService($doctor))

                            <div class="profile-widget">
                                <div class="doc-img">
                                    <a href="{{ url("doctor/$doctor->hashed_id") }}">
                                        <img class="img-fluid" alt="User Image" src="{{$doctor->picture}}">
                                    </a>

                                    @include('views.partials.wishlist_button',['doctor'=>$doctor])
                                </div>


                                <div class="pro-content">
                                    <h3 class="title">

                                        <a href="{{ url("doctor/$doctor->hashed_id") }}">{{$doctor->full_name}}</a>
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
                                        {{--                                        <li>--}}
                                        {{--                                            <i class="far fa-clock"></i> Available on Fri, 22 Mar--}}
                                        {{--                                        </li>--}}
                                        {{--                                        <li>--}}
                                        {{--                                            <i class="far fa-money-bill-alt"></i> $300 - $1000--}}
                                        {{--                                            <i class="fas fa-info-circle" data-toggle="tooltip" title="Lorem Ipsum"></i>--}}
                                        {{--                                        </li>--}}
                                    </ul>
                                    <div class="row row-sm">
                                        <div class="col-6">
                                            <a href="{{ url("doctor/$doctor->hashed_id") }}"
                                               class="btn view-btn">@lang('reservation-doctors::labels.doctors.view_profile')</a>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ url("reserve/schedule/$service->hashed_id") }}"
                                               class="btn book-btn">@lang('reservation-doctors::labels.doctors.book_now')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="section section-blogs" style="background-color: white">
        <div class="container-fluid">

            <!-- Section Header -->
            <div class="section-header text-center">
                <h2>@lang('reservation-doctors::labels.template.home.blogs_news')</h2>
                <p class="sub-title">@lang('reservation-doctors::labels.template.home.blogs_news_header')</p>
            </div>
            <!-- /Section Header -->

            <div class="row blog-grid-row d-flex justify-content-center">
                @foreach(\CMS::getLatestPosts(4) as $post)
                    <div class="col-md-6 col-lg-3 col-sm-12">
                        <div class="blog grid-blog">
                            <div class="blog-image">
                                <a href="{{ url($post->slug) }}"><img class="img-fluid" src="{{$post->featured_image}}"
                                                                      alt="Post Image"></a>
                            </div>
                            <div class="blog-content">
                                <ul class="entry-meta meta-item">
                                    <li>
                                        <div class="post-author">
                                            <a href="{{$post->author->getShowURL()}}"><img
                                                        src="{{$post->author->picture}}"
                                                        alt="Post Author">
                                                <span>{{$post->author->full_name}}</span></a>
                                        </div>
                                    </li>
                                    <li><i class="far fa-clock"></i> {{$post->created_at->format('j M Y')}}</li>
                                </ul>
                                <h3 class="blog-title"><a href="{{ url($post->slug) }}">{{ $post->title }}</a></h3>
                                <p class="mb-0">   {{ \Str::limit(strip_tags($post->rendered ),80) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="view-all text-center">
                <a href="{{url('blog')}}"
                   class="btn btn-primary">@lang('reservation-doctors::labels.template.home.view_all')</a>
            </div>
        </div>
    </section>
@endsection

@section('js')
    {!! Html::script(asset('assets/corals/js/auto_complete_google_address.js')) !!}
@endsection