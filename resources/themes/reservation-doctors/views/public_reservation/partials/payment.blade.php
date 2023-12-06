<div class="row">
    <div class="col-md-12">

        @if($gateway)
            <h4>@lang('reservation-doctors::labels.doctors.enter_payment')</h4>
            <hr>
            @include($gateway->getPaymentViewName('ecommerce'),['action'=>$urlPrefix.'checkout/step/select-payment'])
        @else
            @php \Actions::do_action('pre_order_checkout_form',$gateway) @endphp
            <div class="">
                {!! CoralsForm::openForm(null,['url' => url($urlPrefix.'checkout/step/select-payment'),'method'=>'POST','files'=>true,'class'=>'ajax-form','id'=>'PaymentForm']) !!}
                {{--                <input type="hidden" name="order" value="{{ $order->hashed_id  }}"/>--}}

                <h4>@lang('reservation-doctors::labels.doctors.select_payment')</h4>
                <hr>
                <br>
                {!! CoralsForm::radio('select_gateway','',false,  $available_gateways ) !!}
                <div class="form-group">
                    <span data-name="checkoutToken"></span>
                </div>
            </div>
            {!! CoralsForm::closeForm() !!}
            <div id="gatewayPayment">

            </div>
        @endif
    </div>
</div>

@push('partial_js')

    <script type="application/javascript">
        $(document).ready(function () {
            var reservationId = '{{ $reservation->hashed_id }}';

            $('input[name="select_gateway"]').on('change', function () {

                if ($(this).prop('checked')) {
                    var gatewayName = $(this).val();
                    var url = '{{ url('reservation/checkout/gateway-payment') }}' + "/" + gatewayName + "/" + reservationId;
                    $("#gatewayPayment").empty();
                    $("#gatewayPayment").load(url);
                }
            });
        });
    </script>

@endpush
