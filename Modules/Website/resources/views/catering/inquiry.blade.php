@extends('website::layouts.master')

@section('title', __('Catering Inquiry') . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Request a Quote') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.catering.index') }}">{{ __('Catering') }}</a></li>
                                <li><a href="#">{{ __('Request Quote') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========INQUIRY FORM START===========-->
        <section class="inquiry_section pt_120 xs_pt_100 pb_120 xs_pb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="inquiry_form_card wow fadeInUp">
                            <div class="form_header">
                                <h2>{{ __('Catering Inquiry Form') }}</h2>
                                <p>{{ __('Tell us about your event and we\'ll get back to you with a customized quote.') }}</p>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div id="formAlerts"></div>

                            <form id="inquiryForm" action="{{ route('website.catering.inquiry.submit') }}" method="POST">
                                @csrf

                                <!-- Package Selection -->
                                <div class="form_section">
                                    <h5><i class="fas fa-box-open me-2"></i>{{ __('Package Selection') }}</h5>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Select a Package (Optional)') }}</label>
                                        <select name="package_id" id="packageSelect" class="form-select">
                                            <option value="">{{ __('Custom / No Package') }}</option>
                                            @foreach($packages as $pkg)
                                                <option value="{{ $pkg->id }}"
                                                        data-min="{{ $pkg->min_guests }}"
                                                        data-max="{{ $pkg->max_guests }}"
                                                        data-price="{{ $pkg->price_per_person }}"
                                                        {{ (old('package_id') ?? request('package')) == $pkg->id ? 'selected' : '' }}>
                                                    {{ $pkg->name }} - ${{ number_format($pkg->price_per_person, 2) }}/person
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="packageInfo" class="package_info_box mt-2" style="display: none;">
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i><span id="guestRange"></span> guests |
                                                <i class="fas fa-dollar-sign ms-2 me-1"></i><span id="priceRange"></span>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="form_section">
                                    <h5><i class="fas fa-user me-2"></i>{{ __('Contact Information') }}</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Full Name') }} *</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Email Address') }} *</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Phone Number') }} *</label>
                                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Details -->
                                <div class="form_section">
                                    <h5><i class="fas fa-calendar-alt me-2"></i>{{ __('Event Details') }}</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Event Type') }} *</label>
                                            <select name="event_type" class="form-select" required>
                                                <option value="">{{ __('Select Event Type') }}</option>
                                                @foreach($eventTypes as $key => $label)
                                                    <option value="{{ $key }}" {{ old('event_type') == $key ? 'selected' : '' }}>
                                                        {{ __($label) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('event_type')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Number of Guests') }} *</label>
                                            <input type="number" name="guest_count" id="guestCount" class="form-control"
                                                   min="10" max="500" value="{{ old('guest_count', 10) }}" required>
                                            @error('guest_count')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Event Date') }} *</label>
                                            <input type="date" name="event_date" class="form-control"
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                                   value="{{ old('event_date') }}" required>
                                            @error('event_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Event Time (Optional)') }}</label>
                                            <input type="time" name="event_time" class="form-control" value="{{ old('event_time') }}">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">{{ __('Venue Address (Optional)') }}</label>
                                            <textarea name="venue_address" class="form-control" rows="2"
                                                      placeholder="{{ __('Where will the event be held?') }}">{{ old('venue_address') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Special Requirements -->
                                <div class="form_section">
                                    <h5><i class="fas fa-clipboard-list me-2"></i>{{ __('Special Requirements') }}</h5>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Additional Requirements or Dietary Restrictions') }}</label>
                                        <textarea name="special_requirements" class="form-control" rows="4"
                                                  placeholder="{{ __('Tell us about any dietary restrictions, allergies, theme preferences, or other special requirements...') }}">{{ old('special_requirements') }}</textarea>
                                    </div>
                                </div>

                                <!-- Price Estimate -->
                                <div id="priceEstimate" class="price_estimate" style="display: none;">
                                    <div class="estimate_content">
                                        <span class="label">{{ __('Estimated Price') }}:</span>
                                        <span class="price" id="estimatedPrice">$0.00</span>
                                    </div>
                                    <small class="text-muted">{{ __('Final price may vary based on specific requirements') }}</small>
                                </div>

                                <button type="submit" class="common_btn w-100" id="submitBtn">
                                    <span class="btn-text">{{ __('Submit Inquiry') }}</span>
                                    <span class="btn-loading" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i> {{ __('Submitting...') }}
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========INQUIRY FORM END===========-->

@endsection

@push('styles')
<style>
    .inquiry_form_card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 40px;
    }

    .form_header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form_header h2 {
        margin-bottom: 10px;
        font-weight: 700;
    }

    .form_header p {
        color: #666;
    }

    .form_section {
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 1px solid #eee;
    }

    .form_section:last-of-type {
        border-bottom: none;
    }

    .form_section h5 {
        margin-bottom: 20px;
        font-weight: 600;
        color: #333;
    }

    .form_section h5 i {
        color: #ff6b35;
    }

    .form-control, .form-select {
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .form-control:focus, .form-select:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .package_info_box {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
    }

    .price_estimate {
        background: linear-gradient(135deg, #ff6b35, #f54749);
        color: #fff;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        text-align: center;
    }

    .estimate_content {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-bottom: 5px;
    }

    .estimate_content .label {
        font-size: 16px;
    }

    .estimate_content .price {
        font-size: 32px;
        font-weight: 700;
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

    @media (max-width: 768px) {
        .inquiry_form_card {
            padding: 25px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inquiryForm');
    const packageSelect = document.getElementById('packageSelect');
    const guestInput = document.getElementById('guestCount');
    const packageInfo = document.getElementById('packageInfo');
    const priceEstimate = document.getElementById('priceEstimate');
    const estimatedPrice = document.getElementById('estimatedPrice');
    const submitBtn = document.getElementById('submitBtn');
    const formAlerts = document.getElementById('formAlerts');

    function updatePackageInfo() {
        const selected = packageSelect.options[packageSelect.selectedIndex];
        if (selected.value) {
            const min = selected.dataset.min;
            const max = selected.dataset.max;
            const price = parseFloat(selected.dataset.price);

            document.getElementById('guestRange').textContent = min + '-' + max;
            document.getElementById('priceRange').textContent = '$' + (min * price).toLocaleString() + ' - $' + (max * price).toLocaleString();
            packageInfo.style.display = 'block';

            guestInput.min = min;
            guestInput.max = max;
            if (parseInt(guestInput.value) < min) guestInput.value = min;
            if (parseInt(guestInput.value) > max) guestInput.value = max;

            updatePriceEstimate(price);
        } else {
            packageInfo.style.display = 'none';
            guestInput.min = 10;
            guestInput.max = 500;
            priceEstimate.style.display = 'none';
        }
    }

    function updatePriceEstimate(pricePerPerson) {
        const guests = parseInt(guestInput.value) || 10;
        const total = guests * pricePerPerson;
        estimatedPrice.textContent = '$' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        priceEstimate.style.display = 'block';
    }

    packageSelect.addEventListener('change', updatePackageInfo);
    guestInput.addEventListener('input', function() {
        const selected = packageSelect.options[packageSelect.selectedIndex];
        if (selected.value) {
            updatePriceEstimate(parseFloat(selected.dataset.price));
        }
    });

    // Initialize if package is pre-selected
    if (packageSelect.value) {
        updatePackageInfo();
    }

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
        .then(response => response.json())
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
                {{ __("An error occurred. Please try again.") }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush
