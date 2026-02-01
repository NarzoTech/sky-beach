@extends('website::layouts.master')

@section('title', __('Catering Services') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Catering Services') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('Catering') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========CATERING INTRO START===========-->
        <section class="catering_intro pt_120 xs_pt_100">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 wow fadeInLeft">
                        <div class="intro_content">
                            <h2>{{ __('Make Your Event Unforgettable') }}</h2>
                            <p class="lead">{{ __('From intimate gatherings to grand celebrations, our catering services bring exceptional cuisine and impeccable service to your special occasions.') }}</p>
                            <ul class="intro_features">
                                <li><i class="fas fa-check-circle"></i> {{ __('Customizable Menus') }}</li>
                                <li><i class="fas fa-check-circle"></i> {{ __('Professional Staff') }}</li>
                                <li><i class="fas fa-check-circle"></i> {{ __('Fresh, Quality Ingredients') }}</li>
                                <li><i class="fas fa-check-circle"></i> {{ __('On-Time Delivery') }}</li>
                            </ul>
                            <a href="{{ route('website.catering.inquiry') }}" class="common_btn mt-4">
                                <i class="fas fa-envelope me-2"></i>{{ __('Request a Quote') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInRight">
                        <div class="intro_image">
                            <img src="{{ asset('website/images/catering_hero.jpg') }}" alt="Catering" class="img-fluid rounded-3">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========CATERING INTRO END===========-->


        @if($featuredPackages->count() > 0)
        <!--==========FEATURED PACKAGES START===========-->
        <section class="featured_packages pt_100 xs_pt_80">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center wow fadeInUp">
                        <div class="section_heading mb_40">
                            <h2>{{ __('Featured Packages') }}</h2>
                            <p>{{ __('Our most popular catering options for your events') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach($featuredPackages as $package)
                        <div class="col-lg-4 col-md-6 wow fadeInUp mb-4">
                            <div class="package_card featured">
                                <div class="package_image">
                                    <img src="{{ $package->image_url }}" alt="{{ $package->name }}" class="img-fluid">
                                    <span class="featured_badge"><i class="fas fa-star"></i> {{ __('Featured') }}</span>
                                </div>
                                <div class="package_content">
                                    <h4>{{ $package->name }}</h4>
                                    <p class="package_desc">{{ Str::limit(strip_tags($package->description), 100) }}</p>
                                    <div class="package_info">
                                        <div class="info_item">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $package->min_guests }}-{{ $package->max_guests }} {{ __('guests') }}</span>
                                        </div>
                                        <div class="info_item">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>${{ number_format($package->price_per_person, 2) }}/{{ __('person') }}</span>
                                        </div>
                                    </div>
                                    <div class="package_actions">
                                        <a href="{{ route('website.catering.show', $package) }}" class="common_btn btn_sm">
                                            {{ __('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!--==========FEATURED PACKAGES END===========-->
        @endif


        <!--==========ALL PACKAGES START===========-->
        <section class="all_packages pt_100 xs_pt_80 pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center wow fadeInUp">
                        <div class="section_heading mb_40">
                            <h2>{{ __('Our Catering Packages') }}</h2>
                            <p>{{ __('Choose a package that fits your event') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @forelse($packages as $package)
                        <div class="col-lg-4 col-md-6 wow fadeInUp mb-4">
                            <div class="package_card">
                                <div class="package_image">
                                    <img src="{{ $package->image_url }}" alt="{{ $package->name }}" class="img-fluid">
                                    @if($package->is_featured)
                                        <span class="featured_badge"><i class="fas fa-star"></i></span>
                                    @endif
                                </div>
                                <div class="package_content">
                                    <h4>{{ $package->name }}</h4>
                                    <p class="package_desc">{{ Str::limit(strip_tags($package->description), 100) }}</p>
                                    <div class="package_info">
                                        <div class="info_item">
                                            <i class="fas fa-users"></i>
                                            <span>{{ $package->min_guests }}-{{ $package->max_guests }} {{ __('guests') }}</span>
                                        </div>
                                        <div class="info_item">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>${{ number_format($package->price_per_person, 2) }}/{{ __('person') }}</span>
                                        </div>
                                    </div>
                                    @if($package->includes && count($package->includes) > 0)
                                        <ul class="package_includes">
                                            @foreach(array_slice($package->includes, 0, 3) as $include)
                                                <li><i class="fas fa-check"></i> {{ $include }}</li>
                                            @endforeach
                                            @if(count($package->includes) > 3)
                                                <li class="text-muted">+ {{ count($package->includes) - 3 }} {{ __('more') }}</li>
                                            @endif
                                        </ul>
                                    @endif
                                    <div class="package_actions">
                                        <a href="{{ route('website.catering.show', $package) }}" class="common_btn btn_sm">
                                            {{ __('View Details') }}
                                        </a>
                                        <a href="{{ route('website.catering.inquiry') }}?package={{ $package->id }}" class="common_btn btn_sm btn_outline">
                                            {{ __('Get Quote') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">{{ __('No catering packages available at the moment.') }}</p>
                            <a href="{{ route('website.catering.inquiry') }}" class="common_btn">
                                {{ __('Submit Custom Inquiry') }}
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
        <!--==========ALL PACKAGES END===========-->


        <!--==========EVENT TYPES START===========-->
        <section class="event_types pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center wow fadeInUp">
                        <div class="section_heading mb_40">
                            <h2>{{ __('Events We Cater') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @foreach(array_slice($eventTypes, 0, 6) as $key => $label)
                        <div class="col-lg-2 col-md-4 col-6 wow fadeInUp mb-4">
                            <div class="event_type_card text-center">
                                <div class="icon">
                                    @switch($key)
                                        @case('wedding')
                                            <i class="fas fa-heart"></i>
                                            @break
                                        @case('corporate')
                                            <i class="fas fa-building"></i>
                                            @break
                                        @case('birthday')
                                            <i class="fas fa-birthday-cake"></i>
                                            @break
                                        @case('anniversary')
                                            <i class="fas fa-glass-cheers"></i>
                                            @break
                                        @case('graduation')
                                            <i class="fas fa-graduation-cap"></i>
                                            @break
                                        @default
                                            <i class="fas fa-calendar-alt"></i>
                                    @endswitch
                                </div>
                                <h6>{{ __($label) }}</h6>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4 wow fadeInUp">
                    <a href="{{ route('website.catering.inquiry') }}" class="common_btn">
                        <i class="fas fa-envelope me-2"></i>{{ __('Request a Quote') }}
                    </a>
                </div>
            </div>
        </section>
        <!--==========EVENT TYPES END===========-->
@endsection

@push('styles')
<style>
    .intro_content h2 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .intro_features {
        list-style: none;
        padding: 0;
        margin: 25px 0;
    }

    .intro_features li {
        padding: 8px 0;
        font-size: 16px;
    }

    .intro_features li i {
        color: #71dd37;
        margin-right: 10px;
    }

    .intro_image img {
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    .package_card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .package_card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .package_card.featured {
        border: 2px solid #ff6b35;
    }

    .package_image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .package_image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .featured_badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #ff6b35;
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .package_content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .package_content h4 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .package_desc {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .package_info {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .info_item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #666;
    }

    .info_item i {
        color: #ff6b35;
    }

    .package_includes {
        list-style: none;
        padding: 0;
        margin: 0 0 15px;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }

    .package_includes li {
        font-size: 13px;
        padding: 3px 0;
        color: #666;
    }

    .package_includes li i {
        color: #71dd37;
        margin-right: 8px;
    }

    .package_actions {
        margin-top: auto;
        display: flex;
        gap: 10px;
    }

    .common_btn.btn_sm {
        padding: 8px 15px;
        font-size: 13px;
    }

    .common_btn.btn_outline {
        background: transparent;
        border: 2px solid #ff6b35;
        color: #ff6b35;
    }

    .common_btn.btn_outline:hover {
        background: #ff6b35;
        color: #fff;
    }

    .event_type_card {
        background: #fff;
        padding: 25px 15px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }

    .event_type_card:hover {
        transform: translateY(-5px);
    }

    .event_type_card .icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #ff6b35, #f54749);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }

    .event_type_card .icon i {
        font-size: 24px;
        color: #fff;
    }

    .event_type_card h6 {
        margin-bottom: 0;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .intro_content {
            margin-bottom: 40px;
        }

        .package_actions {
            flex-direction: column;
        }
    }
</style>
@endpush
