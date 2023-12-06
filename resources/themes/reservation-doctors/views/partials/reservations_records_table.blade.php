@forelse($reservations as $reservation)
    @php
        $contactDetails = $reservation->getProperty('contact_details');
        $patient = $reservation->owner;
    @endphp
    <tr>
        <td>
            <h2 class="table-avatar">

                @if($patient)
                    <a href="{{url("patient/$patient->hashed_id")}}"
                       class="avatar avatar-sm mr-2"><img
                                class="avatar-img rounded-circle"
                                src="{{$patient->picture}}" alt="User Image"></a>
                    <a href="{{url("patient/$patient->hashed_id")}}">{{data_get($contactDetails,'first_name')}} {{data_get($contactDetails,'last_name')}}
                    </a>
                @else
                    {{data_get($contactDetails,'first_name')}} {{data_get($contactDetails,'last_name')}}
                @endif

            </h2>
        </td>
        <td>{{$reservation->starts_at->format('d M Y')}}<span
                    class="d-block text-info">{{$reservation->starts_at->format('h.i A')}}</span>
        </td>

        <td>
            {{$reservation->code}}
        </td>
        <td>
            {!! $reservation->present('status') !!}
        </td>
        <td class="text-center">
            {{$reservation->invoice->present('total')}}
        </td>
        <td class="text-right">
            <div class="table-action">
                {{--                                                    <a href="javascript:void(0);" class="btn btn-sm bg-info-light">--}}
                {{--                                                        <i class="far fa-eye"></i> View--}}
                {{--                                                    </a>--}}

                @can('confirm',$reservation)
                    <a href="{{url("reserve/confirm-reservation/$reservation->hashed_id")}}"
                       data-action="post"
                       data-confirmation="@lang('reservation-doctors::labels.doctors.want_to_confirm_reservation')"
                       data-page_action="site_reload"
                       class="btn btn-sm bg-success-light">
                        <i class="fas fa-check"></i> @lang('reservation-doctors::labels.doctors.confirm')
                    </a>
                @endcan

                @can('cancel',$reservation)
                    <a href="{{url("reserve/cancel/$reservation->hashed_id")}}"
                       data-confirmation="@lang('reservation-doctors::labels.doctors.want_to_cancel_reservation')"
                       data-page_action="site_reload"
                       class="btn btn-sm bg-danger-light" data-action="post">
                        @lang('reservation-doctors::labels.doctors.cancel')
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">
            @lang('reservation-doctors::labels.doctors.no_results_found')
        </td>
    </tr>
@endforelse
