@extends('layouts.public')


@section('title',$title)

@section('before_content')

@endsection

@section('content')
    @if(user()->hasRole(['member']))
        @include('views.partials.appointment_invoices')
    @elseif(user()->hasRole(['doctor']))
        @include('views.doctor_dashboard')
    @endif
@endsection
