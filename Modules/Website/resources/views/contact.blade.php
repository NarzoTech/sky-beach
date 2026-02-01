@extends('website::layouts.master')

@section('title', __('Contact Us') . ' - ' . config('app.name'))

@php
    $sections = site_sections('contact');
    $breadcrumb = $sections['contact_breadcrumb'] ?? null;
    $formSection = $sections['contact_form'] ?? null;
    $infoSection = $sections['contact_info'] ?? null;
    $mapSection = $sections['contact_map'] ?? null;
@endphp

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        @if(!$breadcrumb || $breadcrumb->section_status)
        <section class="breadcrumb_area" style="background: url({{ $breadcrumb?->background_image ? asset($breadcrumb->background_image) : asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $breadcrumb?->title ?? __('Contact Us') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.contact') }}">{{ $breadcrumb?->title ?? __('Contact') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========CONTACT START===========-->
        @if(!$formSection || $formSection->section_status)
        <section class="contact_us pt_120 xs_pt_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-8 wow fadeInLeft">
                        <div class="contact_img">
                            <img src="{{ $formSection?->image ? asset($formSection->image) : asset('website/images/contact_img.jpg') }}" alt="contact" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInRight">
                        <div class="contact_form">
                            <h2>{{ $formSection?->title ?? __('Get In Touch') }}</h2>
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            <form action="{{ route('website.contact.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="name" placeholder="{{ __('Your Name') }}" value="{{ old('name') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="email" placeholder="{{ __('Your Email') }}" value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="phone" placeholder="{{ __('Phone Number') }}" value="{{ old('phone') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="subject" placeholder="{{ __('Subject') }}" value="{{ old('subject') }}" required>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea rows="7" name="message" placeholder="{{ __('Write Message...') }}" required>{{ old('message') }}</textarea>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="newsletter" value="1"
                                                id="flexCheckDefault" {{ old('newsletter') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="flexCheckDefault">
                                                {{ __('Subscribe to our newsletter for updates about our services.') }}
                                            </label>
                                        </div>
                                        <button type="submit" class="common_btn">{{ __('Submit Now') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @if(!$infoSection || $infoSection->section_status)
                <div class="row mt_95 xs_mt_75">
                    <div class="col-xl-4 col-md-6 wow fadeInUp">
                        <div class="contact_info">
                            <div class="icon">
                                <img src="{{ asset('website/images/location_2.png') }}" alt="location" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <p>{{ cms_contact('address') ?? '16/A, Romadan House City Tower New York, United States' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 wow fadeInUp">
                        <div class="contact_info">
                            <div class="icon">
                                <img src="{{ asset('website/images/call_icon_3.png') }}" alt="call" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <a href="tel:{{ cms_contact('phone') ?? '+990123456789' }}">{{ cms_contact('phone') ?? '+990 123 456 789' }}</a>
                                @if(cms_contact('phone_2'))
                                <a href="tel:{{ cms_contact('phone_2') }}">{{ cms_contact('phone_2') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 wow fadeInUp">
                        <div class="contact_info">
                            <div class="icon">
                                <img src="{{ asset('website/images/mail_icon.png') }}" alt="mail" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <a href="mailto:{{ cms_contact('email') ?? 'info@skybeach.com' }}">{{ cms_contact('email') ?? 'info@skybeach.com' }}</a>
                                @if(cms_contact('email_2'))
                                <a href="mailto:{{ cms_contact('email_2') }}">{{ cms_contact('email_2') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @php
                $mapUrl = cms_setting('google_map_embed_url');
            @endphp
            @if($mapUrl)
            <div class="contact_map mt_120 xs_mt_100 wow fadeInUp">
                <iframe
                    src="{{ $mapUrl }}"
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
        </section>
        @endif
        <!--==========CONTACT END===========-->
@endsection
