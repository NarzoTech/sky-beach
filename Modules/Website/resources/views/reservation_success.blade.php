@extends('website::layouts.master')

@section('title', __('Reservation Confirmed') . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Reservation Confirmed') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.reservation.index') }}">{{ __('Reservations') }}</a></li>
                                <li><a href="#">{{ __('Confirmation') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========RESERVATION SUCCESS START===========-->
        <section class="reservation_success pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="success_card text-center wow fadeInUp">
                            <div class="success_icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>

                            <h2>{{ __('Reservation Submitted!') }}</h2>
                            <p class="lead mb-4">
                                {{ __('Your reservation request has been received. We will confirm it shortly.') }}
                            </p>

                            <div class="confirmation_code_box">
                                <label>{{ __('Confirmation Code') }}</label>
                                <div class="code">{{ $booking->confirmation_code }}</div>
                                <small class="text-muted">{{ __('Please save this code for your records') }}</small>
                            </div>

                            <div class="reservation_details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail_item">
                                            <i class="far fa-calendar-alt"></i>
                                            <label>{{ __('Date') }}</label>
                                            <span>{{ $booking->booking_date->format('l, F d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail_item">
                                            <i class="far fa-clock"></i>
                                            <label>{{ __('Time') }}</label>
                                            <span>{{ $booking->booking_time->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail_item">
                                            <i class="fas fa-users"></i>
                                            <label>{{ __('Party Size') }}</label>
                                            <span>{{ $booking->number_of_guests }} {{ $booking->number_of_guests === 1 ? __('Person') : __('Persons') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail_item">
                                            <i class="fas fa-chair"></i>
                                            <label>{{ __('Table Preference') }}</label>
                                            <span>{{ $booking->table_preference_label }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="guest_info mt-4">
                                    <h5><i class="fas fa-user me-2"></i>{{ __('Guest Information') }}</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>{{ __('Name') }}:</strong><br>{{ $booking->name }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>{{ __('Email') }}:</strong><br>{{ $booking->email }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>{{ __('Phone') }}:</strong><br>{{ $booking->phone }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($booking->special_request)
                                    <div class="special_request mt-4">
                                        <h5><i class="fas fa-sticky-note me-2"></i>{{ __('Special Requests') }}</h5>
                                        <p>{{ $booking->special_request }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="status_badge mt-4">
                                <span class="badge {{ $booking->status_badge_class }} fs-6">
                                    <i class="fas fa-clock me-1"></i>{{ $booking->status_label }}
                                </span>
                                <p class="text-muted mt-2 mb-0">
                                    {{ __('You will receive a confirmation email once your reservation is confirmed.') }}
                                </p>
                            </div>

                            <div class="action_buttons mt-5">
                                @auth
                                    <a href="{{ route('website.reservations.index') }}" class="common_btn me-3">
                                        <i class="fas fa-list me-2"></i>{{ __('My Reservations') }}
                                    </a>
                                @endauth
                                <a href="{{ route('website.menu') }}" class="common_btn btn_outline">
                                    <i class="fas fa-utensils me-2"></i>{{ __('View Menu') }}
                                </a>
                            </div>

                            <div class="help_text mt-5">
                                <p class="text-muted">
                                    {{ __('Need to make changes?') }}
                                    <a href="{{ route('website.contact') }}">{{ __('Contact us') }}</a>
                                    {{ __('or call') }} <a href="tel:+1234567890">+1 234 567 8900</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========RESERVATION SUCCESS END===========-->

@endsection

@push('styles')
<style>
    .success_card {
        background: #fff;
        padding: 50px 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .success_icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #71dd37, #5cb82b);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }

    .success_icon i {
        font-size: 45px;
        color: #fff;
    }

    .success_card h2 {
        color: #333;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .confirmation_code_box {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 15px;
        margin: 30px 0;
    }

    .confirmation_code_box label {
        display: block;
        color: #888;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .confirmation_code_box .code {
        font-size: 32px;
        font-weight: 700;
        color: #ff6b35;
        letter-spacing: 3px;
        font-family: monospace;
    }

    .reservation_details {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 30px;
        text-align: left;
    }

    .detail_item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .detail_item i {
        font-size: 24px;
        color: #ff6b35;
        display: block;
        margin-bottom: 8px;
    }

    .detail_item label {
        display: block;
        font-size: 12px;
        color: #888;
        margin-bottom: 3px;
    }

    .detail_item span {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .guest_info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
    }

    .guest_info h5 {
        margin-bottom: 15px;
        font-size: 16px;
        color: #333;
    }

    .guest_info p {
        margin-bottom: 0;
        font-size: 14px;
    }

    .special_request {
        background: #fff3e0;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #ff6b35;
    }

    .special_request h5 {
        margin-bottom: 10px;
        font-size: 16px;
        color: #ff6b35;
    }

    .special_request p {
        margin-bottom: 0;
        color: #666;
    }

    .status_badge .badge {
        padding: 10px 25px;
        border-radius: 25px;
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

    @media (max-width: 768px) {
        .success_card {
            padding: 30px 20px;
        }

        .confirmation_code_box .code {
            font-size: 24px;
        }

        .action_buttons .common_btn {
            display: block;
            margin-bottom: 10px;
        }
    }
</style>
@endpush
