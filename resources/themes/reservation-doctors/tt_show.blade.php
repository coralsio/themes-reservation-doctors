@extends('layouts.master')

@section('content')
    <div class="py-5">
        @include('TroubleTicket::troubleTickets.public.partial_show', ['troubleTicket'=>$troubleTicket])
    </div>
@endsection
