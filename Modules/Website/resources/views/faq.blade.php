@extends('website::layouts.master')

@section('title', __('FAQ') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('FAQ') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.faq') }}">{{ __('FAQ') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========FAQ'S START===========-->
        <section class="faq_area pt_95 xs_pt_70">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-xl-6 wow fadeInLeft">
                        <div class="accordion faq_accordion" id="faqAccordion">
                            @forelse($faqs as $index => $faq)
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
                                    aria-labelledby="faqHeading{{ $index }}"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="alert alert-info">
                                <h4>{{ __('No FAQs available') }}</h4>
                                <p>{{ __('Please check back later for frequently asked questions.') }}</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-xl-5 my-auto wow fadeInRight">
                        <div class="faq_img">
                            <img src="{{ asset('website/images/faq_1.png') }}" alt="faq" class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq_contact mt_120 xs_mt_100 pt_120 xs_pt_80 pb_120 xs_pb_80">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-xl-5 col-lg-6 wow fadeInLeft">
                            <div class="faq_contact_img">
                                <img src="{{ asset('website/images/faq_contact_img.png') }}" alt="FAQ's" class="img-fluid w-100">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 wow fadeInRight">
                            <div class="faq_contact_form contact_form">
                                <h2>{{ __('Have Any Question?') }}</h2>
                                <form action="{{ route('website.contact.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="name" placeholder="{{ __('Your Name') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="email" name="email" placeholder="{{ __('Your Email') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="phone" placeholder="{{ __('Phone Number') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="subject" placeholder="{{ __('Subject') }}" required>
                                        </div>
                                        <div class="col-md-12">
                                            <textarea rows="7" name="message" placeholder="{{ __('Write Message...') }}" required></textarea>
                                            <button type="submit" class="common_btn">{{ __('Submit Now') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========FAQ'S END===========-->
@endsection
