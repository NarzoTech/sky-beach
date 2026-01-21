@extends('website::layouts.master')

@section('title', 'menu - CTAKE')

@section('content')
            <!--==========BREADCRUMB AREA START===========-->
            <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
                <div class="container">
                    <div class="row wow fadeInUp">
                        <div class="col-12">
                            <div class="breadcrumb_text">
                                <h1>Our Menu</h1>
                                <ul>
                                    <li><a href="{{ route('website.index') }}">Home</a></li>
                                    <li><a href="{{ route('website.menu') }}">Menu</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--==========BREADCRUMB AREA END===========-->


            <!--==========MENU PAGE START===========-->
            <section class="menu_grid_view mt_120 xs_mt_100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6 order-2 wow fadeInLeft">
                            <div class="menu_sidebar ">
                                <div class="sidebar_wizard sidebar_search">
                                    <h2>Search</h2>
                                    <form method="GET" action="{{ route('website.menu') }}" id="menu-search-form">
                                        <input type="text" name="search" placeholder="Search here..." value="{{ $search ?? '' }}">
                                        <input type="hidden" name="category" value="{{ $categoryId ?? '' }}">
                                        <input type="hidden" name="min_price" id="hidden_min_price" value="{{ $minPrice ?? 0 }}">
                                        <input type="hidden" name="max_price" id="hidden_max_price" value="{{ $maxPrice ?? 100 }}">
                                        <button type="submit"><i class="far fa-search"></i></button>
                                    </form>
                                </div>
                                <div class="sidebar_wizard sidebar_price_ranger mt_25">
                                    <h2>Pricing Filter</h2>
                                    <div class="price_ranger">
                                        <input type="hidden" id="slider_range" class="flat-slider" 
                                               data-slider-min="{{ $priceRange->min_price ?? 0 }}" 
                                               data-slider-max="{{ $priceRange->max_price ?? 100 }}" 
                                               data-slider-step="1" 
                                               data-slider-value="[{{ $minPrice ?? 0 }},{{ $maxPrice ?? 100 }}]" />
                                    </div>
                                    <div class="price-display mt-3 text-center">
                                        <span style="color: #333;">Price: {{ currency_icon() }}<span id="min-price">{{ $minPrice ?? 0 }}</span> - {{ currency_icon() }}<span id="max-price">{{ $maxPrice ?? 100 }}</span></span>
                                        <button type="button" class="btn btn-sm btn-primary mt-2 w-100" onclick="applyPriceFilter()">Apply Filter</button>
                                    </div>
                                </div>
                                <div class="sidebar_wizard sidebar_category mt_25">
                                    <h2>Categories</h2>
                                    <ul>
                                        <li>
                                            <a href="{{ route('website.menu') }}" class="{{ !$categoryId ? 'active' : '' }}" style="{{ !$categoryId ? 'color: #B99D6B; font-weight: 600;' : '' }}">
                                                All Items <span>({{ $menuItems->total() }})</span>
                                            </a>
                                        </li>
                                        @foreach($categories as $category)
                                            <li>
                                                <a href="{{ route('website.menu', ['category' => $category->id, 'search' => $search ?? '', 'min_price' => $minPrice ?? 0, 'max_price' => $maxPrice ?? 100]) }}" 
                                                   class="{{ $categoryId == $category->id ? 'active' : '' }}"
                                                   style="{{ $categoryId == $category->id ? 'color: #B99D6B; font-weight: 600;' : '' }}">
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
                                    <p class="text-muted">Showing {{ $menuItems->firstItem() ?? 0 }} - {{ $menuItems->lastItem() ?? 0 }} of {{ $menuItems->total() }} results</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <label for="sort-select" class="me-2 mb-0" style="white-space: nowrap;">Sort By:</label>
                                        <select id="sort-select" class="form-select form-select-sm" style="width: auto;" onchange="applySorting(this.value)">
                                            <option value="default" {{ $sortBy == 'default' ? 'selected' : '' }}>Default</option>
                                            <option value="popular" {{ $sortBy == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                            <option value="price_low" {{ $sortBy == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                            <option value="price_high" {{ $sortBy == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                            <option value="name_asc" {{ $sortBy == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                            <option value="name_desc" {{ $sortBy == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
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
                                            <a class="category" href="{{ route('website.menu', ['category' => $item->category_id]) }}">{{ $item->category->name }}</a>
                                            @endif
                                            <a class="title" href="{{ route('website.menu-details', $item->slug) }}">{{ $item->name }}</a>
                                            <p class="descrption">{{ Str::limit($item->short_description, 50) }}</p>
                                            <div class="d-flex flex-wrap align-items-center">
                                                <a class="add_to_cart" href="#" onclick="quickAddToCart({{ $item->id }}, '{{ $item->name }}'); return false;">Add to Cart</a>
                                                <h3>{{ currency($item->base_price) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <h4>No menu items found</h4>
                                        <p>Try adjusting your filters or search terms.</p>
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
                showToast('success', data.message);
                // Update cart count if you have a cart counter
                updateCartCount(data.cart_count);
            } else {
                showToast('error', 'Failed to add item to cart');
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
    
    // Update cart count display
    function updateCartCount(count) {
        const cartCounters = document.querySelectorAll('.cart-count');
        cartCounters.forEach(counter => {
            counter.textContent = count;
            // Add animation
            counter.classList.add('pulse');
            setTimeout(() => counter.classList.remove('pulse'), 300);
        });
    }
    
    // Toast notification function
    function showToast(type, message) {
        // Check if toastr is available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            // Fallback to alert
            alert(message);
        }
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
</style>
@endpush
