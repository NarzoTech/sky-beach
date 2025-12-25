@extends('website::layouts.master')

@section('title', 'blogs - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>blogs</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">blogs</a></li>
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
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_img_1.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Burger</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">WHAT IS THE DIFFERENCE BETWEEN
                                        HAMBURGERS & BURGERS?</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_img_2.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Snacks</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">PAIRING WINE WITH INDIAN FOOD:
                                        TIPS FROM A SOMMELIER</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_img_3.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Taste</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">THE WONDERS OF THAI CUISINE
                                        SWEET, SALTY & SOUR</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_2_img_4.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Burger</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">WHAT IS THE DIFFERENCE BETWEEN
                                        HAMBURGERS & BURGERS?</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_2_img_5.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Snacks</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">PAIRING WINE WITH INDIAN FOOD:
                                        TIPS FROM A SOMMELIER</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_2_img_6.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Taste</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">THE WONDERS OF THAI CUISINE
                                        SWEET, SALTY & SOUR</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_2_img_7.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Burger</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">WHAT IS THE DIFFERENCE BETWEEN
                                        HAMBURGERS & BURGERS?</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_2_img_8.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Snacks</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">PAIRING WINE WITH INDIAN FOOD:
                                        TIPS FROM A SOMMELIER</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ asset('website/images/blog_2_img_9.jpg') }}" alt="blog" class="img-fluid w-100">
                                    <a class="category" href="#">Taste</a>
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            April 18, 2026
                                        </li>
                                        <li>BY Admin</li>
                                    </ul>
                                    <a class="title" href="{{ route('website.blog-details') }}">THE WONDERS OF THAI CUISINE
                                        SWEET, SALTY & SOUR</a>
                                    <a class="read_btn" href="{{ route('website.blog-details') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pagination_area mt_60 wow fadeInUp">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <i class="far fa-arrow-left"></i>
                                    </a>
                                </li>
                                <li class="page-item"><a class="page-link active" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </section>
            <!--==========BLOGS END===========-->
@endsection
