@extends('website::layouts.master')

@section('title', 'reservation - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>Reservations</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">Reservations</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========RESERVATION PAGE START===========-->
            <section class="reservation_page pt_120 xs_pt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 wow fadeInLeft">
                            <div class="reservation_img">
                                <img src="{{ asset('website/images/reservation_img_2.jpg') }}" alt="reservation"
                                    class="img-fluid w-100">
                            </div>
                        </div>
                        <div class="col-lg-6 wow fadeInRight">
                            <div class="reservation_form">
                                <h2>ONLINE RESERVATION</h2>
                                <form action="#">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="reservation_form_input">
                                                <input type="text" placeholder="Your Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reservation_form_input">
                                                <input type="email" placeholder="Your Email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reservation_form_input">
                                                <input type="text" placeholder="Phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reservation_form_input">
                                                <input type="date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reservation_form_input">
                                                <select class="select_2" name="state">
                                                    <option value="AL">Select Time</option>
                                                    <option value="">08.00 am to 09.00 am</option>
                                                    <option value="">09.00 am to 10.00 am</option>
                                                    <option value="">11.00 am to 12.00 am</option>
                                                    <option value="">02.00 am to 03.00 am</option>
                                                    <option value="">05.00 am to 06.00 am</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reservation_form_input">
                                                <select class="select_2" name="state">
                                                    <option value="AL">Select Person</option>
                                                    <option value="">1 Person</option>
                                                    <option value="">2 Person</option>
                                                    <option value="">3 Person</option>
                                                    <option value="">4 Person</option>
                                                    <option value="">5 Person</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="reservation_form_input">
                                                <textarea rows="7" placeholder="Write Message..."></textarea>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="flexCheckDefault">
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        Select to subscribe to our newsletter and updates and we will
                                                        send you
                                                        all updates about our services.
                                                    </label>
                                                </div>
                                                <button class="common_btn" type="submit">Make A reserve</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========RESERVATION PAGE END===========-->


            <!--==========GALLERY START===========-->
            <section class="gallery mt_120 xs_mt_100">
                <div class="row gallery_slider">
                    <div class="col-xl-3 wow fadeInUp">
                        <div class="gallery_item">
                            <img src="{{ asset('website/images/gallery_img_1.jpg') }}" alt="gallery" class="img-fluid w-100">
                            <div class="text">
                                <a class="title" href="{{ route('website.menu-details') }}">Breakfast Burritos</a>
                                <p>Breakfast item</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 wow fadeInUp">
                        <div class="gallery_item">
                            <img src="{{ asset('website/images/gallery_img_2.jpg') }}" alt="gallery" class="img-fluid w-100">
                            <div class="text">
                                <a class="title" href="{{ route('website.menu-details') }}">Breakfast Burritos</a>
                                <p>Breakfast item</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 wow fadeInUp">
                        <div class="gallery_item">
                            <img src="{{ asset('website/images/gallery_img_3.jpg') }}" alt="gallery" class="img-fluid w-100">
                            <div class="text">
                                <a class="title" href="{{ route('website.menu-details') }}">Breakfast Burritos</a>
                                <p>Breakfast item</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 wow fadeInUp">
                        <div class="gallery_item">
                            <img src="{{ asset('website/images/gallery_img_4.jpg') }}" alt="gallery" class="img-fluid w-100">
                            <div class="text">
                                <a class="title" href="{{ route('website.menu-details') }}">Breakfast Burritos</a>
                                <p>Breakfast item</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 wow fadeInUp">
                        <div class="gallery_item">
                            <img src="{{ asset('website/images/gallery_img_3.jpg') }}" alt="gallery" class="img-fluid w-100">
                            <div class="text">
                                <a class="title" href="{{ route('website.menu-details') }}">Breakfast Burritos</a>
                                <p>Breakfast item</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========GALLERY START===========-->


            <!--==========BLOG START===========-->
            <section class="blog pt_110 xs_pt_90">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 m-auto wow fadeInUp">
                            <div class="section_heading mb_25">
                                <h2>Our Latest News & Article</h2>
                            </div>
                        </div>
                    </div>
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
                                    <a class="title" href="{{ route('website.blog-details', 'sample-blog') }}">WHAT IS THE DIFFERENCE BETWEEN
                                        HAMBURGERS & BURGERS?</a>
                                    <a class="read_btn" href="{{ route('website.blog-details', 'sample-blog') }}">Read More <i
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
                                    <a class="title" href="{{ route('website.blog-details', 'sample-blog') }}">PAIRING WINE WITH INDIAN FOOD:
                                        TIPS FROM A SOMMELIER</a>
                                    <a class="read_btn" href="{{ route('website.blog-details', 'sample-blog') }}">Read More <i
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
                                    <a class="title" href="{{ route('website.blog-details', 'sample-blog') }}">THE WONDERS OF THAI CUISINE
                                        SWEET, SALTY & SOUR</a>
                                    <a class="read_btn" href="{{ route('website.blog-details', 'sample-blog') }}">Read More <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BLOG END===========-->
@endsection
