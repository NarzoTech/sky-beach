@extends('admin.layouts.master')

@section('title', __('Create Order') . ' - ' . $table->name)

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
        border-radius: 6px;
        padding: 8px 16px;
        margin-right: 8px;
        background: #f5f5f9;
        color: #566a7f;
        border: none;
        font-weight: 500;
    }
    .category-tabs .nav-link.active {
        background: #696cff;
        color: #fff;
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
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .menu-item-card.in-cart {
        border-color: #71dd37;
        background: #f5faf5;
    }
    .menu-item-card.in-cart::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-top: 25px solid #71dd37;
        border-left: 25px solid transparent;
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
        max-height: 400px;
    }
    .cart-item {
        border-bottom: 1px solid #e9ecef;
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
        background: #e8fadf;
        color: #71dd37;
        padding: 2px 8px;
        border-radius: 4px;
        margin-right: 4px;
        margin-top: 2px;
        display: inline-block;
    }
    .table-info-banner {
        background: #696cff;
        color: #fff;
        border-radius: 8px;
        padding: 12px 20px;
    }
    .quick-add-btn {
        position: absolute;
        bottom: 8px;
        right: 0;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        padding: 0;
        font-size: 16px;
        z-index: 10;
    }
    .quick-add-btn i {
        margin-right: 0 !important;
    }
    .guest-count-input {
        background-color: #696cff !important;
        color: #fff !important;
        border-color: #696cff !important;
        font-weight: 600;
    }
    .guest-count-input:focus {
        background-color: #5f61e6 !important;
        color: #fff !important;
        border-color: #5f61e6 !important;
        box-shadow: none;
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
        background: #f5f5f9;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }
    .addon-item.selected {
        background: #e8fadf;
        border-color: #71dd37;
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
        border-radius: 4px;
    }
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8592a3;
    }
    .search-box input {
        padding-left: 38px;
    }
    .item-price {
        font-size: 1rem;
        font-weight: 600;
        color: #71dd37;
    }
    .modal-body-scroll {
        max-height: 60vh;
        overflow-y: auto;
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .modal-header {
        background: linear-gradient(135deg, #696cff 0%, #5f61e6 100%);
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1rem 1.5rem;
    }
    .modal-header .modal-title {
        color: #fff;
        font-weight: 600;
    }
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e9ecef;
    }
    .modal-price-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
    }
    .modal-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #696cff;
    }
    .modal-qty-control {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .modal-qty-control .btn {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
    .modal-qty-control input {
        width: 50px;
        text-align: center;
        font-weight: 600;
        border: 1px solid #e9ecef;
        border-radius: 6px;
    }
    .addon-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px solid #e9ecef;
        cursor: pointer;
        transition: all 0.2s;
    }
    .addon-item:hover {
        border-color: #696cff;
        background: #f5f5ff;
    }
    .addon-item.selected {
        background: #e8e8ff;
        border-color: #696cff;
    }
    .addon-item.selected .addon-check-icon {
        color: #696cff !important;
    }
    .addon-price {
        color: #696cff;
        font-weight: 600;
    }
    .addon-qty-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .addon-qty-btn {
        width: 28px;
        height: 28px;
        padding: 0;
        font-size: 14px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-add-cart {
        background: linear-gradient(135deg, #696cff 0%, #5f61e6 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 8px;
    }
    .btn-add-cart:hover {
        background: linear-gradient(135deg, #5f61e6 0%, #5254d4 100%);
        color: #fff;
    }

    /* Combo Modal - matches theme color */
    .modal-header.combo-header {
        background: linear-gradient(135deg, #696cff 0%, #5f61e6 100%);
    }
    .btn-add-combo {
        background: linear-gradient(135deg, #696cff 0%, #5f61e6 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 8px;
    }
    .btn-add-combo:hover {
        background: linear-gradient(135deg, #5f61e6 0%, #5254d4 100%);
        color: #fff;
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
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .combo-card.in-cart {
        border-color: #71dd37;
        background: #f5faf5;
    }
    .combo-card.in-cart::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-top: 25px solid #71dd37;
        border-left: 25px solid transparent;
    }
    .combo-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 10;
    }
    .combo-savings {
        background: #ff3e1d;
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .combo-original-price {
        text-decoration: line-through;
        color: #8592a3;
        font-size: 0.85rem;
    }
    .combo-item-list {
        font-size: 0.75rem;
        color: #8592a3;
    }
    .combo-section {
        background: #f5f5ff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #d4d5ff;
    }
    .combo-section-title {
        color: #696cff;
        font-weight: 600;
        margin-bottom: 10px;
    }

    /* Menu items scrollable container */
    .menu-items-container {
        max-height: calc(100vh - 280px);
        overflow-y: auto;
        padding-right: 5px;
    }
    .menu-items-container::-webkit-scrollbar {
        width: 6px;
    }
    .menu-items-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .menu-items-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    .menu-items-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Tablet responsive styles */
    @media (max-width: 1199px) {
        .cart-panel {
            position: relative;
            top: 0;
            max-height: none;
            margin-top: 20px;
        }
        .cart-items-wrapper {
            max-height: 300px;
        }
        .menu-items-container {
            max-height: 50vh;
        }
    }

    @media (max-width: 991px) {
        .menu-items-container {
            max-height: 400px;
        }
        .cart-panel {
            margin-top: 15px;
        }
        .cart-header-sticky {
            position: sticky;
            top: 60px;
            z-index: 100;
        }
        .table-info-banner {
            padding: 10px 15px;
        }
        .table-info-banner h5 {
            font-size: 1rem;
        }
        .menu-item-img {
            height: 80px;
        }
        .category-tabs .nav-link {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 767px) {
        .menu-items-container {
            max-height: 350px;
        }
        .cart-items-wrapper {
            max-height: 250px;
        }
        .table-info-banner .col-md-6.text-end {
            text-align: left !important;
            margin-top: 10px;
        }
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
                    <h5 class="mb-1"><i class="bx bx-chair me-1"></i>{{ $table->name }}</h5>
                    <span><i class="bx bx-user me-1"></i>{{ __('Capacity') }}: {{ $table->capacity }} {{ __('seats') }}</span>
                </div>
                <div class="col-md-6 text-end">
                    <button onclick="changeTable()" class="btn btn-light btn-sm">
                        <i class="bx bx-transfer me-1"></i>{{ __('Change Table') }}
                    </button>
                    <button onclick="clearCart()" class="btn btn-outline-light btn-sm ms-1">
                        <i class="bx bx-trash me-1"></i>{{ __('Clear Cart') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Menu Section -->
            <div class="col-lg-8">
                <!-- Search & Guest Count -->
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <div class="row align-items-center g-2">
                            <div class="col-md-6">
                                <div class="search-box">
                                    <i class="bx bx-search"></i>
                                    <input type="text" class="form-control" id="search-items" placeholder="{{ __('Search menu items...') }}" onkeyup="searchItems(this.value)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-end">
                                    <label class="mb-0 me-2"><strong>{{ __('Guests') }}:</strong></label>
                                    <div class="input-group" style="width: 130px;">
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="updateGuestCount(-1)">-</button>
                                        <input type="number" class="form-control form-control-sm text-center guest-count-input" id="guest-count" value="1" min="1" max="{{ $table->capacity }}">
                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="updateGuestCount(1)">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Tabs -->
                <div class="category-tabs mb-3">
                    <nav class="nav">
                        <a class="nav-link active" href="#" data-category="all" onclick="filterCategory('all', this); return false;">
                            <i class="bx bx-grid-alt me-1"></i>{{ __('All') }}
                        </a>
                        @if(isset($combos) && $combos->count() > 0)
                        <a class="nav-link" href="#" data-category="combos" onclick="filterCategory('combos', this); return false;">
                            <i class="bx bx-gift me-1"></i>{{ __('Combos') }}
                            <span class="badge bg-primary ms-1">{{ $combos->count() }}</span>
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

                <!-- Scrollable Menu Container -->
                <div class="menu-items-container">
                <!-- Combos Section -->
                @if(isset($combos) && $combos->count() > 0)
                <div class="combo-section menu-item-wrapper" data-category="combos" id="combos-section">
                    <h5 class="combo-section-title"><i class="bx bx-gift me-2"></i>{{ __('Combo Deals') }} - {{ __('Save More!') }}</h5>
                    <div class="row g-2">
                        @foreach($combos as $combo)
                        <div class="col-lg-4 col-md-6 col-12 combo-item-wrapper" data-name="{{ strtolower($combo->name) }}">
                            <div class="card combo-card h-100" data-combo-id="{{ $combo->id }}" data-combo='@json($combo)' onclick="showComboModal({{ $combo->id }})">
                                <span class="badge bg-success combo-badge cart-qty-badge" style="display: none;">
                                    <i class="bx bx-check"></i> <span class="qty-num">0</span>
                                </span>
                                @if($combo->savings_percentage > 0)
                                <span class="combo-savings" style="position: absolute; top: 8px; right: 8px;">
                                    {{ __('Save') }} {{ $combo->savings_percentage }}%
                                </span>
                                @endif
                                @if($combo->image)
                                <img src="{{ $combo->image_url }}" class="menu-item-img" alt="{{ $combo->name }}">
                                @else
                                <div class="menu-item-img d-flex align-items-center justify-content-center" style="background: rgba(105, 108, 255, 0.15);">
                                    <i class="bx bx-gift bx-lg" style="color: #696cff;"></i>
                                </div>
                                @endif
                                <div class="card-body p-2">
                                    <h6 class="mb-1 small fw-bold" style="line-height: 1.2;">{{ $combo->name }}</h6>
                                    <div class="combo-item-list mb-1">
                                        @foreach($combo->items->take(3) as $comboItem)
                                            <span>{{ $comboItem->menuItem->name }}{{ $comboItem->variant ? ' ('.$comboItem->variant->name.')' : '' }}{{ !$loop->last ? ', ' : '' }}</span>
                                        @endforeach
                                        @if($combo->items->count() > 3)
                                            <span>+{{ $combo->items->count() - 3 }} {{ __('more') }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="combo-original-price">{{ optional($posSettings)->currency ?? 'TK' }}{{ number_format($combo->original_price, 2) }}</span>
                                            <span class="item-price ms-1">{{ optional($posSettings)->currency ?? 'TK' }}{{ number_format($combo->combo_price, 2) }}</span>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); quickAddCombo({{ $combo->id }});">
                                            <i class="bx bx-plus"></i>
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
                                    <i class="bx bx-check"></i> <span class="qty-num">0</span>
                                </span>
                                @if($item->image)
                                <img src="{{ asset($item->image) }}" class="menu-item-img" alt="{{ $item->name }}" onclick="showItemModal({{ $item->id }})">
                                @else
                                <div class="menu-item-img bg-light d-flex align-items-center justify-content-center" onclick="showItemModal({{ $item->id }})">
                                    <i class="bx bx-restaurant text-muted" style="font-size: 2rem;"></i>
                                </div>
                                @endif
                                <div class="card-body p-2" onclick="showItemModal({{ $item->id }})">
                                    <h6 class="mb-1 small" style="line-height: 1.2;">{{ Str::limit($item->name, 25) }}</h6>
                                    <div class="item-price">{{ optional($posSettings)->currency ?? 'TK' }}{{ number_format($item->price, 2) }}</div>
                                </div>
                                @if($item->addons->isEmpty())
                                <button class="btn btn-success quick-add-btn" onclick="event.stopPropagation(); quickAdd({{ $item->id }});" title="{{ __('Quick Add') }}">
                                    <i class="bx bx-plus"></i>
                                </button>
                                @else
                                <button class="btn btn-primary quick-add-btn" onclick="event.stopPropagation(); showItemModal({{ $item->id }});" title="{{ __('Customize') }}">
                                    <i class="bx bx-cog"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>

                @if($categories->isEmpty() || $categories->sum(fn($c) => $c->menuItems->count()) === 0)
                <div class="text-center py-5">
                    <i class="bx bx-restaurant text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">{{ __('No menu items available') }}</h5>
                </div>
                @endif
                </div>
                <!-- End Scrollable Menu Container -->
            </div>

            <!-- Cart Section -->
            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="card cart-panel">
                    <div class="card-header d-flex justify-content-between align-items-center cart-header-sticky" style="background: #696cff; color: #fff; border-radius: 8px 8px 0 0;">
                        <h5 class="mb-0" style="color: #fff;"><i class="bx bx-cart me-2"></i>{{ __('Order') }}</h5>
                        <span class="badge" style="background: #fff; color: #696cff;" id="cart-count">0 {{ __('items') }}</span>
                    </div>
                    <div class="card-body cart-items-wrapper" id="cart-items">
                        <div class="text-center text-muted py-4" id="empty-cart">
                            <i class="bx bx-basket bx-lg mb-3" style="font-size: 3rem;"></i>
                            <p class="mb-1">{{ __('Cart is empty') }}</p>
                            <small>{{ __('Tap items to add') }}</small>
                        </div>
                    </div>
                    <div class="card-footer" id="cart-footer" style="display: none;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">{{ __('Subtotal') }}:</span>
                            <span id="cart-subtotal">{{ currency_icon() }}0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1" id="cart-discount-row" style="display: none;">
                            <span class="text-danger">{{ __('Discount') }}:</span>
                            <span class="text-danger" id="cart-discount">- {{ currency_icon() }}0.00</span>
                        </div>
                        @php
                            $taxRate = optional($posSettings)->pos_tax_rate ?: 15;
                        @endphp
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">{{ __('Tax') }} (<span id="cart-tax-rate">{{ $taxRate }}</span>%):</span>
                            <span id="cart-tax">{{ currency_icon() }}0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pt-2" style="border-top: 1px dashed #dee2e6;">
                            <span class="fw-bold">{{ __('Total') }}:</span>
                            <strong id="cart-total" style="color: #696cff;">{{ currency_icon() }}0.00</strong>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control form-control-sm" id="special-instructions" rows="2" placeholder="{{ __('Special instructions (optional)...') }}"></textarea>
                        </div>
                        <button class="btn btn-success w-100" onclick="placeOrder()" id="place-order-btn">
                            <i class="bx bx-send me-2"></i>{{ __('Send to Kitchen') }}
                        </button>
                        <input type="hidden" id="posTaxRate" value="{{ $taxRate }}">
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
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalTitle">Item Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-scroll">
                <div id="itemModalImage" class="text-center mb-3"></div>
                <p id="itemModalDescription" class="text-muted small mb-3"></p>

                <div class="modal-price-box d-flex justify-content-between align-items-center mb-3">
                    <span class="modal-price" id="itemModalPrice">$0.00</span>
                    <div class="modal-qty-control">
                        <button class="btn btn-outline-primary" type="button" onclick="updateItemQty(-1)">
                            <i class="bx bx-minus"></i>
                        </button>
                        <input type="number" class="form-control guest-count-input" id="item-qty" value="1" min="1" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="updateItemQty(1)">
                            <i class="bx bx-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Addons -->
                <div id="addons-section" style="display: none;">
                    <label class="form-label fw-bold mb-2"><i class="bx bx-plus-circle me-1"></i>{{ __('Add-ons') }}</label>
                    <div id="addons-list"></div>
                </div>

                <!-- Note -->
                <div class="mt-3">
                    <label class="form-label small text-muted">{{ __('Special Request') }}</label>
                    <input type="text" class="form-control" id="item-note" placeholder="{{ __('E.g., No onions, extra spicy...') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-add-cart" onclick="addToCart()">
                    <i class="bx bx-cart-add me-1"></i>{{ __('Add') }} - <span id="modal-total"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Combo Modal -->
<div class="modal fade" id="comboModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header combo-header">
                <h5 class="modal-title" id="comboModalTitle">
                    <i class="bx bx-gift me-2"></i>Combo Name
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-scroll">
                <div id="comboModalImage" class="text-center mb-3"></div>
                <p id="comboModalDescription" class="text-muted small mb-3"></p>

                <!-- Combo Items List -->
                <div class="mb-3">
                    <label class="form-label fw-bold mb-2"><i class="bx bx-list-ul me-1"></i>{{ __('Includes') }}:</label>
                    <ul class="list-group" id="combo-items-list">
                    </ul>
                </div>

                <!-- Price Display -->
                <div class="modal-price-box d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-muted text-decoration-line-through me-2" id="comboModalOriginalPrice">$0.00</span>
                        <span class="modal-price" id="comboModalPrice">$0.00</span>
                        <span class="badge bg-danger ms-2" id="comboModalSavings">Save 0%</span>
                    </div>
                    <div class="modal-qty-control">
                        <button class="btn btn-outline-primary" type="button" onclick="updateComboQty(-1)">
                            <i class="bx bx-minus"></i>
                        </button>
                        <input type="number" class="form-control guest-count-input" id="combo-qty" value="1" min="1" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="updateComboQty(1)">
                            <i class="bx bx-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Note -->
                <div class="mt-3">
                    <label class="form-label small text-muted">{{ __('Special Request') }}</label>
                    <input type="text" class="form-control" id="combo-note" placeholder="{{ __('E.g., No onions, extra spicy...') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-add-combo" onclick="addComboToCart()">
                    <i class="bx bx-cart-add me-1"></i>{{ __('Add Combo') }} - <span id="combo-modal-total"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const CART_KEY = 'waiter_cart_{{ auth()->id() }}';
    const currency = '{{ optional($posSettings)->currency ?? "TK" }}';
    let cart = [];
    let comboCart = [];
    let currentItem = null;
    let currentItemData = null;
    let currentCombo = null;
    const tableId = {{ $table->id }};
    const tableCapacity = {{ $table->capacity }};
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

    // Load cart from storage on page load
    $(document).ready(function() {
        loadCart();
        updateCartUI();
    });

    function saveCart() {
        sessionStorage.setItem(CART_KEY, JSON.stringify({
            cart: cart,
            comboCart: comboCart,
            tableId: tableId,
            guestCount: document.getElementById('guest-count').value,
            instructions: document.getElementById('special-instructions').value
        }));
    }

    function loadCart() {
        const saved = sessionStorage.getItem(CART_KEY);
        if (saved) {
            const data = JSON.parse(saved);
            cart = data.cart || [];
            comboCart = data.comboCart || [];
            if (data.guestCount) {
                document.getElementById('guest-count').value = data.guestCount;
            }
            if (data.instructions) {
                document.getElementById('special-instructions').value = data.instructions;
            }
        }
    }

    function clearCartStorage() {
        sessionStorage.removeItem(CART_KEY);
    }

    function updateGuestCount(delta) {
        const input = document.getElementById('guest-count');
        let value = parseInt(input.value) + delta;
        value = Math.max(1, Math.min(tableCapacity, value));
        input.value = value;
        saveCart();
    }

    function searchItems(query) {
        query = query.toLowerCase().trim();
        // Search menu items
        document.querySelectorAll('#menu-items-grid .menu-item-wrapper').forEach(item => {
            const name = item.dataset.name || '';
            if (query === '' || name.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
        // Search combo items
        document.querySelectorAll('.combo-item-wrapper').forEach(item => {
            const name = item.dataset.name || '';
            if (query === '' || name.includes(query)) {
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
            // Show only combos
            if (combosSection) combosSection.style.display = '';
            menuGrid.style.display = 'none';
        } else if (categoryId === 'all') {
            // Show everything
            if (combosSection) combosSection.style.display = '';
            menuGrid.style.display = '';
            document.querySelectorAll('#menu-items-grid .menu-item-wrapper').forEach(item => {
                item.style.display = '';
            });
        } else {
            // Show only selected category items
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

        // Add directly to cart without modal
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
        saveCart();

        // Show feedback
        toastSuccess(item.name + ' {{ __("added to cart") }}');
    }

    function showItemModal(itemId) {
        const item = menuItems[itemId];
        if (!item) return;

        currentItem = item;
        currentItemData = { addons: [] };

        document.getElementById('itemModalTitle').textContent = item.name;
        document.getElementById('itemModalPrice').textContent = currency + parseFloat(item.price).toFixed(2);
        document.getElementById('itemModalDescription').textContent = item.short_description || '';
        document.getElementById('item-qty').value = 1;
        document.getElementById('item-note').value = '';

        // Image
        const imageDiv = document.getElementById('itemModalImage');
        if (item.image) {
            imageDiv.innerHTML = `<img src="{{ asset('') }}${item.image}" class="img-fluid rounded" style="max-height: 150px;">`;
        } else {
            imageDiv.innerHTML = '';
        }

        // Addons
        const addonsSection = document.getElementById('addons-section');
        const addonsList = document.getElementById('addons-list');

        if (item.addons && item.addons.length > 0) {
            addonsSection.style.display = 'block';
            addonsList.innerHTML = item.addons.map(addon => `
                <div class="addon-item" data-addon-id="${addon.id}" onclick="toggleAddon(${addon.id}, '${addon.name.replace(/'/g, "\\'")}', ${addon.price})">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-square addon-check-icon text-muted me-2" style="font-size: 1.2rem;"></i>
                        <span class="fw-medium">${addon.name}</span>
                        <span class="addon-price ms-2">+${currency}${parseFloat(addon.price).toFixed(2)}</span>
                    </div>
                    <div class="addon-qty-control" style="display: none;" onclick="event.stopPropagation();">
                        <button class="btn btn-outline-primary addon-qty-btn" onclick="updateAddonQty(${addon.id}, -1)">-</button>
                        <span class="addon-qty-value fw-bold">1</span>
                        <button class="btn btn-outline-primary addon-qty-btn" onclick="updateAddonQty(${addon.id}, 1)">+</button>
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
            // Remove addon
            currentItemData.addons.splice(existingIndex, 1);
            addonItem.classList.remove('selected');
            icon.classList.remove('bx-checkbox-checked', 'text-success');
            icon.classList.add('bx-square', 'text-muted');
            qtyControl.style.display = 'none';
        } else {
            // Add addon
            currentItemData.addons.push({
                id: addonId,
                name: addonName,
                price: addonPrice,
                qty: 1
            });
            addonItem.classList.add('selected');
            icon.classList.remove('bx-square', 'text-muted');
            icon.classList.add('bx-checkbox-checked', 'text-success');
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

        document.getElementById('modal-total').textContent = currency + total.toFixed(2);
    }

    function addToCart() {
        const qty = parseInt(document.getElementById('item-qty').value);
        const note = document.getElementById('item-note').value.trim();
        const addons = [...currentItemData.addons];

        // Calculate subtotal
        let subtotal = parseFloat(currentItem.price) * qty;
        addons.forEach(addon => {
            subtotal += addon.price * addon.qty * qty;
        });

        // Check if identical item exists
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
        saveCart();
        bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
        toastSuccess(currentItem.name + ' {{ __("added to cart") }}');
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

        // Add directly to combo cart without modal
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
        saveCart();
        toastSuccess(combo.name + ' {{ __("added to cart") }}');
    }

    function showComboModal(comboId) {
        const combo = combos[comboId];
        if (!combo) return;

        currentCombo = combo;

        document.getElementById('comboModalTitle').innerHTML = '<i class="bx bx-gift me-2"></i>' + combo.name;
        document.getElementById('comboModalPrice').textContent = currency + parseFloat(combo.combo_price).toFixed(2);
        document.getElementById('comboModalOriginalPrice').textContent = currency + parseFloat(combo.original_price).toFixed(2);
        document.getElementById('comboModalSavings').textContent = '{{ __("Save") }} ' + combo.savings_percentage + '%';
        document.getElementById('comboModalDescription').textContent = combo.description || '';
        document.getElementById('combo-qty').value = 1;
        document.getElementById('combo-note').value = '';

        // Image
        const imageDiv = document.getElementById('comboModalImage');
        if (combo.image) {
            imageDiv.innerHTML = `<img src="${combo.image_url}" class="img-fluid rounded" style="max-height: 150px;">`;
        } else {
            imageDiv.innerHTML = '';
        }

        // Items list
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
                        <span><i class="bx bx-check text-success me-2"></i>${name}</span>
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
        document.getElementById('combo-modal-total').textContent = currency + total.toFixed(2);
    }

    function addComboToCart() {
        const qty = parseInt(document.getElementById('combo-qty').value);
        const note = document.getElementById('combo-note').value.trim();

        const subtotal = parseFloat(currentCombo.combo_price) * qty;

        // Check if identical combo exists
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
        saveCart();
        bootstrap.Modal.getInstance(document.getElementById('comboModal')).hide();
        toastSuccess(currentCombo.name + ' {{ __("added to cart") }}');
    }

    function updateComboCartItemQty(index, delta) {
        comboCart[index].quantity += delta;
        if (comboCart[index].quantity <= 0) {
            comboCart.splice(index, 1);
        } else {
            comboCart[index].subtotal = comboCart[index].price * comboCart[index].quantity;
        }
        updateCartUI();
        saveCart();
    }

    function removeComboFromCart(index) {
        comboCart.splice(index, 1);
        updateCartUI();
        saveCart();
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
                    <i class="bx bx-basket mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-1">{{ __('Cart is empty') }}</p>
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

        // Display combo items first with special styling
        comboCart.forEach((combo, index) => {
            subtotal += combo.subtotal;
            html += `
                <div class="cart-item" style="background: #f5f5ff; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 pe-2">
                            <div class="fw-bold small">
                                <i class="bx bx-gift me-1" style="color: #696cff;"></i>${combo.name}
                                <span class="badge bg-primary ms-1">{{ __('Combo') }}</span>
                            </div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span class="text-decoration-line-through">${currency}${combo.original_price.toFixed(2)}</span>
                                <span class="text-success ms-1">${currency}${combo.price.toFixed(2)}</span> x ${combo.quantity}
                            </div>
                            ${combo.note ? `<div class="text-info small mt-1"><i class="bx bx-comment-detail"></i> ${combo.note}</div>` : ''}
                        </div>
                        <div class="text-end" style="min-width: 100px;">
                            <div class="fw-bold text-success mb-1">${currency}${combo.subtotal.toFixed(2)}</div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary qty-btn" onclick="updateComboCartItemQty(${index}, -1)">-</button>
                                <span class="btn btn-primary qty-btn">${combo.quantity}</span>
                                <button class="btn btn-outline-primary qty-btn" onclick="updateComboCartItemQty(${index}, 1)">+</button>
                            </div>
                            <button class="btn btn-link btn-sm text-danger p-0 ms-1" onclick="removeComboFromCart(${index})" title="{{ __('Remove') }}">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        // Display regular menu items
        cart.forEach((item, index) => {
            subtotal += item.subtotal;
            html += `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1 pe-2">
                            <div class="fw-bold small">${item.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${currency}${item.price.toFixed(2)} x ${item.quantity}</div>
                            ${item.addons.length > 0 ? `
                                <div class="mt-1">
                                    ${item.addons.map(a => `<span class="addon-badge">${a.name}${a.qty > 1 ? ' x' + a.qty : ''}</span>`).join('')}
                                </div>
                            ` : ''}
                            ${item.note ? `<div class="text-info small mt-1"><i class="bx bx-comment-detail"></i> ${item.note}</div>` : ''}
                        </div>
                        <div class="text-end" style="min-width: 100px;">
                            <div class="fw-bold text-success mb-1">${currency}${item.subtotal.toFixed(2)}</div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary qty-btn" onclick="updateCartItemQty(${index}, -1)">-</button>
                                <span class="btn btn-primary qty-btn">${item.quantity}</span>
                                <button class="btn btn-outline-primary qty-btn" onclick="updateCartItemQty(${index}, 1)">+</button>
                            </div>
                            <button class="btn btn-link btn-sm text-danger p-0 ms-1" onclick="removeFromCart(${index})" title="{{ __('Remove') }}">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItemsDiv.innerHTML = html;

        // Calculate tax and total
        const taxRate = parseFloat(document.getElementById('posTaxRate')?.value) || 0;
        const taxAmount = subtotal * taxRate / 100;
        const total = subtotal + taxAmount;

        document.getElementById('cart-subtotal').textContent = currency + subtotal.toFixed(2);
        document.getElementById('cart-tax').textContent = currency + taxAmount.toFixed(2);
        document.getElementById('cart-total').textContent = currency + total.toFixed(2);

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
                badge.style.display = 'inline';
                badge.querySelector('.qty-num').textContent = totalQty;
            } else {
                card.classList.remove('in-cart');
                badge.style.display = 'none';
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
        saveCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
        saveCart();
    }

    function clearCart() {
        if (cart.length === 0 && comboCart.length === 0) return;

        Swal.fire({
            title: '{{ __("Clear Cart?") }}',
            text: '{{ __("All items will be removed from the cart.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: '{{ __("Yes, Clear") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                comboCart = [];
                updateCartUI();
                clearCartStorage();
            }
        });
    }

    function changeTable() {
        if (cart.length > 0 || comboCart.length > 0) {
            Swal.fire({
                title: '{{ __("Change Table?") }}',
                text: '{{ __("Your cart will be preserved. Continue?") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __("Yes, Change Table") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveCart();
                    window.location.href = "{{ route('admin.waiter.select-table') }}";
                }
            });
        } else {
            window.location.href = "{{ route('admin.waiter.select-table') }}";
        }
    }

    function placeOrder() {
        if (cart.length === 0 && comboCart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("Cart Empty") }}',
                text: '{{ __("Please add items before placing an order.") }}'
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
            combos: comboCart.map(combo => ({
                combo_id: combo.combo_id,
                quantity: combo.quantity,
                note: combo.note
            })),
            special_instructions: specialInstructions
        };

        const btn = document.getElementById('place-order-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>{{ __("Sending...") }}';

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
                    clearCartStorage();

                    // Print kitchen ticket and cash slip
                    printOrderReceipts(response.order_id);

                    // Show toastr success notification
                    toastr.success('{{ __("Order") }} #' + response.order_id + ' {{ __("has been sent to the kitchen.") }}', '{{ __("Order Sent!") }}', {
                        timeOut: 3000,
                        closeButton: true,
                        progressBar: true
                    });

                    // Redirect after a short delay
                    setTimeout(function() {
                        window.location.href = response.redirect || "{{ route('admin.waiter.dashboard') }}";
                    }, 1500);
                } else {
                    throw new Error(response.message);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || '{{ __("Failed to place order. Please try again.") }}';
                toastr.error(message, '{{ __("Error") }}', {
                    timeOut: 5000,
                    closeButton: true,
                    progressBar: true
                });
                btn.disabled = false;
                btn.innerHTML = '<i class="bx bx-send me-2"></i>{{ __("Send to Kitchen") }}';
            }
        });
    }

    function toastSuccess(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message, '', {
                timeOut: 1500,
                positionClass: 'toast-top-right',
                progressBar: true
            });
        }
    }

    function printOrderReceipts(orderId) {
        // Print kitchen ticket
        const kitchenUrl = "{{ url('admin/waiter/print/kitchen') }}/" + orderId;
        const kitchenWindow = window.open(kitchenUrl, 'kitchen_ticket_' + orderId, 'width=400,height=600');

        // Print cash slip after a short delay to allow first print dialog
        setTimeout(function() {
            const cashUrl = "{{ url('admin/waiter/print/cash') }}/" + orderId;
            window.open(cashUrl, 'cash_slip_' + orderId, 'width=400,height=600');
        }, 2000);
    }
</script>
@endpush
