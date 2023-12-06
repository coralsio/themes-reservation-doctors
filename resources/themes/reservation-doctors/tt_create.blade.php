@extends('layouts.master')

@section('content')
    <div class="py-5">
        @include('TroubleTicket::troubleTickets.public.create', ['troubleTicket'=>$troubleTicket])
    </div>
@endsection
