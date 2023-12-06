@extends('layouts.master')


@section('before_content')

    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/')}}">@lang('reservation-doctors::labels.doctors.home')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('reservation-doctors::labels.doctors.blog')</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.blog.blog_grid')</h2>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12">

                    <div class="row blog-grid-row">
                        @forelse($posts as $post)
                            <div class="col-md-6 col-sm-12">

                                <!-- Blog Post -->
                                <div class="blog grid-blog">
                                    <div class="blog-image">
                                        <a href="{{ url($post->slug) }}"><img class="img-fluid"
                                                                              src="{{ $post->featured_image }}"
                                                                              alt=""></a>
                                    </div>
                                    <div class="blog-content">
                                        <ul class="entry-meta meta-item">
                                            <li>
                                                <div class="post-author">
                                                    <a href="{{ $post->author->getShowURL() }}"><img
                                                                src="{{ $post->author->picture }}"
                                                                alt="Post Author">
                                                        <span>{{ $post->author->full_name }}</span></a>
                                                </div>
                                            </li>
                                            <li><i class="far fa-clock"></i> {{ format_date($post->published_at) }}</li>
                                        </ul>
                                        <h3 class="blog-title"><a href="{{ url($post->slug) }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="mb-0"> {{ \Str::limit(strip_tags($post->rendered ),250) }}</p>
                                    </div>
                                </div>
                                <!-- /Blog Post -->
                            </div>
                        @empty
                            <div class="col-md-12">

                                <div class="alert alert-warning text-center">
                                    <h4>
                                        <i class="fa fa-warning"></i>@lang('reservation-doctors::labels.blog.no_posts_found')
                                    </h4>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Blog Pagination -->
                {{ $posts->links('partials.paginator') }}
                <!-- /Blog Pagination -->

                </div>

                <!-- Blog Sidebar -->
                <div class="col-lg-4 col-md-12 sidebar-right theiaStickySidebar">

                    <!-- Search -->
                @include('partials.search')
                <!-- /Search -->

                    <!-- Latest Posts -->
                    <div class="card post-widget">
                        <div class="card-header">
                            <h4 class="card-title">@lang('reservation-doctors::labels.blog.latest_posts')</h4>
                        </div>
                        <div class="card-body">
                            <ul class="latest-posts">
                                @foreach(\CMS::getLatestPosts(5) as $post)
                                    <li>
                                        <div class="post-thumb">
                                            <a href="{{ url($post->slug) }}">
                                                <img class="img-fluid" src="{{$post->featured_image}}" alt="">
                                            </a>
                                        </div>
                                        <div class="post-info">
                                            <h4>
                                                <a href="{{ url($post->slug) }}">{{$post->title}}</a>
                                            </h4>
                                            <p> {{ format_date($post->published_at) }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop