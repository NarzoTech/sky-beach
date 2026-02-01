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
            <img src="{{ !empty($setting->frontend_logo ?? null) ? asset($setting->frontend_logo) : (!empty($setting->logo ?? null) ? asset($setting->logo) : asset('website/images/logo.png')) }}" alt="{{ $setting->app_name ?? config('app.name') }}" class="img-fluid">
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
                    <div class="img" style="width: 100px; min-width: 100px; height: 100px; margin-right: 15px;">
                        @if($cartItem->menuItem && $cartItem->menuItem->image)
                            <img src="{{ asset($cartItem->menuItem->image) }}" alt="{{ $cartItem->menuItem->name }}" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
                        @else
                            <img src="{{ asset('website/images/menu_img_1.jpg') }}" alt="Item" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
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
                <li class="empty-cart-message">
                    <i class="fas fa-shopping-cart"></i>
                    <p>{{ __('Your cart is empty') }}</p>
                    <a href="{{ route('website.menu') }}">{{ __('Browse Menu') }}</a>
                </li>
                @endforelse
            </ul>
            <div class="mini_cart_button" id="mini-cart-footer" style="{{ $headerCartCount == 0 ? 'display:none;' : '' }}">
                <h6>{{ __('Total') }} <span id="mini-cart-total">{{ currency($headerCartTotal) }}</span></h6>
                <a class="common_btn" href="{{ route('website.cart.index') }}">{{ __('View Cart') }}</a>
                <a class="common_btn" href="{{ route('website.checkout.index') }}">{{ __('Checkout') }}</a>
                <a class="common_btn clear_cart_btn" href="javascript:void(0);" onclick="clearMiniCart()">{{ __('Clear Cart') }}</a>
            </div>
        </div>
    </div>
</div>
<!--==========MENU END===========-->

<script>
    // Mini cart remove item function
    function removeMiniCartItem(itemId) {
        // Immediately remove item from UI for instant feedback
        const item = document.querySelector(`#mini-cart-items li[data-cart-item-id="${itemId}"]`);
        if (item) {
            item.style.opacity = '0.5';
        }

        fetch("{{ url('/cart/remove') }}/" + itemId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove item from mini cart
                if (item) item.remove();

                // Update counts
                updateCartBadge(data.cart_count);
                const countEl = document.getElementById('mini-cart-count');
                const totalEl = document.getElementById('mini-cart-total');
                const qntyEl = document.querySelector('.menu_cart .qnty');

                if (countEl) countEl.textContent = '(' + data.cart_count + ')';
                if (totalEl) totalEl.textContent = '{{ currency_icon() }}' + parseFloat(data.cart_total).toFixed(2);
                if (qntyEl) qntyEl.textContent = data.cart_count;

                // Show empty message if cart is empty
                if (data.cart_count === 0) {
                    document.getElementById('mini-cart-items').innerHTML = `
                        <li class="empty-cart-message">
                            <i class="fas fa-shopping-cart"></i>
                            <p>{{ __('Your cart is empty') }}</p>
                            <a href="{{ route('website.menu') }}">{{ __('Browse Menu') }}</a>
                        </li>
                    `;
                    document.getElementById('mini-cart-footer').style.display = 'none';
                }
            } else {
                // Restore item if failed
                if (item) item.style.opacity = '1';
                showHeaderToast(data.message || '{{ __("Failed to remove item") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Restore item if error
            if (item) item.style.opacity = '1';
            showHeaderToast('{{ __("Failed to remove item. Please try again.") }}', 'error');
        });
    }

    // Clear all items from mini cart
    function clearMiniCart() {
        if (!confirm('{{ __("Are you sure you want to clear your cart?") }}')) {
            return;
        }

        fetch("{{ route('website.cart.clear') }}", {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update counts
                updateCartBadge(0);
                document.getElementById('mini-cart-count').textContent = '(0)';
                document.getElementById('mini-cart-total').textContent = '{{ currency_icon() }}0.00';

                // Show empty message
                document.getElementById('mini-cart-items').innerHTML = `
                    <li class="empty-cart-message">
                        <i class="fas fa-shopping-cart"></i>
                        <p>{{ __('Your cart is empty') }}</p>
                        <a href="{{ route('website.menu') }}">{{ __('Browse Menu') }}</a>
                    </li>
                `;
                document.getElementById('mini-cart-footer').style.display = 'none';

                // Update header cart badge
                const qntyEl = document.querySelector('.menu_cart .qnty');
                if (qntyEl) qntyEl.textContent = '0';
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

    // Toast notification for header actions
    function showHeaderToast(message, type = 'success') {
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
</script>
