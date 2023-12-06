@extends('layouts.master')


@section('before_content')

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Blog</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">@lang('reservation-doctors::labels.blog.blog_details')</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->
@stop
@section('content')

    <!-- Page Content -->
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="blog-view">
                        <div class="blog blog-single-post">
                            <div class="blog-image">
                                <a href="javascript:void(0);"><img alt="" src="{{$item->featured_image}}"
                                                                   class="img-fluid"></a>
                            </div>
                            <h3 class="blog-title">{{$item->title}}</h3>
                            <div class="blog-info clearfix">
                                <div class="post-left">
                                    <ul>
                                        <li>
                                            <div class="post-author">
                                                <a href="{{ $item->author->getShowURL() }}"><img
                                                            src="{{ $item->author->picture }}"
                                                            alt="Post Author"> <span>{{$item->author->full_name}}</span></a>
                                            </div>
                                        </li>
                                        <li><i class="far fa-calendar"></i>{{$item->present('published_at')}}</li>
                                        <li><i class="fa fa-tags"></i>{!!  $item->presentStripTags('categories') !!}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="blog-content">
                                {!! $item->rendered !!}
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Blog Sidebar -->
                <div class="col-lg-4 col-md-12 sidebar-right theiaStickySidebar">

                @include('partials.search')

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
                    <!-- /Latest Posts -->

                    <!-- Categories -->
                    <div class="card category-widget">
                        <div class="card-header">
                            <h4 class="card-title">@lang('reservation-doctors::labels.post.blog_categories')</h4>
                        </div>
                        <div class="card-body">
                            <ul class="categories">
                                @foreach($item->post->activeCategories as $category)
                                    <li><a href="{{url('category/'.$category->slug)}}">{{$category->name}}
                                            <span>({{$category->count()}})</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!-- /Categories -->

                    <!-- Tags -->

                    @if($item->post->activeTags()->count())
                        <div class="card tags-widget">
                            <div class="card-header">
                                <h4 class="card-title">@lang('reservation-doctors::labels.post.tags')</h4>
                            </div>
                            <div class="card-body">
                                <ul class="tags">
                                    @foreach($item->post->activeTags as $tag)
                                        <li><a href="{{ url('tag/'.$tag->slug) }}" class="tag">{{$tag->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                @endif
                <!-- /Tags -->

                </div>
                <!-- /Blog Sidebar -->

            </div>
        </div>

    </div>    @stop