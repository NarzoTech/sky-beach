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
                <div class="col-xl-4 col-md-8 col-lg-5 wow fadeInLeft">
                    <div class="menu_det_slider_area">
                        @php
                            $galleryImages = [];
                            if ($combo->gallery && is_array($combo->gallery)) {
                                foreach ($combo->gallery as $img) {
                                    $galleryImages[] = asset($img);
                                }
                            }
                        @endphp

                        @if (count($galleryImages) > 0)
                            <div class="row slider-for">
                                @foreach ($galleryImages as $image)
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ $image }}" alt="{{ $combo->name }}" class="img-fluid w-100">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (count($galleryImages) > 1)
                                <div class="row slider-nav">
                                    @foreach ($galleryImages as $image)
                                        <div class="col-xl-3">
                                            <div class="details_small_img">
                                                <img src="{{ $image }}" alt="{{ $combo->name }}" class="img-fluid w-100">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="row slider-for">
                                <div class="col-12">
                                    <div class="details_large_img">
                                        <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" class="img-fluid w-100">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-xl-5 col-md-8 col-lg-7 wow fadeInUp">
                    <div class="menu_det_text">
                        <div class="menu_det_text_area d-flex align-items-center gap-3 flex-wrap mb-2">
                            <h2 class="details_title mb-0">{{ $combo->name }}</h2>
                            <span class="badge bg-secondary">{{ __('Combo Pack') }}</span>
                        </div>

                        <p class="price" id="displayPrice">
                            @if ($combo->original_price > $combo->combo_price)
                                <span id="currentPrice" style="color: var(--colorPrimary); font-size: 28px; font-weight: 600;">{{ currency($combo->combo_price) }}</span>
                                <del style="color: #999; font-size: 16px; margin-left: 10px;">{{ currency($combo->original_price) }}</del>
                                @if ($combo->savings > 0)
                                    <span class="badge bg-danger ms-2" style="font-size: 12px; vertical-align: middle;">{{ __('Save') }} {{ currency($combo->savings) }}</span>
                                @endif
                            @else
                                <span id="currentPrice" style="font-size: 28px; font-weight: 600;">{{ currency($combo->combo_price) }}</span>
                            @endif
                        </p>

                        @if ($combo->description)
                            <div class="details_short_description">
                                {!! $combo->description !!}
                            </div>
                        @endif

                        <!-- Duration Info -->
                        @if ($combo->start_date || $combo->end_date)
                            <div class="dietary_info mb-3">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>
                                    @if ($combo->start_date && $combo->end_date)
                                        {{ __('Valid') }}: {{ $combo->start_date->format('M d') }} - {{ $combo->end_date->format('M d, Y') }}
                                    @elseif ($combo->end_date)
                                        {{ __('Ends') }}: {{ $combo->end_date->format('M d, Y') }}
                                    @elseif ($combo->start_date)
                                        {{ __('Starts') }}: {{ $combo->start_date->format('M d, Y') }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Combo Items -->
                        <div class="details_extra_item">
                            <h5>{{ __('Includes') }}</h5>
                            @foreach ($combo->comboItems as $comboItem)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked disabled>
                                    <label class="form-check-label">
                                        {{ $comboItem->quantity }}x {{ $comboItem->menuItem->name ?? __('Item') }}
                                        @if ($comboItem->variant)
                                            <span>({{ $comboItem->variant->name }})</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
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
                            <button type="button" class="common_btn" id="addToCartBtn" data-combo-id="{{ $combo->id }}">
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

                <div class="col-xl-3 col-md-8 d-lg-none d-xl-block wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">
                    <div class="">
                        <!-- Featured Offer -->
                        @php
                            $featuredOffer = site_section('menu_detail_featured_offer', 'menu_detail');
                        @endphp
                        @if ($featuredOffer && $featuredOffer->section_status)
                            <div class="menu_details_offer">
                                <p>{{ $featuredOffer->title }}</p>
                                @if ($featuredOffer->description)
                                    <a tabindex="0" data-bs-placement="bottom" data-bs-toggle="popover"
                                        data-bs-trigger="focus" data-bs-custom-class="custom-popover"
                                        data-bs-title="{{ $featuredOffer->title }}"
                                        data-bs-content="{{ strip_tags($featuredOffer->description) }}">
                                        {{ $featuredOffer->button_text ?? __('learn more') }}
                                        <i class="far fa-chevron-down" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Sidebar Banner -->
                        @php
                            $sidebarBanner = site_section('menu_detail_sidebar_banner', 'menu_detail');
                        @endphp
                        @if ($sidebarBanner && $sidebarBanner->section_status)
                            <div class="menu_details_banner">
                                <img src="{{ $sidebarBanner->image ? asset($sidebarBanner->image) : asset('website/images/details_banner_img.png') }}"
                                    alt="{{ __('offer') }}" class="img-fluid w-100">
                                <div class="text">
                                    <h5>{{ $sidebarBanner->subtitle ?? __('Get Up to 50% Off') }}</h5>
                                    <h3>{{ $sidebarBanner->title ?? __('Combo Pack') }}</h3>
                                    <a href="{{ $sidebarBanner->button_link ?? route('website.menu') }}">
                                        <span><img src="{{ asset('website/images/cart_icon_2.png') }}"
                                                alt="{{ __('cart') }}" class="img-fluid w-100"></span>
                                        {{ $sidebarBanner->button_text ?? __('Shop Now') }}
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Fallback to promotional banner system --}}
                            @php
                                $cmsBanner = cms_banner('sidebar');
                            @endphp
                            @if ($cmsBanner)
                                <div class="menu_details_banner">
                                    <img src="{{ $cmsBanner->image ? asset($cmsBanner->image) : asset('website/images/details_banner_img.png') }}"
                                        alt="{{ __('offer') }}" class="img-fluid w-100">
                                    <div class="text">
                                        <h5>{{ $cmsBanner->subtitle ?? __('Get Up to 50% Off') }}</h5>
                                        <h3>{{ $cmsBanner->title ?? __('Combo Pack') }}</h3>
                                        <a href="{{ $cmsBanner->button_link ?? route('website.menu') }}">
                                            <span><img src="{{ asset('website/images/cart_icon_2.png') }}"
                                                    alt="{{ __('cart') }}" class="img-fluid w-100"></span>
                                            {{ $cmsBanner->button_text ?? __('Shop Now') }}
                                            <i class="far fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description Tab -->
            @if ($combo->description)
            <div class="row mt_120 xs_mt_100 wow fadeInUp">
                <div class="col-12">
                    <div class="menu_det_content_area">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-description-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-description" type="button" role="tab"
                                    aria-controls="nav-description" aria-selected="true">{{ __('Description') }}</button>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-description" role="tabpanel"
                                aria-labelledby="nav-description-tab" tabindex="0">
                                <div class="menu_det_description">
                                    {!! $combo->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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

            document.getElementById('addToCartBtn').addEventListener('click', function() {
                addToCart(false);
            });

            document.getElementById('buyNowBtn').addEventListener('click', function() {
                addToCart(true);
            });

            function addToCart(buyNow) {
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
                            updateCartBadge(data.cart_count);
                            updateMiniCart(data.cart_count, data.cart_total, data.cart_item);

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

            function updateCartBadge(count) {
                const badge = document.querySelector('.cart-badge');
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                    badge.classList.add('pulse');
                    setTimeout(() => badge.classList.remove('pulse'), 300);
                }
            }

            function updateMiniCart(count, total, newItem) {
                const miniCartCount = document.getElementById('mini-cart-count');
                if (miniCartCount) {
                    miniCartCount.textContent = '(' + count + ')';
                }

                const miniCartTotal = document.getElementById('mini-cart-total');
                if (miniCartTotal) {
                    miniCartTotal.textContent = '{{ currency_icon() }}' + parseFloat(total).toFixed(2);
                }

                const miniCartFooter = document.getElementById('mini-cart-footer');
                if (miniCartFooter && count > 0) {
                    miniCartFooter.style.display = '';
                }

                const emptyMessage = document.querySelector('#mini-cart-items .empty-cart-message');
                if (emptyMessage) {
                    emptyMessage.remove();
                }

                if (newItem) {
                    const existingItem = document.querySelector(
                        `#mini-cart-items li[data-cart-item-id="${newItem.id}"]`);

                    if (existingItem) {
                        const qtySpan = existingItem.querySelector('.text p span');
                        if (qtySpan) {
                            qtySpan.textContent = '{{ __('Qty') }}: ' + newItem.quantity;
                        }
                        existingItem.style.backgroundColor = 'rgba(185, 157, 107, 0.1)';
                        setTimeout(() => existingItem.style.backgroundColor = '', 500);
                    } else {
                        const miniCartItems = document.getElementById('mini-cart-items');
                        if (miniCartItems) {
                            const itemHtml = `
                                <li data-cart-item-id="${newItem.id}" style="animation: fadeInSlide 0.4s ease;">
                                    <div class="img" style="width: 100px; min-width: 100px; height: 100px; margin-right: 15px;">
                                        <img src="${newItem.image || '{{ asset('website/images/menu_img_1.jpg') }}'}" alt="${newItem.name}" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
                                    </div>
                                    <div class="text">
                                        <h5>${newItem.name}</h5>
                                        <p>
                                            {{ currency_icon() }}${parseFloat(newItem.unit_price).toFixed(2)}
                                            <span>{{ __('Qty') }}: ${newItem.quantity}</span>
                                        </p>
                                    </div>
                                    <span class="close_cart" onclick="removeMiniCartItem(${newItem.id})">
                                        <i class="far fa-times"></i>
                                    </span>
                                </li>
                            `;
                            miniCartItems.insertAdjacentHTML('afterbegin', itemHtml);
                        }
                    }
                }
            }

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
                        @keyframes fadeInSlide {
                            from { opacity: 0; transform: translateY(-10px); background-color: rgba(185, 157, 107, 0.1); }
                            to { opacity: 1; transform: translateY(0); background-color: transparent; }
                        }
                        @keyframes pulse {
                            0% { transform: scale(1); }
                            50% { transform: scale(1.2); }
                            100% { transform: scale(1); }
                        }
                        .pulse { animation: pulse 0.3s ease-in-out; }
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
