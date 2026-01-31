<style>
.product-modal-content {
    padding: 0;
}
.product-modal-header {
    position: relative;
    background: #696cff;
    padding: 20px;
    border-radius: 0;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding-right: 50px;
}
.product-modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.product-modal-close:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}
.product-modal-body {
    display: flex;
    flex-wrap: wrap;
}
.product-image-section {
    flex: 0 0 280px;
    max-width: 280px;
    padding: 20px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}
.product-image-wrapper {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.product-image-wrapper img {
    width: 100%;
    height: 240px;
    object-fit: cover;
}
.product-details-section {
    flex: 1;
    padding: 24px;
    min-width: 0;
}
.product-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0;
}
.product-modal-header .product-name {
    color: #fff;
}
.product-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 0;
}
.product-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.product-badge.vegetarian { background: rgba(255,255,255,0.9); color: #155724; }
.product-badge.vegan { background: rgba(255,255,255,0.9); color: #004085; }
.product-badge.spicy { background: rgba(255,255,255,0.9); color: #721c24; }
.product-description {
    color: #718096;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 20px;
}
.product-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding: 12px 16px;
    background: #f7fafc;
    border-radius: 8px;
}
.product-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #4a5568;
}
.product-meta-item i {
    color: #696cff;
}
.option-section {
    margin-bottom: 20px;
}
.option-label {
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.variant-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #2d3748;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
}
.variant-select:focus {
    outline: none;
    border-color: #696cff;
    box-shadow: 0 0 0 3px rgba(105,108,255,0.1);
}
.addons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}
.addon-item {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    background: #f7fafc;
    border: 2px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.addon-item:hover {
    background: #edf2f7;
}
.addon-item.selected {
    background: #ebf4ff;
    border-color: #696cff;
}
.addon-item input {
    display: none;
}
.addon-checkbox-visual {
    width: 20px;
    height: 20px;
    border: 2px solid #cbd5e0;
    border-radius: 4px;
    margin-right: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.addon-item.selected .addon-checkbox-visual {
    background: #696cff;
    border-color: #696cff;
}
.addon-item.selected .addon-checkbox-visual::after {
    content: '\2713';
    color: #fff;
    font-size: 12px;
    font-weight: bold;
}
.addon-info {
    flex: 1;
}
.addon-name {
    font-size: 13px;
    font-weight: 500;
    color: #2d3748;
}
.addon-price {
    font-size: 12px;
    color: #696cff;
    font-weight: 600;
}
.product-action-section {
    flex: 0 0 200px;
    max-width: 200px;
    padding: 24px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-left: 1px solid #e2e8f0;
}
.price-display {
    text-align: center;
    padding: 20px 0;
}
.price-label {
    font-size: 12px;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 4px;
}
.price-value {
    font-size: 2rem;
    font-weight: 800;
    color: #696cff;
}
.quantity-control {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 20px;
}
.qty-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #e2e8f0;
    background: #fff;
    border-radius: 10px;
    font-size: 20px;
    font-weight: 600;
    color: #4a5568;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.qty-btn:hover {
    border-color: #696cff;
    color: #696cff;
}
.qty-btn:active {
    transform: scale(0.95);
}
.qty-input {
    width: 60px;
    height: 40px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
}
.qty-input:focus {
    outline: none;
    border-color: #696cff;
}
.add-to-cart-btn {
    width: 100%;
    padding: 14px 20px;
    background: #696cff;
    border: none;
    border-radius: 12px;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(105,108,255,0.4);
}
.add-to-cart-btn:active {
    transform: translateY(0);
}
.add-to-cart-btn i {
    font-size: 18px;
}

@media (max-width: 992px) {
    .product-modal-body {
        flex-direction: column;
    }
    .product-image-section,
    .product-action-section {
        flex: none;
        max-width: 100%;
        width: 100%;
    }
    .product-image-section {
        padding: 20px;
    }
    .product-image-wrapper img {
        height: 200px;
    }
    .product-action-section {
        border-left: none;
        border-top: 1px solid #e2e8f0;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }
    .price-display {
        padding: 10px 0;
    }
    .quantity-control {
        margin-bottom: 0;
    }
}
</style>

<form id="modal_add_to_cart_form" method="POST">
    @csrf
    <input type="hidden" name="menu_item_id" value="{{ $menuItem->id }}">
    <input type="hidden" name="serviceType" value="menu_item">
    <input type="hidden" name="price" value="{{ $menuItem->base_price }}" id="modal_price">
    <input type="hidden" name="variant_price" value="{{ $menuItem->base_price }}" id="modal_variant_price">
    <input type="hidden" name="variant_id" value="" id="modal_variant_id">
    <input type="hidden" name="variant_sku" value="{{ $menuItem->sku }}" id="modal_variant_sku">

    <div class="product-modal-content">
        {{-- Header --}}
        <div class="product-modal-header">
            <div class="product-name text-white">{{ $menuItem->name }}</div>
            <div class="product-badges">
                @if($menuItem->is_vegetarian)
                    <span class="product-badge vegetarian"><i class="bx bx-leaf"></i> {{ __('Vegetarian') }}</span>
                @endif
                @if($menuItem->is_vegan)
                    <span class="product-badge vegan"><i class="bx bx-leaf"></i> {{ __('Vegan') }}</span>
                @endif
                @if($menuItem->is_spicy)
                    <span class="product-badge spicy"><i class="bx bx-hot"></i> {{ __('Spicy') }}</span>
                @endif
            </div>
            <button type="button" class="product-modal-close" data-bs-dismiss="modal">
                <i class="bx bx-x"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="product-modal-body">
            {{-- Image Section --}}
            <div class="product-image-section">
                <div class="product-image-wrapper">
                    <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->name }}">
                </div>
            </div>

            {{-- Details Section --}}
            <div class="product-details-section">
                @if($menuItem->short_description)
                <p class="product-description">{{ strip_tags($menuItem->short_description) }}</p>
                @endif

                {{-- Meta Info --}}
                @if($menuItem->preparation_time || $menuItem->calories)
                <div class="product-meta">
                    @if($menuItem->preparation_time)
                    <div class="product-meta-item">
                        <i class="bx bx-time"></i>
                        <span>{{ $menuItem->preparation_time }} {{ __('min') }}</span>
                    </div>
                    @endif
                    @if($menuItem->calories)
                    <div class="product-meta-item">
                        <i class="bx bx-flame"></i>
                        <span>{{ $menuItem->calories }} {{ __('cal') }}</span>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Variants --}}
                @if($variants && count($variants) > 0)
                <div class="option-section">
                    <div class="option-label">{{ __('Select Size/Variant') }}</div>
                    <select name="variant_id" id="variant_select" class="variant-select">
                        <option value="" data-price="{{ $menuItem->base_price }}" data-sku="{{ $menuItem->sku }}">
                            {{ __('Regular') }} - {{ currency($menuItem->base_price) }}
                        </option>
                        @foreach ($variants as $variant)
                            <option value="{{ $variant->id }}"
                                data-price="{{ $menuItem->base_price + ($variant->price_adjustment ?? 0) }}"
                                data-sku="{{ $variant->sku ?? $menuItem->sku }}">
                                {{ $variant->name }} - {{ currency($menuItem->base_price + ($variant->price_adjustment ?? 0)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Add-ons --}}
                @if($menuItem->activeAddons && count($menuItem->activeAddons) > 0)
                <div class="option-section">
                    <div class="option-label">{{ __('Add-ons') }}</div>
                    <div class="addons-grid">
                        @foreach ($menuItem->activeAddons as $addon)
                        <label class="addon-item" for="addon_{{ $addon->id }}">
                            <input class="addon-checkbox" type="checkbox"
                                name="addons[]" value="{{ $addon->id }}"
                                data-price="{{ $addon->price }}"
                                id="addon_{{ $addon->id }}">
                            <span class="addon-checkbox-visual"></span>
                            <span class="addon-info">
                                <span class="addon-name">{{ $addon->name }}</span>
                                <span class="addon-price">+{{ currency($addon->price) }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Action Section --}}
            <div class="product-action-section">
                <div class="price-display">
                    <div class="price-label">{{ __('Total Price') }}</div>
                    <div class="price-value productPrice">{{ currency($menuItem->base_price) }}</div>
                </div>

                <div>
                    <div class="quantity-control">
                        <button type="button" class="qty-btn" id="qty_minus">-</button>
                        <input type="number" class="qty-input" min="1" value="1" name="qty" id="modal_qty">
                        <button type="button" class="qty-btn" id="qty_plus">+</button>
                    </div>

                    <button class="add-to-cart-btn" type="button" id="modal_add_to_cart">
                        <i class="bx bx-cart-add"></i>
                        {{ __('Add to Cart') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function() {
    const basePrice = {{ $menuItem->base_price }};
    const currencySymbol = '{{ currency_icon() }}';

    function formatCurrency(amount) {
        return currencySymbol + parseFloat(amount).toFixed(2);
    }

    function updatePrice() {
        let price = basePrice;

        // Add variant price adjustment
        const variantSelect = document.getElementById('variant_select');
        if (variantSelect) {
            const selectedOption = variantSelect.options[variantSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.price) {
                price = parseFloat(selectedOption.dataset.price);
            }
        }

        // Add addon prices
        document.querySelectorAll('.addon-checkbox:checked').forEach(function(checkbox) {
            price += parseFloat(checkbox.dataset.price || 0);
        });

        // Multiply by quantity
        const qty = parseInt(document.getElementById('modal_qty').value) || 1;
        const totalPrice = price * qty;

        // Update display
        document.querySelector('.productPrice').textContent = formatCurrency(totalPrice);
        document.getElementById('modal_variant_price').value = price;
        document.getElementById('modal_price').value = price;
    }

    // Variant select change
    const variantSelect = document.getElementById('variant_select');
    if (variantSelect) {
        variantSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('modal_variant_id').value = this.value;
            document.getElementById('modal_variant_sku').value = selectedOption.dataset.sku || '';
            updatePrice();
        });
    }

    // Addon checkbox changes
    document.querySelectorAll('.addon-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const label = this.closest('.addon-item');
            if (this.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
            updatePrice();
        });
    });

    // Quantity controls
    document.getElementById('qty_minus').addEventListener('click', function() {
        const input = document.getElementById('modal_qty');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
            updatePrice();
        }
    });

    document.getElementById('qty_plus').addEventListener('click', function() {
        const input = document.getElementById('modal_qty');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
        updatePrice();
    });

    document.getElementById('modal_qty').addEventListener('change', function() {
        if (parseInt(this.value) < 1) this.value = 1;
        updatePrice();
    });

    // Add to cart button
    document.getElementById('modal_add_to_cart').addEventListener('click', function(e) {
        e.preventDefault();

        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> {{ __("Adding...") }}';
        btn.disabled = true;

        const formData = new FormData(document.getElementById('modal_add_to_cart_form'));
        const params = new URLSearchParams(formData).toString();

        fetch("{{ url('/admin/pos/add-to-cart') }}?" + params, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector('.product-table-container').innerHTML = html;

            // Call totalSummery if it exists
            if (typeof totalSummery === 'function') {
                totalSummery();
            }

            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success("{{ __('Item added to cart') }}");
            }

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
            if (modal) {
                modal.hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error("{{ __('Failed to add item to cart') }}");
            }
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    });

    // Initialize price
    updatePrice();
})();
</script>
