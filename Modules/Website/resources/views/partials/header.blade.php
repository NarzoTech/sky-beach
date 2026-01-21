@php
    use Modules\Website\app\Models\WebsiteCart;
    $headerCartItems = WebsiteCart::getCart();
    $headerCartCount = $headerCartItems->sum('quantity');
    $headerCartTotal = $headerCartItems->sum('subtotal');
@endphp
<!--==========MENU START===========-->
<nav class="navbar navbar-expand-lg main_menu">
    <div class="container">
        <a class="navbar-brand" href="{{ route('website.index') }}">
            <img src="{{ asset('website/images/logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid w-100">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="fas fa-bars bar_icon"></i>
            <i class="far fa-times close_icon"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav m-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.index') ? 'active' : '' }}" href="{{ route('website.index') }}">{{ __('Home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.menu*') ? 'active' : '' }}" href="{{ route('website.menu') }}">{{ __('Menu') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.service*') ? 'active' : '' }}" href="{{ route('website.service') }}">{{ __('Services') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.about') ? 'active' : '' }}" href="{{ route('website.about') }}">{{ __('About') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.contact') ? 'active' : '' }}" href="{{ route('website.contact') }}">{{ __('Contact') }}</a>
                </li>
            </ul>
            <ul class="menu_right">
                <li>
                    <a class="menu_search"><i class="far fa-search"></i></a>
                </li>
                <li>
                    <a class="menu_cart" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                        aria-controls="offcanvasRight">
                        <i class="far fa-shopping-basket"></i>
                        <span class="qnty cart-badge" style="{{ $headerCartCount == 0 ? 'display:none;' : '' }}">{{ $headerCartCount }}</span>
                    </a>
                </li>
                <li>
                    <a class="menu_order common_btn" href="{{ route('website.reservation.index') }}">
                        {{ __('Reserve Now') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="menu_search_area">
    <form action="{{ route('website.menu') }}" method="GET">
        <input type="text" name="search" placeholder="{{ __('Search Menu...') }}" value="{{ request('search') }}">
        <button class="common_btn" type="submit">{{ __('Search') }}</button>
        <span class="close_search"><i class="far fa-times"></i></span>
    </form>
</div>

<div class="mini_cart">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">
                {{ __('My Cart') }} <span id="mini-cart-count">({{ $headerCartCount }})</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="far fa-times"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <ul id="mini-cart-items">
                @forelse($headerCartItems as $cartItem)
                <li data-cart-item-id="{{ $cartItem->id }}">
                    <div class="img">
                        @if($cartItem->menuItem && $cartItem->menuItem->image)
                            <img src="{{ asset($cartItem->menuItem->image) }}" alt="{{ $cartItem->menuItem->name }}" class="img-fluid w-100">
                        @else
                            <img src="{{ asset('website/images/placeholder-food.png') }}" alt="Item" class="img-fluid w-100">
                        @endif
                    </div>
                    <div class="text">
                        <h5>{{ $cartItem->menuItem->name ?? __('Item') }}</h5>
                        <p>
                            {{ currency($cartItem->unit_price) }}
                            <span>{{ __('Qty') }}: {{ $cartItem->quantity }}</span>
                        </p>
                        @if($cartItem->variant_name)
                            <small class="text-muted">{{ $cartItem->variant_name }}</small>
                        @endif
                    </div>
                    <span class="close_cart" onclick="removeMiniCartItem({{ $cartItem->id }})">
                        <i class="far fa-times"></i>
                    </span>
                </li>
                @empty
                <li class="empty-cart-message text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('Your cart is empty') }}</p>
                    <a href="{{ route('website.menu') }}" class="btn btn-sm btn-outline-primary">
                        {{ __('Browse Menu') }}
                    </a>
                </li>
                @endforelse
            </ul>
            <div class="mini_cart_button" id="mini-cart-footer" style="{{ $headerCartCount == 0 ? 'display:none;' : '' }}">
                <h6>{{ __('Total') }} <span id="mini-cart-total">{{ currency($headerCartTotal) }}</span></h6>
                <a class="common_btn" href="{{ route('website.cart.index') }}">{{ __('View Cart') }}</a>
                <a class="common_btn" href="{{ route('website.checkout.index') }}">{{ __('Checkout') }}</a>
            </div>
        </div>
    </div>
</div>
<!--==========MENU END===========-->

<script>
    // Mini cart remove item function
    function removeMiniCartItem(itemId) {
        fetch("{{ url('/cart/remove') }}/" + itemId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove item from mini cart
                const item = document.querySelector(`#mini-cart-items li[data-cart-item-id="${itemId}"]`);
                if (item) item.remove();

                // Update counts
                updateCartBadge(data.cart_count);
                document.getElementById('mini-cart-count').textContent = '(' + data.cart_count + ')';
                document.getElementById('mini-cart-total').textContent = '{{ currency_icon() }}' + data.cart_total.toFixed(2);

                // Show empty message if cart is empty
                if (data.cart_count === 0) {
                    document.getElementById('mini-cart-items').innerHTML = `
                        <li class="empty-cart-message text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('Your cart is empty') }}</p>
                            <a href="{{ route('website.menu') }}" class="btn btn-sm btn-outline-primary">
                                {{ __('Browse Menu') }}
                            </a>
                        </li>
                    `;
                    document.getElementById('mini-cart-footer').style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Update cart badge in header
    function updateCartBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
</script>
