@extends('website::layouts.master')

@section('title', 'about - CTAKE')

@section('content')
<div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url(assets/images/breadcrumb_bg.jpg);">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>about Us</h1>
                                <ul>
                                    <li><a href="#">Home </a></li>
                                    <li><a href="#">About us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========ABOUT US PAGE START===========-->
            <section class="about_us_story pt_95 xs_pt_65">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-xl-5 wow fadeInLeft">
                            <div class="about_us_story_text">
                                <h2>We invite you to visit our restaurant</h2>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                    laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                                    architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia
                                    voluptas sit
                                    aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione
                                    voluptatem sequi nesciunt</p>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                    laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                                    architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia
                                    voluptas sit
                                    aspernatur aut odit aut fugit</p>
                                <a class="common_btn" href="menu_03.html">
                                    <span class="icon">
                                        <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                    </span>
                                    View All Menu
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="about_us_story_img">
                                <div class="row">
                                    <div class="col-lg-8 col-sm-6 wow fadeInLeft">
                                        <div class="about_us_story_img_large">
                                            <img src="{{ asset('website/images/about_story_img_1.jpg') }}" alt="story"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 wow fadeInRight">
                                        <div class="about_us_story_img_small">
                                            <img src="{{ asset('website/images/about_story_img_3.jpg') }}" alt="story"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 wow fadeInLeft">
                                        <div class="about_us_story_img_small">
                                            <img src="{{ asset('website/images/about_story_img_4.jpg') }}" alt="story"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-sm-6 wow fadeInRight">
                                        <div class="about_us_story_img_large">
                                            <img src="{{ asset('website/images/about_story_img_2.jpg') }}" alt="story"
                                                class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="about_showsase pt_95 xs_pt_65">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 wow fadeInLeft">
                        <div class="about_showsase_img_large">
                            <img src="{{ asset('website/images/showcase_img_1.jpg') }}" alt="showcase" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 wow fadeInUp">
                        <div class="about_showsase_img_small">
                            <img src="{{ asset('website/images/showcase_img_2.jpg') }}" alt="showcase" class="img-fluid w-100">
                        </div>
                        <div class="about_showsase_img_small">
                            <img src="{{ asset('website/images/showcase_img_3.jpg') }}" alt="showcase" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 wow fadeInRight">
                        <div class="about_showsase_img_large">
                            <img src="{{ asset('website/images/showcase_img_4.jpg') }}" alt="showcase" class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </section>

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

            <section class="testimonial mt_120 xs_mt_100" style="background: url(assets/images/testimonial_bg.jpg);">
                <div class="testimonial_overlay pt_250 xs_pt_100">
                    <div class="container mt_20">
                        <div class="row wow fadeInUp">
                            <div class="col-md-9">
                                <div class="testimonial_content">
                                    <div class="row testi_slider">
                                        <div class="col-12">
                                            <div class="single_testimonial">
                                                <p class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </p>
                                                <p class="description">"I love that solvency lets us manage everything
                                                    in one
                                                    place. It's super helpful to be able to listen to voice samples,
                                                    upload our
                                                    own lists, and find quality salespeople that can grow with our
                                                    team."</p>
                                                <div class="single_testimonial_footer">
                                                    <div class="img">
                                                        <img src="{{ asset('website/images/client_img_1.png') }}" alt="clien"
                                                            class="img-fluis w-100">
                                                    </div>
                                                    <h3>Indigo Violet <span>Co - Founder</span></h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="single_testimonial">
                                                <p class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </p>
                                                <p class="description">"I love that solvency lets us manage everything
                                                    in one
                                                    place. It's super helpful to be able to listen to voice samples,
                                                    upload our
                                                    own lists, and find quality salespeople that can grow with our
                                                    team."</p>
                                                <div class="single_testimonial_footer">
                                                    <div class="img">
                                                        <img src="{{ asset('website/images/client_img_2.png') }}" alt="client"
                                                            class="img-fluis w-100">
                                                    </div>
                                                    <h3>jihan ahmed <span>Co - Founder</span></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="testimonial_video">
                                    <a class="venobox play_btn" data-autoplay="true" data-vbtype="video"
                                        href="https://youtu.be/nqye02H_H6I?si=ougeOsfL0tat6YbT">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="counter_area">
                <div class="counter_bg pt_30 pb_35">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 wow fadeInUp">
                                <div class="single_counter">
                                    <h2 class="counter">45</h2>
                                    <span>Dishes</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 wow fadeInUp">
                                <div class="single_counter">
                                    <h2 class="counter">68</h2>
                                    <span>Location</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 wow fadeInUp">
                                <div class="single_counter">
                                    <h2 class="counter">32</h2>
                                    <span>Chefs</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 wow fadeInUp">
                                <div class="single_counter">
                                    <h2 class="counter">120</h2>
                                    <span>Cities</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="shefs pt_110 xs_pt_90">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 m-auto wow fadeInUp">
                            <div class="section_heading mb_25">
                                <h2>Meet Our special Chefs </h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                            <div class="single_chef">
                                <a href="chefs_details.html" class="single_chef_img">
                                    <img src="{{ asset('website/images/chef_img_1.jpg') }}" alt="Chef" class="img-fluid w-100">
                                    <span>Main Chef</span>
                                </a>
                                <div class="single_chef_text">
                                    <a class="title" href="chefs_details.html">Nathaneal Down</a>
                                    <ul>
                                        <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                            <div class="single_chef">
                                <a href="chefs_details.html" class="single_chef_img">
                                    <img src="{{ asset('website/images/chef_img_2.jpg') }}" alt="Chef" class="img-fluid w-100">
                                    <span>Executive Chef</span>
                                </a>
                                <div class="single_chef_text">
                                    <a class="title" href="chefs_details.html">Pelican Steve</a>
                                    <ul>
                                        <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                            <div class="single_chef">
                                <a href="chefs_details.html" class="single_chef_img">
                                    <img src="{{ asset('website/images/chef_img_3.jpg') }}" alt="Chef" class="img-fluid w-100">
                                    <span>Master Chef</span>
                                </a>
                                <div class="single_chef_text">
                                    <a class="title" href="chefs_details.html">Dylan Meringue</a>
                                    <ul>
                                        <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                            <div class="single_chef">
                                <a href="chefs_details.html" class="single_chef_img">
                                    <img src="{{ asset('website/images/chef_img_4.jpg') }}" alt="Chef" class="img-fluid w-100">
                                    <span>Executive Chef</span>
                                </a>
                                <div class="single_chef_text">
                                    <a class="title" href="chefs_details.html">Fergus Douchebag</a>
                                    <ul>
                                        <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 about_2_shefs text-center mt_60 wow fadeInUp">
                            <a class="common_btn" href="{{ route('website.chefs') }}">
                                <span class="icon">
                                    <img src="{{ asset('website/images/eye.png') }}" alt="order" class="img-fluid w-100">
                                </span>
                                View All Chef
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="blog pt_100 xs_pt_80">
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
                    </div>
                </div>
            </section>
            <!--==========ABOUT US PAGE END===========-->
@endsection
