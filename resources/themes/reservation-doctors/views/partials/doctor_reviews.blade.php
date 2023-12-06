<!-- Review Listing -->
<div class="widget review-listing">
    <ul class="comments-list">

        @forelse(Facades\Corals\Modules\Reservation\Classes\Reservation::getServiceRating($service) as $review)
            <li>
                <div class="comment">
                    <img class="avatar avatar-sm rounded-circle" alt="User Image"
                         src="{{$review->author->picture}}">
                    <div class="comment-body w-100">
                        <div class="meta-data">
                            <span class="comment-author">{{$review->author->full_name}}</span>
                            <span class="comment-date">@lang('reservation-doctors::labels.doctors.reviewed') {{$review->created_at->diffForHumans()}}</span>
                            <div class="review-count rating">
                                @include('views.partials.doctor_ratings',['reviewRating'=>$review->rating,'reviewsCount'=>null])
                            </div>
                        </div>
                        <p class="{{ $review->rating >= 4?'recommended':'' }}">
                            @if($review->rating >= 4)
                                <i class="far fa-thumbs-up"></i>
                            @elseif($review->rating < 2 )
                                <i class="far fa-thumbs-down"></i>
                            @endif

                            {{ $review->title }}
                        </p>
                        <p class="comment-content">
                            {!! $review->body !!}
                        </p>

                    </div>
                </div>

            </li>
        @empty

        @endforelse
    </ul>

    <!-- Show All -->
{{--    <div class="all-feedback text-center">--}}
{{--        <a href="#" class="btn btn-primary btn-sm">--}}
{{--            Show all feedback <strong>(167)</strong>--}}
{{--        </a>--}}
{{--    </div>--}}
<!-- /Show All -->

</div>
<!-- /Review Listing -->

<div class="write-review">
    <h4>@lang('reservation-doctors::labels.doctors.write_review_for') <strong>{{$user->full_name}}</strong></h4>
    @auth
        <div class="row">
            <div class="col-md-6">
                <form action="{{url("reservation/$service->hashed_id/create-rate")}}" method="post" class="ajax-form">
                    <div class="form-group">
                        <label>@lang('reservation-doctors::labels.doctors.review')</label>
                        <div class="star-rating">
                            <input id="star-5" type="radio" name="review_rating" value="5">
                            <label for="star-5" title="5 stars">
                                <i class="active fa fa-star"></i>
                            </label>
                            <input id="star-4" type="radio" name="review_rating" value="4">
                            <label for="star-4" title="4 stars">
                                <i class="active fa fa-star"></i>
                            </label>
                            <input id="star-3" type="radio" name="review_rating" value="3">
                            <label for="star-3" title="3 stars">
                                <i class="active fa fa-star"></i>
                            </label>
                            <input id="star-2" type="radio" name="review_rating" value="2">
                            <label for="star-2" title="2 stars">
                                <i class="active fa fa-star"></i>
                            </label>
                            <input id="star-1" type="radio" name="review_rating" value="1">
                            <label for="star-1" title="1 star">
                                <i class="active fa fa-star"></i>
                            </label>
                        </div>
                    </div>
                    {!! CoralsForm::text('review_subject','reservation-doctors::attributes.tab.subject',true) !!}

                    <div class="form-group">
                        {!! CoralsForm::textarea('review_text','reservation-doctors::attributes.tab.review',true,null,['rows'=>4]) !!}


                        {{--            <div class="d-flex justify-content-between mt-3"><small class="text-muted"><span--}}
                        {{--                            id="chars">100</span> characters remaining</small></div>--}}
                    </div>
                    <hr>
                    {{--        <div class="form-group">--}}
                    {{--            <div class="terms-accept">--}}
                    {{--                <div class="custom-checkbox">--}}
                    {{--                    <input type="checkbox" id="terms_accept">--}}
                    {{--                    <label for="terms_accept">I have read and accept <a href="#">Terms &amp;--}}
                    {{--                            Conditions</a></label>--}}
                    {{--                </div>--}}
                    {{--            </div>--}}
                    {{--        </div>--}}
                    <div class="submit-section">
                        <button type="submit"
                                class="btn btn-primary submit-btn">@lang('reservation-doctors::labels.doctors.add_review')</button>
                    </div>
                </form>
            </div>

        </div>

    @else
        <h6 class="text-warning">
            @lang('reservation-doctors::labels.partial.tabs.need_login_review')
        </h6>
    @endauth
</div>

