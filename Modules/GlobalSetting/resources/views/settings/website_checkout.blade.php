@extends('admin.layouts.master')

@section('title')
    {{ __('Website Checkout Settings') }}
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('Website Checkout Settings') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">{{ __('Settings') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Website Checkout') }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Settings') }}
    </a>
</div>

<form action="{{ route('admin.update-website-checkout-settings') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-8">
            <!-- Tax Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>{{ __('Tax Settings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="website_tax_enabled"
                                    name="website_tax_enabled" value="1"
                                    {{ ($setting->website_tax_enabled ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="website_tax_enabled">
                                    {{ __('Enable Tax on Website Orders') }}
                                </label>
                            </div>
                            <small class="text-muted">{{ __('When enabled, tax will be calculated on all website orders') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="website_tax_rate">{{ __('Tax Rate (%)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="website_tax_rate"
                                    name="website_tax_rate" step="0.01" min="0" max="100"
                                    value="{{ $setting->website_tax_rate ?? '15' }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">{{ __('Default: 15%') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Fee Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-car me-2"></i>{{ __('Delivery Fee Settings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="website_delivery_fee_enabled"
                                    name="website_delivery_fee_enabled" value="1"
                                    {{ ($setting->website_delivery_fee_enabled ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="website_delivery_fee_enabled">
                                    {{ __('Enable Delivery Fee') }}
                                </label>
                            </div>
                            <small class="text-muted">{{ __('When enabled, delivery fee will be charged on delivery orders') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="website_delivery_fee">{{ __('Delivery Fee Amount') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">{{ currency_icon() }}</span>
                                <input type="number" class="form-control" id="website_delivery_fee"
                                    name="website_delivery_fee" step="0.01" min="0"
                                    value="{{ $setting->website_delivery_fee ?? '50' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="website_free_delivery_threshold">{{ __('Free Delivery Above') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ currency_icon() }}</span>
                                <input type="number" class="form-control" id="website_free_delivery_threshold"
                                    name="website_free_delivery_threshold" step="0.01" min="0"
                                    value="{{ $setting->website_free_delivery_threshold ?? '0' }}">
                            </div>
                            <small class="text-muted">{{ __('Set 0 to disable free delivery threshold') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loyalty Points Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-star me-2"></i>{{ __('Membership Points Settings') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="website_loyalty_enabled"
                                    name="website_loyalty_enabled" value="1"
                                    {{ ($setting->website_loyalty_enabled ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="website_loyalty_enabled">
                                    {{ __('Enable Loyalty Points on Website Orders') }}
                                </label>
                            </div>
                            <small class="text-muted">{{ __('When enabled, customers will earn points on their orders based on their phone number') }}</small>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center mb-3">
                        <i class="bx bx-info-circle me-2 fs-5"></i>
                        <div>
                            {{ __('Points earning rules are managed in') }}
                            <a href="{{ route('membership.programs.index') }}" class="alert-link">
                                {{ __('Membership Programs') }} <i class="bx bx-link-external"></i>
                            </a>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">{{ __('How it works') }}:</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            {{ __('Customers are automatically registered using their phone number') }}
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            {{ __('Points are earned after successful order completion') }}
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            {{ __('Points can be viewed and redeemed in future orders') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Save Settings Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Actions') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ __('Review your settings and save changes.') }}</p>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-save me-1"></i> {{ __('Save Settings') }}
                    </button>
                </div>
            </div>

            <!-- Quick Info Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-help-circle me-2"></i>{{ __('Information') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        <strong>{{ __('Tax Settings') }}:</strong> {{ __('Configure tax calculation for website orders.') }}
                    </p>
                    <p class="text-muted small mb-2">
                        <strong>{{ __('Delivery Fee') }}:</strong> {{ __('Set delivery charges and free delivery threshold.') }}
                    </p>
                    <p class="text-muted small mb-0">
                        <strong>{{ __('Loyalty Points') }}:</strong> {{ __('Enable customer reward points system.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Tax preview calculation
        function updateTaxPreview() {
            var rate = parseFloat($('#website_tax_rate').val()) || 0;
            var tax = (1000 * rate / 100).toFixed(2);
            $('#tax-preview').text('{{ currency_icon() }}' + tax);
        }

        $('#website_tax_rate').on('input', updateTaxPreview);
        updateTaxPreview();
    });
</script>
@endpush
