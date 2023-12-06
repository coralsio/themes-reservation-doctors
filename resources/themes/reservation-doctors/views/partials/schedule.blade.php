<div class="container">

    <div class="row" id="schedule">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <div class="booking-doc-info">
                        <a href="{{url("doctor/$owner->hashed_id")}}" class="booking-doc-img">
                            <img src="{{$owner->picture}}" alt="User Image">
                        </a>
                        <div class="booking-info">
                            <h4><a href="{{url("doctor/$owner->hashed_id")}}">{{$owner->full_name}}</a></h4>
                            <div class="rating">
                                @include('views.partials.doctor_ratings',[
                                                       'reviewRating'=>Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceAverageRating($service,true)[0],
                                                          'reviewsCount'=>Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceCountRating($service)[0]
                                                ])
                            </div>
                            <p class="text-muted mb-0"><i
                                        class="fas fa-map-marker-alt"></i> {{$service->getProperty('address')}}</p>
                        </div>
                    </div>
                </div>
            </div>
{{--            <div class="row">--}}
{{--                                <div class="col-12 col-sm-4 col-md-6">--}}
{{--                                    <h4 class="mb-1">11 November 2019</h4>--}}
{{--                                    <p class="text-muted">Monday</p>--}}
{{--                                </div>--}}
{{--                                <div class="col-12 col-sm-8 col-md-6 text-sm-right">--}}
{{--                                    <div class="bookingrange btn btn-white btn-sm mb-3">--}}
{{--                                        <i class="far fa-calendar-alt mr-2"></i>--}}
{{--                                        <span>August 1, 2020 - August 31, 2020</span>--}}
{{--                                        <i class="fas fa-chevron-down ml-2"></i>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--            </div>--}}

            <Schedule service-hashed-id="{{$service->hashed_id}}" class="py-4"/>


        </div>
    </div>
</div>



@push('partial_js')
    {!! \Html::script('/assets/core/compiled/js/manifest.js') !!}
    {!! \Html::script('/assets/core/compiled/js/vendor.js') !!}
    {!! \Theme::js('dist/js/Schedule.js') !!}
@endpush
