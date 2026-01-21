@extends('website::layouts.master')

@section('title', $service->title . ' - CTAKE')

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $service->title }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.service') }}">Services</a></li>
                                <li><a href="{{ route('website.service-details', $service->slug) }}">{{ $service->title }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========SERVICE DETAILS START===========-->
        <section class="service_details pt_120 xs_pt_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 wow fadeInLeft">
                        <div class="service_details_img">
                            @if($service->image)
                                <img src="{{ asset($service->image) }}" alt="{{ $service->title }}" class="img-fluid w-100">
                            @else
                                <img src="{{ asset('website/images/service_details.jpg') }}" alt="{{ $service->title }}" class="img-fluid w-100">
                            @endif
                        </div>
                        <div class="service_details_text">
                            <h2>{{ $service->title }}</h2>
                            @if($service->short_description)
                                <p class="lead">{{ $service->short_description }}</p>
                            @endif

                            @if($service->description)
                                {!! $service->description !!}
                            @else
                                <p>No detailed description available for this service.</p>
                            @endif

                            @if($service->price)
                                <div class="service_price mt-4">
                                    <h4>Price: <span class="text-primary">${{ number_format($service->price, 2) }}</span></h4>
                                </div>
                            @endif

                            @if($service->duration)
                                <div class="service_duration mt-2">
                                    <p><strong>Duration:</strong> {{ $service->duration }} minutes</p>
                                </div>
                            @endif
                        </div>

                        @if($faqs->count() > 0)
                        <div class="accordion faq_accordion service_accordion" id="serviceFaqAccordion">
                            <h3>Frequently Asked Questions</h3>
                            @foreach($faqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqHeading{{ $index }}">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faqCollapse{{ $index }}"
                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                        aria-controls="faqCollapse{{ $index }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="faqCollapse{{ $index }}"
                                    class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                    aria-labelledby="faqHeading{{ $index }}">
                                    <div class="accordion-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-8 wow fadeInRight">
                        <div class="blog_sidebar">
                            <div class="sidebar_wizard sidebar_search">
                                <h2>Search Services</h2>
                                <form action="{{ route('website.service') }}" method="GET">
                                    <input type="text" name="search" placeholder="Search here..." value="{{ request('search') }}">
                                    <button type="submit"><i class="far fa-search"></i></button>
                                </form>
                            </div>

                            @if($relatedServices->count() > 0)
                            <div class="sidebar_wizard service_category mt_25">
                                <h2>Other Services</h2>
                                <div class="row">
                                    @foreach($relatedServices as $relatedService)
                                    <div class="col-xl-6 col-sm-6">
                                        <div class="service_category_img">
                                            @if($relatedService->image)
                                                <img src="{{ asset($relatedService->image) }}" alt="{{ $relatedService->title }}" class="img-fluid w-100">
                                            @else
                                                <img src="{{ asset('website/images/service_catg_1.jpg') }}" alt="{{ $relatedService->title }}" class="img-fluid w-100">
                                            @endif
                                            <div class="service_category_text">
                                                <a href="{{ route('website.service-details', $relatedService->slug) }}">{{ $relatedService->title }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="sidebar_banner menu_details_banner mt_25">
                                <img src="{{ asset('website/images/offer_bg.jpg') }}" alt="offer" class="img-fluid w-100">
                                <div class="text">
                                    <h5>Get Up to 50% Off</h5>
                                    <h3>Special Combo Pack</h3>
                                    <a href="{{ route('website.menu') }}">
                                        <span><img src="{{ asset('website/images/cart_icon_2.png') }}" alt="cart" class="img-fluid w-100"></span>
                                        shop now
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========SERVICE DETAILS END===========-->
@endsection
