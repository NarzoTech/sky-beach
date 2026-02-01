@extends('website::layouts.master')

@section('title', $menuItem->name . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $menuItem->name }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.menu') }}">{{ __('Menu') }}</a></li>
                                @if($menuItem->category)
                                    <li><a href="{{ route('website.menu', ['category' => $menuItem->category->slug]) }}">{{ $menuItem->category->name }}</a></li>
                                @endif
                                <li><a href="#">{{ $menuItem->name }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========MENU DETAILS START===========-->
        <section class="menu_details pt_120 xs_pt_100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-md-8 col-lg-5 wow fadeInLeft">
                        <div class="menu_det_slider_area">
                            @php
                                $images = [];
                                if ($menuItem->image) {
                                    $images[] = $menuItem->image_url;
                                }
                                if ($menuItem->gallery && is_array($menuItem->gallery)) {
                                    foreach ($menuItem->gallery as $img) {
                                        $images[] = asset('storage/' . $img);
                                    }
                                }
                                if (empty($images)) {
                                    $images[] = asset('website/images/placeholder_food.jpg');
                                }
                            @endphp

                            <div class="row slider-for">
                                @foreach($images as $image)
                                    <div class="col-12">
                                        <div class="details_large_img">
                                            <img src="{{ $image }}" alt="{{ $menuItem->name }}" class="img-fluid w-100">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if(count($images) > 1)
                                <div class="row slider-nav">
                                    @foreach($images as $image)
                                        <div class="col-xl-3">
                                            <div class="details_small_img">
                                                <img src="{{ $image }}" alt="{{ $menuItem->name }}" class="img-fluid w-100">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xl-5 col-md-8 col-lg-7 wow fadeInUp">
                        <div class="menu_det_text">
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                                <h2 class="details_title mb-0">{{ $menuItem->name }}</h2>
                                @if($menuItem->category)
                                    <a href="{{ route('website.menu', ['category' => $menuItem->category->slug]) }}" class="badge bg-secondary">
                                        {{ $menuItem->category->name }}
                                    </a>
                                @endif
                            </div>

                            <p class="price" id="displayPrice">{{ currency($menuItem->base_price) }}</p>

                            @if($menuItem->short_description)
                                <div class="details_short_description">
                                    <p>{{ $menuItem->short_description }}</p>
                                </div>
                            @endif

                            <!-- Dietary Info -->
                            <div class="dietary_info mb-3">
                                @if($menuItem->is_vegetarian)
                                    <span class="badge bg-success me-1"><i class="fas fa-leaf me-1"></i>{{ __('Vegetarian') }}</span>
                                @endif
                                @if($menuItem->is_vegan)
                                    <span class="badge bg-success me-1"><i class="fas fa-seedling me-1"></i>{{ __('Vegan') }}</span>
                                @endif
                                @if($menuItem->is_spicy)
                                    <span class="badge bg-danger me-1"><i class="fas fa-pepper-hot me-1"></i>{{ __('Spicy') }}</span>
                                @endif
                                @if($menuItem->calories)
                                    <span class="badge bg-info me-1"><i class="fas fa-fire me-1"></i>{{ $menuItem->calories }} {{ __('cal') }}</span>
                                @endif
                                @if($menuItem->preparation_time)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>{{ $menuItem->preparation_time }} {{ __('min') }}</span>
                                @endif
                            </div>

                            <!-- Size Variants -->
                            @if($menuItem->activeVariants->count() > 0)
                                <div class="details_size">
                                    <h5>{{ __('Select Size') }}</h5>
                                    @foreach($menuItem->activeVariants as $index => $variant)
                                        <div class="form-check">
                                            <input class="form-check-input variant-radio" type="radio" name="variant_id"
                                                   id="variant_{{ $variant->id }}"
                                                   value="{{ $variant->id }}"
                                                   data-price="{{ $menuItem->base_price + $variant->price_adjustment }}"
                                                   {{ $index === 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="variant_{{ $variant->id }}">
                                                {{ $variant->name }}
                                                @if($variant->price_adjustment > 0)
                                                    <span>+ {{ currency($variant->price_adjustment) }}</span>
                                                @elseif($variant->price_adjustment < 0)
                                                    <span>- {{ currency(abs($variant->price_adjustment)) }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Addons -->
                            @if($menuItem->activeAddons->count() > 0)
                                <div class="details_extra_item">
                                    <h5>{{ __('Select Options') }} <span>({{ __('optional') }})</span></h5>
                                    @foreach($menuItem->activeAddons as $addon)
                                        <div class="form-check">
                                            <input class="form-check-input addon-checkbox" type="checkbox"
                                                   id="addon_{{ $addon->id }}"
                                                   value="{{ $addon->id }}"
                                                   data-price="{{ $addon->price }}">
                                            <label class="form-check-label" for="addon_{{ $addon->id }}">
                                                {{ $addon->name }}
                                                @if($addon->price > 0)
                                                    <span>+ {{ currency($addon->price) }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Quantity & Total -->
                            <div class="details_quentity">
                                <div class="quentity_btn_area d-flex flex-wrapa align-items-center">
                                    <div class="quentity_btn">
                                        <button type="button" class="btn btn-danger" id="decreaseQty"><i class="fal fa-minus"></i></button>
                                        <input type="text" id="quantity" value="1" readonly>
                                        <button type="button" class="btn btn-success" id="increaseQty"><i class="fal fa-plus"></i></button>
                                    </div>
                                    <h3 id="totalPrice">{{ currency($menuItem->base_price) }}</h3>
                                </div>
                            </div>

                            <!-- Special Instructions -->
                            <div class="special_instructions mb-3">
                                <label class="form-label">{{ __('Special Instructions') }} <small class="text-muted">({{ __('optional') }})</small></label>
                                <textarea id="specialInstructions" rows="2" placeholder="{{ __('Any special requests or dietary notes...') }}" maxlength="500"></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="details_cart_btn">
                                <button type="button" class="common_btn" id="addToCartBtn" data-item-id="{{ $menuItem->id }}">
                                    <span class="icon">
                                        <img src="{{ asset('website/images/cart_icon_1.png') }}" alt="cart" class="img-fluid w-100">
                                    </span>
                                    {{ __('Add to Cart') }}
                                </button>
                                <button type="button" class="common_btn" id="buyNowBtn" data-item-id="{{ $menuItem->id }}">
                                    {{ __('Buy Now') }}
                                </button>
                                <a class="love favorite-btn {{ session('favorites.' . $menuItem->id) ? 'active' : '' }}" href="#" data-item-id="{{ $menuItem->id }}">
                                    <i class="{{ session('favorites.' . $menuItem->id) ? 'fas' : 'far' }} fa-heart"></i>
                                </a>
                            </div>

                            <!-- Share -->
                            <ul class="share">
                                <li>{{ __('Share with friends') }}:</li>
                                <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($menuItem->name) }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="https://wa.me/?text={{ urlencode($menuItem->name . ' - ' . request()->url()) }}" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-8 d-lg-none d-xl-block wow fadeInRight">
                        <div class="">
                            <!-- Allergen Info -->
                            @if($menuItem->allergens && count($menuItem->allergens) > 0)
                                <div class="menu_details_offer mb-3">
                                    <p><strong><i class="fas fa-exclamation-triangle text-warning me-2"></i>{{ __('Allergens') }}</strong></p>
                                    <p class="small">{{ implode(', ', $menuItem->allergens) }}</p>
                                </div>
                            @endif

                            @php
                                $sidebarBanner = cms_banner('sidebar');
                            @endphp
                            @if($sidebarBanner)
                            <div class="menu_details_banner">
                                <img src="{{ $sidebarBanner->image ? asset($sidebarBanner->image) : asset('website/images/details_banner_img.png') }}" alt="{{ __('offer') }}" class="img-fluid w-100">
                                <div class="text">
                                    <h5>{{ $sidebarBanner->subtitle ?? __('Get Up to 50% Off') }}</h5>
                                    <h3>{{ $sidebarBanner->title ?? __('Combo Pack') }}</h3>
                                    <a href="{{ $sidebarBanner->button_link ?? route('website.menu') }}">
                                        <span><img src="{{ asset('website/images/cart_icon_2.png') }}" alt="{{ __('cart') }}" class="img-fluid w-100"></span>
                                        {{ $sidebarBanner->button_text ?? __('Shop Now') }}
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description Tab -->
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
                                <!-- Description Tab -->
                                <div class="tab-pane fade show active" id="nav-description" role="tabpanel"
                                     aria-labelledby="nav-description-tab" tabindex="0">
                                    <div class="menu_det_description">
                                        @if($menuItem->long_description)
                                            {!! nl2br(e($menuItem->long_description)) !!}
                                        @elseif($menuItem->short_description)
                                            <p>{{ $menuItem->short_description }}</p>
                                        @else
                                            <p class="text-muted">{{ __('No detailed description available.') }}</p>
                                        @endif

                                        <!-- Nutritional Info -->
                                        @if($menuItem->calories || $menuItem->preparation_time)
                                            <div class="nutritional_info mt-4">
                                                <h4>{{ __('Additional Information') }}</h4>
                                                <ul>
                                                    @if($menuItem->calories)
                                                        <li><strong>{{ __('Calories') }}:</strong> {{ $menuItem->calories }} kcal</li>
                                                    @endif
                                                    @if($menuItem->preparation_time)
                                                        <li><strong>{{ __('Preparation Time') }}:</strong> {{ $menuItem->preparation_time }} {{ __('minutes') }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========MENU DETAILS END===========-->


        <!--==========RELATED MENU START===========-->
        @if($relatedItems->count() > 0)
            <section class="related_menu pt_105 xs_pt_85">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-xl-5">
                            <div class="section_heading heading_left mb_25">
                                <h2>{{ __('Related Food') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row related_slider">
                        @foreach($relatedItems as $item)
                            <div class="col-xl-3 wow fadeInUp">
                                <div class="single_menu">
                                    <div class="single_menu_img">
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-fluid w-100">
                                        <ul>
                                            <li><a href="{{ route('website.menu-details', $item->slug) }}"><i class="far fa-eye"></i></a></li>
                                            <li><a href="#" class="favorite-btn" data-item-id="{{ $item->id }}"><i class="far fa-heart"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="single_menu_text">
                                        @if($item->category)
                                            <a class="category" href="{{ route('website.menu', ['category' => $item->category->slug]) }}">{{ $item->category->name }}</a>
                                        @endif
                                        <a class="title" href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                        @if($item->short_description)
                                            <p class="descrption">{{ Str::limit($item->short_description, 50) }}</p>
                                        @endif
                                        <div class="d-flex flex-wrap align-items-center">
                                            <a class="add_to_cart" href="{{ route('website.menu-details', $item->slug) }}">{{ __('View') }}</a>
                                            <h3>{{ currency($item->base_price) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <!--==========RELATED MENU END===========-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const basePrice = {{ $menuItem->base_price }};
    let currentPrice = basePrice;
    let quantity = 1;

    const quantityInput = document.getElementById('quantity');
    const displayPrice = document.getElementById('displayPrice');
    const totalPrice = document.getElementById('totalPrice');
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    const variantRadios = document.querySelectorAll('.variant-radio');
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');

    function calculatePrice() {
        let price = basePrice;

        // Add variant price
        const selectedVariant = document.querySelector('.variant-radio:checked');
        if (selectedVariant) {
            price = parseFloat(selectedVariant.dataset.price);
        }

        // Add addon prices
        addonCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                price += parseFloat(checkbox.dataset.price);
            }
        });

        currentPrice = price;
        updateDisplay();
    }

    function updateDisplay() {
        displayPrice.textContent = '{{ currency_icon() }}' + currentPrice.toFixed(2);
        totalPrice.textContent = '{{ currency_icon() }}' + (currentPrice * quantity).toFixed(2);
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

    // Variant selection
    variantRadios.forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    // Addon selection
    addonCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculatePrice);
    });

    // Add to Cart
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        addToCart(false);
    });

    // Buy Now
    document.getElementById('buyNowBtn').addEventListener('click', function() {
        addToCart(true);
    });

    function addToCart(buyNow) {
        const itemId = {{ $menuItem->id }};
        const selectedVariant = document.querySelector('.variant-radio:checked');
        const variantId = selectedVariant ? selectedVariant.value : null;
        const addons = [];

        addonCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                addons.push(checkbox.value);
            }
        });

        const specialInstructions = document.getElementById('specialInstructions').value;

        fetch('{{ route("website.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                menu_item_id: itemId,
                quantity: quantity,
                variant_id: variantId,
                addons: addons,
                special_instructions: specialInstructions
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart badge in header
                updateCartBadge(data.cart_count);

                // Update mini cart
                updateMiniCart(data.cart_count, data.cart_total, data.cart_item);

                if (buyNow) {
                    window.location.href = '{{ route("website.checkout.index") }}';
                } else {
                    // Show success toast
                    showToast('{{ $menuItem->name }} {{ __("added to cart!") }}', 'success');
                }
            } else {
                showToast(data.message || '{{ __("Failed to add item to cart.") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ __("An error occurred. Please try again.") }}', 'error');
        });
    }

    // Update cart badge in header
    function updateCartBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
            // Add pulse animation
            badge.classList.add('pulse');
            setTimeout(() => badge.classList.remove('pulse'), 300);
        }
    }

    // Update mini cart sidebar
    function updateMiniCart(count, total, newItem) {
        // Update mini cart count
        const miniCartCount = document.getElementById('mini-cart-count');
        if (miniCartCount) {
            miniCartCount.textContent = '(' + count + ')';
        }

        // Update mini cart total
        const miniCartTotal = document.getElementById('mini-cart-total');
        if (miniCartTotal) {
            miniCartTotal.textContent = '{{ currency_icon() }}' + parseFloat(total).toFixed(2);
        }

        // Show mini cart footer if it was hidden
        const miniCartFooter = document.getElementById('mini-cart-footer');
        if (miniCartFooter && count > 0) {
            miniCartFooter.style.display = '';
        }

        // Remove empty cart message if exists
        const emptyMessage = document.querySelector('#mini-cart-items .empty-cart-message');
        if (emptyMessage) {
            emptyMessage.remove();
        }

        // Add or update item in mini cart
        if (newItem) {
            const existingItem = document.querySelector(`#mini-cart-items li[data-cart-item-id="${newItem.id}"]`);

            if (existingItem) {
                // Update quantity of existing item
                const qtySpan = existingItem.querySelector('.text p span');
                if (qtySpan) {
                    qtySpan.textContent = '{{ __("Qty") }}: ' + newItem.quantity;
                }
                // Add highlight effect
                existingItem.style.backgroundColor = 'rgba(185, 157, 107, 0.1)';
                setTimeout(() => existingItem.style.backgroundColor = '', 500);
            } else {
                // Add new item to mini cart
                const miniCartItems = document.getElementById('mini-cart-items');
                if (miniCartItems) {
                    const itemHtml = `
                        <li data-cart-item-id="${newItem.id}" style="animation: fadeInSlide 0.4s ease;">
                            <div class="img">
                                <img src="${newItem.image || '{{ asset("website/images/menu_img_1.jpg") }}'}" alt="${newItem.name}" class="img-fluid w-100">
                            </div>
                            <div class="text">
                                <h5>${newItem.name}</h5>
                                <p>
                                    {{ currency_icon() }}${parseFloat(newItem.unit_price).toFixed(2)}
                                    <span>{{ __("Qty") }}: ${newItem.quantity}</span>
                                </p>
                                ${newItem.variant_name ? `<small class="text-muted">${newItem.variant_name}</small>` : ''}
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

    // Show toast notification
    function showToast(message, type = 'success') {
        // Remove existing toast
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }

        // Create toast element
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

        // Add animation styles if not already added
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

        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Initialize price calculation
    calculatePrice();
});
</script>
@endpush
