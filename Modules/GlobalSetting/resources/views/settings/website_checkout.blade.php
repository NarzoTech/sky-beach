@extends('admin.layouts.master')

@section('title', __('Website Checkout Settings'))

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.settings') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>
                <h1>{{ __('Website Checkout Settings') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item active">
                        <a href="{{ route('admin.settings') }}">{{ __('Settings') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Website Checkout') }}</div>
                </div>
            </div>

            <div class="section-body">
                <form action="{{ route('admin.update-website-checkout-settings') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Tax Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4><i class="fas fa-percent mr-2"></i>{{ __('Tax Settings') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="website_tax_enabled"
                                                name="website_tax_enabled" value="1"
                                                {{ ($setting->website_tax_enabled ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="website_tax_enabled">
                                                {{ __('Enable Tax on Website Orders') }}
                                            </label>
                                        </div>
                                        <small class="text-muted">{{ __('When enabled, tax will be calculated on all website orders') }}</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="website_tax_rate">{{ __('Tax Rate (%)') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="website_tax_rate"
                                                name="website_tax_rate" step="0.01" min="0" max="100"
                                                value="{{ $setting->website_tax_rate ?? '15' }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ __('Default: 15%') }}</small>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-calculator mr-2"></i>
                                        <strong>{{ __('Preview') }}:</strong>
                                        {{ __('Tax on') }} {{ currency(1000) }} = <span id="tax-preview">{{ currency(150) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Fee Settings -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4><i class="fas fa-truck mr-2"></i>{{ __('Delivery Fee Settings') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="website_delivery_fee_enabled"
                                                name="website_delivery_fee_enabled" value="1"
                                                {{ ($setting->website_delivery_fee_enabled ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="website_delivery_fee_enabled">
                                                {{ __('Enable Delivery Fee') }}
                                            </label>
                                        </div>
                                        <small class="text-muted">{{ __('When enabled, delivery fee will be charged on delivery orders') }}</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="website_delivery_fee">{{ __('Delivery Fee Amount') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ currency_icon() }}</span>
                                            </div>
                                            <input type="number" class="form-control" id="website_delivery_fee"
                                                name="website_delivery_fee" step="0.01" min="0"
                                                value="{{ $setting->website_delivery_fee ?? '50' }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="website_free_delivery_threshold">{{ __('Free Delivery Above') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ currency_icon() }}</span>
                                            </div>
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
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4><i class="fas fa-star mr-2"></i>{{ __('Membership Points Settings') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="website_loyalty_enabled"
                                                        name="website_loyalty_enabled" value="1"
                                                        {{ ($setting->website_loyalty_enabled ?? '1') == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="website_loyalty_enabled">
                                                        {{ __('Enable Loyalty Points on Website Orders') }}
                                                    </label>
                                                </div>
                                                <small class="text-muted">{{ __('When enabled, customers will earn points on their orders based on their phone number') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                {{ __('Points earning rules are managed in') }}
                                                <a href="{{ route('admin.membership.programs.index') }}" class="alert-link">
                                                    {{ __('Membership Programs') }} <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-muted mb-3">{{ __('How it works') }}:</h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    {{ __('Customers are automatically registered using their phone number') }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    {{ __('Points are earned after successful order completion') }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    {{ __('Points can be viewed and redeemed in future orders') }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save mr-2"></i>{{ __('Save Settings') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('js')
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
