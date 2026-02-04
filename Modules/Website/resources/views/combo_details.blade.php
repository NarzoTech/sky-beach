@extends('website::layouts.master')

@section('title', $combo->name . ' - ' . config('app.name'))

@section('content')
    <!--==========BREADCRUMB AREA START===========-->
    <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
        <div class="container">
            <div class="row wow fadeInUp">
                <div class="col-12">
                    <div class="breadcrumb_text">
                        <h1>{{ $combo->name }}</h1>
                        <ul>
                            <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                            <li><a href="{{ route('website.menu') }}">{{ __('Menu') }}</a></li>
                            <li><a href="#">{{ $combo->name }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========BREADCRUMB AREA END===========-->


    <!--==========COMBO DETAILS START===========-->
    <section class="menu_details pt_120 xs_pt_100">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-md-8 col-lg-5 wow fadeInLeft">
                    <div class="menu_det_slider_area">
                        <div class="row slider-for">
                            <div class="col-12">
                                <div class="details_large_img">
                                    @if ($combo->image)
                                        <img src="{{ asset($combo->image) }}" alt="{{ $combo->name }}" class="img-fluid w-100">
                                    @else
                                        <img src="{{ asset('website/images/combo_default.jpg') }}" alt="{{ $combo->name }}" class="img-fluid w-100">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-md-8 col-lg-7 wow fadeInUp">
                    <div class="menu_det_text">
                        <div class="menu_det_text_area d-flex align-items-center gap-3 flex-wrap mb-2">
                            <h2 class="details_title mb-0">{{ $combo->name }}</h2>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-gift me-1"></i>{{ __('COMBO') }}
                            </span>
                        </div>

                        <p class="price" id="displayPrice">
                            @if ($combo->original_price > $combo->combo_price)
                                <span style="text-decoration: line-through; color: #999; font-size: 18px; margin-right: 8px;">{{ currency($combo->original_price) }}</span>
                                <span style="color: var(--colorPrimary);">{{ currency($combo->combo_price) }}</span>
                                @if ($combo->savings > 0)
                                    <span class="badge bg-success ms-2">{{ __('Save') }} {{ currency($combo->savings) }}</span>
                                @endif
                            @else
                                <span>{{ currency($combo->combo_price) }}</span>
                            @endif
                        </p>

                        @if ($combo->description)
                            <div class="details_short_description mb-3">
                                {!! $combo->description !!}
                            </div>
                        @endif

                        <!-- Duration Info -->
                        @if ($combo->start_date || $combo->end_date)
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-calendar me-2"></i>
                                @if ($combo->start_date && $combo->end_date)
                                    {{ __('Valid from') }} {{ $combo->start_date->format('M d, Y') }}
                                    {{ __('to') }} {{ $combo->end_date->format('M d, Y') }}
                                @elseif ($combo->end_date)
                                    {{ __('Offer ends') }} {{ $combo->end_date->format('M d, Y') }}
                                @elseif ($combo->start_date)
                                    {{ __('Starts') }} {{ $combo->start_date->format('M d, Y') }}
                                @endif
                            </div>
                        @endif

                        <!-- Combo Items List -->
                        <div class="combo_items_list mb-4">
                            <h5 class="mb-3"><i class="fas fa-list-ul me-2"></i>{{ __("What's Included") }}</h5>
                            <div class="row g-3">
                                @foreach ($combo->comboItems as $comboItem)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded">
                                            @if ($comboItem->menuItem && $comboItem->menuItem->image)
                                                <img src="{{ $comboItem->menuItem->image_url }}"
                                                    alt="{{ $comboItem->menuItem->name }}"
                                                    class="rounded me-3"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="rounded me-3 bg-secondary d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-utensils text-white"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h6 class="mb-1">{{ $comboItem->menuItem->name ?? __('Item') }}</h6>
                                                @if ($comboItem->variant)
                                                    <small class="text-muted">{{ $comboItem->variant->name }}</small>
                                                @endif
                                                <div class="text-primary fw-bold">
                                                    {{ __('Qty') }}: {{ $comboItem->quantity }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Quantity & Total -->
                        <div class="details_quentity">
                            <div class="quentity_btn_area d-flex flex-wrapa align-items-center">
                                <div class="quentity_btn">
                                    <button type="button" class="btn btn-danger" id="decreaseQty"><i
                                            class="fal fa-minus"></i></button>
                                    <input type="text" id="quantity" value="1" readonly>
                                    <button type="button" class="btn btn-success" id="increaseQty"><i
                                            class="fal fa-plus"></i></button>
                                </div>
                                <h3 id="totalPrice">{{ currency($combo->combo_price) }}</h3>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="details_cart_btn">
                            <button type="button" class="common_btn" id="addComboToCartBtn" data-combo-id="{{ $combo->id }}">
                                <span class="icon">
                                    <img src="{{ asset('website/images/cart_icon_1.png') }}" alt="cart"
                                        class="img-fluid w-100">
                                </span>
                                {{ __('Add to Cart') }}
                            </button>
                            <button type="button" class="common_btn" id="buyNowBtn" data-combo-id="{{ $combo->id }}">
                                {{ __('Buy Now') }}
                            </button>
                        </div>

                        <!-- Share -->
                        <ul class="share">
                            <li>{{ __('Share with friends') }}:</li>
                            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                    target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($combo->name) }}"
                                    target="_blank"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}"
                                    target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="https://wa.me/?text={{ urlencode($combo->name . ' - ' . request()->url()) }}"
                                    target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==========COMBO DETAILS END===========-->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const basePrice = {{ $combo->combo_price }};
            let quantity = 1;

            const quantityInput = document.getElementById('quantity');
            const totalPrice = document.getElementById('totalPrice');
            const decreaseBtn = document.getElementById('decreaseQty');
            const increaseBtn = document.getElementById('increaseQty');

            function updateDisplay() {
                totalPrice.textContent = '{{ currency_icon() }}' + (basePrice * quantity).toFixed(2);
            }

            // Quantity controls
            decreaseBtn.addEventListener('click', function() {
                if (quantity > 1) {
                    quantity--;
                    quantityInput.value = quantity;
                    updateDisplay();
                }
            });

            increaseBtn.addEventListener('click', function() {
                if (quantity < 99) {
                    quantity++;
                    quantityInput.value = quantity;
                    updateDisplay();
                }
            });

            // Add Combo to Cart
            document.getElementById('addComboToCartBtn').addEventListener('click', function() {
                addComboToCart(false);
            });

            // Buy Now
            document.getElementById('buyNowBtn').addEventListener('click', function() {
                addComboToCart(true);
            });

            function addComboToCart(buyNow) {
                const comboId = {{ $combo->id }};

                fetch('{{ route('website.cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            combo_id: comboId,
                            quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart badge in header
                            const badge = document.querySelector('.cart-badge');
                            if (badge) {
                                badge.textContent = data.cart_count;
                                badge.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
                            }

                            if (buyNow) {
                                window.location.href = '{{ route('website.checkout.index') }}';
                            } else {
                                showToast('{{ $combo->name }} {{ __('added to cart!') }}', 'success');
                            }
                        } else {
                            showToast(data.message || '{{ __('Failed to add combo to cart.') }}', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('{{ __('An error occurred. Please try again.') }}', 'error');
                    });
            }

            // Show toast notification
            function showToast(message, type = 'success') {
                const existingToast = document.querySelector('.toast-notification');
                if (existingToast) {
                    existingToast.remove();
                }

                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 25px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 500;
                    z-index: 9999;
                    animation: slideIn 0.3s ease;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    ${type === 'success' ? 'background: #28a745;' : 'background: #dc3545;'}
                `;
                toast.textContent = message;

                if (!document.getElementById('toast-animation-styles')) {
                    const style = document.createElement('style');
                    style.id = 'toast-animation-styles';
                    style.textContent = `
                        @keyframes slideIn {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                        @keyframes slideOut {
                            from { transform: translateX(0); opacity: 1; }
                            to { transform: translateX(100%); opacity: 0; }
                        }
                    `;
                    document.head.appendChild(style);
                }

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        });
    </script>
@endpush
