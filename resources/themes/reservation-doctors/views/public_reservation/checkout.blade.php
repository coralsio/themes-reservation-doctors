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
                                aria-current="page">@lang('reservation-doctors::labels.doctors.checkout')</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.doctors.checkout')</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-lg-8">
                    <div class="card">
                        <div class="card-body">

                            <!-- Checkout Form -->
                            <form action="{{url("reserve/checkout/$reservation->hashed_id")}}" method="post"
                                  id="payment-form"
                                  class="ajax-form"
                                  data-page_action="redirectTo">

                                <!-- Personal Information -->
                                <div class="info-widget">
                                    <h4 class="card-title">@lang('reservation-doctors::labels.doctors.personal_information')</h4>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label required-field">
                                                <label>@lang('reservation-doctors::attributes.checkout.first_name')</label>
                                                <input name="first_name" class="form-control" type="text"
                                                       value="{{optional(user())->name}}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label required-field">
                                                <label>@lang('reservation-doctors::attributes.checkout.last_name')</label>
                                                <input name="last_name" class="form-control" type="text"
                                                       value="{{optional(user())->last_name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label required-field">
                                                <label>@lang('reservation-doctors::attributes.checkout.email')</label>
                                                <input name="email" class="form-control" type="email"
                                                       value="{{optional(user())->email}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group card-label required-field">
                                                <label>@lang('reservation-doctors::attributes.checkout.phone')</label>
                                                <input name="phone" class="form-control" type="text"
                                                       value="{{optional(user())->phone_number}}">
                                            </div>
                                        </div>
                                    </div>
                                    @guest
                                        <div class="exist-customer">@lang('reservation-doctors::labels.doctors.existing_customer?')
                                            <a href="#">@lang('reservation-doctors::labels.doctors.click_here_to_login')</a>
                                        </div>
                                    @endguest

                                </div>
                                <!-- /Personal Information -->

                                <div class="payment-widget">
                                    @include('views.public_reservation.partials.payment')

                                    <div class="terms-accept">
                                        <div class="custom-checkbox form-group">
                                            <input type="checkbox" id="terms_accept" name="terms">
                                            <label for="terms_accept">@lang('reservation-doctors::labels.auth.agree')

                                            </label>
                                        </div>
                                    </div>

                                    <div class="submit-section mt-4">
                                        <button id="checkout-pay" type="submit" class="btn btn-primary submit-btn">
                                            @lang('reservation-doctors::labels.doctors.confirm_and_pay')
                                        </button>

                                        <a href="{{url("reserve/cancel/$reservation->hashed_id")}}"
                                           data-confirmation="want to to cancel reservation ?"
                                           data-page_action="redirectTo"
                                           class="btn btn-secondary submit-btn" data-action="post">
                                            @lang('reservation-doctors::labels.doctors.cancel')
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <!-- /Checkout Form -->

                        </div>
                    </div>

                </div>

                <div class="col-md-5 col-lg-4 theiaStickySidebar">

                    <!-- Booking Summary -->
                    <div class="card booking-card">
                        <div class="card-header">
                            <h4 class="card-title">@lang('reservation-doctors::labels.doctors.booking_summary')</h4>
                        </div>
                        <div class="card-body">

                            <!-- Booking Doctor Info -->
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
                                    <div class="clinic-details">
                                        <p class="doc-location"><i
                                                    class="fas fa-map-marker-alt"></i> {{$service->getProperty('address')}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Booking Doctor Info -->

                            <div class="booking-summary mt-3">
                                <div class="booking-item-wrap">
                                    <ul class="booking-date">
                                        <li>@lang('reservation-doctors::labels.doctors.date')
                                            <span>{{$reservation->starts_at->format('j M Y')}}</span></li>
                                        <li>@lang('reservation-doctors::labels.doctors.time')
                                            <span>{{$reservation->starts_at->format('h:i A')}}</span></li>
                                    </ul>
                                    <ul class="booking-fee">
                                        @foreach($invoice->items as $item)
                                            <li>{{$item->description}} <span>{{$item->present('total')}}</span></li>
                                        @endforeach
                                    </ul>
                                    <div class="booking-total">
                                        <ul class="booking-total-list">
                                            <li>
                                                <span>@lang('reservation-doctors::labels.doctors.total')</span>
                                                <span class="total-cost">{{$invoice->present('total')}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Booking Summary -->

                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_content')
    @component('components.modal',['id'=>'terms','header'=>\Settings::get('site_name').' Terms and policy'])
        {!! \Settings::get('terms_and_policy') !!}
    @endcomponent

    @guest
        @component('components.modal',['id'=>'login-modal','header'=>trans('reservation-doctors::labels.auth.login')])
            <form method="post" action="{{ route('login') }}" id="login-form"
                  class="ajax-form login-box"
                  data-page_action="loggedInSuccess">
                {{ csrf_field() }}
                <div class="padding-top-3x hidden-md-up"></div>


                <div class="col-md-12 col-sm-12">
                    <div class="form-group card-label required-field">
                        <label>@lang('User::attributes.user.email')</label>
                        <input name="email" class="form-control" type="text">
                    </div>
                </div>

                <div class="col-md-12 col-sm-12">
                    <div class="form-group card-label required-field">
                        <label>@lang('User::attributes.user.password')</label>
                        <input name="password" class="form-control" type="password">
                    </div>
                </div>

                <div class="text-center text-sm-right">
                    <button type="submit"
                            class="btn btn-primary margin-bottom-none">@lang('reservation-doctors::labels.auth.login')</button>
                </div>

            </form>

        @endcomponent
    @endguest
@endsection

@section('js')
    @guest
        <script>
            $('.exist-customer').on('click', function (e) {
                e.preventDefault();
                $('#login-modal').modal();
            });

            function loggedInSuccess(response) {
                $('.exist-customer').remove();
                $('#login-modal').modal('hide');

                let user = response.user;

                $(`[name='first_name']`).val(user.name);
                $(`[name='last_name']`).val(user.last_name);
                $(`[name='email']`).val(user.email);
                $(`[name='phone']`).val(user.phone_number);

            }
        </script>
    @endguest
@endsection
