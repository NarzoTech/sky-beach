@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Add Items to Order') }} #{{ $order->id }}</title>
@endsection

@push('css')
<style>
    .category-tabs {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 0;
        scrollbar-width: none;
    }
    .category-tabs::-webkit-scrollbar {
        display: none;
    }
    .category-tabs .nav-link {
        border-radius: 20px;
        padding: 8px 20px;
        margin-right: 10px;
        background: #f8f9fa;
        color: #333;
        border: none;
        font-weight: 500;
    }
    .category-tabs .nav-link.active {
        background: #28a745;
        color: white;
    }
    .menu-item-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .menu-item-card:hover {
        border-color: #28a745;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .menu-item-card.in-cart {
        border-color: #28a745;
        background: #f0fff0;
    }
    .menu-item-card.in-cart::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-top: 30px solid #28a745;
        border-left: 30px solid transparent;
    }
    .menu-item-img {
        height: 100px;
        object-fit: cover;
        width: 100%;
    }
    .cart-panel {
        position: sticky;
        top: 70px;
        max-height: calc(100vh - 90px);
        display: flex;
        flex-direction: column;
    }
    .cart-items-wrapper {
        flex: 1;
        overflow-y: auto;
        max-height: 300px;
    }
    .cart-item {
        border-bottom: 1px solid #eee;
        padding: 12px 0;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .qty-btn {
        width: 28px;
        height: 28px;
        padding: 0;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .addon-badge {
        font-size: 0.7rem;
        background: #d4edda;
        color: #155724;
        padding: 2px 8px;
        border-radius: 10px;
        margin-right: 4px;
        margin-top: 2px;
        display: inline-block;
    }
    .order-info-banner {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-radius: 10px;
        padding: 12px 20px;
    }
    .quick-add-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        padding: 0;
        font-size: 18px;
        z-index: 10;
    }
    .cart-qty-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 10;
    }
    .addon-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .addon-item.selected {
        background: #d4edda;
        border-color: #28a745;
    }
    .addon-qty-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .addon-qty-btn {
        width: 26px;
        height: 26px;
        padding: 0;
        font-size: 12px;
        border-radius: 50%;
    }
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .search-box input {
        padding-left: 38px;
    }
    .item-price {
        font-size: 1rem;
        font-weight: 600;
        color: #28a745;
    }
    .modal-body-scroll {
        max-height: 60vh;
        overflow-y: auto;
    }
    .existing-items-section {
        background: #f8f9fa;
        border-radius: 10px;
        max-height: 200px;
        overflow-y: auto;
    }
    .existing-item {
        padding: 8px 12px;
        border-bottom: 1px solid #e9ecef;
    }
    .existing-item:last-child {
        border-bottom: none;
    }
    /* Combo styles */
    .combo-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .combo-card:hover {
        border-color: #ffc107;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .combo-card.in-cart {
        border-color: #28a745;
        background: #f0fff0;
    }
    .combo-savings {
        background: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    .combo-original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 0.85rem;
    }
    .combo-item-list {
        font-size: 0.75rem;
        color: #666;
    }
    .combo-section {
        background: linear-gradient(135deg, #fff3cd, #ffeeba);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .combo-section-title {
        color: #856404;
        font-weight: bold;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header with Order Info -->
        <div class="order-info-banner mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1">
                        <i class="fas fa-plus-circle me-2"></i>{{ __('Add Items to Order') }} #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </h4>
                    <span>
                        @if($order->table)
                        <i class="fas fa-utensils me-1"></i>{{ $order->table->name }} |
                        @endif
                        <i class="fas fa-clock me-1"></i>{{ $order->created_at->diffForHumans() }} |
                        <i class="fas fa-user me-1"></i>{{ $order->guest_count ?? 1 }} {{ __('guests') }}
                    </span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.order-details', $order->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-eye me-1"></i>{{ __('View Order') }}
                    </a>
                    <a href="{{ route('admin.waiter.dashboard') }}" class="btn btn-outline-light btn-sm ms-1">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Dashboard') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Menu Section -->
            <div class="col-lg-8">
                <!-- Search -->
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" id="search-items" placeholder="{{ __('Search menu items...') }}" onkeyup="searchItems(this.value)">
                        </div>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="category-tabs mb-3">
                    <nav class="nav">
                        <a class="nav-link active" href="#" data-category="all" onclick="filterCategory('all', this); return false;">
                            <i class="fas fa-th me-1"></i>{{ __('All') }}
                        </a>
                        @if(isset($combos) && $combos->count() > 0)
                        <a class="nav-link" href="#" data-category="combos" onclick="filterCategory('combos', this); return false;">
                            <i class="fas fa-gift me-1"></i>{{ __('Combos') }}
                            <span class="badge bg-warning text-dark ms-1">{{ $combos->count() }}</span>
                        </a>
                        @endif
                        @foreach($categories as $category)
                        <a class="nav-link" href="#" data-category="{{ $category->id }}" onclick="filterCategory({{ $category->id }}, this); return false;">
                            {{ $category->name }}
                            <span class="badge bg-secondary ms-1">{{ $category->menuItems->count() }}</span>
                        </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Combos Section -->
                @if(isset($combos) && $combos->count() > 0)
                <div class="combo-section menu-item-wrapper" data-category="combos" id="combos-section">
                    <h5 class="combo-section-title"><i class="fas fa-gift me-2"></i>{{ __('Combo Deals') }}</h5>
                    <div class="row g-2">
                        @foreach($combos as $combo)
                        <div class="col-lg-4 col-md-6 col-12 combo-item-wrapper" data-name="{{ strtolower($combo->name) }}">
                            <div class="card combo-card h-100" data-combo-id="{{ $combo->id }}" data-combo='@json($combo)' onclick="showComboModal({{ $combo->id }})">
                                <span class="badge bg-success cart-qty-badge" style="display: none;">
                                    <i class="fas fa-check"></i> <span class="qty-num">0</span>
                                </span>
                                @if($combo->savings_percentage > 0)
                                <span class="combo-savings" style="position: absolute; top: 8px; right: 8px;">
                                    {{ __('Save') }} {{ $combo->savings_percentage }}%
                                </span>
                                @endif
                                @if($combo->image)
                                <img src="{{ $combo->image_url }}" class="menu-item-img" alt="{{ $combo->name }}">
                                @else
                                <div class="menu-item-img bg-warning bg-opacity-25 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-gift fa-2x text-warning"></i>
                                </div>
                                @endif
                                <div class="card-body p-2">
                                    <h6 class="mb-1 small fw-bold">{{ $combo->name }}</h6>
                                    <div class="combo-item-list mb-1">
                                        @foreach($combo->items->take(3) as $comboItem)
                                            <span>{{ $comboItem->menuItem->name ?? '' }}{{ !$loop->last ? ', ' : '' }}</span>
                                        @endforeach
                                        @if($combo->items->count() > 3)
                                            <span>+{{ $combo->items->count() - 3 }} {{ __('more') }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="combo-original-price">${{ number_format($combo->original_price, 2) }}</span>
                                            <span class="item-price ms-1">${{ number_format($combo->combo_price, 2) }}</span>
                                        </div>
                                        <button class="btn btn-warning btn-sm" onclick="event.stopPropagation(); quickAddCombo({{ $combo->id }});">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Menu Items Grid -->
                <div class="row g-2" id="menu-items-grid">
                    @foreach($categories as $category)
                        @foreach($category->menuItems as $item)
                        <div class="col-lg-3 col-md-4 col-6 menu-item-wrapper" data-category="{{ $category->id }}" data-name="{{ strtolower($item->name) }}">
                            <div class="card menu-item-card h-100" data-item-id="{{ $item->id }}" data-item='@json($item)'>
                                <span class="badge bg-success cart-qty-badge" style="display: none;">
                                    <i class="fas fa-check"></i> <span class="qty-num">0</span>
                                </span>
                                @if($item->image)
                                <img src="{{ asset($item->image) }}" class="menu-item-img" alt="{{ $item->name }}" onclick="showItemModal({{ $item->id }})">
                                @else
                                <div class="menu-item-img bg-light d-flex align-items-center justify-content-center" onclick="showItemModal({{ $item->id }})">
                                    <i class="fas fa-utensils fa-2x text-muted"></i>
                                </div>
                                @endif
                                <div class="card-body p-2" onclick="showItemModal({{ $item->id }})">
                                    <h6 class="mb-1 small" style="line-height: 1.2;">{{ Str::limit($item->name, 25) }}</h6>
                                    <div class="item-price">${{ number_format($item->price, 2) }}</div>
                                </div>
                                @if($item->addons->isEmpty())
                                <button class="btn btn-success quick-add-btn" onclick="event.stopPropagation(); quickAdd({{ $item->id }});" title="{{ __('Quick Add') }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                                @else
                                <button class="btn btn-primary quick-add-btn" onclick="event.stopPropagation(); showItemModal({{ $item->id }});" title="{{ __('Customize') }}">
                                    <i class="fas fa-cog"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <!-- Cart Section -->
            <div class="col-lg-4">
                <!-- Existing Order Items -->
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>{{ __('Current Order') }} ({{ $order->details->count() }} {{ __('items') }})</h6>
                    </div>
                    <div class="card-body existing-items-section p-2">
                        @foreach($order->details as $detail)
                        <div class="existing-item">
                            <div class="d-flex justify-content-between">
                                <span class="small">
                                    <strong>{{ $detail->quantity }}x</strong> {{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}
                                </span>
                                <span class="small fw-bold">${{ number_format($detail->sub_total, 2) }}</span>
                            </div>
                        </div>
                        @endforeach
                        <div class="p-2 bg-light text-end">
                            <strong>{{ __('Current Total') }}: ${{ number_format($order->total, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- New Items Cart -->
                <div class="card cart-panel">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>{{ __('New Items') }}</h6>
                        <span class="badge bg-light text-success" id="cart-count">0 {{ __('items') }}</span>
                    </div>
                    <div class="card-body cart-items-wrapper p-2" id="cart-items">
                        <div class="text-center text-muted py-4" id="empty-cart">
                            <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                            <p class="mb-1">{{ __('No new items') }}</p>
                            <small>{{ __('Tap items to add') }}</small>
                        </div>
                    </div>
                    <div class="card-footer" id="cart-footer" style="display: none;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('New Items Total') }}:</span>
                            <strong class="text-success" id="cart-subtotal">$0.00</strong>
                        </div>
                        <button class="btn btn-success w-100" onclick="addItemsToOrder()" id="add-items-btn">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Send to Kitchen') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="itemModalTitle">Item Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-scroll">
                <div id="itemModalImage" class="text-center mb-3"></div>
                <p id="itemModalDescription" class="text-muted small"></p>

                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                    <span class="h5 mb-0 text-success" id="itemModalPrice">$0.00</span>
                    <div class="input-group" style="width: 140px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(-1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="form-control text-center fw-bold" id="item-qty" value="1" min="1" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Addons -->
                <div id="addons-section" style="display: none;">
                    <label class="form-label fw-bold"><i class="fas fa-plus-circle me-1"></i>{{ __('Add-ons') }}</label>
                    <div id="addons-list"></div>
                </div>

                <!-- Note -->
                <div class="mt-3">
                    <label class="form-label small text-muted">{{ __('Special Request') }}</label>
                    <input type="text" class="form-control form-control-sm" id="item-note" placeholder="{{ __('E.g., No onions, extra spicy...') }}">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-success px-4" onclick="addToCart()">
                    <i class="fas fa-cart-plus me-1"></i>{{ __('Add') }} <span id="modal-total"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Combo Modal -->
<div class="modal fade" id="comboModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-25 border-0 pb-0">
                <h5 class="modal-title" id="comboModalTitle">
                    <i class="fas fa-gift text-warning me-2"></i>Combo Name
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-scroll">
                <div id="comboModalImage" class="text-center mb-3"></div>
                <p id="comboModalDescription" class="text-muted small"></p>

                <!-- Combo Items List -->
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-list me-1"></i>{{ __('Includes') }}:</label>
                    <ul class="list-group list-group-flush" id="combo-items-list"></ul>
                </div>

                <!-- Price Display -->
                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-warning bg-opacity-10 rounded">
                    <div>
                        <span class="combo-original-price h6" id="comboModalOriginalPrice">$0.00</span>
                        <span class="h4 mb-0 text-success ms-2" id="comboModalPrice">$0.00</span>
                        <span class="badge bg-danger ms-2" id="comboModalSavings">Save 0%</span>
                    </div>
                    <div class="input-group" style="width: 140px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateComboQty(-1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="form-control text-center fw-bold" id="combo-qty" value="1" min="1" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="updateComboQty(1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Note -->
                <div class="mt-3">
                    <label class="form-label small text-muted">{{ __('Special Request') }}</label>
                    <input type="text" class="form-control form-control-sm" id="combo-note" placeholder="{{ __('E.g., No onions, extra spicy...') }}">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-warning px-4" onclick="addComboToCart()">
                    <i class="fas fa-cart-plus me-1"></i>{{ __('Add Combo') }} <span id="combo-modal-total"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let cart = [];
    let comboCart = [];
    let currentItem = null;
    let currentItemData = null;
    let currentCombo = null;
    const orderId = {{ $order->id }};
    const menuItems = {};
    const combos = {};

    // Initialize menu items data
    document.querySelectorAll('.menu-item-card[data-item]').forEach(card => {
        const item = JSON.parse(card.dataset.item);
        menuItems[item.id] = item;
    });

    // Initialize combos data
    document.querySelectorAll('.combo-card[data-combo]').forEach(card => {
        const combo = JSON.parse(card.dataset.combo);
        combos[combo.id] = combo;
    });

    function searchItems(query) {
        query = query.toLowerCase().trim();
        document.querySelectorAll('.menu-item-wrapper').forEach(item => {
            const name = item.dataset.name;
            if (query === '' || (name && name.includes(query))) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
        document.querySelectorAll('.combo-item-wrapper').forEach(item => {
            const name = item.dataset.name;
            if (query === '' || (name && name.includes(query))) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterCategory(categoryId, element) {
        document.querySelectorAll('.category-tabs .nav-link').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('search-items').value = '';

        const combosSection = document.getElementById('combos-section');
        const menuGrid = document.getElementById('menu-items-grid');

        if (categoryId === 'combos') {
            if (combosSection) combosSection.style.display = '';
            menuGrid.style.display = 'none';
        } else if (categoryId === 'all') {
            if (combosSection) combosSection.style.display = '';
            menuGrid.style.display = '';
            document.querySelectorAll('#menu-items-grid .menu-item-wrapper').forEach(item => {
                item.style.display = '';
            });
        } else {
            if (combosSection) combosSection.style.display = 'none';
            menuGrid.style.display = '';
            document.querySelectorAll('#menu-items-grid .menu-item-wrapper').forEach(item => {
                if (item.dataset.category == categoryId) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    }

    function quickAdd(itemId) {
        const item = menuItems[itemId];
        if (!item) return;

        const existingIndex = cart.findIndex(c =>
            c.menu_item_id === item.id &&
            c.addons.length === 0 &&
            !c.note
        );

        if (existingIndex > -1) {
            cart[existingIndex].quantity += 1;
            cart[existingIndex].subtotal = cart[existingIndex].price * cart[existingIndex].quantity;
        } else {
            cart.push({
                menu_item_id: item.id,
                name: item.name,
                price: parseFloat(item.price),
                quantity: 1,
                addons: [],
                note: '',
                subtotal: parseFloat(item.price)
            });
        }

        updateCartUI();
        toastSuccess(item.name + ' {{ __("added") }}');
    }

    function showItemModal(itemId) {
        const item = menuItems[itemId];
        if (!item) return;

        currentItem = item;
        currentItemData = { addons: [] };

        document.getElementById('itemModalTitle').textContent = item.name;
        document.getElementById('itemModalPrice').textContent = '$' + parseFloat(item.price).toFixed(2);
        document.getElementById('itemModalDescription').textContent = item.short_description || '';
        document.getElementById('item-qty').value = 1;
        document.getElementById('item-note').value = '';

        const imageDiv = document.getElementById('itemModalImage');
        if (item.image) {
            imageDiv.innerHTML = `<img src="{{ asset('') }}${item.image}" class="img-fluid rounded" style="max-height: 150px;">`;
        } else {
            imageDiv.innerHTML = '';
        }

        const addonsSection = document.getElementById('addons-section');
        const addonsList = document.getElementById('addons-list');

        if (item.addons && item.addons.length > 0) {
            addonsSection.style.display = 'block';
            addonsList.innerHTML = item.addons.map(addon => `
                <div class="addon-item" data-addon-id="${addon.id}" onclick="toggleAddon(${addon.id}, '${addon.name.replace(/'/g, "\\'")}', ${addon.price})">
                    <div>
                        <i class="fas fa-square addon-check-icon text-muted me-2"></i>
                        <span>${addon.name}</span>
                        <span class="text-success ms-2">+$${parseFloat(addon.price).toFixed(2)}</span>
                    </div>
                    <div class="addon-qty-control" style="display: none;" onclick="event.stopPropagation();">
                        <button class="btn btn-outline-secondary addon-qty-btn" onclick="updateAddonQty(${addon.id}, -1)">-</button>
                        <span class="addon-qty-value fw-bold">1</span>
                        <button class="btn btn-outline-secondary addon-qty-btn" onclick="updateAddonQty(${addon.id}, 1)">+</button>
                    </div>
                </div>
            `).join('');
        } else {
            addonsSection.style.display = 'none';
            addonsList.innerHTML = '';
        }

        updateModalTotal();
        new bootstrap.Modal(document.getElementById('itemModal')).show();
    }

    function toggleAddon(addonId, addonName, addonPrice) {
        const addonItem = document.querySelector(`.addon-item[data-addon-id="${addonId}"]`);
        const icon = addonItem.querySelector('.addon-check-icon');
        const qtyControl = addonItem.querySelector('.addon-qty-control');

        const existingIndex = currentItemData.addons.findIndex(a => a.id === addonId);

        if (existingIndex > -1) {
            currentItemData.addons.splice(existingIndex, 1);
            addonItem.classList.remove('selected');
            icon.classList.remove('fa-check-square', 'text-success');
            icon.classList.add('fa-square', 'text-muted');
            qtyControl.style.display = 'none';
        } else {
            currentItemData.addons.push({
                id: addonId,
                name: addonName,
                price: addonPrice,
                qty: 1
            });
            addonItem.classList.add('selected');
            icon.classList.remove('fa-square', 'text-muted');
            icon.classList.add('fa-check-square', 'text-success');
            qtyControl.style.display = 'flex';
        }

        updateModalTotal();
    }

    function updateAddonQty(addonId, delta) {
        const addon = currentItemData.addons.find(a => a.id === addonId);
        if (!addon) return;

        addon.qty = Math.max(1, addon.qty + delta);

        const addonItem = document.querySelector(`.addon-item[data-addon-id="${addonId}"]`);
        addonItem.querySelector('.addon-qty-value').textContent = addon.qty;

        updateModalTotal();
    }

    function updateItemQty(delta) {
        const input = document.getElementById('item-qty');
        let value = parseInt(input.value) + delta;
        value = Math.max(1, value);
        input.value = value;
        updateModalTotal();
    }

    function updateModalTotal() {
        const qty = parseInt(document.getElementById('item-qty').value);
        let total = parseFloat(currentItem.price) * qty;

        currentItemData.addons.forEach(addon => {
            total += addon.price * addon.qty * qty;
        });

        document.getElementById('modal-total').textContent = '$' + total.toFixed(2);
    }

    function addToCart() {
        const qty = parseInt(document.getElementById('item-qty').value);
        const note = document.getElementById('item-note').value.trim();
        const addons = [...currentItemData.addons];

        let subtotal = parseFloat(currentItem.price) * qty;
        addons.forEach(addon => {
            subtotal += addon.price * addon.qty * qty;
        });

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
                subtotal: subtotal
            });
        }

        updateCartUI();
        bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
        toastSuccess(currentItem.name + ' {{ __("added") }}');
    }

    function calculateItemSubtotal(item) {
        let subtotal = item.price * item.quantity;
        item.addons.forEach(addon => {
            subtotal += addon.price * (addon.qty || 1) * item.quantity;
        });
        return subtotal;
    }

    // ============ COMBO FUNCTIONS ============

    function quickAddCombo(comboId) {
        const combo = combos[comboId];
        if (!combo) return;

        const existingIndex = comboCart.findIndex(c => c.combo_id === combo.id && !c.note);

        if (existingIndex > -1) {
            comboCart[existingIndex].quantity += 1;
            comboCart[existingIndex].subtotal = comboCart[existingIndex].price * comboCart[existingIndex].quantity;
        } else {
            comboCart.push({
                combo_id: combo.id,
                name: combo.name,
                price: parseFloat(combo.combo_price),
                original_price: parseFloat(combo.original_price),
                quantity: 1,
                items: combo.items || [],
                note: '',
                subtotal: parseFloat(combo.combo_price)
            });
        }

        updateCartUI();
        toastSuccess(combo.name + ' {{ __("added") }}');
    }

    function showComboModal(comboId) {
        const combo = combos[comboId];
        if (!combo) return;

        currentCombo = combo;

        document.getElementById('comboModalTitle').innerHTML = '<i class="fas fa-gift text-warning me-2"></i>' + combo.name;
        document.getElementById('comboModalPrice').textContent = '$' + parseFloat(combo.combo_price).toFixed(2);
        document.getElementById('comboModalOriginalPrice').textContent = '$' + parseFloat(combo.original_price).toFixed(2);
        document.getElementById('comboModalSavings').textContent = '{{ __("Save") }} ' + combo.savings_percentage + '%';
        document.getElementById('comboModalDescription').textContent = combo.description || '';
        document.getElementById('combo-qty').value = 1;
        document.getElementById('combo-note').value = '';

        const imageDiv = document.getElementById('comboModalImage');
        if (combo.image) {
            imageDiv.innerHTML = `<img src="${combo.image_url}" class="img-fluid rounded" style="max-height: 150px;">`;
        } else {
            imageDiv.innerHTML = '';
        }

        const itemsList = document.getElementById('combo-items-list');
        if (combo.items && combo.items.length > 0) {
            itemsList.innerHTML = combo.items.map(item => {
                const menuItem = item.menu_item;
                const variant = item.variant;
                let name = menuItem ? menuItem.name : 'Unknown Item';
                if (variant) {
                    name += ' (' + variant.name + ')';
                }
                return `
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-check text-success me-2"></i>${name}</span>
                        <span class="badge bg-secondary">${item.quantity}x</span>
                    </li>
                `;
            }).join('');
        } else {
            itemsList.innerHTML = '<li class="list-group-item px-0 text-muted">{{ __("No items") }}</li>';
        }

        updateComboModalTotal();
        new bootstrap.Modal(document.getElementById('comboModal')).show();
    }

    function updateComboQty(delta) {
        const input = document.getElementById('combo-qty');
        let value = parseInt(input.value) + delta;
        value = Math.max(1, value);
        input.value = value;
        updateComboModalTotal();
    }

    function updateComboModalTotal() {
        const qty = parseInt(document.getElementById('combo-qty').value);
        const total = parseFloat(currentCombo.combo_price) * qty;
        document.getElementById('combo-modal-total').textContent = '$' + total.toFixed(2);
    }

    function addComboToCart() {
        const qty = parseInt(document.getElementById('combo-qty').value);
        const note = document.getElementById('combo-note').value.trim();

        const subtotal = parseFloat(currentCombo.combo_price) * qty;

        const existingIndex = comboCart.findIndex(item =>
            item.combo_id === currentCombo.id && item.note === note
        );

        if (existingIndex > -1) {
            comboCart[existingIndex].quantity += qty;
            comboCart[existingIndex].subtotal = comboCart[existingIndex].price * comboCart[existingIndex].quantity;
        } else {
            comboCart.push({
                combo_id: currentCombo.id,
                name: currentCombo.name,
                price: parseFloat(currentCombo.combo_price),
                original_price: parseFloat(currentCombo.original_price),
                quantity: qty,
                items: currentCombo.items || [],
                note: note,
                subtotal: subtotal
            });
        }

        updateCartUI();
        bootstrap.Modal.getInstance(document.getElementById('comboModal')).hide();
        toastSuccess(currentCombo.name + ' {{ __("added") }}');
    }

    function updateComboCartItemQty(index, delta) {
        comboCart[index].quantity += delta;
        if (comboCart[index].quantity <= 0) {
            comboCart.splice(index, 1);
        } else {
            comboCart[index].subtotal = comboCart[index].price * comboCart[index].quantity;
        }
        updateCartUI();
    }

    function removeComboFromCart(index) {
        comboCart.splice(index, 1);
        updateCartUI();
    }

    // ============ END COMBO FUNCTIONS ============

    function updateCartUI() {
        const cartItemsDiv = document.getElementById('cart-items');
        const cartFooter = document.getElementById('cart-footer');
        const cartCount = document.getElementById('cart-count');

        const totalMenuItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalCombos = comboCart.reduce((sum, item) => sum + item.quantity, 0);
        const totalItems = totalMenuItems + totalCombos;
        cartCount.textContent = totalItems + ' {{ __("items") }}';

        if (cart.length === 0 && comboCart.length === 0) {
            cartItemsDiv.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-shopping-basket fa-3x mb-3"></i>
                    <p class="mb-1">{{ __('No new items') }}</p>
                    <small>{{ __('Tap items to add') }}</small>
                </div>
            `;
            cartFooter.style.display = 'none';
            updateMenuItemBadges();
            updateComboBadges();
            return;
        }

        cartFooter.style.display = 'block';

        let html = '';
        let subtotal = 0;

        // Display combo items first
        comboCart.forEach((combo, index) => {
            subtotal += combo.subtotal;
            html += `
                <div class="cart-item" style="background: #fff8e1; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 pe-2">
                            <div class="fw-bold small">
                                <i class="fas fa-gift text-warning me-1"></i>${combo.name}
                            </div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                $${combo.price.toFixed(2)} x ${combo.quantity}
                            </div>
                            ${combo.note ? `<div class="text-info small mt-1"><i class="fas fa-comment-alt"></i> ${combo.note}</div>` : ''}
                        </div>
                        <div class="text-end" style="min-width: 90px;">
                            <div class="fw-bold text-success mb-1">$${combo.subtotal.toFixed(2)}</div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning qty-btn" onclick="updateComboCartItemQty(${index}, -1)">-</button>
                                <span class="btn btn-warning qty-btn text-dark">${combo.quantity}</span>
                                <button class="btn btn-outline-warning qty-btn" onclick="updateComboCartItemQty(${index}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        // Display regular items
        cart.forEach((item, index) => {
            subtotal += item.subtotal;
            html += `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 pe-2">
                            <div class="fw-bold small">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">$${item.price.toFixed(2)} x ${item.quantity}</div>
                            ${item.addons.length > 0 ? `
                                <div class="mt-1">
                                    ${item.addons.map(a => `<span class="addon-badge">${a.name}${a.qty > 1 ? ' x' + a.qty : ''}</span>`).join('')}
                                </div>
                            ` : ''}
                            ${item.note ? `<div class="text-info small mt-1"><i class="fas fa-comment-alt"></i> ${item.note}</div>` : ''}
                        </div>
                        <div class="text-end" style="min-width: 90px;">
                            <div class="fw-bold text-success mb-1">$${item.subtotal.toFixed(2)}</div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary qty-btn" onclick="updateCartItemQty(${index}, -1)">-</button>
                                <span class="btn btn-light qty-btn">${item.quantity}</span>
                                <button class="btn btn-outline-secondary qty-btn" onclick="updateCartItemQty(${index}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItemsDiv.innerHTML = html;
        document.getElementById('cart-subtotal').textContent = '$' + subtotal.toFixed(2);
        updateMenuItemBadges();
        updateComboBadges();
    }

    function updateMenuItemBadges() {
        document.querySelectorAll('.menu-item-card').forEach(card => {
            const itemId = parseInt(card.dataset.itemId);
            const inCart = cart.filter(item => item.menu_item_id === itemId);
            const totalQty = inCart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = card.querySelector('.cart-qty-badge');

            if (totalQty > 0) {
                card.classList.add('in-cart');
                if (badge) {
                    badge.style.display = 'inline';
                    badge.querySelector('.qty-num').textContent = totalQty;
                }
            } else {
                card.classList.remove('in-cart');
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        });
    }

    function updateComboBadges() {
        document.querySelectorAll('.combo-card').forEach(card => {
            const comboId = parseInt(card.dataset.comboId);
            const inCart = comboCart.filter(item => item.combo_id === comboId);
            const totalQty = inCart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = card.querySelector('.cart-qty-badge');

            if (totalQty > 0) {
                card.classList.add('in-cart');
                if (badge) {
                    badge.style.display = 'inline';
                    badge.querySelector('.qty-num').textContent = totalQty;
                }
            } else {
                card.classList.remove('in-cart');
                if (badge) {
                    badge.style.display = 'none';
                }
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

    function addItemsToOrder() {
        if (cart.length === 0 && comboCart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("No Items") }}',
                text: '{{ __("Please add items to the cart first.") }}'
            });
            return;
        }

        const orderData = {
            items: cart.map(item => ({
                menu_item_id: item.menu_item_id,
                quantity: item.quantity,
                addons: item.addons,
                note: item.note
            })),
            combos: comboCart.map(combo => ({
                combo_id: combo.combo_id,
                quantity: combo.quantity,
                note: combo.note
            }))
        };

        const btn = document.getElementById('add-items-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Sending...") }}';

        $.ajax({
            url: "{{ route('admin.waiter.add-to-order.store', $order->id) }}",
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
                        title: '{{ __("Items Added!") }}',
                        text: response.message,
                        confirmButtonText: '{{ __("View Order") }}'
                    }).then(() => {
                        window.location.href = "{{ route('admin.waiter.order-details', $order->id) }}";
                    });
                } else {
                    throw new Error(response.message);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || '{{ __("Failed to add items. Please try again.") }}';
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("Error") }}',
                    text: message
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>{{ __("Send to Kitchen") }}';
            }
        });
    }

    function toastSuccess(message) {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: message
            });
        }
    }
</script>
@endpush
