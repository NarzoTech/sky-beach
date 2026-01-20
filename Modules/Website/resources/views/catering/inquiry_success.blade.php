@extends('website::layouts.master')

@section('title', __('Inquiry Submitted') . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Inquiry Submitted') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.catering.index') }}">{{ __('Catering') }}</a></li>
                                <li><a href="#">{{ __('Confirmation') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========SUCCESS SECTION START===========-->
        <section class="success_section pt_120 xs_pt_100 pb_120 xs_pb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="success_card wow fadeInUp">
                            <div class="success_icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2>{{ __('Thank You!') }}</h2>
                            <p class="lead">{{ __('Your catering inquiry has been submitted successfully.') }}</p>

                            <div class="inquiry_details">
                                <div class="detail_header">
                                    <span class="inquiry_number">{{ $inquiry->inquiry_number }}</span>
                                    <span class="status_badge status_{{ $inquiry->status }}">{{ $inquiry->status_label }}</span>
                                </div>

                                <div class="detail_grid">
                                    <div class="detail_item">
                                        <label>{{ __('Name') }}</label>
                                        <span>{{ $inquiry->name }}</span>
                                    </div>
                                    <div class="detail_item">
                                        <label>{{ __('Email') }}</label>
                                        <span>{{ $inquiry->email }}</span>
                                    </div>
                                    <div class="detail_item">
                                        <label>{{ __('Phone') }}</label>
                                        <span>{{ $inquiry->phone }}</span>
                                    </div>
                                    <div class="detail_item">
                                        <label>{{ __('Event Type') }}</label>
                                        <span>{{ $inquiry->event_type_label }}</span>
                                    </div>
                                    <div class="detail_item">
                                        <label>{{ __('Event Date') }}</label>
                                        <span>{{ $inquiry->event_date->format('F j, Y') }}</span>
                                    </div>
                                    @if($inquiry->event_time)
                                        <div class="detail_item">
                                            <label>{{ __('Event Time') }}</label>
                                            <span>{{ \Carbon\Carbon::parse($inquiry->event_time)->format('g:i A') }}</span>
                                        </div>
                                    @endif
                                    <div class="detail_item">
                                        <label>{{ __('Number of Guests') }}</label>
                                        <span>{{ $inquiry->guest_count }}</span>
                                    </div>
                                    @if($inquiry->package)
                                        <div class="detail_item">
                                            <label>{{ __('Selected Package') }}</label>
                                            <span>{{ $inquiry->package->name }}</span>
                                        </div>
                                        <div class="detail_item highlight">
                                            <label>{{ __('Estimated Price') }}</label>
                                            <span>${{ number_format($inquiry->estimated_price, 2) }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($inquiry->venue_address)
                                    <div class="detail_full">
                                        <label>{{ __('Venue Address') }}</label>
                                        <p>{{ $inquiry->venue_address }}</p>
                                    </div>
                                @endif

                                @if($inquiry->special_requirements)
                                    <div class="detail_full">
                                        <label>{{ __('Special Requirements') }}</label>
                                        <p>{{ $inquiry->special_requirements }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="next_steps">
                                <h5><i class="fas fa-info-circle me-2"></i>{{ __('What Happens Next?') }}</h5>
                                <ol>
                                    <li>{{ __('Our catering team will review your inquiry within 24-48 hours.') }}</li>
                                    <li>{{ __('We\'ll contact you to discuss your requirements in detail.') }}</li>
                                    <li>{{ __('You\'ll receive a customized quote based on your event needs.') }}</li>
                                    <li>{{ __('Once confirmed, we\'ll finalize all the details for your event.') }}</li>
                                </ol>
                            </div>

                            <div class="action_buttons">
                                <a href="{{ route('website.catering.index') }}" class="common_btn">
                                    <i class="fas fa-utensils me-2"></i>{{ __('Browse More Packages') }}
                                </a>
                                <a href="{{ route('website.index') }}" class="common_btn btn_outline">
                                    <i class="fas fa-home me-2"></i>{{ __('Back to Home') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========SUCCESS SECTION END===========-->

@endsection

@push('styles')
<style>
    .success_card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 50px;
        text-align: center;
    }

    .success_icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #71dd37, #5cb82f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }

    .success_icon i {
        font-size: 50px;
        color: #fff;
    }

    .success_card h2 {
        margin-bottom: 10px;
        font-weight: 700;
    }

    .success_card .lead {
        color: #666;
        margin-bottom: 30px;
    }

    .inquiry_details {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        text-align: left;
        margin-bottom: 30px;
    }

    .detail_header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .inquiry_number {
        font-size: 20px;
        font-weight: 700;
        color: #333;
    }

    .status_badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status_pending {
        background: #fff3cd;
        color: #856404;
    }

    .status_contacted {
        background: #cce5ff;
        color: #004085;
    }

    .status_quoted {
        background: #d4edda;
        color: #155724;
    }

    .detail_grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .detail_item {
        padding: 10px 0;
    }

    .detail_item label {
        display: block;
        font-size: 12px;
        color: #888;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .detail_item span {
        font-weight: 600;
        color: #333;
    }

    .detail_item.highlight span {
        color: #ff6b35;
        font-size: 18px;
    }

    .detail_full {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .detail_full label {
        display: block;
        font-size: 12px;
        color: #888;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .detail_full p {
        margin: 0;
        color: #333;
    }

    .next_steps {
        background: #fff3e0;
        border-radius: 15px;
        padding: 25px;
        text-align: left;
        margin-bottom: 30px;
    }

    .next_steps h5 {
        margin-bottom: 15px;
        color: #ff6b35;
    }

    .next_steps ol {
        margin: 0;
        padding-left: 20px;
    }

    .next_steps li {
        padding: 5px 0;
        color: #666;
    }

    .action_buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
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

        .detail_grid {
            grid-template-columns: 1fr;
        }

        .detail_header {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .action_buttons {
            flex-direction: column;
        }
    }
</style>
@endpush
