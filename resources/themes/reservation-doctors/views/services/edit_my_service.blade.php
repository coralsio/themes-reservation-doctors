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
                {!! CoralsForm::openForm($service,['url'=>url("doctor/my-service/$service->hashed_id"),'method'=>'PUT']) !!}
                <div class="row">
                    <div class="col-md-4">
                        {!! CoralsForm::text('name','Reservation::attributes.service.name',true) !!}
                        {!! CoralsForm::radio('status','Corals::attributes.status', true, trans('Corals::attributes.status_options')) !!}
                        {!! CoralsForm::checkbox('has_time_slot', 'Reservation::attributes.service.has_time_slot', $service->slot_in_minutes, 1, ['id' => 'has_time_slot']) !!}
                        {!! CoralsForm::checkbox('properties[use_entity_models]', 'Reservation::labels.reservation.use_entity_models',optional($service)->getProperty('use_entity_models') ,true,['id'=>'use_entity_models'] ) !!}
                    </div>
                    <div class="col-md-4">
                        {!! CoralsForm::select('object_type', 'Reservation::attributes.service.object_type',
                                \ReservationFacade::getServiceObjectTypesList($service->object_type == getMorphAlias(\Corals\Modules\Entity\Models\Entity::class)),
                                false, $service->getObjectType(), ['help_text'=>'Reservation::attributes.service.object_type_help'],'select2') !!}

                        {!! CoralsForm::hidden('properties[entity_id]',null,['id'=>'hidden_entity_id']) !!}

                        {!! CoralsForm::select('category','Reservation::attributes.service.category', ReservationFacade::getServiceCategories(),true,$service->categories()->first()->id,['multiple'=>false], 'select2') !!}

                        <div class="d-none">

                            @include('Reservation::partials.owner_fields',['object'=>$service,'dependencyData'=>[
                                                'dependency-field' => 'main_line_item',
                                                 'dependency-ajax-url' => url('reservation/services/get-line-items'),
                                                'selected_value'=> $service->exists ? $service->mainLineItem()->first()->id : null,
                                                'dependency-args'=> 'owner_type',
            ]                       ])
                        </div>
                    </div>
                    <div class="col-md-4">
                        {!! CoralsForm::textarea('description','Reservation::attributes.service.description') !!}

                        {!! CoralsForm::text('caption','Reservation::attributes.service.caption',true) !!}

                        <div class="has_time_slot" style="{{ $service->slot_in_minutes?'':'display:none;' }}">
                            {!! CoralsForm::number('slot_in_minutes','Reservation::attributes.service.slot_in_minutes', true, $service->slot_in_minutes,
                            ['help_text'=>'Reservation::attributes.service.slot_in_minutes_help']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {!! CoralsForm::select('main_line_item','Reservation::attributes.service.main_line_item',[], true,$service->exists ?$service->mainLineItem()->first()->id:null,[
                                        'class'=>'dependent-select',
                                       'data'=>[
                                           'dependency-field'=>'optional_line_items',
                                           'dependency-ajax-url'=> url('reservation/services/get-line-items'),
                                           'selected_value'=> $service->exists ? $service->mainLineItem()->first()->id : null,
                                           'dependency-args'=> 'owner_type,owner_id'
                                           ],
                               ], 'select2') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::select('optional_line_items[]','Reservation::attributes.service.optional_line_items', [], false, null,[
                                       'data' => [
                                           'selected_value'=> $service->optionalLineItems  ? json_encode($service->optionalLineItems->pluck('id')->toArray()) :null
                                          ],
                                          'multiple'=>true,
                                        'id'=>'optional_line_items'
                               ],'select2') !!}
                    </div>
                </div>
                <div class="row has_time_slot" style="{{ $service->slot_in_minutes?'':'display:none;' }}">
                    <div class="col-md-12">
                        <h4>@lang('Reservation::attributes.service.schedule')</h4>
                        @include('Reservation::services.partials.schedule',['editable'=>true])
                    </div>
                </div>

                {!! CoralsForm::customFields($service) !!}

                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::formButtons() !!}
                    </div>
                </div>
                {!! CoralsForm::closeForm($service) !!}
            @endcomponent
        </div>
    </div>
@endsection
@section('js')
    @include('Reservation::services.partials.js_create_edit')
@endsection
