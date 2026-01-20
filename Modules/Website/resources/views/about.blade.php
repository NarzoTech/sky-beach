@extends('website::layouts.master')

@section('title', 'About Us - CTAKE')

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>About Us</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.about') }}">About Us</a></li>
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
                            <a class="common_btn" href="{{ route('website.menu') }}">
                                <span class="icon">
                                    <img src="{{ asset('website/images/eye.png') }}" alt="menu" class="img-fluid w-100">
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
                            <form action="{{ route('website.reservation.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="text" name="name" placeholder="Your Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="email" name="email" placeholder="Your Email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="text" name="phone" placeholder="Phone" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="time" required>
                                                <option value="">Select Time</option>
                                                <option value="08:00-09:00">08:00 AM to 09:00 AM</option>
                                                <option value="09:00-10:00">09:00 AM to 10:00 AM</option>
                                                <option value="11:00-12:00">11:00 AM to 12:00 PM</option>
                                                <option value="14:00-15:00">02:00 PM to 03:00 PM</option>
                                                <option value="17:00-18:00">05:00 PM to 06:00 PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="guests" required>
                                                <option value="">Select Person</option>
                                                <option value="1">1 Person</option>
                                                <option value="2">2 Persons</option>
                                                <option value="3">3 Persons</option>
                                                <option value="4">4 Persons</option>
                                                <option value="5">5 Persons</option>
                                                <option value="6">6+ Persons</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="reservation_form_input">
                                            <textarea rows="7" name="message" placeholder="Write Message..."></textarea>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="newsletter" value="1"
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault">
                                                    Subscribe to our newsletter for updates about our services.
                                                </label>
                                            </div>
                                            <button class="common_btn" type="submit">Make A Reservation</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="testimonial mt_120 xs_mt_100" style="background: url({{ asset('website/images/testimonial_bg.jpg') }});">
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
                                                    <img src="{{ asset('website/images/client_img_1.png') }}" alt="client"
                                                        class="img-fluid w-100">
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
                                                        class="img-fluid w-100">
                                                </div>
                                                <h3>Jihan Ahmed <span>Co - Founder</span></h3>
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
                            <h2>Meet Our Special Chefs</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @forelse($chefs as $chef)
                    <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                        <div class="single_chef">
                            <a href="{{ route('website.chefs') }}" class="single_chef_img">
                                @if($chef->image)
                                    <img src="{{ asset($chef->image) }}" alt="{{ $chef->name }}" class="img-fluid w-100">
                                @else
                                    <img src="{{ asset('website/images/chef_img_1.jpg') }}" alt="{{ $chef->name }}" class="img-fluid w-100">
                                @endif
                                <span>{{ $chef->designation }}</span>
                            </a>
                            <div class="single_chef_text">
                                <a class="title" href="{{ route('website.chefs') }}">{{ $chef->name }}</a>
                                <ul>
                                    @if($chef->facebook)
                                        <li><a class="facebook" href="{{ $chef->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    @endif
                                    @if($chef->twitter)
                                        <li><a class="twitter" href="{{ $chef->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    @endif
                                    @if($chef->linkedin)
                                        <li><a class="linkedin" href="{{ $chef->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p>No chefs available at the moment.</p>
                    </div>
                    @endforelse
                    <div class="col-12 about_2_shefs text-center mt_60 wow fadeInUp">
                        <a class="common_btn" href="{{ route('website.chefs') }}">
                            <span class="icon">
                                <img src="{{ asset('website/images/eye.png') }}" alt="view" class="img-fluid w-100">
                            </span>
                            View All Chefs
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
                                <a class="title" href="{{ route('website.blog-details', $blog->slug) }}">{{ Str::upper($blog->title) }}</a>
                                <a class="read_btn" href="{{ route('website.blog-details', $blog->slug) }}">Read More <i class="far fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p>No blogs available at the moment.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
        <!--==========ABOUT US PAGE END===========-->

    </div>
</div>
@endsection
