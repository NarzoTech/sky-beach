@extends('website::layouts.master')

@section('title', 'CTAKE - Food & Restaurant')

@section('content')




    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTAKE - Food & Restaurant HTML Template With RTL</title>
    <link rel="icon" type="image/png" href="{{ asset('website/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('website/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/ranger_slider.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/scroll_button.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/custom_spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/colorfulTab.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('website/css/responsive.css') }}">



    <!--==========MENU START===========-->
    <nav class="navbar navbar-expand-lg main_menu">
        <div class="container">
            <a class="navbar-brand" href="{{ route('website.index') }}">
                <img src="{{ asset('website/images/logo.png') }}" alt="CTAKE" class="img-fluid w-100">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="fas fa-bars bar_icon"></i>
                <i class="far fa-times close_icon"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav m-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('website.index') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('website.menu') }}">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('website.about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pages <i class="fal fa-plus"></i></a>
                        <ul class="droap_menu">
                            <li><a href="{{ route('website.blogs') }}">blogs</a></li>
                            <li><a href="{{ route('website.blog-details', 'sample-blog') }}">blog details</a></li>
                            <li><a href="{{ route('website.chefs') }}">chefs</a></li>
                            <li><a href="{{ route('website.cart-view') }}">cart view</a></li>
                            <li><a href="{{ route('website.checkout') }}">checkout</a></li>
                            <li><a href="{{ route('website.faq') }}">FAQ's</a></li>
                            <li><a href="{{ route('website.reservation.index') }}">reservation</a></li>
                            <li><a href="{{ route('website.service') }}">service</a></li>
                            <li><a href="{{ route('website.service-details', 'sample-service') }}">service details</a></li>
                            <li><a href="{{ route('website.privacy-policy') }}">privacy policy</a></li>
                            <li><a href="{{ route('website.terms-condition') }}">terms & condition</a></li>
                            <li><a href="{{ route('website.error') }}">error/404</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('website.contact') }}">Contact</a>
                    </li>
                </ul>
                <ul class="menu_right">
                    <li>
                        <a class="menu_search"><i class="far fa-search"></i></a>
                    </li>
                    <li>
                        <a class="menu_cart" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                            aria-controls="offcanvasRight"><i class="far fa-shopping-basket"></i> <span
                                class="qnty">15</span></a>
                    </li>
                    <li>
                        <a class="menu_order common_btn" href="{{ route('website.reservation.index') }}">
                            reserve now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="menu_search_area">
        <form>
            <input type="text" placeholder="Search Item...">
            <button class="common_btn" type="submit">Search</button>
            <span class="close_search"><i class="far fa-times"></i></span>
        </form>
    </div>

    <div class="mini_cart">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasRightLabel"> my cart <span>(05)</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"><i
                        class="far fa-times"></i></button>
            </div>
            <div class="offcanvas-body">
                <ul>
                    <li>
                        <div class="cart_img">
                            <img src="{{ asset('website/images/cart_img_1.png') }}" alt="product" class="img-fluid w-100">
                            <a class="del_icon" href="#"><i class="fas fa-minus-circle"></i></a>
                        </div>
                        <div class="cart_text">
                            <a class="cart_title" href="#">Lemon Meat Bone</a>
                            <p>$140 <del>$150</del></p>
                        </div>
                    </li>
                    <li>
                        <div class="cart_img">
                            <img src="{{ asset('website/images/cart_img_2.png') }}" alt="product" class="img-fluid w-100">
                            <a class="del_icon" href="#"><i class="fas fa-minus-circle"></i></a>
                        </div>
                        <div class="cart_text">
                            <a class="cart_title" href="#">Three Carrot Vegetables</a>
                            <p>$130 <del>$160</del></p>
                        </div>
                    </li>
                    <li>
                        <div class="cart_img">
                            <img src="{{ asset('website/images/cart_img_3.png') }}" alt="product" class="img-fluid w-100">
                            <a class="del_icon" href="#"><i class="fas fa-minus-circle"></i></a>
                        </div>
                        <div class="cart_text">
                            <a class="cart_title" href="#">Bengal Meat Beef Bone</a>
                            <p>$140 <del>$150</del></p>
                        </div>
                    </li>
                    <li>
                        <div class="cart_img">
                            <img src="{{ asset('website/images/cart_img_4.png') }}" alt="product" class="img-fluid w-100">
                            <a class="del_icon" href="#"><i class="fas fa-minus-circle"></i></a>
                        </div>
                        <div class="cart_text">
                            <a class="cart_title" href="#">Three Carrot Vegetables</a>
                            <p>$140</p>
                        </div>
                    </li>
                    <li>
                        <div class="cart_img">
                            <img src="{{ asset('website/images/cart_img_5.png') }}" alt="product" class="img-fluid w-100">
                            <a class="del_icon" href="#"><i class="fas fa-minus-circle"></i></a>
                        </div>
                        <div class="cart_text">
                            <a class="cart_title" href="#">Orange Slice Mix</a>
                            <p>$140</p>
                        </div>
                    </li>
                </ul>
                <h5>sub total <span>$3540</span></h5>
                <div class="minicart_btn_area">
                    <a class="common_btn" href="{{ route('website.cart-view') }}">view cart<span></span></a>
                </div>
            </div>
        </div>
    </div>
    <!--==========MENU END===========-->


    <div id="smooth-wrapper">
        <div id="smooth-content">

            <!--==========BANNER START===========-->
            <section class="banner" style="background: url(assets/images/banner_bg.jpg);">
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
                                <a class="common_btn wow fadeInUpBig" href="#">order now</a>
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
                            <a href="menu_03.html" class="category_item">
                                <img src="{{ asset('website/images/category_img_1.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Pizza</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="menu_03.html" class="category_item">
                                <img src="{{ asset('website/images/category_img_2.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Dessert</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="menu_03.html" class="category_item">
                                <img src="{{ asset('website/images/category_img_3.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Burger</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="menu_03.html" class="category_item">
                                <img src="{{ asset('website/images/category_img_4.jpg') }}" alt="category" class="img-fluid w-100">
                                <h3>Drinks</h3>
                            </a>
                        </div>
                        <div class="col-xl-3 wow fadeInUp">
                            <a href="menu_03.html" class="category_item">
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
                            @php
                                $menuItem = $item->menuItem ?? $item;
                            @endphp
                            <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                                <div class="single_menu">
                                    <div class="single_menu_img">
                                        @if($menuItem->image)
                                            <img src="{{ asset($menuItem->image) }}" alt="{{ $menuItem->name }}" class="img-fluid w-100">
                                        @else
                                            <img src="{{ asset('website/images/menu_img_1.jpg') }}" alt="{{ $menuItem->name }}" class="img-fluid w-100">
                                        @endif
                                        <ul>
                                            <li><a href="{{ route('website.menu-details', $menuItem->slug) }}"><i class="far fa-eye"></i></a></li>
                                            <li><a href="#" class="favorite-btn" data-item-id="{{ $menuItem->id }}"><i class="far fa-heart"></i></a></li>
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
                                        @if($menuItem->category)
                                            <a class="category" href="{{ route('website.menu', ['category' => $menuItem->category_id]) }}">{{ $menuItem->category->name }}</a>
                                        @endif
                                        <a class="title" href="{{ route('website.menu-details', $menuItem->slug) }}">{{ $menuItem->name }}</a>
                                        <p class="descrption">{{ Str::limit($menuItem->short_description, 40) }}</p>
                                        <div class="d-flex flex-wrap align-items-center">
                                            <a class="add_to_cart" href="{{ route('website.menu-details', $menuItem->slug) }}">buy now</a>
                                            <h3>${{ number_format($menuItem->base_price, 2) }}</h3>
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
                                            Play Story</a>
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
            <section class="testimonial mt_140 xs_mt_100" style="background: url(assets/images/testimonial_bg.jpg);">
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


            <!--==========BLOG END===========-->
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


            <!--==========FOOTER START===========-->
            <footer class="pt_100 mt_120 xs_mt_100">
                <div class="container">
                    <div class="row justify-content-between wow fadeInUp">
                        <div class="col-lg-3 col-md-4">
                            <div class="footer_info">
                                <a class="footer_logo" href="{{ route('website.index') }}">
                                    <img src="{{ asset('website/images/footer_logo.png') }}" alt="CTAKE" class="img-fluid w-100">
                                </a>
                                <p>Cras incident lobotids feudist makes viramas sagittas eu valuta.</p>
                                <ul>
                                    <li><a class="facebook" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a class="twitter" href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a class="linkedin" href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-md-4">
                            <div class="footer_link">
                                <h3>Our Menu</h3>
                                <ul>
                                    <li><a href="#">Breakfast</a></li>
                                    <li><a href="#">Lunch</a></li>
                                    <li><a href="#">Dinner</a></li>
                                    <li><a href="#">Vegetable</a></li>
                                    <li><a href="#">Korean Food</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-md-4">
                            <div class="footer_link">
                                <h3>Resources</h3>
                                <ul>
                                    <li><a href="#">Home</a></li>
                                    <li><a href="#">About</a></li>
                                    <li><a href="#">Contact</a></li>
                                    <li><a href="#">Blog</a></li>
                                    <li><a href="#">Services</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-9">
                            <div class=" footer_post">
                                <h3>Recent Post</h3>
                                <ul>
                                    <li>
                                        <div class="img">
                                            <img src="{{ asset('website/images/footer_post_img_1.jpg') }}" alt="post"
                                                class="img-fluid w-100">
                                        </div>
                                        <div class="text">
                                            <p><i class="far fa-clock"></i> March 24, 2026</p>
                                            <a href="#">THE WONDERS OF THAI CUISINE SWEET, SALTY & SOUR</a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="img">
                                            <img src="{{ asset('website/images/footer_post_img_2.jpg') }}" alt="post"
                                                class="img-fluid w-100">
                                        </div>
                                        <div class="text">
                                            <p><i class="far fa-clock"></i> March 24, 2026</p>
                                            <a href="#">PAIRING WINE WITH INDIAN FOOD: TIPS FROM A SOMMELIER</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer_copyright_area">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="footer_copyright">
                                    <p>Copyright Â© CTAKE 2026. All Rights Reserved</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!--==========FOOTER END===========-->

        </div>
    </div>


    <!--=========SCROLL BUTTON START===========-->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!--=========SCROLL BUTTON END===========-->


    <!--jquery library js-->
    <script src="{{ asset('website/js/jquery-3.7.1.min.js') }}"></script>
    <!--bootstrap js-->
    <script src="{{ asset('website/js/bootstrap.bundle.min.js') }}"></script>
    <!--font-awesome js-->
    <script src="{{ asset('website/js/Font-Awesome.js') }}"></script>
    <!--slick js-->
    <script src="{{ asset('website/js/slick.min.js') }}"></script>
    <!--countup js-->
    <script src="{{ asset('website/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('website/js/jquery.countup.min.js') }}"></script>
    <!--scroll button js-->
    <script src="{{ asset('website/js/scroll_button.js') }}"></script>
    <!--price ranger js-->
    <script src="{{ asset('website/js/ranger_jquery-ui.min.js') }}"></script>
    <script src="{{ asset('website/js/ranger_slider.js') }}"></script>
    <!--select 2 js-->
    <script src="{{ asset('website/js/select2.min.js') }}"></script>
    <!--aos js-->
    <script src="{{ asset('website/js/wow.min.js') }}"></script>
    <!--colorfulTab js-->
    <script src="{{ asset('website/js/colorfulTab.min.js') }}"></script>
    <!--GSAP js-->
    <script src="{{ asset('website/js/gsap.min.js') }}"></script>
    <script src="{{ asset('website/js/ScrollSmoother.min.js') }}"></script>
    <script src="{{ asset('website/js/ScrollTrigger.min.js') }}"></script>
    <!--script/custom js-->
    <script src="{{ asset('website/js/script.js') }}"></script>




@endsection