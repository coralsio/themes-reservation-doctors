@extends('layouts.public')


@section('title',$title)

@section('before_content')
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                        href="{{url('/')}}">@lang('reservation-doctors::labels.doctors.home')</a></li>

                            <li class="breadcrumb-item">
                                <a href="{{url('/doctor/line-items')}}">@lang('reservation-doctors::labels.doctors.line_items')</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>

                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">{{$title}}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @component('components.box',['box_title'=>$title])
                {!! CoralsForm::openForm($lineItem,['url'=>$url,'method'=>$method]) !!}
                <div class="row">
                    <div class="col-md-6">
                        {!! CoralsForm::text('code','Reservation::attributes.line_item.code',true) !!}
                        {!! CoralsForm::text('name','Reservation::attributes.line_item.name',true) !!}
                        {!! CoralsForm::radio('status','Corals::attributes.status', true, trans('Corals::attributes.status_options')) !!}
                        {!! CoralsForm::number('rate_value','Reservation::attributes.line_item.rate_value',true,null,['step'=>'0.1','min'=>0]) !!}
                        {!! CoralsForm::select('rate_type','Reservation::attributes.line_item.rate_type',trans('Reservation::attributes.line_item.rate_type_options'), true,null,[], 'select2') !!}
                    </div>
                    <div class="col-md-6">
                        {!! CoralsForm::number('min_qty','Reservation::attributes.line_item.min_qty',false,$lineItem->min_qty ?? 0,['step'=>'1','min'=>0]) !!}
                        {!! CoralsForm::number('max_qty','Reservation::attributes.line_item.max_qty',false,$lineItem->max_qty ??0,['step'=>'1','min'=>0]) !!}
                        {!! CoralsForm::textarea('description','Reservation::attributes.line_item.description',true) !!}
                    </div>
                </div>

                {!! CoralsForm::customFields($lineItem) !!}

                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::formButtons() !!}
                    </div>
                </div>

                {!! CoralsForm::closeForm($lineItem) !!}
            @endcomponent
        </div>
    </div>
@endsection
