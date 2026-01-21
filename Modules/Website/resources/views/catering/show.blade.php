@extends('website::layouts.master')

@section('title', $package->name . ' - ' . __('Catering') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $package->name }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.catering.index') }}">{{ __('Catering') }}</a></li>
                                <li><a href="#">{{ $package->name }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========PACKAGE DETAILS START===========-->
        <section class="package_details pt_120 xs_pt_100 pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Package Image -->
                        <div class="package_hero wow fadeInUp mb-4">
                            <img src="{{ $package->image_url }}" alt="{{ $package->name }}" class="img-fluid rounded-3">
                            @if($package->is_featured)
                                <span class="featured_badge"><i class="fas fa-star me-1"></i>{{ __('Featured Package') }}</span>
                            @endif
                        </div>

                        <!-- Package Description -->
                        <div class="package_description wow fadeInUp mb-4">
                            <h3>{{ __('About This Package') }}</h3>
                            @if($package->long_description)
                                {!! nl2br(e($package->long_description)) !!}
                            @else
                                <p>{{ $package->description }}</p>
                            @endif
                        </div>

                        <!-- What's Included -->
                        @if($package->includes && count($package->includes) > 0)
                            <div class="package_includes_section wow fadeInUp mb-4">
                                <h3>{{ __("What's Included") }}</h3>
                                <div class="includes_grid">
                                    @foreach($package->includes as $include)
                                        <div class="include_item">
                                            <i class="fas fa-check-circle"></i>
                                            <span>{{ $include }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <!-- Pricing Card -->
                        <div class="pricing_card wow fadeInUp mb-4">
                            <div class="pricing_header">
                                <h4>{{ __('Package Pricing') }}</h4>
                            </div>
                            <div class="pricing_body">
                                <div class="price_main">
                                    <span class="currency">$</span>
                                    <span class="amount">{{ number_format($package->price_per_person, 2) }}</span>
                                    <span class="per">/{{ __('person') }}</span>
                                </div>

                                <div class="price_info">
                                    <div class="info_row">
                                        <span>{{ __('Minimum Guests') }}</span>
                                        <strong>{{ $package->min_guests }}</strong>
                                    </div>
                                    <div class="info_row">
                                        <span>{{ __('Maximum Guests') }}</span>
                                        <strong>{{ $package->max_guests }}</strong>
                                    </div>
                                    <hr>
                                    <div class="info_row highlight">
                                        <span>{{ __('Price Range') }}</span>
                                        <strong>{{ $package->price_range['formatted'] }}</strong>
                                    </div>
                                </div>

                                <!-- Price Calculator -->
                                <div class="price_calculator mt-4">
                                    <label>{{ __('Calculate Your Price') }}</label>
                                    <div class="calculator_input">
                                        <input type="number" id="guestCount"
                                               min="{{ $package->min_guests }}"
                                               max="{{ $package->max_guests }}"
                                               value="{{ $package->min_guests }}"
                                               placeholder="{{ __('Number of guests') }}">
                                        <span>{{ __('guests') }}</span>
                                    </div>
                                    <div class="calculated_price" id="calculatedPrice">
                                        <span class="label">{{ __('Estimated Total') }}:</span>
                                        <span class="price">${{ number_format($package->min_guests * $package->price_per_person, 2) }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('website.catering.inquiry') }}?package={{ $package->id }}" class="common_btn w-100 mt-4">
                                    <i class="fas fa-envelope me-2"></i>{{ __('Request a Quote') }}
                                </a>
                            </div>
                        </div>

                        <!-- Contact Card -->
                        <div class="contact_card wow fadeInUp">
                            <h5><i class="fas fa-phone-alt me-2"></i>{{ __('Need Help?') }}</h5>
                            <p>{{ __('Contact us for custom packages or special requirements.') }}</p>
                            <a href="{{ route('website.contact') }}" class="common_btn btn_outline w-100">
                                {{ __('Contact Us') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Related Packages -->
                @if($relatedPackages->count() > 0)
                    <div class="related_packages mt-5 pt-5 border-top">
                        <div class="row">
                            <div class="col-12 wow fadeInUp">
                                <h3 class="mb-4">{{ __('Other Packages You Might Like') }}</h3>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($relatedPackages as $related)
                                <div class="col-lg-4 col-md-6 wow fadeInUp mb-4">
                                    <div class="package_card_mini">
                                        <div class="card_image">
                                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}">
                                        </div>
                                        <div class="card_content">
                                            <h5>{{ $related->name }}</h5>
                                            <p>${{ number_format($related->price_per_person, 2) }}/person</p>
                                            <a href="{{ route('website.catering.show', $related) }}" class="btn_link">
                                                {{ __('View Details') }} <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
        <!--==========PACKAGE DETAILS END===========-->
@endsection

@push('styles')
<style>
    .package_hero {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
    }

    .package_hero img {
        width: 100%;
        height: auto;
    }

    .package_hero .featured_badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #ff6b35;
        color: #fff;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
    }

    .package_description,
    .package_includes_section {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .package_description h3,
    .package_includes_section h3 {
        margin-bottom: 20px;
        font-weight: 600;
    }

    .includes_grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .include_item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .include_item i {
        color: #71dd37;
        font-size: 18px;
    }

    .pricing_card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .pricing_header {
        background: linear-gradient(135deg, #ff6b35, #f54749);
        color: #fff;
        padding: 20px;
        text-align: center;
    }

    .pricing_header h4 {
        margin-bottom: 0;
    }

    .pricing_body {
        padding: 25px;
    }

    .price_main {
        text-align: center;
        margin-bottom: 25px;
    }

    .price_main .currency {
        font-size: 24px;
        color: #ff6b35;
        vertical-align: top;
    }

    .price_main .amount {
        font-size: 48px;
        font-weight: 700;
        color: #333;
    }

    .price_main .per {
        font-size: 16px;
        color: #888;
    }

    .price_info .info_row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .price_info .info_row:last-child {
        border-bottom: none;
    }

    .price_info .info_row.highlight {
        background: #f8f9fa;
        margin: 0 -25px;
        padding: 15px 25px;
    }

    .price_info .info_row.highlight strong {
        color: #ff6b35;
    }

    .price_calculator {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
    }

    .price_calculator label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .calculator_input {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .calculator_input input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
    }

    .calculated_price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed #ddd;
    }

    .calculated_price .price {
        font-size: 24px;
        font-weight: 700;
        color: #ff6b35;
    }

    .contact_card {
        background: #fff;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .contact_card h5 {
        margin-bottom: 10px;
    }

    .contact_card p {
        color: #666;
        margin-bottom: 15px;
    }

    .common_btn.btn_outline {
        background: transparent;
        border: 2px solid #ff6b35;
        color: #ff6b35;
    }

    .package_card_mini {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        display: flex;
        transition: transform 0.3s;
    }

    .package_card_mini:hover {
        transform: translateY(-3px);
    }

    .package_card_mini .card_image {
        width: 120px;
        flex-shrink: 0;
    }

    .package_card_mini .card_image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .package_card_mini .card_content {
        padding: 15px;
        flex: 1;
    }

    .package_card_mini h5 {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .package_card_mini p {
        color: #ff6b35;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .btn_link {
        color: #ff6b35;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }

    .btn_link i {
        margin-left: 5px;
        transition: transform 0.3s;
    }

    .btn_link:hover i {
        transform: translateX(5px);
    }

    @media (max-width: 768px) {
        .includes_grid {
            grid-template-columns: 1fr;
        }

        .package_card_mini {
            flex-direction: column;
        }

        .package_card_mini .card_image {
            width: 100%;
            height: 150px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const guestInput = document.getElementById('guestCount');
    const priceDisplay = document.getElementById('calculatedPrice').querySelector('.price');
    const pricePerPerson = {{ $package->price_per_person }};
    const minGuests = {{ $package->min_guests }};
    const maxGuests = {{ $package->max_guests }};

    guestInput.addEventListener('input', function() {
        let guests = parseInt(this.value) || minGuests;
        guests = Math.max(minGuests, Math.min(maxGuests, guests));
        const total = guests * pricePerPerson;
        priceDisplay.textContent = '$' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    });
});
</script>
@endpush
