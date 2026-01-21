@extends('website::layouts.master')

@section('title', __('Shopping Cart') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Shopping Cart') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('Cart') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========CART VIEW START===========-->
        <section class="cart_view mt_115 xs_mt_95">
            <div class="container">
                @if($cartItems->isEmpty())
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                                <h3>{{ __('Your cart is empty') }}</h3>
                                <p class="text-muted mb-4">{{ __('Add some delicious items from our menu!') }}</p>
                                <a href="{{ route('website.menu') }}" class="common_btn">
                                    <i class="fas fa-utensils me-2"></i>{{ __('Browse Menu') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row wow fadeInUp">
                        <div class="col-lg-12">
                            <div class="cart_list">
                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="pro_img">{{ __('Image') }}</th>
                                                <th class="pro_name">{{ __('Product Details') }}</th>
                                                <th class="pro_tk">{{ __('Price') }}</th>
                                                <th class="pro_select">{{ __('Quantity') }}</th>
                                                <th class="pro_tk">{{ __('Subtotal') }}</th>
                                                <th class="pro_icon">
                                                    <a class="clear_all" href="#" onclick="clearCart(); return false;">{{ __('Clear All') }}</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-items-body">
                                            @foreach($cartItems as $item)
                                            <tr data-cart-item-id="{{ $item->id }}">
                                                <td class="pro_img">
                                                    @if($item->menuItem && $item->menuItem->image)
                                                        <img src="{{ asset($item->menuItem->image) }}" alt="{{ $item->menuItem->name }}" class="img-fluid w-100">
                                                    @else
                                                        <img src="{{ asset('website/images/placeholder-food.png') }}" alt="{{ $item->menuItem->name ?? 'Item' }}" class="img-fluid w-100">
                                                    @endif
                                                </td>

                                                <td class="pro_name">
                                                    <a href="{{ route('website.menu-details', $item->menuItem->slug ?? $item->menu_item_id) }}">
                                                        {{ $item->menuItem->name ?? __('Unknown Item') }}
                                                    </a>
                                                    @if($item->variant_name)
                                                        <span>{{ $item->variant_name }}</span>
                                                    @endif
                                                    @if(!empty($item->addon_names))
                                                        <p>{{ implode(', ', $item->addon_names) }}</p>
                                                    @endif
                                                    @if($item->special_instructions)
                                                        <small class="text-muted d-block mt-1">
                                                            <i class="fas fa-sticky-note"></i> {{ $item->special_instructions }}
                                                        </small>
                                                    @endif
                                                </td>

                                                <td class="pro_tk">
                                                    <h6>${{ number_format($item->unit_price, 2) }}</h6>
                                                </td>

                                                <td class="pro_select">
                                                    <div class="quentity_btn">
                                                        <button type="button" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">
                                                            <i class="fal fa-minus"></i>
                                                        </button>
                                                        <input type="text" value="{{ $item->quantity }}" readonly class="cart-qty-input" data-id="{{ $item->id }}">
                                                        <button type="button" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">
                                                            <i class="fal fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                                <td class="pro_tk">
                                                    <h6 class="item-subtotal">${{ number_format($item->subtotal, 2) }}</h6>
                                                </td>

                                                <td class="pro_icon">
                                                    <a href="#" onclick="removeItem({{ $item->id }}); return false;">
                                                        <i class="far fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cart_list_footer_button mt_60">
                        <div class="row wow fadeInUp">
                            <div class="col-xl-8 col-md-6 col-lg-7">
                                <form id="coupon-form" onsubmit="applyCoupon(event)">
                                    <input type="text" name="coupon_code" id="coupon_code" placeholder="{{ __('Coupon Code') }}">
                                    <button class="common_btn" type="submit">{{ __('Apply Coupon') }}</button>
                                </form>
                            </div>
                            <div class="col-xl-4 col-md-6 col-lg-5">
                                <div class="cart_summery">
                                    <h6>{{ __('Cart Summary') }} (<span id="cart-count">{{ $cartCount }}</span> {{ __('items') }})</h6>
                                    <p>{{ __('Subtotal') }}: <span id="cart-subtotal">${{ number_format($cartTotal, 2) }}</span></p>
                                    <p>{{ __('Delivery') }}: <span id="cart-delivery">$0.00</span></p>
                                    <p id="discount-row" style="display: none;">{{ __('Discount') }}: <span id="cart-discount">$0.00</span></p>
                                    <p class="total"><span>{{ __('Total') }}:</span> <span id="cart-total">${{ number_format($cartTotal, 2) }}</span></p>
                                    <a class="common_btn" href="{{ route('website.checkout.index') }}">{{ __('Proceed to Checkout') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
        <!--==========CART VIEW END===========-->
@endsection

@push('scripts')
<script>
    const CART_ROUTES = {
        update: "{{ route('website.cart.update', ':id') }}",
        remove: "{{ route('website.cart.remove', ':id') }}",
        clear: "{{ route('website.cart.clear') }}",
        coupon: "{{ route('website.cart.coupon') }}",
        index: "{{ route('website.cart.index') }}"
    };

    function updateQuantity(itemId, newQuantity) {
        if (newQuantity < 0) return;

        const url = CART_ROUTES.update.replace(':id', itemId);

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (newQuantity === 0) {
                    // Remove the row
                    document.querySelector(`tr[data-cart-item-id="${itemId}"]`).remove();
                } else {
                    // Update quantity input
                    const row = document.querySelector(`tr[data-cart-item-id="${itemId}"]`);
                    row.querySelector('.cart-qty-input').value = newQuantity;
                    row.querySelector('.item-subtotal').textContent = '$' + data.item.subtotal.toFixed(2);

                    // Update buttons
                    const minusBtn = row.querySelector('.quentity_btn button:first-child');
                    const plusBtn = row.querySelector('.quentity_btn button:last-child');
                    minusBtn.setAttribute('onclick', `updateQuantity(${itemId}, ${newQuantity - 1})`);
                    plusBtn.setAttribute('onclick', `updateQuantity(${itemId}, ${newQuantity + 1})`);
                }

                updateCartSummary(data.cart_count, data.cart_total);
                updateCartBadge(data.cart_count);

                // Check if cart is empty
                if (data.cart_count === 0) {
                    location.reload();
                }
            } else {
                showToast(data.message || 'Failed to update cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to update cart', 'error');
        });
    }

    function removeItem(itemId) {
        if (!confirm('{{ __("Are you sure you want to remove this item?") }}')) return;

        const url = CART_ROUTES.remove.replace(':id', itemId);

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`tr[data-cart-item-id="${itemId}"]`).remove();
                updateCartSummary(data.cart_count, data.cart_total);
                updateCartBadge(data.cart_count);

                if (data.cart_count === 0) {
                    location.reload();
                }

                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Failed to remove item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to remove item', 'error');
        });
    }

    function clearCart() {
        if (!confirm('{{ __("Are you sure you want to clear your entire cart?") }}')) return;

        fetch(CART_ROUTES.clear, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast(data.message || 'Failed to clear cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to clear cart', 'error');
        });
    }

    function applyCoupon(event) {
        event.preventDefault();
        const code = document.getElementById('coupon_code').value.trim();

        if (!code) {
            showToast('{{ __("Please enter a coupon code") }}', 'warning');
            return;
        }

        fetch(CART_ROUTES.coupon, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Update discount display
                document.getElementById('discount-row').style.display = 'block';
                document.getElementById('cart-discount').textContent = '-$' + data.discount.toFixed(2);
                document.getElementById('cart-total').textContent = '$' + data.new_total.toFixed(2);
            } else {
                showToast(data.message || 'Invalid coupon code', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to apply coupon', 'error');
        });
    }

    function updateCartSummary(count, total) {
        document.getElementById('cart-count').textContent = count;
        document.getElementById('cart-subtotal').textContent = '$' + total.toFixed(2);
        document.getElementById('cart-total').textContent = '$' + total.toFixed(2);
    }

    function updateCartBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    function showToast(message, type = 'info') {
        // Use SweetAlert if available, otherwise use alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }
</script>
@endpush
