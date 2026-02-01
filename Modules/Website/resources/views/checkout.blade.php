@extends('website::layouts.master')

@section('title', __('Checkout') . ' - ' . config('app.name'))

@section('content')
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
                                                value="{{ old('first_name', $user->first_name ?? $savedCheckoutData['first_name'] ?? '') }}" required>
                                            @error('first_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="last_name" placeholder="{{ __('Last Name') }} *"
                                                value="{{ old('last_name', $user->last_name ?? $savedCheckoutData['last_name'] ?? '') }}" required>
                                            @error('last_name')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="tel" name="phone" id="phone" placeholder="01XXX-XXXXXX *"
                                                value="{{ old('phone', $user->phone ?? $savedCheckoutData['phone'] ?? '') }}" required
                                                maxlength="12" pattern="01[3-9][0-9]{2}-?[0-9]{6}"
                                                title="{{ __('Enter a valid Bangladesh mobile number (e.g., 01712-345678)') }}">
                                            @error('phone')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="email" name="email" placeholder="{{ __('Email (Optional)') }}"
                                                value="{{ old('email', $user->email ?? $savedCheckoutData['email'] ?? '') }}">
                                            @error('email')
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
                                                    value="{{ old('address', $savedCheckoutData['address'] ?? '') }}">
                                                @error('address')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="city" id="city" placeholder="{{ __('City') }} *"
                                                    value="{{ old('city', $savedCheckoutData['city'] ?? '') }}">
                                                @error('city')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="postal_code" placeholder="{{ __('Postal Code') }}"
                                                    value="{{ old('postal_code', $savedCheckoutData['postal_code'] ?? '') }}">
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
                                    <div class="col-md-6">
                                        <div class="payment-method-card" data-payment="cash">
                                            <input type="radio" name="payment_method" value="cash" id="pay_cash" checked>
                                            <label for="pay_cash">
                                                <i class="fas fa-money-bill-wave fa-lg me-2 cod-icon"></i>
                                                <span class="cod-text">{{ __('Cash on Delivery') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="payment-method-card" data-payment="bkash">
                                            <input type="radio" name="payment_method" value="bkash" id="pay_bkash">
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
                                                {{ currency($item->subtotal) }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="cart_summery">
                                    <h6>{{ __('Order Summary') }}</h6>
                                    <p>{{ __('Subtotal') }}: <span>{{ currency($cartTotal) }}</span></p>
                                    <p id="delivery-fee-row" style="{{ $deliveryFeeEnabled ? '' : 'display:none;' }}">
                                        {{ __('Delivery Fee') }}:
                                        <span id="delivery-fee">
                                            @if($freeDeliveryThreshold > 0 && $cartTotal >= $freeDeliveryThreshold)
                                                <span class="text-success">{{ __('FREE') }}</span>
                                            @else
                                                {{ currency($calculatedDeliveryFee) }}
                                            @endif
                                        </span>
                                    </p>
                                    @if($taxEnabled)
                                    <p>{{ __('Tax') }} ({{ $taxRate }}%): <span id="tax-amount">{{ currency($calculatedTax) }}</span></p>
                                    @endif
                                    <p class="total"><span>{{ __('Total') }}:</span> <span id="order-total">{{ currency($cartTotal + $calculatedDeliveryFee + $calculatedTax) }}</span></p>
                                    @if($freeDeliveryThreshold > 0 && $cartTotal < $freeDeliveryThreshold)
                                    <p class="text-info small" id="free-delivery-hint">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('Add') }} {{ currency($freeDeliveryThreshold - $cartTotal) }} {{ __('more for free delivery!') }}
                                    </p>
                                    @endif

                                    <button type="submit" class="common_btn w-100" id="place-order-btn">
                                        <i class="fas fa-check-circle me-2"></i>{{ __('Place Order') }}
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
        border-color: var(--colorPrimary, #AB162C);
    }

    .payment-method-card:has(input[value="bkash"]:checked) {
        border-color: #e2136e !important;
        background-color: rgba(226, 19, 110, 0.05) !important;
    }

    .payment-method-card:has(input[value="bkash"]:checked) label span {
        color: #e2136e !important;
    }

    .payment-method-card:has(input[value="cash"]:checked) {
        border-color: var(--colorPrimary) !important;
        background-color: rgba(171, 22, 44, 0.05) !important;
    }

    .payment-method-card:has(input[value="cash"]:checked) label span,
    .payment-method-card:has(input[value="cash"]:checked) .cod-text {
        color: var(--colorPrimary) !important;
    }

    .payment-method-card:has(input[value="cash"]:checked) .cod-icon {
        color: var(--colorPrimary) !important;
    }

    .cod-icon {
        color: var(--colorPrimary);
    }

    .cod-text {
        color: var(--colorPrimary);
        font-weight: 600;
    }

    .order-type-card input,
    .payment-method-card input {
        display: none;
    }

    .order-type-card input:checked + label,
    .payment-method-card input:checked + label {
        color: var(--colorPrimary, #AB162C);
    }

    .order-type-card:has(input:checked) {
        border-color: var(--colorPrimary, #AB162C);
        background-color: rgba(171, 22, 44, 0.05);
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
        color: var(--colorPrimary, #AB162C);
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
        border-color: var(--colorPrimary, #AB162C);
        outline: none;
    }

    #place-order-btn {
        padding: 15px 30px;
        font-size: 16px;
        background: var(--colorPrimary) !important;
        color: #fff !important;
        border: none !important;
    }

    #place-order-btn:hover {
        background: var(--colorPrimary) !important;
        color: #fff !important;
        border: none !important;
    }

    #place-order-btn:hover::before,
    #place-order-btn:hover::after {
        display: none !important;
    }

    #place-order-btn i {
        color: #fff !important;
    }

    #place-order-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Cart Summary Styling */
    .cart_summery {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .cart_summery h6 {
        color: var(--colorPrimary);
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--colorPrimary);
    }

    .cart_summery p {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: #555;
        font-size: 15px;
    }

    .cart_summery p span {
        font-weight: 600;
        color: #333;
    }

    .cart_summery p.total {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px dashed #ddd;
        font-size: 18px;
        font-weight: 700;
    }

    .cart_summery p.total span:first-child {
        color: #333;
    }

    .cart_summery p.total span:last-child {
        color: var(--colorPrimary);
        font-size: 20px;
    }

    .cart_summery a {
        color: var(--colorPrimary);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .cart_summery a:hover {
        color: var(--colorYellow);
    }

    .cart_summery .text-info {
        color: var(--colorPrimary) !important;
        background: rgba(171, 22, 44, 0.1);
        padding: 8px 12px;
        border-radius: 5px;
        margin-top: 10px;
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

        const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
        const deliverySection = document.getElementById('delivery-address-section');
        const deliveryFeeRow = document.getElementById('delivery-fee-row');
        const addressInput = document.getElementById('address');
        const cityInput = document.getElementById('city');

        // Pricing variables from server
        const subtotal = {{ $cartTotal }};
        const deliveryFeeAmount = {{ $calculatedDeliveryFee }};
        const taxAmount = {{ $calculatedTax }};
        const deliveryFeeEnabled = {{ $deliveryFeeEnabled ? 'true' : 'false' }};
        const freeDeliveryThreshold = {{ $freeDeliveryThreshold }};
        const isFreeDelivery = freeDeliveryThreshold > 0 && subtotal >= freeDeliveryThreshold;

        // Function to update totals
        function updateTotals(isDelivery) {
            let currentDeliveryFee = 0;
            if (isDelivery && deliveryFeeEnabled && !isFreeDelivery) {
                currentDeliveryFee = deliveryFeeAmount;
            }
            const total = subtotal + currentDeliveryFee + taxAmount;
            document.getElementById('order-total').textContent = '{{ currency_icon() }}' + total.toFixed(2);
        }

        // Toggle delivery address section based on order type
        orderTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'delivery') {
                    deliverySection.style.display = 'block';
                    if (deliveryFeeEnabled) {
                        deliveryFeeRow.style.display = 'block';
                    }
                    addressInput.setAttribute('required', '');
                    cityInput.setAttribute('required', '');
                    updateTotals(true);
                } else {
                    deliverySection.style.display = 'none';
                    deliveryFeeRow.style.display = 'none';
                    addressInput.removeAttribute('required');
                    cityInput.removeAttribute('required');
                    updateTotals(false);
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
                radio.dispatchEvent(new Event('change'));
            });
        });

        // Update button based on payment method
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const submitBtn = document.getElementById('place-order-btn');

        function updateSubmitButton(paymentMethod) {
            if (paymentMethod === 'bkash') {
                submitBtn.style.background = 'var(--colorPrimary)';
                submitBtn.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>{{ __("Pay with bKash") }}';
            } else {
                submitBtn.style.background = '';
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>{{ __("Place Order") }}';
            }
        }

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateSubmitButton(this.value);
            });
        });

        // Form submission
        const form = document.getElementById('checkout-form');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Disable button to prevent double submission
            submitBtn.disabled = true;

            if (paymentMethod === 'bkash') {
                // bKash payment flow
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Connecting to bKash...") }}';

                const formData = new FormData(form);

                fetch('{{ route("website.bkash.create") }}', {
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
                        window.location.href = data.bkashURL;
                    } else {
                        showError(data.message || '{{ __("Failed to initiate payment. Please try again.") }}');
                        resetButton('bkash');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('{{ __("Something went wrong. Please try again.") }}');
                    resetButton('bkash');
                });
            } else {
                // Cash on Delivery flow
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Placing Order...") }}';

                const formData = new FormData(form);

                fetch('{{ route("website.checkout.process") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Order Placed!") }}',
                                text: '{{ __("Your order has been placed successfully.") }}',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            window.location.href = data.redirect_url;
                        }
                    } else {
                        showError(data.message || '{{ __("Failed to place order. Please try again.") }}');
                        resetButton('cash');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('{{ __("Something went wrong. Please try again.") }}');
                    resetButton('cash');
                });
            }
        });

        function showError(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("Error") }}',
                    text: message
                });
            } else {
                alert(message);
            }
        }

        function resetButton(paymentMethod) {
            submitBtn.disabled = false;
            updateSubmitButton(paymentMethod);
        }
    });
</script>
@endpush
