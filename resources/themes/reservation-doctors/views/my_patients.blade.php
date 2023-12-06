@extends('layouts.public')

@section('before_content')
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                            <li class="breadcrumb-item active"
                                aria-current="page">@lang('reservation-doctors::labels.doctors.my_patients')</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.doctors.my_patients')</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('title',$title)

@section('content')


    <div class="row row-grid">
        @forelse($reservations as $reservation)

            @php($contactDetails = $reservation->getProperty('contact_details'))

            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card widget-profile pat-widget-profile">
                    <div class="card-body">
                        <div class="pro-widget-content">
                            <div class="profile-info-widget">
                                @if($reservation->owner_id)
                                    <a href="{{url('patient/'.$reservation->owner->hashed_id)}}" class="booking-doc-img">
                                        <img src="{{$reservation->owner->picture}}" alt="User Image">
                                    </a>
                                @endif
                                <div class="profile-det-info">
                                    <h3>

                                        @if($reservation->owner_id)
                                            <a href="{{url('patient/'.$reservation->owner->hashed_id)}}">
                                                {{data_get($contactDetails,'first_name')}} {{ data_get($contactDetails,'last_name') }}
                                            </a>
                                        @else
                                            {{data_get($contactDetails,'first_name')}} {{ data_get($contactDetails,'last_name') }}

                                        @endif

                                    </h3>

                                    <div class=" patient-details">
                                        {{--                                        <h5><b>Patient ID :</b> P0016</h5>--}}
                                        <h5><b>email :</b> {{data_get($contactDetails,'email')}}</h5>
                                        {{--                                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Alabama, USA</h5>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="patient-info">
                            <ul>
                                <li>Phone <span>{{data_get($contactDetails,'phone')}}</span></li>
                                {{--                                <li>Age <span>38 Years, Male</span></li>--}}
                                {{--                                <li>Blood Group <span>AB+</span></li>--}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="card widget-profile pat-widget-profile">
                    <div class="card-body text-center">
                        <h4>  @lang('reservation-doctors::labels.doctors.no_results_found') </h4>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12">
            {!! $reservations->links() !!}
        </div>
    </div>
@endsection