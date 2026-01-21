@extends('website::layouts.master')

@section('title', 'Blogs - CTAKE')

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>Our Blogs</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.blogs') }}">Blogs</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========BLOGS START===========-->
        <section class="blog_page mt_95 xs_mt_70">
            <div class="container">
                <div class="row">
                    @forelse($blogs as $blog)
                    <div class="col-lg-4 col-sm-6 wow fadeInUp">
                        <div class="single_blog">
                            <div class="single_blog_img">
                                @if($blog->image)
                                    <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}" class="img-fluid w-100">
                                @else
                                    <img src="{{ asset('website/images/blog_img_1.jpg') }}" alt="{{ $blog->title }}" class="img-fluid w-100">
                                @endif
                                @if($blog->tags)
                                    <a class="category" href="#">{{ trim(explode(',', $blog->tags)[0]) }}</a>
                                @else
                                    <a class="category" href="#">Blog</a>
                                @endif
                            </div>
                            <div class="single_blog_text">
                                <ul>
                                    <li>
                                        <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar" class="img-fluid"></span>
                                        {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
                                    </li>
                                    <li>BY {{ $blog->author ?? 'Admin' }}</li>
                                </ul>
                                <a class="title" href="{{ route('website.blog-details', $blog->slug) }}">{{ Str::upper(Str::limit($blog->title, 50)) }}</a>
                                <a class="read_btn" href="{{ route('website.blog-details', $blog->slug) }}">Read More <i class="far fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <h4>No blogs available</h4>
                            <p>Please check back later for our latest articles.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                @if($blogs->hasPages())
                <div class="pagination_area mt_60 wow fadeInUp">
                    <nav aria-label="Page navigation">
                        {{ $blogs->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
                @endif
            </div>
        </section>
        <!--==========BLOGS END===========-->
@endsection
