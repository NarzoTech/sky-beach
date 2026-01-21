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
                        </div>

                        @if($serviceFaqs->count() > 0 || $faqs->count() > 0)
                        <div class="accordion faq_accordion service_accordion" id="serviceFaqAccordion">
                            <h3>Frequently Asked Questions</h3>
                            @php $faqIndex = 0; @endphp

                            {{-- Service-specific FAQs --}}
                            @foreach($serviceFaqs as $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqHeading{{ $faqIndex }}">
                                    <button class="accordion-button {{ $faqIndex > 0 ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faqCollapse{{ $faqIndex }}"
                                        aria-expanded="{{ $faqIndex == 0 ? 'true' : 'false' }}"
                                        aria-controls="faqCollapse{{ $faqIndex }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="faqCollapse{{ $faqIndex }}"
                                    class="accordion-collapse collapse {{ $faqIndex == 0 ? 'show' : '' }}"
                                    aria-labelledby="faqHeading{{ $faqIndex }}">
                                    <div class="accordion-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                            @php $faqIndex++; @endphp
                            @endforeach

                            {{-- General FAQs (shown if no service-specific FAQs) --}}
                            @if($serviceFaqs->count() == 0)
                            @foreach($faqs as $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqHeading{{ $faqIndex }}">
                                    <button class="accordion-button {{ $faqIndex > 0 ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#faqCollapse{{ $faqIndex }}"
                                        aria-expanded="{{ $faqIndex == 0 ? 'true' : 'false' }}"
                                        aria-controls="faqCollapse{{ $faqIndex }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="faqCollapse{{ $faqIndex }}"
                                    class="accordion-collapse collapse {{ $faqIndex == 0 ? 'show' : '' }}"
                                    aria-labelledby="faqHeading{{ $faqIndex }}">
                                    <div class="accordion-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                            @php $faqIndex++; @endphp
                            @endforeach
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-8 wow fadeInRight">
                        <div class="blog_sidebar">
                            <!-- Contact Form for Service -->
                            <div class="sidebar_wizard contact_sidebar mb-4">
                                <h2>Inquire About This Service</h2>
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                <form action="{{ route('website.service-contact.store') }}" method="POST" class="service_contact_form">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <div class="mb-3">
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your Name *" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Your Email *" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone Number" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" placeholder="Your Message *" required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="common_btn w-100">Send Inquiry</button>
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
