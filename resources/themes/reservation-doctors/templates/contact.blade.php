@extends('layouts.master')

@section('content')
    <div class="contact-us-info">
        <div class="container">
            <!--Google Maps-->
            <div class="row">
                <div class="col-md-12">
                    <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                            width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! $item->rendered !!}
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 message">
                    <form id="main-contact-form" class="saas-contact ajax-form" name="contact-form" method="post"
                          data-page_action="clearContactForm"
                          action="{{ url('contact/email') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="text" name="name" class="form-control"
                                   placeholder="Name *">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control"
                                   placeholder="Email *">
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone" class="form-control" placeholder="Phone">
                        </div>
                        <div class="form-group">
                            <input type="text" name="company" class="form-control" placeholder="Company Name">
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control"
                                   placeholder="Subject *">
                        </div>
                        <div class="form-group">
                                <textarea name="message" id="message" class="form-control"
                                          rows="6" placeholder="Message *"></textarea>
                        </div>
                        <div class="form-group">

                            {!! NoCaptcha::display() !!}

                        </div>
                        <div class="form-group text-right">
                            <button type="submit" name="submit"
                                    class="btn btn-primary btn-rounded">@lang('reservation-doctors::labels.template.contact.submit_message')

                            </button>

                        </div>
                    </form>
                </div>
                <div class="col-lg-4 contact">
                    <div class="address">
                        <h3>@lang('reservation-doctors::labels.template.contact.our_address')</h3>
                        <p> {!!  \Settings::get('contact_address','Al-Masyoun, Ramallah, Palestine.')  !!}</p>
                    </div>
                    <div class="phone">
                        <h3> @lang('reservation-doctors::labels.template.contact.phone')</h3>
                        <p>
                            {{ \Settings::get('contact_mobile','+970599593301') }}
                        </p>
                    </div>
                    <div class="online-support">
                        <strong>@lang('reservation-doctors::labels.template.contact.looking_for_online')</strong>
                        <p>
                            @lang('reservation-doctors::labels.template.contact.talk_now_online_chat')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')

    {!! NoCaptcha::renderJs() !!}

@endsection