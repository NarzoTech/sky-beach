@extends('website::layouts.master')

@section('title', __('About Us') . ' - ' . config('app.name'))

@php
    $sections = site_sections('about');
    $breadcrumb = $sections['about_breadcrumb'] ?? null;
    $story = $sections['about_story'] ?? null;
    $gallery = $sections['about_gallery'] ?? null;
    $showcase = $sections['about_showcase'] ?? null;
    $reservation = $sections['about_reservation'] ?? null;
    $testimonialSection = $sections['about_testimonials'] ?? null;
    $countersSection = $sections['about_counters'] ?? null;
    $chefsSection = $sections['about_chefs'] ?? null;
    $blogsSection = $sections['about_blogs'] ?? null;
    $testimonials = cms_testimonials();
    $counters = cms_counters();
@endphp

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        @if(!$breadcrumb || $breadcrumb->section_status)
        <section class="breadcrumb_area" style="background: url({{ $breadcrumb?->background_image ? asset($breadcrumb->background_image) : asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $breadcrumb?->title ?? __('About Us') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.about') }}">{{ $breadcrumb?->title ?? __('About Us') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========ABOUT US PAGE START===========-->
        @if(!$story || $story->section_status)
        <section class="about_us_story pt_95 xs_pt_65">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-xl-5 wow fadeInLeft">
                        <div class="about_us_story_text">
                            <h2>{{ $story?->title ?? __('We invite you to visit our restaurant') }}</h2>
                            {!! nl2br(e($story?->description ?? __('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.'))) !!}
                            @if($story?->button_text)
                            <a class="common_btn" href="{{ $story->button_link ?? route('website.menu') }}">
                                <span class="icon">
                                    <img src="{{ asset('website/images/eye.png') }}" alt="{{ __('menu') }}" class="img-fluid w-100">
                                </span>
                                {{ $story->button_text }}
                            </a>
                            @else
                            <a class="common_btn" href="{{ route('website.menu') }}">
                                <span class="icon">
                                    <img src="{{ asset('website/images/eye.png') }}" alt="{{ __('menu') }}" class="img-fluid w-100">
                                </span>
                                {{ __('View All Menu') }}
                            </a>
                            @endif
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
        @endif

        @if(!$showcase || $showcase->section_status)
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
        @endif

        @if(!$reservation || $reservation->section_status)
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
                            <h2>{{ $reservation?->title ?? __('ONLINE RESERVATION') }}</h2>
                            <div id="aboutFormAlerts"></div>
                            <form id="aboutReservationForm" action="{{ route('website.reservation.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="text" name="name" placeholder="{{ __('Your Name') }} *" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="tel" name="phone" id="aboutPhone" placeholder="01XXX-XXXXXX *"
                                                   required maxlength="12" pattern="01[3-9][0-9]{2}-?[0-9]{6}"
                                                   title="{{ __('Enter a valid Bangladesh mobile number (e.g., 01712-345678)') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="reservation_form_input">
                                            <input type="email" name="email" placeholder="{{ __('Your Email (Optional)') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="text" name="booking_date" class="flatpickr-date" placeholder="{{ __('Select Date') }} *" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="booking_time" required>
                                                <option value="">{{ __('Select Time') }} *</option>
                                                <option value="10:00">10:00 AM</option>
                                                <option value="10:30">10:30 AM</option>
                                                <option value="11:00">11:00 AM</option>
                                                <option value="11:30">11:30 AM</option>
                                                <option value="12:00">12:00 PM</option>
                                                <option value="12:30">12:30 PM</option>
                                                <option value="13:00">01:00 PM</option>
                                                <option value="13:30">01:30 PM</option>
                                                <option value="14:00">02:00 PM</option>
                                                <option value="14:30">02:30 PM</option>
                                                <option value="15:00">03:00 PM</option>
                                                <option value="15:30">03:30 PM</option>
                                                <option value="16:00">04:00 PM</option>
                                                <option value="16:30">04:30 PM</option>
                                                <option value="17:00">05:00 PM</option>
                                                <option value="17:30">05:30 PM</option>
                                                <option value="18:00">06:00 PM</option>
                                                <option value="18:30">06:30 PM</option>
                                                <option value="19:00">07:00 PM</option>
                                                <option value="19:30">07:30 PM</option>
                                                <option value="20:00">08:00 PM</option>
                                                <option value="20:30">08:30 PM</option>
                                                <option value="21:00">09:00 PM</option>
                                                <option value="21:30">09:30 PM</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="number_of_guests" required>
                                                <option value="">{{ __('Number of Guests') }} *</option>
                                                @for($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? __('Person') : __('Persons') }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="reservation_form_input">
                                            <textarea rows="5" name="special_request" placeholder="{{ __('Special Requests (dietary requirements, occasion, etc.)') }}"></textarea>
                                            <button class="common_btn" type="submit" id="aboutSubmitBtn">
                                                <span class="btn-text">{{ __('Make A Reservation') }}</span>
                                                <span class="btn-loading">
                                                    <i class="fas fa-spinner fa-spin"></i> {{ __('Submitting...') }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if(!$testimonialSection || $testimonialSection->section_status)
        <section class="testimonial mt_120 xs_mt_100" style="background: url({{ $testimonialSection?->background_image ? asset($testimonialSection->background_image) : asset('website/images/testimonial_bg.jpg') }});">
            <div class="testimonial_overlay pt_250 xs_pt_100">
                <div class="container mt_20">
                    <div class="row wow fadeInUp">
                        <div class="col-md-9">
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
                                            <p class="description">"{{ $testimonial->content ?? $testimonial->testimonial ?? '' }}"</p>
                                            <div class="single_testimonial_footer">
                                                <div class="img">
                                                    @if($testimonial->image)
                                                        <img src="{{ asset($testimonial->image) }}" alt="{{ $testimonial->name }}" class="img-fluid w-100">
                                                    @else
                                                        <img src="{{ asset('website/images/client_img_1.png') }}" alt="{{ $testimonial->name }}" class="img-fluid w-100">
                                                    @endif
                                                </div>
                                                <h3>{{ $testimonial->name }} <span>{{ $testimonial->designation ?? $testimonial->position ?? '' }}</span></h3>
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
                                            <p class="description">"{{ __('Amazing food and excellent service! The atmosphere is wonderful and the staff is very friendly. Highly recommended!') }}"</p>
                                            <div class="single_testimonial_footer">
                                                <div class="img">
                                                    <img src="{{ asset('website/images/client_img_1.png') }}" alt="{{ __('client') }}" class="img-fluid w-100">
                                                </div>
                                                <h3>{{ __('Happy Customer') }} <span>{{ __('Regular Guest') }}</span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="testimonial_video">
                                <a class="venobox play_btn" data-autoplay="true" data-vbtype="video"
                                    href="{{ $testimonialSection?->video ?? 'https://youtu.be/nqye02H_H6I?si=ougeOsfL0tat6YbT' }}">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if(!$countersSection || $countersSection->section_status)
        <section class="counter_area">
            <div class="counter_bg pt_30 pb_35">
                <div class="container">
                    <div class="row">
                        @if($counters && count($counters) > 0)
                            @foreach($counters as $counter)
                            <div class="col-lg-3 col-sm-6 wow fadeInUp">
                                <div class="single_counter">
                                    <h2 class="counter">{{ $counter->value ?? $counter->count ?? 0 }}</h2>
                                    <span>{{ $counter->label ?? $counter->title ?? '' }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="col-lg-3 col-sm-6 wow fadeInUp">
                            <div class="single_counter">
                                <h2 class="counter">{{ cms_setting('counter_dishes', 45) }}</h2>
                                <span>{{ __('Dishes') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 wow fadeInUp">
                            <div class="single_counter">
                                <h2 class="counter">{{ cms_setting('counter_locations', 68) }}</h2>
                                <span>{{ __('Locations') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 wow fadeInUp">
                            <div class="single_counter">
                                <h2 class="counter">{{ cms_setting('counter_chefs', 32) }}</h2>
                                <span>{{ __('Chefs') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 wow fadeInUp">
                            <div class="single_counter">
                                <h2 class="counter">{{ cms_setting('counter_cities', 120) }}</h2>
                                <span>{{ __('Cities') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if(!$chefsSection || $chefsSection->section_status)
        <section class="shefs pt_110 xs_pt_90">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 m-auto wow fadeInUp">
                        <div class="section_heading mb_25">
                            <h2>{{ $chefsSection?->title ?? __('Meet Our Special Chefs') }}</h2>
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
                        <p>{{ __('No chefs available at the moment.') }}</p>
                    </div>
                    @endforelse
                    <div class="col-12 about_2_shefs text-center mt_60 wow fadeInUp">
                        <a class="common_btn" href="{{ $chefsSection?->button_link ?? route('website.chefs') }}">
                            <span class="icon">
                                <img src="{{ asset('website/images/eye.png') }}" alt="{{ __('view') }}" class="img-fluid w-100">
                            </span>
                            {{ $chefsSection?->button_text ?? __('View All Chefs') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if(!$blogsSection || $blogsSection->section_status)
        <section class="blog pt_100 xs_pt_80">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 m-auto wow fadeInUp">
                        <div class="section_heading mb_25">
                            <h2>{{ $blogsSection?->title ?? __('Our Latest News & Article') }}</h2>
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
                                    <a class="category" href="#">{{ __('Blog') }}</a>
                                @endif
                            </div>
                            <div class="single_blog_text">
                                <ul>
                                    <li>
                                        <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar" class="img-fluid"></span>
                                        {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
                                    </li>
                                    <li>{{ __('BY') }} {{ $blog->author ?? __('Admin') }}</li>
                                </ul>
                                <a class="title" href="{{ route('website.blog-details', $blog->slug) }}">{{ Str::upper($blog->title) }}</a>
                                <a class="read_btn" href="{{ route('website.blog-details', $blog->slug) }}">Read More <i class="far fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <p>{{ __('No blogs available at the moment.') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>
        @endif
        <!--==========ABOUT US PAGE END===========-->
@endsection

@push('styles')
<style>
    .common_btn .btn-loading {
        display: none;
    }
    .common_btn.loading .btn-text {
        display: none;
    }
    .common_btn.loading .btn-loading {
        display: inline;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr date picker
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.flatpickr-date', {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            disableMobile: true,
            altInput: true,
            altFormat: 'F j, Y',
        });
    }

    // Bangladesh phone number formatting
    const phoneInput = document.getElementById('aboutPhone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            e.target.value = value;
        });
    }

    // Form submission
    const form = document.getElementById('aboutReservationForm');
    const submitBtn = document.getElementById('aboutSubmitBtn');
    const formAlerts = document.getElementById('aboutFormAlerts');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            formAlerts.innerHTML = '';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat().join('<br>');
                            throw new Error(errorMessages);
                        }
                        throw new Error(data.message || '{{ __("An error occurred. Please try again.") }}');
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    formAlerts.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${data.message || '{{ __("An error occurred. Please try again.") }}'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                formAlerts.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${error.message || '{{ __("An error occurred. Please try again.") }}'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
@endpush
