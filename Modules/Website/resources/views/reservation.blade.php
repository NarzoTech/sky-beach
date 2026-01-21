@extends('website::layouts.master')

@section('title', __('Reservations') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Reservations') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('Reservations') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========RESERVATION PAGE START===========-->
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
                            <h2>{{ __('ONLINE RESERVATION') }}</h2>

                            @if(session('error'))
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
                                                   value="{{ old('phone', $user->phone ?? '') }}" required
                                                   maxlength="12" pattern="01[3-9][0-9]{2}-?[0-9]{6}"
                                                   title="{{ __('Enter a valid Bangladesh mobile number (e.g., 01712-345678)') }}">
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="reservation_form_input">
                                            <input type="email" name="email" placeholder="{{ __('Your Email (Optional)') }}"
                                                   value="{{ old('email', $user->email ?? '') }}">
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <input type="date" name="booking_date" id="bookingDate"
                                                   min="{{ date('Y-m-d') }}"
                                                   value="{{ old('booking_date') }}" required>
                                            @error('booking_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="booking_time" id="bookingTime" required>
                                                <option value="">{{ __('Select Time') }} *</option>
                                                @foreach($timeSlots as $value => $display)
                                                    <option value="{{ $value }}" {{ old('booking_time') == $value ? 'selected' : '' }}>
                                                        {{ $display }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('booking_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small id="timeAvailability" class="availability-hint"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="number_of_guests" id="numberOfGuests" required>
                                                <option value="">{{ __('Number of Guests') }} *</option>
                                                @for($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}" {{ old('number_of_guests') == $i ? 'selected' : '' }}>
                                                        {{ $i }} {{ $i === 1 ? __('Person') : __('Persons') }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('number_of_guests')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="reservation_form_input">
                                            <select class="select_2" name="table_preference">
                                                <option value="any">{{ __('Table Preference (Optional)') }}</option>
                                                @foreach($tablePreferences as $value => $label)
                                                    <option value="{{ $value }}" {{ old('table_preference') == $value ? 'selected' : '' }}>
                                                        {{ __($label) }}
                                                    </option>
                                                @endforeach
                                            </select>
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
        <!--==========RESERVATION PAGE END===========-->


        <!--==========INFO SECTION START===========-->
        <section class="reservation_info pt_100 xs_pt_80 pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="info_card">
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4>{{ __('Opening Hours') }}</h4>
                            <p>{{ __('Monday - Sunday') }}<br>10:00 AM - 10:00 PM</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="info_card">
                            <div class="icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <h4>{{ __('Call Us') }}</h4>
                            <p>{{ __('For immediate assistance') }}<br>+1 234 567 8900</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="info_card">
                            <div class="icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h4>{{ __('Cancellation Policy') }}</h4>
                            <p>{{ __('Free cancellation up to 2 hours before your reservation') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========INFO SECTION END===========-->
@endsection

@push('styles')
<style>
    .reservation_form {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
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

    .availability-hint {
        display: block;
        margin-top: 5px;
        font-size: 13px;
    }

    .availability-hint.available {
        color: #28a745;
    }

    .availability-hint.unavailable {
        color: #dc3545;
    }

    .availability-hint.checking {
        color: #666;
    }

    .info_card {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        text-align: center;
        height: 100%;
    }

    .info_card .icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #ff6b35, #f54749);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .info_card .icon i {
        font-size: 28px;
        color: #fff;
    }

    .info_card h4 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .info_card p {
        color: #666;
        margin-bottom: 0;
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
    // Bangladesh phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

            // Limit to 11 digits
            if (value.length > 11) {
                value = value.slice(0, 11);
            }

            // Format as 01XXX-XXXXXX
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }

            e.target.value = value;
        });

        // Format existing value on page load
        if (phoneInput.value) {
            let value = phoneInput.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 5) value = value.slice(0, 5) + '-' + value.slice(5);
            phoneInput.value = value;
        }
    }

    const form = document.getElementById('reservationForm');
    const dateInput = document.getElementById('bookingDate');
    const timeSelect = document.getElementById('bookingTime');
    const guestsSelect = document.getElementById('numberOfGuests');
    const submitBtn = document.getElementById('submitBtn');
    const availabilityHint = document.getElementById('timeAvailability');
    const formAlerts = document.getElementById('formAlerts');

    // Check availability when date or time changes
    function checkAvailability() {
        const date = dateInput.value;
        const time = timeSelect.value;
        const guests = guestsSelect.value || 1;

        if (!date || !time) {
            availabilityHint.textContent = '';
            availabilityHint.className = 'availability-hint';
            return;
        }

        availabilityHint.textContent = '{{ __("Checking availability...") }}';
        availabilityHint.className = 'availability-hint checking';

        fetch(`{{ route('website.reservation.check') }}?date=${date}&time=${time}&guests=${guests}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                availabilityHint.textContent = '{{ __("✓ This time slot is available!") }}';
                availabilityHint.className = 'availability-hint available';
            } else {
                availabilityHint.textContent = '{{ __("✗ This time slot is not available") }}';
                availabilityHint.className = 'availability-hint unavailable';
            }
        })
        .catch(error => {
            availabilityHint.textContent = '';
            availabilityHint.className = 'availability-hint';
        });
    }

    dateInput.addEventListener('change', checkAvailability);
    timeSelect.addEventListener('change', checkAvailability);
    guestsSelect.addEventListener('change', checkAvailability);

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
                    // Handle validation errors (422) or other errors
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
                // Redirect to success page
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
});
</script>
@endpush
