@extends('website::layouts.master')

@php
    // Get all homepage sections
    $sections = site_sections('home');

    // Individual sections
    $hero = $sections->get('hero_banner');
    $popularCategories = $sections->get('popular_categories');
    $adLarge = $sections->get('advertisement_large');
    $adSmall = $sections->get('advertisement_small');
    $featuredMenu = $sections->get('featured_menu');
    $specialOffer = $sections->get('special_offer');
    $appDownload = $sections->get('app_download');
    $ourChefs = $sections->get('our_chefs');
    $testimonialSection = $sections->get('testimonials');
    $counterSection = $sections->get('counters');
    $blogsSection = $sections->get('latest_blogs');

    // Get dynamic data
    $testimonials = cms_testimonials();
    $counters = cms_counters();
    $menuCategories = \Modules\Menu\app\Models\MenuCategory::active()->ordered()->take($popularCategories->quantity ?? 6)->get();
@endphp

@section('title', cms_setting('site_name', 'Sky Beach - Food & Restaurant'))

@section('content')
    @if(!$hero || $hero->section_status)
    <!--==========BANNER START===========-->
    <section class="banner" style="background: url({{ $hero && $hero->background_image ? asset($hero->background_image) : asset('website/images/banner_bg.jpg') }});">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-xxl-6 col-lg-6 col-xl-6 col-md-9">
                    <div class="banner_text">
                        <h5 class="wow fadeInRightBig" data-wow-duration="1.5s">{{ $hero->subtitle ?? 'Delicious Food' }}</h5>
                        <h1 class="wow fadeInLeftBig" data-wow-duration="1.5s">{{ $hero->title ?? 'Special Foods for your Eating' }}</h1>
                        <p class="wow fadeInRightBig" data-wow-duration="1.5s">{{ $hero->description ?? 'Experience the finest culinary delights crafted with passion and served with love.' }}</p>
                        <a class="common_btn wow fadeInUpBig" href="{{ $hero->button_link ?? route('website.menu') }}">{{ $hero->button_text ?? 'Order Now' }}</a>
                    </div>
                </div>
                <div class="col-xxl-5 col-lg-6 col-xl-6">
                    <div class="banner_img">
                        <div class="img wow fadeInUp">
                            <img src="{{ $hero && $hero->image ? asset($hero->image) : asset('website/images/banner_img.png') }}" alt="banner" class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========BANNER END===========-->
    @endif


    @if(!$popularCategories || $popularCategories->section_status)
    <!--==========CATEGORY START===========-->
    <section class="category pt_130 xs_pt_90">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 wow fadeInUp">
                    <div class="section_heading heading_left mb_50">
                        <h2 class="wow bounceIn">{{ $popularCategories->title ?? 'Our Popular Category' }}</h2>
                    </div>
                </div>
            </div>
            <div class="row category_slider">
                @forelse($menuCategories as $category)
                <div class="col-xl-3 wow fadeInUp">
                    <a href="{{ route('website.menu', ['category' => $category->slug]) }}" class="category_item">
                        <img src="{{ $category->image ? asset($category->image) : asset('website/images/category_img_1.jpg') }}" alt="{{ $category->name }}" class="img-fluid w-100">
                        <h3>{{ $category->name }}</h3>
                    </a>
                </div>
                @empty
                <div class="col-xl-3 wow fadeInUp">
                    <a href="{{ route('website.menu') }}" class="category_item">
                        <img src="{{ asset('website/images/category_img_1.jpg') }}" alt="category" class="img-fluid w-100">
                        <h3>Menu</h3>
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    <!--==========CATEGORY END===========-->
    @endif


    @if((!$adLarge || $adLarge->section_status) || (!$adSmall || $adSmall->section_status))
    <!--==========ADD BANNER START===========-->
    <section class="add_banner">
        <div class="container">
            <div class="row">
                @if(!$adLarge || $adLarge->section_status)
                <div class="col-xl-8 col-lg-7 wow fadeInLeft">
                    <div class="add_banner_large"
                        style="background: url({{ $adLarge && $adLarge->image ? asset($adLarge->image) : asset('website/images/large_banner_img_1.jpg') }});">
                        <div class="text">
                            <h3>{{ $adLarge->title ?? 'The Best Burger Place in Town' }}</h3>
                            <a href="{{ $adLarge->button_link ?? route('website.menu') }}">{{ $adLarge->button_text ?? 'Order Now' }} <i class="fas fa-chevron-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif
                @if(!$adSmall || $adSmall->section_status)
                <div class="col-xl-4 col-lg-5 wow fadeInRight">
                    <div class="add_banner_small"
                        style="background: url({{ $adSmall && $adSmall->image ? asset($adSmall->image) : asset('website/images/small_banner_img_1.jpg') }});">
                        <div class="text">
                            <h3>{{ $adSmall->title ?? 'Great Value Mixed Drinks' }}</h3>
                            <a href="{{ $adSmall->button_link ?? route('website.menu') }}">{{ $adSmall->button_text ?? 'Order Now' }} <i class="fas fa-chevron-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    <!--==========ADD BANNER END===========-->
    @endif


    @if(!$featuredMenu || $featuredMenu->section_status)
    @php
        // Get categories with their menu items for the filter
        $menuCategoriesWithItems = \Modules\Menu\app\Models\MenuCategory::active()
            ->ordered()
            ->with(['activeMenuItems' => function($query) {
                $query->where('status', 1)
                    ->where('is_available', 1)
                    ->orderBy('display_order')
                    ->take(8);
            }])
            ->take(6)
            ->get();
    @endphp
    <!--==========MENU ITEM START===========-->
    <section class="menu_item pt_125 xs_pt_85">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 m-auto wow fadeInUp">
                    <div class="section_heading mb_45 xs_mb_50">
                        <h2 class="wow bounceIn">{{ $featuredMenu->title ?? 'Delicious Menu' }}</h2>
                        @if($featuredMenu && $featuredMenu->subtitle)
                        <p>{{ $featuredMenu->subtitle }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($menuCategoriesWithItems->count() > 0)
            <div id="schedule">
                <div class="colorful-tab-wrapper" id="filter_area">
                    <div class="row mb_15 wow fadeInUp">
                        <div class="col-xxl-10 col-lg-11 m-auto">
                            <ul class="filter_btn_area">
                                @foreach($menuCategoriesWithItems as $index => $cat)
                                    <li class="{{ $index === 0 ? 'active' : '' }}"><a href="#category_{{ $cat->id }}">{{ strtoupper($cat->name) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @foreach($menuCategoriesWithItems as $index => $cat)
                            <div class="colorful-tab-content {{ $index === 0 ? 'active' : '' }}" id="category_{{ $cat->id }}">
                                <div class="row">
                                    @forelse($cat->activeMenuItems as $item)
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
                                                <a class="category" href="{{ route('website.menu', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                                                <a class="title" href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                                <p class="descrption">{{ Str::limit($item->short_description, 40) }}</p>
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <a class="add_to_cart" href="{{ route('website.menu-details', $item->slug) }}">Buy Now</a>
                                                    <h3>{{ currency($item->base_price) }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12 text-center">
                                        <p>{{ __('No items in this category.') }}</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @else
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
                                    <a class="category" href="{{ route('website.menu', ['category' => $item->category->slug]) }}">{{ $item->category->name }}</a>
                                @endif
                                <a class="title" href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                <p class="descrption">{{ Str::limit($item->short_description, 40) }}</p>
                                <div class="d-flex flex-wrap align-items-center">
                                    <a class="add_to_cart" href="{{ route('website.menu-details', $item->slug) }}">Buy Now</a>
                                    <h3>{{ currency($item->base_price) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>No menu items available at the moment.</p>
                    </div>
                @endforelse
            </div>
            @endif

            <div class="row">
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
    @endif


    @if(!$specialOffer || $specialOffer->section_status)
    <!--==========ADD BANNER FULL START===========-->
    <section class="add_banner_full mt_140 xs_mt_100 pt_155 xs_pt_100 pb_155 xs_pb_100"
        style="background: url({{ $specialOffer && $specialOffer->background_image ? asset($specialOffer->background_image) : asset('website/images/add_banner_full_bg.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-md-6">
                    <div class="add_banner_full_text wow fadeInLeft">
                        <h4>{{ $specialOffer->subtitle ?? 'Today Special Offer' }}</h4>
                        <h2 class="wow bounceIn">{{ $specialOffer->title ?? 'Delicious Food with us.' }}</h2>
                        <a class="common_btn" href="{{ $specialOffer->button_link ?? route('website.menu') }}">
                            <span class="icon">
                                <img src="{{ asset('website/images/cart_icon_1.png') }}" alt="order" class="img-fluid w-100">
                            </span>
                            {{ $specialOffer->button_text ?? 'Order Now' }}
                        </a>
                        <div class="img">
                            <img src="{{ $specialOffer && $specialOffer->image ? asset($specialOffer->image) : asset('website/images/add_banner_full_img.png') }}" alt="add banner"
                                class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========ADD BANNER FULL END===========-->
    @endif


    @if(!$appDownload || $appDownload->section_status)
    <!--==========APP DOWNLOAD START===========-->
    <section class="app_download pt_120 xs_pt_100">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-xxl-5 col-md-6 col-lg-5 wow fadeInLeft">
                    <div class="app_download_img">
                        <img src="{{ $appDownload && $appDownload->image ? asset($appDownload->image) : asset('website/images/download_img.png') }}" alt="download" class="img-fluid w-100">
                    </div>
                </div>
                <div class="col-xxl-5 col-md-6 col-lg-6 wow fadeInRight">
                    <div class="app_download_text">
                        <h2 class="wow bounceIn">{{ $appDownload->title ?? 'Are you Ready to Start your Order?' }}</h2>
                        <p>{{ $appDownload->description ?? 'Download our app and enjoy exclusive offers, easy ordering, and fast delivery.' }}</p>
                        <ul class="d-flex flex-wrap">
                            <li>
                                <a class="common_btn" href="tel:{{ cms_contact('phone') ?? '+990123456789' }}">
                                    <i class="fas fa-phone-alt me-2"></i>
                                    {{ __('Call Us') }}
                                </a>
                            </li>
                            <li>
                                <a class="common_btn" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', cms_contact('whatsapp') ?? cms_contact('phone') ?? '990123456789') }}" target="_blank">
                                    <i class="fab fa-whatsapp me-2" style="font-size: 20px;"></i>
                                    {{ __('WhatsApp') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========APP DOWNLOAD END===========-->
    @endif


    @if(!$ourChefs || $ourChefs->section_status)
    <!--==========CHEFS START===========-->
    <section class="shefs pt_125 xs_pt_90">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 m-auto wow fadeInUp">
                    <div class="section_heading mb_25">
                        <h2 class="wow bounceIn">{{ $ourChefs->title ?? 'Meet Our Special Chefs' }}</h2>
                        @if($ourChefs && $ourChefs->subtitle)
                        <p>{{ $ourChefs->subtitle }}</p>
                        @endif
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
    @endif


    @if(!$testimonialSection || $testimonialSection->section_status)
    <!--==========TESTIMONIAL START===========-->
    <section class="testimonial mt_140 xs_mt_100" style="background: url({{ $testimonialSection && $testimonialSection->background_image ? asset($testimonialSection->background_image) : asset('website/images/testimonial_bg.jpg') }});">
        <div class="testimonial_overlay pt_250 xs_pt_100">
            <div class="container mt_20">
                <div class="row">
                    <div class="col-md-9 wow fadeInUp">
                        <div class="testimonial_content">
                            <div class="row testi_slider">
                                @forelse($testimonials as $testimonial)
                                <div class="col-12">
                                    <div class="single_testimonial">
                                        <p class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= ($testimonial->rating ?? 5) ? '' : '-half-alt' }}"></i>
                                            @endfor
                                        </p>
                                        <p class="description">"{{ $testimonial->content }}"</p>
                                        <div class="single_testimonial_footer">
                                            <div class="img">
                                                <img src="{{ $testimonial->image ? asset($testimonial->image) : asset('website/images/client_img_1.png') }}" alt="{{ $testimonial->name }}" class="img-fluid w-100">
                                            </div>
                                            <h3>{{ $testimonial->name }} <span>{{ $testimonial->designation }}</span></h3>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="single_testimonial">
                                        <p class="rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </p>
                                        <p class="description">"{{ __('Great food and excellent service! Highly recommended.') }}"</p>
                                        <div class="single_testimonial_footer">
                                            <div class="img">
                                                <img src="{{ asset('website/images/client_img_1.png') }}" alt="{{ __('Customer') }}" class="img-fluid w-100">
                                            </div>
                                            <h3>{{ __('Happy Customer') }} <span>{{ __('Guest') }}</span></h3>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="testimonial_video">
                            @php
                                $videoUrl = $testimonialSection->video ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                                // Convert YouTube URL to embed format
                                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                                    $videoUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1';
                                }
                            @endphp
                            <a class="venobox play_btn" data-autoplay="true" data-vbtype="iframe"
                                href="{{ $videoUrl }}">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========TESTIMONIAL END===========-->
    @endif


    @if(!$counterSection || $counterSection->section_status)
    <!--==========COUNTER START===========-->
    <section class="counter_area">
        <div class="counter_bg pt_30 pb_35">
            <div class="container">
                <div class="row">
                    @forelse($counters as $counter)
                    <div class="col-lg-3 col-sm-6 wow fadeInUp">
                        <div class="single_counter">
                            <h2 class="counter">{{ $counter->value }}</h2>
                            <span>{{ $counter->label }}</span>
                        </div>
                    </div>
                    @empty
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
                    @endforelse
                </div>
            </div>
        </div>
    </section>
    <!--==========COUNTER END===========-->
    @endif


    @if(!$blogsSection || $blogsSection->section_status)
    <!--==========BLOG START===========-->
    <section class="blog pt_110 xs_pt_90">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 m-auto wow fadeInUp">
                    <div class="section_heading mb_25">
                        <h2 class="wow bounceIn">{{ $blogsSection->title ?? 'Our Latest News & Article' }}</h2>
                        @if($blogsSection && $blogsSection->subtitle)
                        <p>{{ $blogsSection->subtitle }}</p>
                        @endif
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
    <!--==========BLOG END===========-->
    @endif
@endsection
