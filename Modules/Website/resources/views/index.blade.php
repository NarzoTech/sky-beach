@extends('website::layouts.master')

@section('title', 'CTAKE - Food & Restaurant')

@section('content')
            <!--==========BANNER START===========-->
            <section class="banner" style="background: url({{ asset('website/images/banner_bg.jpg') }});">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-xxl-6 col-lg-6 col-xl-6 col-md-9">
                            <div class="banner_text">
                                <h5 class="wow fadeInRightBig" data-wow-duration="1.5s">Delicious
                                    Food
                                </h5>
                                <h1 class="wow fadeInLeftBig" data-wow-duration="1.5s">Special Foods for your Eating
                                </h1>
                                <p class="wow fadeInRightBig" data-wow-duration="1.5s">Commodo ullamcorper a lacus
                                    vestibulum sed arcu non. Non
                                    blandit massa enim
                                    Sem viverra aliquet eget sit amet tellus cras</p>
                                <a class="common_btn wow fadeInUpBig" href="{{ route('website.menu') }}">order now</a>
                            </div>
                        </div>
                        <div class="col-xxl-5 col-lg-6 col-xl-6">
                            <div class="banner_img">
                                <div class="img wow fadeInUp">
                                    <img src="{{ asset('website/images/banner_img.png') }}" alt="banner" class="img-fluid w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BANNER END===========-->


            <!--==========CATEGORY START===========-->
            <section class="category pt_130 xs_pt_90">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 wow fadeInUp">
                            <div class="section_heading heading_left mb_50">
                                <h2 class="wow bounceIn"> Our Popular category</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row category_slider">
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="{{ route('website.menu') }}" class="category_item">
                                <img src="{{ asset('website/images/category_img_1.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Pizza</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="{{ route('website.menu') }}" class="category_item">
                                <img src="{{ asset('website/images/category_img_2.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Dessert</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="{{ route('website.menu') }}" class="category_item">
                                <img src="{{ asset('website/images/category_img_3.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Burger</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="{{ route('website.menu') }}" class="category_item">
                                <img src="{{ asset('website/images/category_img_4.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Drinks</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="{{ route('website.menu') }}" class="category_item">
                                <img src="{{ asset('website/images/category_img_2.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Dessert</h3>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========CATEGORY END===========-->


            <!--==========ADD BANNER START===========-->
            <section class="add_banner">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 col-lg-7 wow fadeInLeft">
                            <div class="add_banner_large"
                                style="background: url({{ asset('website/images/large_banner_img_1.jpg') }});">
                                <div class="text">
                                    <h3>The best Burger place in town</h3>
                                    <a href="{{ route('website.menu') }}"> order now <i class="fas fa-chevron-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 wow fadeInRight">
                            <div class="add_banner_small"
                                style="background: url({{ asset('website/images/small_banner_img_1.jpg') }});">
                                <div class="text">
                                    <h3>Great Value Mixed Drinks</h3>
                                    <a href="{{ route('website.menu') }}"> order now <i class="fas fa-chevron-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========ADD BANNER END===========-->


            <!--==========MENU ITEM START===========-->
            <section class="menu_item pt_125 xs_pt_85">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 m-auto wow fadeInUp">
                            <div class="section_heading mb_45 xs_mb_50">
                                <h2 class="wow bounceIn">Delicious Menu</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @forelse($featuredMenuItems as $item)
                            <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                                <div class="single_menu">
                                    <div class="single_menu_img">
                                        @if($item->image)
                                            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="img-fluid w-100">
                                        @else
                                            <img src="{{ asset('website/images/menu_img_1.jpg') }}" alt="{{ $item->name }}" class="img-fluid w-100">
                                        @endif
                                        <ul>
                                            <li><a href="{{ route('website.menu-details', $item->slug) }}"><i class="far fa-eye"></i></a></li>
                                            <li><a href="#" class="favorite-btn" data-item-id="{{ $item->id }}"><i class="far fa-heart"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="single_menu_text">
                                        <p class="rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </p>
                                        @if($item->category)
                                            <a class="category" href="{{ route('website.menu', ['category' => $item->category_id]) }}">{{ $item->category->name }}</a>
                                        @endif
                                        <a class="title" href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                        <p class="descrption">{{ Str::limit($item->short_description, 40) }}</p>
                                        <div class="d-flex flex-wrap align-items-center">
                                            <a class="add_to_cart" href="{{ route('website.menu-details', $item->slug) }}">buy now</a>
                                            <h3>${{ number_format($item->base_price, 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p>No menu items available at the moment.</p>
                            </div>
                        @endforelse
                        <div class="col-12 text-center mt_60 wow fadeInUp">
                            <a class="common_btn" href="{{ route('website.menu') }}">
                                <span class="icon">
                                    <img src="{{ asset('website/images/eye.png') }}" alt="menu" class="img-fluid w-100">
                                </span>
                                View All Menu
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========MENU ITEM END===========-->


            <!--==========ADD BANNER FULL START===========-->
            <section class="add_banner_full mt_140 xs_mt_100 pt_155 xs_pt_100 pb_155 xs_pb_100"
                style="background: url({{ asset('website/images/add_banner_full_bg.jpg') }});">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-md-6">
                            <div class="add_banner_full_text wow fadeInLeft">
                                <h4>Today special offer</h4>
                                <h2 class="wow bounceIn">Delicious Food with us.</h2>
                                <a class="common_btn" href="{{ route('website.menu') }}">
                                    <span class="icon">
                                        <img src="{{ asset('website/images/cart_icon_1.png') }}" alt="order" class="img-fluid w-100">
                                    </span>
                                    order now
                                </a>
                                <div class="img">
                                    <img src="{{ asset('website/images/add_banner_full_img.png') }}" alt="add banner"
                                        class="img-fluid w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========ADD BANNER FULL END===========-->


            <!--==========APP DOWNLOAD START===========-->
            <section class="app_download pt_120 xs_pt_100">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-xxl-5 col-md-6 col-lg-5 wow fadeInLeft">
                            <div class="app_download_img">
                                <img src="{{ asset('website/images/download_img.png') }}" alt="download" class="img-fluid w-100">
                            </div>
                        </div>
                        <div class="col-xxl-5 col-md-6 col-lg-6 wow fadeInRight">
                            <div class="app_download_text">
                                <h2 class="wow bounceIn">Are you Ready to Start your Order?</h2>
                                <p>Commodo ullamcorper lacus vestibulum sed Non blandit massa enim.</p>
                                <ul class="d-flex flex-wrap">
                                    <li>
                                        <a class="common_btn" href="#">
                                            <span class="icon">
                                                <img src="{{ asset('website/images/apple_icon.png') }}" alt="order"
                                                    class="img-fluid w-100">
                                            </span>
                                            Apple Store</a>
                                    </li>
                                    <li>
                                        <a class="common_btn" href="#">
                                            <span class="icon">
                                                <img src="{{ asset('website/images/play_store_icon.png') }}" alt="order"
                                                    class="img-fluid w-100">
                                            </span>
                                            Play Store</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========APP DOWNLOAD END===========-->


            <!--==========CHEFS START===========-->
            <section class="shefs pt_125 xs_pt_90">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 m-auto wow fadeInUp">
                            <div class="section_heading mb_25">
                                <h2 class="wow bounceIn">Meet Our special Chefs </h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @forelse($featuredChefs as $chef)
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
                                            @if($chef->instagram)
                                                <li><a class="instagram" href="{{ $chef->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
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
                        <div class="col-12 text-center mt_60 wow fadeInUp">
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
            <!--==========CHEFS END===========-->


            <!--==========TESTIMONIAL START===========-->
            <section class="testimonial mt_140 xs_mt_100" style="background: url({{ asset('website/images/testimonial_bg.jpg') }});">
                <div class="testimonial_overlay pt_250 xs_pt_100">
                    <div class="container mt_20">
                        <div class="row">
                            <div class="col-md-9 wow fadeInUp">
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
            <!--==========TESTIMONIAL END===========-->


            <!--==========COUNTER START===========-->
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
            <!--==========COUNTER END===========-->


            <!--==========BLOG START===========-->
            <section class="blog pt_110 xs_pt_90">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 m-auto wow fadeInUp">
                            <div class="section_heading mb_25">
                                <h2 class="wow bounceIn">Our Latest News & Article</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @forelse($recentBlogs as $blog)
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
                                                <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                        class="img-fluid"></span>
                                                {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
                                            </li>
                                            <li>BY {{ $blog->author ?? 'Admin' }}</li>
                                        </ul>
                                        <a class="title" href="{{ route('website.blog-details', $blog->slug) }}">{{ Str::upper($blog->title) }}</a>
                                        <a class="read_btn" href="{{ route('website.blog-details', $blog->slug) }}">Read More <i
                                                class="far fa-arrow-right"></i></a>
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
            <!--==========BLOG END===========-->
@endsection
