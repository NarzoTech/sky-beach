@extends('website::layouts.master')

@section('title', __('Checkout') . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Checkout') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.cart.index') }}">{{ __('Cart') }}</a></li>
                                <li><a href="#">{{ __('Checkout') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========CHECKOUT START===========-->
        <section class="checkout pt_110 xs_pt_90">
            <div class="container">
                <form id="checkout-form" action="{{ route('website.bkash.create') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8 wow fadeInLeft">
                            <div class="checkout_area">
                                <!-- Order Type Selection -->
                                <h2>{{ __('Order Type') }}</h2>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="order-type-card" data-type="delivery">
                                            <input type="radio" name="order_type" value="delivery" id="type_delivery" checked>
                                            <label for="type_delivery">
                                                <i class="fas fa-motorcycle fa-2x mb-2"></i>
                                                <span>{{ __('Delivery') }}</span>
                                                <small>{{ __('Get it delivered to your door') }}</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="order-type-card" data-type="take_away">
                                            <input type="radio" name="order_type" value="take_away" id="type_takeaway">
                                            <label for="type_takeaway">
                                                <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                                <span>{{ __('Take Away') }}</span>
                                                <small>{{ __('Pick up from our location') }}</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <h2>{{ __('Contact Details') }}</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="first_name" placeholder="{{ __('First Name') }} *"
                                                value="{{ $user->first_name ?? old('first_name') }}" required>
                                            @error('first_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="last_name" placeholder="{{ __('Last Name') }} *"
                                                value="{{ $user->last_name ?? old('last_name') }}" required>
                                            @error('last_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="tel" name="phone" placeholder="{{ __('Phone Number') }} *"
                                                value="{{ $user->phone ?? old('phone') }}" required>
                                            @error('phone')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery Address Section -->
                                <div id="delivery-address-section">
                                    <h2>{{ __('Delivery Address') }}</h2>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" name="address" id="address" placeholder="{{ __('Street Address') }} *"
                                                    value="{{ old('address') }}">
                                                @error('address')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="city" id="city" placeholder="{{ __('City') }} *"
                                                    value="{{ old('city') }}">
                                                @error('city')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="postal_code" placeholder="{{ __('Postal Code') }}"
                                                    value="{{ old('postal_code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea name="delivery_notes" rows="3" placeholder="{{ __('Delivery Instructions (Optional)') }}">{{ old('delivery_notes') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <h2>{{ __('Payment Method') }}</h2>
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="payment-method-card" style="border-color: #e2136e; background-color: rgba(226, 19, 110, 0.05);">
                                            <input type="radio" name="payment_method" value="bkash" id="pay_bkash" checked>
                                            <label for="pay_bkash">
                                                <img src="{{ asset('website/images/bkash-logo.png') }}" alt="bKash" style="height: 30px; margin-right: 10px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                <i class="fas fa-mobile-alt fa-lg me-2" style="display: none; color: #e2136e;"></i>
                                                <span style="color: #e2136e; font-weight: 600;">{{ __('bKash') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-7 wow fadeInRight">
                            <div class="checkout_sidebar">
                                <h2>{{ __('Your Order') }}</h2>
                                <div class="order_items">
                                    @foreach($cartItems as $item)
                                    <div class="order_item">
                                        <div class="d-flex justify-content-between">
                                            <div class="item_info">
                                                <strong>{{ $item->menuItem->name ?? 'Item' }}</strong>
                                                <span class="quantity">x{{ $item->quantity }}</span>
                                                @if($item->variant_name)
                                                    <small class="d-block text-muted">{{ $item->variant_name }}</small>
                                                @endif
                                                @if(!empty($item->addon_names))
                                                    <small class="d-block text-muted">+ {{ implode(', ', $item->addon_names) }}</small>
                                                @endif
                                            </div>
                                            <div class="item_price">
                                                TK {{ number_format($item->subtotal, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="cart_summery">
                                    <h6>{{ __('Order Summary') }}</h6>
                                    <p>{{ __('Subtotal') }}: <span>TK {{ number_format($cartTotal, 2) }}</span></p>
                                    <p id="delivery-fee-row">{{ __('Delivery Fee') }}: <span id="delivery-fee">TK 0.00</span></p>
                                    <p>{{ __('Tax') }}: <span>TK 0.00</span></p>
                                    <p class="total"><span>{{ __('Total') }}:</span> <span id="order-total">TK {{ number_format($cartTotal, 2) }}</span></p>

                                    <button type="submit" class="common_btn w-100" id="place-order-btn" style="background: linear-gradient(135deg, #e2136e 0%, #d1105d 100%);">
                                        <i class="fas fa-mobile-alt me-2"></i>{{ __('Pay with bKash') }}
                                    </button>

                                    <a href="{{ route('website.cart.index') }}" class="text-center d-block mt-3">
                                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Cart') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!--==========CHECKOUT END===========-->

    </div>
</div>
@endsection

@push('styles')
<style>
    .order-type-card,
    .payment-method-card {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .order-type-card:hover,
    .payment-method-card:hover {
        border-color: #e2136e;
    }

    .payment-method-card:has(input[value="bkash"]:checked) {
        border-color: #e2136e !important;
        background-color: rgba(226, 19, 110, 0.05) !important;
    }

    .payment-method-card:has(input[value="bkash"]:checked) label span {
        color: #e2136e !important;
    }

    .order-type-card input,
    .payment-method-card input {
        display: none;
    }

    .order-type-card input:checked + label,
    .payment-method-card input:checked + label {
        color: #ff6b35;
    }

    .order-type-card:has(input:checked),
    .payment-method-card:has(input:checked) {
        border-color: #ff6b35;
        background-color: rgba(255, 107, 53, 0.05);
    }

    .order-type-card label,
    .payment-method-card label {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        margin: 0;
    }

    .order-type-card label span,
    .payment-method-card label span {
        font-weight: 600;
        margin-top: 5px;
    }

    .order-type-card label small {
        color: #888;
        font-size: 12px;
    }

    .payment-method-card label {
        flex-direction: row;
        justify-content: center;
    }

    .order_items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 20px;
        padding-right: 10px;
    }

    .order_item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .order_item:last-child {
        border-bottom: none;
    }

    .order_item .quantity {
        color: #ff6b35;
        margin-left: 5px;
        font-size: 14px;
    }

    .checkout_area .form-group {
        margin-bottom: 20px;
    }

    .checkout_area input,
    .checkout_area textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .checkout_area input:focus,
    .checkout_area textarea:focus {
        border-color: #ff6b35;
        outline: none;
    }

    #place-order-btn {
        padding: 15px 30px;
        font-size: 16px;
    }

    #place-order-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
        const deliverySection = document.getElementById('delivery-address-section');
        const deliveryFeeRow = document.getElementById('delivery-fee-row');
        const addressInput = document.getElementById('address');
        const cityInput = document.getElementById('city');

        // Toggle delivery address section based on order type
        orderTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'delivery') {
                    deliverySection.style.display = 'block';
                    deliveryFeeRow.style.display = 'block';
                    addressInput.setAttribute('required', '');
                    cityInput.setAttribute('required', '');
                } else {
                    deliverySection.style.display = 'none';
                    deliveryFeeRow.style.display = 'none';
                    addressInput.removeAttribute('required');
                    cityInput.removeAttribute('required');
                }
            });
        });

        // Make order type cards clickable
        document.querySelectorAll('.order-type-card').forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            });
        });

        // Make payment method cards clickable
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });

        // Form submission - bKash Payment
        const form = document.getElementById('checkout-form');
        const submitBtn = document.getElementById('place-order-btn');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Connecting to bKash...") }}';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.bkashURL) {
                    // Redirect to bKash payment page
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __("Redirecting to bKash") }}',
                            text: '{{ __("Please complete your payment on bKash...") }}',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                    // Redirect to bKash
                    window.location.href = data.bkashURL;
                } else {
                    // Show error
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Payment Error") }}',
                            text: data.message || '{{ __("Failed to initiate payment. Please try again.") }}'
                        });
                    } else {
                        alert(data.message || '{{ __("Failed to initiate payment. Please try again.") }}');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>{{ __("Pay with bKash") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error") }}',
                        text: '{{ __("Something went wrong. Please try again.") }}'
                    });
                } else {
                    alert('{{ __("Something went wrong. Please try again.") }}');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>{{ __("Pay with bKash") }}';
            });
        });
    });
</script>
@endpush
