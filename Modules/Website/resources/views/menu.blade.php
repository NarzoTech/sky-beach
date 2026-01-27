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
                                               data-slider-min="{{ $priceRange->min_price ?? 0 }}" 
                                               data-slider-max="{{ $priceRange->max_price ?? 100 }}" 
                                               data-slider-step="1" 
                                               data-slider-value="[{{ $minPrice ?? 0 }},{{ $maxPrice ?? 100 }}]" />
                                    </div>
                                    <div class="price-display mt-3 text-center">
                                        <span style="color: #333;">{{ __('Price') }}: {{ currency_icon() }}<span id="min-price">{{ $minPrice ?? 0 }}</span> - {{ currency_icon() }}<span id="max-price">{{ $maxPrice ?? 100 }}</span></span>
                                        <button type="button" class="btn btn-sm btn-primary mt-2 w-100" onclick="applyPriceFilter()">{{ __('Apply Filter') }}</button>
                                    </div>
                                </div>
                                <div class="sidebar_wizard sidebar_category mt_25">
                                    <h2>{{ __('Categories') }}</h2>
                                    <ul>
                                        <li>
                                            <a href="{{ route('website.menu') }}" class="{{ !$categorySlug ? 'active' : '' }}" style="{{ !$categorySlug ? 'color: #B99D6B; font-weight: 600;' : '' }}">
                                                {{ __('All Items') }} <span>({{ $menuItems->total() }})</span>
                                            </a>
                                        </li>
                                        @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('website.menu', ['category' => $category->slug, 'search' => $search ?? '', 'min_price' => $minPrice ?? 0, 'max_price' => $maxPrice ?? 100]) }}"
                                                   class="{{ $categorySlug == $category->slug ? 'active' : '' }}"
                                                   style="{{ $categorySlug == $category->slug ? 'color: #B99D6B; font-weight: 600;' : '' }}">
                                                    {{ $category->name }} <span>({{ $category->active_menu_items_count }})</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-8 order-lg-2">
                            <!-- Sorting and Results Count -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="text-muted">{{ __('Showing') }} {{ $menuItems->firstItem() ?? 0 }} - {{ $menuItems->lastItem() ?? 0 }} {{ __('of') }} {{ $menuItems->total() }} {{ __('results') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <label for="sort-select" class="me-2 mb-0" style="white-space: nowrap;">{{ __('Sort By') }}:</label>
                                        <select id="sort-select" class="form-select form-select-sm" style="width: auto;" onchange="applySorting(this.value)">
                                            <option value="default" {{ $sortBy == 'default' ? 'selected' : '' }}>{{ __('Default') }}</option>
                                            <option value="popular" {{ $sortBy == 'popular' ? 'selected' : '' }}>{{ __('Most Popular') }}</option>
                                            <option value="price_low" {{ $sortBy == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                            <option value="price_high" {{ $sortBy == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                            <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>{{ __('Name: A to Z') }}</option>
                                            <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>{{ __('Name: Z to A') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                @forelse($menuItems as $item)
                                <div class="col-xl-4 col-sm-6 wow fadeInUp">
                                    <div class="single_menu">
                                        <div class="single_menu_img">
                                            <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('website/images/menu_img_1.jpg') }}" alt="{{ $item->name }}" class="img-fluid w-100">
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
                                            <p class="descrption">{{ Str::limit($item->short_description, 50) }}</p>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <a class="add_to_cart" href="#" onclick="quickAddToCart({{ $item->id }}, '{{ $item->name }}'); return false;">{{ __('Add to Cart') }}</a>
                                                <h3>{{ currency($item->base_price) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <h4>{{ __('No menu items found') }}</h4>
                                        <p>{{ __('Try adjusting your filters or search terms.') }}</p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            @if($menuItems->hasPages())
                            <div class="pagination_area mt_35 xs_mb_60 wow fadeInUp">
                                <nav aria-label="Page navigation example">
                                    {{ $menuItems->appends(request()->query())->links('pagination::bootstrap-4') }}
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
        if (slider && slider._flatpickr) {
            const values = slider._flatpickr.selectedDates;
            const minPrice = values[0];
            const maxPrice = values[1];
            
            document.getElementById('hidden_min_price').value = minPrice;
            document.getElementById('hidden_max_price').value = maxPrice;
            document.getElementById('menu-search-form').submit();
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
                    icon.style.color = '#B99D6B';
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
                            <div class="img">
                                <img src="${newItem.image || '{{ asset("website/images/placeholder-food.png") }}'}" alt="${newItem.name}" class="img-fluid w-100">
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
    
    // Load user favorites on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Price slider updates
        const slider = document.getElementById('slider_range');
        if (slider) {
            slider.addEventListener('change', function(e) {
                const values = e.target.value.split(',');
                if (values.length === 2) {
                    document.getElementById('min-price').textContent = Math.round(values[0]);
                    document.getElementById('max-price').textContent = Math.round(values[1]);
                }
            });
        }
        
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
                        icon.style.color = '#B99D6B';
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
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .pulse {
        animation: pulse 0.3s ease-in-out;
    }

    .favorite-btn .fas {
        color: #B99D6B;
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
</style>
@endpush
