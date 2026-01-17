@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Create Order') }} - {{ $table->name }}</title>
@endsection

@push('css')
<style>
    .category-tabs {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 0;
    }
    .category-tabs .nav-link {
        border-radius: 20px;
        padding: 8px 20px;
        margin-right: 10px;
        background: #f8f9fa;
        color: #333;
        border: none;
    }
    .category-tabs .nav-link.active {
        background: #007bff;
        color: white;
    }
    .menu-item-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        height: 100%;
    }
    .menu-item-card:hover {
        border-color: #007bff;
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .menu-item-card.in-cart {
        border-color: #28a745;
        background: #f8fff8;
    }
    .menu-item-img {
        height: 120px;
        object-fit: cover;
        border-radius: 8px 8px 0 0;
    }
    .cart-panel {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
    .cart-item {
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .qty-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        font-size: 14px;
    }
    .addon-badge {
        font-size: 0.75rem;
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 10px;
        margin-right: 5px;
        margin-top: 3px;
        display: inline-block;
    }
    .table-info-banner {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border-radius: 10px;
        padding: 15px 20px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header with Table Info -->
        <div class="table-info-banner mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1"><i class="fas fa-utensils me-2"></i>{{ $table->name }}</h4>
                    <span><i class="fas fa-users me-1"></i>Capacity: {{ $table->capacity }} seats</span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.select-table') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-exchange-alt me-1"></i>{{ __('Change Table') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Menu Section -->
            <div class="col-lg-8">
                <!-- Guest Count -->
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label class="mb-0"><strong>{{ __('Number of Guests') }}:</strong></label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group" style="width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateGuestCount(-1)">-</button>
                                    <input type="number" class="form-control text-center" id="guest-count" value="1" min="1" max="{{ $table->capacity }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateGuestCount(1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="category-tabs mb-3">
                    <nav class="nav">
                        <a class="nav-link active" href="#" data-category="all" onclick="filterCategory('all', this)">
                            {{ __('All Items') }}
                        </a>
                        @foreach($categories as $category)
                        <a class="nav-link" href="#" data-category="{{ $category->id }}" onclick="filterCategory({{ $category->id }}, this)">
                            {{ $category->name }}
                        </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Menu Items Grid -->
                <div class="row" id="menu-items-grid">
                    @foreach($categories as $category)
                        @foreach($category->menuItems as $item)
                        <div class="col-md-4 col-6 mb-3 menu-item-wrapper" data-category="{{ $category->id }}">
                            <div class="card menu-item-card h-100" onclick="showItemModal({{ json_encode($item) }})" data-item-id="{{ $item->id }}">
                                @if($item->image)
                                <img src="{{ asset($item->image) }}" class="menu-item-img" alt="{{ $item->name }}">
                                @else
                                <div class="menu-item-img bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-utensils fa-3x text-muted"></i>
                                </div>
                                @endif
                                <div class="card-body p-2">
                                    <h6 class="mb-1 text-truncate">{{ $item->name }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary">{{ number_format($item->price, 2) }}</strong>
                                        <span class="badge bg-light text-dark cart-qty-badge" style="display: none;">
                                            <i class="fas fa-shopping-cart"></i> <span>0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <!-- Cart Section -->
            <div class="col-lg-4">
                <div class="card cart-panel">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>{{ __('Order Cart') }}</h5>
                    </div>
                    <div class="card-body" id="cart-items">
                        <div class="text-center text-muted py-4" id="empty-cart">
                            <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                            <p>{{ __('Cart is empty') }}</p>
                            <small>{{ __('Tap items to add to cart') }}</small>
                        </div>
                    </div>
                    <div class="card-footer" id="cart-footer" style="display: none;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Subtotal') }}:</span>
                            <strong id="cart-subtotal">0.00</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">{{ __('Special Instructions') }}</label>
                            <textarea class="form-control" id="special-instructions" rows="2" placeholder="{{ __('Any special requests...') }}"></textarea>
                        </div>
                        <button class="btn btn-success w-100 btn-lg" onclick="placeOrder()" id="place-order-btn">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Place Order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalTitle">Item Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="itemModalImage" class="text-center mb-3"></div>
                <p id="itemModalDescription" class="text-muted"></p>
                <h5 class="text-primary" id="itemModalPrice">$0.00</h5>

                <!-- Quantity -->
                <div class="mb-3">
                    <label class="form-label">{{ __('Quantity') }}</label>
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(-1)">-</button>
                        <input type="number" class="form-control text-center" id="item-qty" value="1" min="1">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(1)">+</button>
                    </div>
                </div>

                <!-- Addons -->
                <div id="addons-section" style="display: none;">
                    <label class="form-label">{{ __('Add-ons') }}</label>
                    <div id="addons-list"></div>
                </div>

                <!-- Note -->
                <div class="mb-3">
                    <label class="form-label">{{ __('Item Note') }}</label>
                    <input type="text" class="form-control" id="item-note" placeholder="{{ __('Special request for this item...') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addToCart()">
                    <i class="fas fa-plus me-1"></i>{{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let cart = [];
    let currentItem = null;
    const tableId = {{ $table->id }};
    const tableCapacity = {{ $table->capacity }};

    function updateGuestCount(delta) {
        const input = document.getElementById('guest-count');
        let value = parseInt(input.value) + delta;
        value = Math.max(1, Math.min(tableCapacity, value));
        input.value = value;
    }

    function filterCategory(categoryId, element) {
        document.querySelectorAll('.category-tabs .nav-link').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        document.querySelectorAll('.menu-item-wrapper').forEach(item => {
            if (categoryId === 'all' || item.dataset.category == categoryId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function showItemModal(item) {
        currentItem = item;
        document.getElementById('itemModalTitle').textContent = item.name;
        document.getElementById('itemModalPrice').textContent = parseFloat(item.price).toFixed(2);
        document.getElementById('itemModalDescription').textContent = item.description || '';
        document.getElementById('item-qty').value = 1;
        document.getElementById('item-note').value = '';

        // Image
        const imageDiv = document.getElementById('itemModalImage');
        if (item.image) {
            imageDiv.innerHTML = `<img src="{{ asset('') }}${item.image}" class="img-fluid rounded" style="max-height: 200px;">`;
        } else {
            imageDiv.innerHTML = '';
        }

        // Addons
        const addonsSection = document.getElementById('addons-section');
        const addonsList = document.getElementById('addons-list');
        if (item.addons && item.addons.length > 0) {
            addonsSection.style.display = 'block';
            addonsList.innerHTML = item.addons.map(addon => `
                <div class="form-check mb-2">
                    <input class="form-check-input addon-checkbox" type="checkbox" value="${addon.id}"
                           data-name="${addon.name}" data-price="${addon.price}" id="addon-${addon.id}">
                    <label class="form-check-label" for="addon-${addon.id}">
                        ${addon.name} <span class="text-primary">(+${parseFloat(addon.price).toFixed(2)})</span>
                    </label>
                </div>
            `).join('');
        } else {
            addonsSection.style.display = 'none';
            addonsList.innerHTML = '';
        }

        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }

    function updateItemQty(delta) {
        const input = document.getElementById('item-qty');
        let value = parseInt(input.value) + delta;
        value = Math.max(1, value);
        input.value = value;
    }

    function addToCart() {
        const qty = parseInt(document.getElementById('item-qty').value);
        const note = document.getElementById('item-note').value;

        // Get selected addons
        const addons = [];
        document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
            addons.push({
                id: checkbox.value,
                name: checkbox.dataset.name,
                price: parseFloat(checkbox.dataset.price),
                qty: 1
            });
        });

        // Calculate item total
        let itemTotal = parseFloat(currentItem.price) * qty;
        addons.forEach(addon => {
            itemTotal += addon.price * addon.qty;
        });

        // Check if item already in cart (without addons for simplicity)
        const existingIndex = cart.findIndex(item =>
            item.menu_item_id === currentItem.id &&
            JSON.stringify(item.addons) === JSON.stringify(addons) &&
            item.note === note
        );

        if (existingIndex > -1) {
            cart[existingIndex].quantity += qty;
            cart[existingIndex].subtotal = calculateItemSubtotal(cart[existingIndex]);
        } else {
            cart.push({
                menu_item_id: currentItem.id,
                name: currentItem.name,
                price: parseFloat(currentItem.price),
                quantity: qty,
                addons: addons,
                note: note,
                subtotal: itemTotal
            });
        }

        updateCartUI();
        bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
    }

    function calculateItemSubtotal(item) {
        let subtotal = item.price * item.quantity;
        item.addons.forEach(addon => {
            subtotal += addon.price * (addon.qty || 1) * item.quantity;
        });
        return subtotal;
    }

    function updateCartUI() {
        const cartItemsDiv = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');
        const cartFooter = document.getElementById('cart-footer');

        if (cart.length === 0) {
            emptyCart.style.display = 'block';
            cartFooter.style.display = 'none';
            cartItemsDiv.innerHTML = emptyCart.outerHTML;
            return;
        }

        emptyCart.style.display = 'none';
        cartFooter.style.display = 'block';

        let html = '';
        let subtotal = 0;

        cart.forEach((item, index) => {
            subtotal += item.subtotal;
            html += `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <strong>${item.name}</strong>
                            <div class="small text-muted">${item.price.toFixed(2)} each</div>
                            ${item.addons.length > 0 ? `
                                <div class="mt-1">
                                    ${item.addons.map(a => `<span class="addon-badge">+ ${a.name}</span>`).join('')}
                                </div>
                            ` : ''}
                            ${item.note ? `<div class="small text-info mt-1"><i class="fas fa-sticky-note"></i> ${item.note}</div>` : ''}
                        </div>
                        <div class="text-end">
                            <div class="mb-1">${item.subtotal.toFixed(2)}</div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary qty-btn" onclick="updateCartItemQty(${index}, -1)">-</button>
                                <span class="btn btn-outline-secondary qty-btn" style="pointer-events: none;">${item.quantity}</span>
                                <button class="btn btn-outline-secondary qty-btn" onclick="updateCartItemQty(${index}, 1)">+</button>
                            </div>
                            <button class="btn btn-outline-danger btn-sm ms-1" onclick="removeFromCart(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItemsDiv.innerHTML = html;
        document.getElementById('cart-subtotal').textContent = subtotal.toFixed(2);

        // Update item cards to show quantities
        document.querySelectorAll('.menu-item-card').forEach(card => {
            const itemId = parseInt(card.dataset.itemId);
            const inCart = cart.filter(item => item.menu_item_id === itemId);
            const totalQty = inCart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = card.querySelector('.cart-qty-badge');

            if (totalQty > 0) {
                card.classList.add('in-cart');
                badge.style.display = 'inline';
                badge.querySelector('span').textContent = totalQty;
            } else {
                card.classList.remove('in-cart');
                badge.style.display = 'none';
            }
        });
    }

    function updateCartItemQty(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        } else {
            cart[index].subtotal = calculateItemSubtotal(cart[index]);
        }
        updateCartUI();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    function placeOrder() {
        if (cart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Cart Empty',
                text: 'Please add items to the cart before placing an order.'
            });
            return;
        }

        const guestCount = parseInt(document.getElementById('guest-count').value);
        const specialInstructions = document.getElementById('special-instructions').value;

        const orderData = {
            table_id: tableId,
            guest_count: guestCount,
            items: cart.map(item => ({
                menu_item_id: item.menu_item_id,
                quantity: item.quantity,
                addons: item.addons,
                note: item.note
            })),
            special_instructions: specialInstructions
        };

        document.getElementById('place-order-btn').disabled = true;
        document.getElementById('place-order-btn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Placing Order...';

        $.ajax({
            url: "{{ route('admin.waiter.store-order') }}",
            method: 'POST',
            data: JSON.stringify(orderData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed!',
                        text: 'Order #' + response.order_id + ' has been sent to the kitchen.',
                        confirmButtonText: 'Back to Dashboard'
                    }).then(() => {
                        window.location.href = response.redirect || "{{ route('admin.waiter.dashboard') }}";
                    });
                } else {
                    throw new Error(response.message);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to place order. Please try again.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                document.getElementById('place-order-btn').disabled = false;
                document.getElementById('place-order-btn').innerHTML = '<i class="fas fa-paper-plane me-2"></i>Place Order';
            }
        });
    }
</script>
@endpush
