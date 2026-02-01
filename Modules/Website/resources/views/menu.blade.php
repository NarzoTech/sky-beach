@extends('website::layouts.master')

@section('title', __('Menu') . ' - ' . config('app.name'))

@php
    $sections = site_sections('menu');
    $breadcrumb = $sections['menu_breadcrumb'] ?? null;
    $filtersSection = $sections['menu_filters'] ?? null;
@endphp

@section('content')
            <!--==========BREADCRUMB AREA START===========-->
            @if(!$breadcrumb || $breadcrumb->section_status)
            <section class="breadcrumb_area" style="background: url({{ $breadcrumb?->background_image ? asset($breadcrumb->background_image) : asset('website/images/breadcrumb_bg.jpg') }});">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>{{ $breadcrumb?->title ?? __('Our Menu') }}</h1>
                                <ul>
                                    <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                    <li><a href="{{ route('website.menu') }}">{{ $breadcrumb?->title ?? __('Menu') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========MENU PAGE START===========-->
            <section class="menu_grid_view mt_120 xs_mt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6 order-2 wow fadeInLeft">
                            <div class="menu_sidebar ">
                                <div class="sidebar_wizard sidebar_search">
                                    <h2>{{ __('Search') }}</h2>
                                    <form method="GET" action="{{ route('website.menu') }}" id="menu-search-form">
                                        <input type="text" name="search" placeholder="{{ __('Search here...') }}" value="{{ $search ?? '' }}">
                                        <input type="hidden" name="category" value="{{ $categorySlug ?? '' }}">
                                        <input type="hidden" name="min_price" id="hidden_min_price" value="{{ $minPrice ?? 0 }}">
                                        <input type="hidden" name="max_price" id="hidden_max_price" value="{{ $maxPrice ?? 100 }}">
                                        <button type="submit"><i class="far fa-search"></i></button>
                                    </form>
                                </div>
                                <div class="sidebar_wizard sidebar_price_ranger mt_25">
                                    <h2>{{ __('Pricing Filter') }}</h2>
                                    <div class="price_ranger">
                                        <input type="hidden" id="slider_range" class="flat-slider"
                                               data-slider-min="{{ floor($priceRange->min_price ?? 0) }}"
                                               data-slider-max="{{ ceil($priceRange->max_price ?? 100) }}"
                                               data-slider-step="1"
                                               data-slider-value="[{{ floor($minPrice ?? 0) }},{{ ceil($maxPrice ?? 100) }}]" />
                                    </div>
                                    <div class="price-display mt-3 text-center">
                                        <button type="button" class="common_btn w-100" onclick="applyPriceFilter()">{{ __('Apply Filter') }}</button>
                                    </div>
                                </div>
                                <div class="sidebar_wizard sidebar_category mt_25">
                                    <h2>{{ __('Categories') }}</h2>
                                    <ul>
                                        <li>
                                            <a href="{{ route('website.menu') }}" class="{{ !$categorySlug ? 'active' : '' }}">
                                                {{ __('All Items') }} <span>({{ $menuItems->total() }})</span>
                                            </a>
                                        </li>
                                        @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('website.menu', ['category' => $category->slug, 'search' => $search ?? '', 'min_price' => $minPrice ?? 0, 'max_price' => $maxPrice ?? 100]) }}"
                                                   class="{{ $categorySlug == $category->slug ? 'active' : '' }}">
                                                    {{ $category->name }} <span>({{ $category->active_menu_items_count }})</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-8 order-lg-2">
                            <!-- Combo Packages Section -->
                            @if(isset($combos) && $combos->count() > 0)
                            <div class="combo-packages-section mb-5">
                                <div class="section-title mb-4">
                                    <h3 style="color: #000; font-weight: 600; border-bottom: 2px solid #000; padding-bottom: 10px; display: inline-block;">
                                        <i class="fas fa-gift me-2"></i>{{ __('Combo Packages') }}
                                    </h3>
                                    <p class="text-muted">{{ __('Save more with our special combo deals!') }}</p>
                                </div>
                                <div class="row">
                                    @foreach($combos as $combo)
                                    <div class="col-xl-4 col-sm-6 wow fadeInUp mb-4">
                                        <div class="single_menu combo-card" style="border: 2px solid var(--colorPrimary); border-radius: 10px; overflow: hidden;">
                                            <div class="single_menu_img position-relative">
                                                <img src="{{ $combo->image_url ?? asset('website/images/combo_default.jpg') }}" alt="{{ $combo->name }}" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
                                                @if($combo->savings > 0)
                                                <span class="badge position-absolute" style="top: 10px; right: 10px; font-size: 14px; padding: 8px 12px; background: var(--colorPrimary); color: #fff;">
                                                    {{ __('Save') }} {{ currency($combo->savings) }}
                                                </span>
                                                @endif
                                                <span class="badge position-absolute" style="top: 10px; left: 10px; font-size: 12px; background: var(--colorYellow); color: #000;">
                                                    <i class="fas fa-box me-1"></i>{{ __('COMBO') }}
                                                </span>
                                            </div>
                                            <div class="single_menu_text" style="padding: 15px;">
                                                <h4 class="title mb-2" style="color: #333; font-size: 18px;">{{ $combo->name }}</h4>
                                                @if($combo->description)
                                                <p class="descrption text-muted mb-2" style="font-size: 13px;">{{ Str::limit(strip_tags($combo->description), 60) }}</p>
                                                @endif
                                                <!-- Combo Items List -->
                                                <div class="combo-items mb-3" style="font-size: 12px; color: #666;">
                                                    <strong>{{ __('Includes') }}:</strong>
                                                    <ul class="mb-0 ps-3" style="list-style: disc;">
                                                        @foreach($combo->comboItems->take(3) as $comboItem)
                                                        <li>{{ $comboItem->quantity }}x {{ $comboItem->menuItem->name ?? 'Item' }}</li>
                                                        @endforeach
                                                        @if($combo->comboItems->count() > 3)
                                                        <li>{{ __('+ :count more items', ['count' => $combo->comboItems->count() - 3]) }}</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                    <div>
                                                        @if($combo->original_price > $combo->combo_price)
                                                        <span style="text-decoration: line-through; color: #999; font-size: 14px;">{{ currency($combo->original_price) }}</span>
                                                        @endif
                                                        <h3 class="mb-0" style="color: var(--colorPrimary); font-size: 22px;">{{ currency($combo->combo_price) }}</h3>
                                                    </div>
                                                    <a class="add_to_cart" href="#" onclick="addComboToCart({{ $combo->id }}, '{{ $combo->name }}'); return false;" style="padding: 8px 15px;">
                                                        <i class="fas fa-cart-plus me-1"></i>{{ __('Add') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <hr class="mb-4" style="border-color: #ddd;">
                            @endif

                            <!-- Sorting and Results Count -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="text-muted">{{ __('Showing') }} {{ $menuItems->firstItem() ?? 0 }} - {{ $menuItems->lastItem() ?? 0 }} {{ __('of') }} {{ $menuItems->total() }} {{ __('results') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <label class="me-2 mb-0" style="white-space: nowrap;">{{ __('Sort By') }}:</label>
                                        <div class="custom-sort-dropdown">
                                            <div class="sort-dropdown-selected" onclick="toggleSortDropdown()">
                                                <span id="sort-selected-text">
                                                    @switch($sortBy)
                                                        @case('popular') {{ __('Most Popular') }} @break
                                                        @case('price_low') {{ __('Price: Low to High') }} @break
                                                        @case('price_high') {{ __('Price: High to Low') }} @break
                                                        @case('name_asc') {{ __('Name: A to Z') }} @break
                                                        @case('name_desc') {{ __('Name: Z to A') }} @break
                                                        @default {{ __('Default') }}
                                                    @endswitch
                                                </span>
                                                <i class="fas fa-chevron-down sort-arrow"></i>
                                            </div>
                                            <ul class="sort-dropdown-options" id="sort-options">
                                                <li data-value="default" class="{{ $sortBy == 'default' ? 'active' : '' }}">{{ __('Default') }}</li>
                                                <li data-value="popular" class="{{ $sortBy == 'popular' ? 'active' : '' }}">{{ __('Most Popular') }}</li>
                                                <li data-value="price_low" class="{{ $sortBy == 'price_low' ? 'active' : '' }}">{{ __('Price: Low to High') }}</li>
                                                <li data-value="price_high" class="{{ $sortBy == 'price_high' ? 'active' : '' }}">{{ __('Price: High to Low') }}</li>
                                                <li data-value="name_asc" class="{{ $sortBy == 'name_asc' ? 'active' : '' }}">{{ __('Name: A to Z') }}</li>
                                                <li data-value="name_desc" class="{{ $sortBy == 'name_desc' ? 'active' : '' }}">{{ __('Name: Z to A') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @forelse($menuItems as $item)
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
                                    <div class="single_menu">
                                        <div class="single_menu_img">
                                            <img src="{{ $item->image ? asset($item->image) : asset('website/images/menu_img_1.jpg') }}" alt="{{ $item->name }}" class="img-fluid w-100">
                                            <ul>
                                                <li><a href="#" data-item-id="{{ $item->id }}" onclick="showQuickView({{ $item->id }}); return false;"><i class="far fa-eye"></i></a></li>
                                                <li>
                                                    <a href="#" class="favorite-btn" data-item-id="{{ $item->id }}" onclick="toggleFavorite({{ $item->id }}); return false;">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="single_menu_text">
                                            @if($item->category)
                                            <a class="category" href="{{ route('website.menu', ['category' => $item->category->slug]) }}">{{ $item->category->name }}</a>
                                            @endif
                                            <a class="title" href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                            <p class="descrption">{{ Str::limit(strip_tags($item->short_description), 50) }}</p>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                <div class="price-wrapper">
                                                    @if($item->discount_price && $item->discount_price < $item->base_price)
                                                        <span style="text-decoration: line-through; color: #999; font-size: 13px; display: block;">{{ currency($item->base_price) }}</span>
                                                        <h3 style="margin: 0;">{{ currency($item->final_price) }}</h3>
                                                    @else
                                                        <h3 style="margin: 0;">{{ currency($item->base_price) }}</h3>
                                                    @endif
                                                </div>
                                                <a class="add_to_cart" href="#" onclick="quickAddToCart({{ $item->id }}, '{{ $item->name }}'); return false;">{{ __('Add to Cart') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-search fa-3x mb-3" style="color: var(--colorPrimary); opacity: 0.5;"></i>
                                        <h4 style="color: var(--colorPrimary);">{{ __('No menu items found') }}</h4>
                                        <p class="text-muted">{{ __('Try adjusting your filters or search terms.') }}</p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            @if($menuItems->hasPages())
                            <div class="pagination_area mt_35 xs_mb_60 wow fadeInUp">
                                <nav aria-label="Page navigation example">
                                    {{ $menuItems->appends(request()->query())->links('website::partials.pagination') }}
                                </nav>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            <!--==========MENU PAGE END===========-->
@endsection

@push('scripts')
<script>
    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    // Price filter functionality
    function applyPriceFilter() {
        const slider = document.getElementById('slider_range');
        if (slider) {
            // Get values from the flatslider (stored as "min;max" in the input value)
            const sliderValue = slider.value || '';
            const values = sliderValue.split(';');

            if (values.length === 2) {
                const minPrice = parseFloat(values[0]) || 0;
                const maxPrice = parseFloat(values[1]) || 100;

                // Build URL with price filter
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('min_price', minPrice);
                currentUrl.searchParams.set('max_price', maxPrice);
                window.location.href = currentUrl.toString();
            }
        }
    }
    
    // Sorting functionality
    function applySorting(sortValue) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort_by', sortValue);
        window.location.href = currentUrl.toString();
    }
    
    // Toggle favorite
    function toggleFavorite(itemId) {
        const btn = document.querySelector(`.favorite-btn[data-item-id="${itemId}"]`);
        const icon = btn.querySelector('i');
        
        fetch(`{{ route('website.menu.favorite', '') }}/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle heart icon
                if (data.is_favorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    icon.style.color = 'var(--colorPrimary)';
                    showToast('success', data.message);
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    icon.style.color = '';
                    showToast('info', data.message);
                }
            } else {
                showToast('error', 'Failed to update favorite');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'An error occurred. Please try again.');
        });
    }
    
    // Quick add to cart
    function quickAddToCart(itemId, itemName) {
        fetch('{{ route('website.menu.add-to-cart') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                menu_item_id: itemId,
                quantity: 1,
                variant_id: null,
                addons: []
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', itemName + ' added to cart');
                // Update cart badge and mini cart
                updateCartBadge(data.cart_count);
                updateMiniCart(data.cart_count, data.cart_total, data.cart_item);
            } else {
                showToast('error', data.message || 'Failed to add item to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'An error occurred. Please try again.');
        });
    }

    // Add combo to cart
    function addComboToCart(comboId, comboName) {
        fetch('{{ route('website.cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                combo_id: comboId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', comboName + ' {{ __("combo added to cart") }}');
                // Update cart badge and mini cart
                updateCartBadge(data.cart_count);
                updateMiniCart(data.cart_count, data.cart_total, data.cart_item);
            } else {
                showToast('error', data.message || '{{ __("Failed to add combo to cart") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', '{{ __("An error occurred. Please try again.") }}');
        });
    }

    // Show quick view modal (placeholder - implement based on your modal)
    function showQuickView(itemId) {
        // Implement quick view modal functionality
        console.log('Quick view for item:', itemId);
        showToast('info', 'Quick view coming soon!');
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
            } else {
                // Add new item to mini cart
                const miniCartItems = document.getElementById('mini-cart-items');
                if (miniCartItems) {
                    const itemHtml = `
                        <li data-cart-item-id="${newItem.id}" class="mini-cart-item-new">
                            <div class="img" style="width: 100px; min-width: 100px; height: 100px; margin-right: 15px;">
                                <img src="${newItem.image || '{{ asset("website/images/menu_img_1.jpg") }}'}" alt="${newItem.name}" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
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

                    // Remove animation class after animation completes
                    setTimeout(() => {
                        const newElement = miniCartItems.querySelector('.mini-cart-item-new');
                        if (newElement) newElement.classList.remove('mini-cart-item-new');
                    }, 500);
                }
            }
        }
    }

    // Toast notification function - custom implementation
    function showToast(type, message) {
        // Check if toastr is available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
            return;
        }

        // Check if Swal (SweetAlert) is available
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            Toast.fire({
                icon: type === 'error' ? 'error' : (type === 'info' ? 'info' : 'success'),
                title: message
            });
            return;
        }

        // Custom toast fallback
        let toastContainer = document.getElementById('custom-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'custom-toast-container';
            toastContainer.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px;';
            document.body.appendChild(toastContainer);
        }

        const colors = {
            success: { bg: '#28a745', icon: 'fa-check-circle' },
            error: { bg: '#dc3545', icon: 'fa-times-circle' },
            info: { bg: '#17a2b8', icon: 'fa-info-circle' },
            warning: { bg: '#ffc107', icon: 'fa-exclamation-circle' }
        };

        const config = colors[type] || colors.info;

        const toast = document.createElement('div');
        toast.className = 'custom-toast';
        toast.style.cssText = `background:${config.bg};color:#fff;padding:12px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:flex;align-items:center;gap:10px;animation:slideIn 0.3s ease;min-width:250px;`;
        toast.innerHTML = `<i class="fas ${config.icon}"></i><span>${message}</span>`;

        toastContainer.appendChild(toast);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Custom sort dropdown functions
    function toggleSortDropdown() {
        document.querySelector('.custom-sort-dropdown').classList.toggle('open');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.custom-sort-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    // Handle sort option selection
    document.addEventListener('DOMContentLoaded', function() {
        const sortOptions = document.querySelectorAll('.sort-dropdown-options li');
        sortOptions.forEach(function(option) {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                const text = this.textContent;

                // Update selected text
                document.getElementById('sort-selected-text').textContent = text;

                // Update active state
                sortOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                // Close dropdown
                document.querySelector('.custom-sort-dropdown').classList.remove('open');

                // Apply sorting
                applySorting(value);
            });
        });
    });

    // Load user favorites on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize price slider with dynamic values from database
        var $sliderInput = jQuery("#slider_range");
        if ($sliderInput.length) {
            var sliderMin = {{ floor($priceRange->min_price ?? 0) }};
            var sliderMax = {{ ceil($priceRange->max_price ?? 1000) }};
            var currentMin = {{ floor($minPrice ?? $priceRange->min_price ?? 0) }};
            var currentMax = {{ ceil($maxPrice ?? $priceRange->max_price ?? 1000) }};

            $sliderInput.flatslider({
                min: sliderMin,
                max: sliderMax,
                step: 1,
                values: [currentMin, currentMax],
                range: true,
                einheit: 'TK'
            });
        }

        // Fix slider value labels positioning after initialization
        setTimeout(function() {
            const sliderInput = document.getElementById('slider_range');
            if (sliderInput && jQuery) {
                const $slider = jQuery(sliderInput).next('.flat-slider');
                if ($slider.length) {
                    const minValueEl = $slider.find('.min_value');
                    const maxValueEl = $slider.find('.max_value');

                    // Reset inline styles and let CSS handle positioning
                    minValueEl.css({'left': '0', 'right': 'auto'});
                    maxValueEl.css({'left': 'auto', 'right': '0'});
                }
            }
        }, 100);
        
        // Load favorites
        fetch('{{ route('website.menu.favorites.get') }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.favorites) {
                data.favorites.forEach(itemId => {
                    const btn = document.querySelector(`.favorite-btn[data-item-id="${itemId}"]`);
                    if (btn) {
                        const icon = btn.querySelector('i');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        icon.style.color = 'var(--colorPrimary)';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading favorites:', error);
        });
    });
</script>

<style>
    /* Custom sort dropdown styling */
    .custom-sort-dropdown {
        position: relative;
        min-width: 180px;
    }

    .sort-dropdown-selected {
        padding: 10px 35px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        color: #333;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sort-dropdown-selected:hover {
        border-color: var(--colorPrimary);
    }

    .sort-dropdown-selected .sort-arrow {
        color: var(--colorPrimary);
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .custom-sort-dropdown.open .sort-arrow {
        transform: rotate(180deg);
    }

    .custom-sort-dropdown.open .sort-dropdown-selected {
        border-color: var(--colorPrimary);
        border-radius: 8px 8px 0 0;
    }

    .sort-dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid var(--colorPrimary);
        border-top: none;
        border-radius: 0 0 8px 8px;
        list-style: none;
        margin: 0;
        padding: 0;
        display: none;
        z-index: 100;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .custom-sort-dropdown.open .sort-dropdown-options {
        display: block;
    }

    .sort-dropdown-options li {
        padding: 10px 15px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .sort-dropdown-options li:hover {
        background-color: var(--colorPrimary);
        color: #fff;
    }

    .sort-dropdown-options li.active {
        background-color: rgba(171, 22, 44, 0.1);
        color: var(--colorPrimary);
        font-weight: 500;
    }

    .sort-dropdown-options li.active:hover {
        background-color: var(--colorPrimary);
        color: #fff;
    }

    .sort-dropdown-options li:last-child {
        border-radius: 0 0 8px 8px;
    }

    /* Active category link styling */
    .sidebar_category ul li a.active {
        color: var(--colorPrimary) !important;
        font-weight: 600;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .pulse {
        animation: pulse 0.3s ease-in-out;
    }

    .favorite-btn .fas {
        color: var(--colorPrimary);
    }

    .favorite-btn:hover i {
        transform: scale(1.2);
        transition: transform 0.2s;
    }

    /* Custom toast animations */
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    /* Mini cart new item animation */
    .mini-cart-item-new {
        animation: fadeInSlide 0.4s ease;
    }

    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
            background-color: rgba(185, 157, 107, 0.1);
        }
        to {
            opacity: 1;
            transform: translateY(0);
            background-color: transparent;
        }
    }

    /* Price slider fixes */
    .price_ranger {
        overflow: visible;
        position: relative;
    }

    .price_ranger .flat-slider {
        position: relative;
        overflow: visible;
    }

    .price_ranger .flat-slider .min_value,
    .price_ranger .flat-slider .max_value {
        position: absolute;
        bottom: -25px;
        white-space: nowrap;
        font-size: 12px;
        font-weight: 500;
    }

    .price_ranger .flat-slider .min_value {
        left: 0 !important;
        transform: none;
    }

    .price_ranger .flat-slider .max_value {
        left: auto !important;
        right: 0 !important;
        transform: none;
    }
</style>
@endpush
