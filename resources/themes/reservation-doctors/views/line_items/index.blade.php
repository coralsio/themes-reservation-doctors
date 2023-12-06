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
    @can('create',\Corals\Modules\Reservation\Models\LineItem::class)

        <div class="row">
            <div class="col-md-12">
                <a href="{{url("doctor/line-items/create")}}"
                   class="btn bg-success-light mb-2" style="float: right">
                    <i class="fa fa-plus"></i> @lang('reservation-doctors::labels.doctors.create')
                </a>
            </div>
        </div>
    @endcan

    <div class="card card-table">
        <div class="card-body">

            <!-- Invoice Table -->
            <div class="table-responsive">
                <table class="table table-hover table-center mb-0">
                    <thead>
                    <tr>
                        <th>@lang('Reservation::attributes.line_item.code')</th>
                        <th>@lang('Reservation::attributes.line_item.name')</th>
                        <th>@lang('Reservation::attributes.line_item.rate_value')</th>
                        <th>@lang('Reservation::attributes.line_item.rate_type')</th>
                        <th>@lang('Reservation::attributes.line_item.status')</th>
                        <th>@lang('Corals::labels.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lineItems as $lineItem)
                        <tr>
                            <td>
                                {{$lineItem->code}}
                            </td>

                            <td>{{$lineItem->name}} </td>

                            <td> {{$lineItem->present('rate_value')}} </td>
                            <td> {{$lineItem->present('rate_type')}} </td>
                            <td> {!! $lineItem->present('status')  !!}</td>

                            <td>
                                @can('update',$lineItem)
                                    <a href="{{url("doctor/line-items/$lineItem->hashed_id/edit")}}"
                                       class="btn btn-sm bg-success-light">
                                        <i class="far fa-trash-alt"></i> @lang('reservation-doctors::labels.doctors.edit')
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                @lang('reservation-doctors::labels.doctors.no_results_found')
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /Invoice Table -->

        </div>
    </div>
@endsection
