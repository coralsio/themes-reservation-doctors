@extends('layouts.master')


@section('content')

    {!! $pricing->rendered !!}

    <section class="content">
        <section class="block">
            <div class="container">

                <section>
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-6 col-sm-6 col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text">{{$product->description}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        {!!   \Shortcode::compile( 'pricing',$product->id ) !!}
                    </div>
                </section>
            </div>
        </section>
    </section>
@endsection