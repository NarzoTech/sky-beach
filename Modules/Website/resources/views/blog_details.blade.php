@extends('website::layouts.master')

@section('title', $blog->title . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Blog Details') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.blogs') }}">{{ __('Blogs') }}</a></li>
                                <li><a href="#">{{ Str::limit($blog->title, 30) }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========BLOG DETAILS START===========-->
        <section class="blog_details mt_120 xs_mt_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 wow fadeInLeft">
                        <div class="blog_details_img">
                            @if($blog->image)
                                <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="img-fluid w-100">
                            @else
                                <img src="{{ asset('website/images/blog_details_img.jpg') }}" alt="{{ $blog->title }}" class="img-fluid w-100">
                            @endif
                        </div>
                        <div class="blog_details_header">
                            <ul class="left_info">
                                @if($blog->tags)
                                    <li><span>{{ trim(explode(',', $blog->tags)[0]) }}</span></li>
                                @endif
                                <li><i class="far fa-user-circle"></i> {{ $blog->author ?? __('Admin') }}</li>
                                <li><i class="far fa-calendar-alt"></i> {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}</li>
                            </ul>
                            <ul class="right_info">
                                <li><i class="far fa-eye"></i> {{ $blog->views ?? 0 }}</li>
                            </ul>
                        </div>
                        <div class="blog_details_text">
                            <h2>{{ $blog->title }}</h2>
                            {!! $blog->content !!}
                        </div>

                        @if($blog->tags)
                        <div class="details_tags_share">
                            <ul>
                                <li><span>{{ __('Tags') }}:</span></li>
                                @foreach(explode(',', $blog->tags) as $tag)
                                    <li><a href="#">{{ trim($tag) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="input_comment_area review_input_area mt_80">
                            <h2>{{ __('Leave A Comment') }}</h2>
                            <span>{{ __('Share your thoughts about this article.') }}</span>
                            <form action="#" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="review_input_box">
                                            <input type="text" name="name" placeholder="{{ __('Name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="review_input_box">
                                            <input type="email" name="email" placeholder="{{ __('Your Email') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="review_input_box">
                                            <textarea rows="5" name="comment" placeholder="{{ __('Type your comment') }}" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="common_btn">{{ __('Submit Comment') }}</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-8 wow fadeInRight">
                        <div class="blog_sidebar">
                            <div class="sidebar_wizard sidebar_search">
                                <h2>{{ __('Search') }}</h2>
                                <form action="{{ route('website.blogs') }}" method="GET">
                                    <input type="text" name="search" placeholder="{{ __('Search blogs...') }}" value="{{ request('search') }}">
                                    <button type="submit"><i class="far fa-search"></i></button>
                                </form>
                            </div>

                            @if($relatedBlogs->count() > 0)
                            <div class="sidebar_wizard sidebar_post mt_25">
                                <h2>{{ __('Related Posts') }}</h2>
                                <ul>
                                    @foreach($relatedBlogs as $relatedBlog)
                                    <li>
                                        <div class="img">
                                            @if($relatedBlog->image)
                                                <img src="{{ asset($relatedBlog->image) }}" alt="{{ $relatedBlog->title }}" class="img-fluid w-100">
                                            @else
                                                <img src="{{ asset('website/images/sidebar_post_img_1.jpg') }}" alt="{{ $relatedBlog->title }}" class="img-fluid w-100">
                                            @endif
                                        </div>
                                        <div class="text">
                                            <p><i class="far fa-calendar-alt"></i> {{ $relatedBlog->published_at ? $relatedBlog->published_at->format('M d, Y') : $relatedBlog->created_at->format('M d, Y') }}</p>
                                            <a class="title" href="{{ route('website.blog-details', $relatedBlog->slug) }}">{{ Str::limit($relatedBlog->title, 40) }}</a>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($blog->tags)
                            <div class="sidebar_wizard sidebar_tags mt_25">
                                <h2>{{ __('Tags') }}</h2>
                                <ul>
                                    @foreach(explode(',', $blog->tags) as $tag)
                                        <li><a href="#">{{ trim($tag) }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <div class="sidebar_banner menu_details_banner mt_25">
                                <img src="{{ asset('website/images/details_banner_img.png') }}" alt="{{ __('offer') }}" class="img-fluid w-100">
                                <div class="text">
                                    <h5>{{ __('Get Up to 50% Off') }}</h5>
                                    <h3>{{ __('Special Combo Pack') }}</h3>
                                    <a href="{{ route('website.menu') }}">
                                        <span><img src="{{ asset('website/images/cart_icon_2.png') }}" alt="{{ __('cart') }}" class="img-fluid w-100"></span>
                                        {{ __('Shop Now') }}
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BLOG DETAILS END===========-->
@endsection
