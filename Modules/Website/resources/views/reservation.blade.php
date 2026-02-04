@extends('website::layouts.master')

@section('title', __('Reservations') . ' - ' . config('app.name'))

@php
    $sections = site_sections('reservation');
    $breadcrumb = $sections['reservation_breadcrumb'] ?? null;
    $formSection = $sections['reservation_form'] ?? null;
    $infoSection = $sections['reservation_info'] ?? null;
    $mapSection = $sections['reservation_map'] ?? null;
@endphp

@section('content')
    <!--==========BREADCRUMB AREA START===========-->
    @if (!$breadcrumb || $breadcrumb->section_status)
        <section class="breadcrumb_area"
            style="background: url({{ $breadcrumb?->background_image ? asset($breadcrumb->background_image) : asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $breadcrumb?->title ?? __('Reservations') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ $breadcrumb?->title ?? __('Reservations') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!--==========BREADCRUMB AREA END===========-->


    <!--==========RESERVATION PAGE START===========-->
    @if (!$formSection || $formSection->section_status)
        <section class="reservation_page pt_120 xs_pt_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 wow fadeInLeft">
                        <div class="reservation_img">
                            <img src="{{ $formSection?->image ? asset($formSection->image) : asset('website/images/reservation_img_2.jpg') }}"
                                alt="reservation" class="img-fluid w-100">
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInRight">
                        <div class="reservation_form">
                            <h2>{{ $formSection?->title ?? __('ONLINE RESERVATION') }}</h2>

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div id="formAlerts"></div>

                            <form id="reservationForm" action="{{ route('website.reservation.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="text" name="name" placeholder="{{ __('Your Name') }} *"
                                                value="{{ old('name', $user->name ?? '') }}" required>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="tel" name="phone" id="phone" placeholder="01XXX-XXXXXX *"
                                                value="{{ old('phone', $user->phone ?? '') }}" required maxlength="12"
                                                pattern="01[3-9][0-9]{2}-?[0-9]{6}"
                                                title="{{ __('Enter a valid Bangladesh mobile number (e.g., 01712-345678)') }}">
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="reservation_form_input">
                                            <input type="email" name="email"
                                                placeholder="{{ __('Your Email (Optional)') }}"
                                                value="{{ old('email', $user->email ?? '') }}">
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="text" name="booking_date" class="flatpickr-date"
                                                placeholder="{{ __('Select Date') }} *" required>
                                            @error('booking_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="booking_time" required>
                                                <option value="">{{ __('Select Time') }} *</option>
                                                @foreach ($timeSlots as $value => $display)
                                                    <option value="{{ $value }}"
                                                        {{ old('booking_time') == $value ? 'selected' : '' }}>
                                                        {{ $display }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('booking_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="number_of_guests" required>
                                                <option value="">{{ __('Number of Guests') }} *</option>
                                                @for ($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ old('number_of_guests') == $i ? 'selected' : '' }}>
                                                        {{ $i }} {{ $i === 1 ? __('Person') : __('Persons') }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('number_of_guests')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="reservation_form_input">
                                            <textarea name="special_request" rows="5"
                                                placeholder="{{ __('Special Requests (dietary requirements, occasion, etc.)') }}">{{ old('special_request') }}</textarea>
                                            @error('special_request')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror

                                            <button class="common_btn" type="submit" id="submitBtn">
                                                <span class="btn-text">{{ __('Make A Reservation') }}</span>
                                                <span class="btn-loading">
                                                    <i class="fas fa-spinner fa-spin"></i> {{ __('Submitting...') }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @auth
                                <div class="mt-3 text-center">
                                    <a href="{{ route('website.reservations.index') }}" class="text-primary">
                                        <i class="fas fa-list me-1"></i> {{ __('View My Reservations') }}
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!--==========RESERVATION PAGE END===========-->



    <!--==========GALLERY START===========-->
    @if (isset($galleryItems) && $galleryItems->count() > 0)
        <section class="gallery mt_120 xs_mt_100">
            <div class="row gallery_slider">
                @foreach ($galleryItems as $item)
                    <div class="col-xl-3 wow fadeInUp">
                        <div class="gallery_item">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-fluid w-100">
                            <div class="text">
                                <a class="title"
                                    href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                <p>{{ $item->category->name ?? __('Menu Item') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
    <!--==========GALLERY END===========-->


    <!--==========BLOG START===========-->
    @if (isset($recentBlogs) && $recentBlogs->count() > 0)
        <section class="blog pt_110 xs_pt_90">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 m-auto wow fadeInUp">
                        <div class="section_heading mb_25">
                            <h2>{{ __('Our Latest News & Article') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($recentBlogs as $blog)
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="single_blog">
                                <div class="single_blog_img">
                                    <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}"
                                        class="img-fluid w-100">
                                    @if ($blog->tags)
                                        <a class="category" href="#">{{ trim(explode(',', $blog->tags)[0]) }}</a>
                                    @else
                                        <a class="category" href="#">{{ __('Blog') }}</a>
                                    @endif
                                </div>
                                <div class="single_blog_text">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('website/images/calendar.svg') }}" alt="calendar"
                                                    class="img-fluid"></span>
                                            {{ $blog->published_at ? $blog->published_at->format('F d, Y') : $blog->created_at->format('F d, Y') }}
                                        </li>
                                        <li>{{ __('BY') }} {{ $blog->author ?? 'Admin' }}</li>
                                    </ul>
                                    <a class="title"
                                        href="{{ route('website.blog-details', $blog->slug) }}">{{ Str::upper($blog->title) }}</a>
                                    <a class="read_btn"
                                        href="{{ route('website.blog-details', $blog->slug) }}">{{ __('Read More') }} <i
                                            class="far fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <!--==========BLOG END===========-->
@endsection

@push('styles')
    <style>
        .reservation_form {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .reservation_form h2 {
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }

        .reservation_form_input {
            margin-bottom: 20px;
        }

        .reservation_form_input input,
        .reservation_form_input select,
        .reservation_form_input textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .reservation_form_input input:focus,
        .reservation_form_input select:focus,
        .reservation_form_input textarea:focus {
            border-color: #ff6b35;
            outline: none;
        }

        .reservation_form_input small.text-danger {
            display: block;
            margin-top: 5px;
        }

        .common_btn .btn-loading {
            display: none;
        }

        .common_btn.loading .btn-text {
            display: none;
        }

        .common_btn.loading .btn-loading {
            display: inline;
        }

        @media (max-width: 992px) {
            .reservation_form {
                margin-top: 40px;
            }
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
            const phoneInput = document.getElementById('phone');
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

            const form = document.getElementById('reservationForm');
            const submitBtn = document.getElementById('submitBtn');
            const formAlerts = document.getElementById('formAlerts');

            // Form submission
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
                                    const errorMessages = Object.values(data.errors).flat()
                                        .join('<br>');
                                    throw new Error(errorMessages);
                                }
                                throw new Error(data.message ||
                                    '{{ __('An error occurred. Please try again.') }}');
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            formAlerts.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${data.message || '{{ __('An error occurred. Please try again.') }}'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                            submitBtn.classList.remove('loading');
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        formAlerts.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${error.message || '{{ __('An error occurred. Please try again.') }}'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    });
            });
        });
    </script>
@endpush
