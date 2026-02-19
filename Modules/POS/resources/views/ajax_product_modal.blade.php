<style>
.product-modal-content {
    padding: 0;
}
.product-modal-header {
    position: relative;
    background: #696cff;
    padding: 20px 24px;
    border-radius: 0;
    border-bottom: none;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding-right: 50px;
}
.product-modal-header .btn-close {
    position: absolute;
    top: 50%;
    right: 16px;
    transform: translateY(-50%);
    opacity: 0.8;
    transition: opacity 0.2s;
}
.product-modal-header .btn-close:hover {
    opacity: 1;
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
    font-size: 1.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.product-description {
    color: #718096;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 20px;
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
.attributes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
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
.price-original {
    font-size: 14px;
    color: #a0aec0;
    text-decoration: line-through;
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
.add-to-cart-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
.variant-warning {
    background: #fff3cd;
    color: #856404;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    text-align: center;
    margin-bottom: 15px;
    display: none;
}
.variant-warning.show {
    display: block;
}

/* Tablet and below (768px - 1199px) */
@media (max-width: 1199px) {
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
        height: 220px;
    }
    .product-action-section {
        border-left: none;
        border-top: 1px solid #e2e8f0;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 20px 24px;
    }
    .price-display {
        padding: 0;
        text-align: left;
        flex: 0 0 auto;
    }
    .price-label {
        font-size: 11px;
        margin-bottom: 2px;
    }
    .price-value {
        font-size: 1.5rem;
    }
    .product-action-section > div:last-child {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        justify-content: flex-end;
    }
    .quantity-control {
        margin-bottom: 0;
        gap: 8px;
    }
    .qty-btn {
        width: 36px;
        height: 36px;
        font-size: 18px;
    }
    .qty-input {
        width: 50px;
        height: 36px;
        font-size: 16px;
    }
    .add-to-cart-btn {
        padding: 10px 20px;
        font-size: 14px;
        white-space: nowrap;
    }
}

/* Mobile */
@media (max-width: 767px) {
    .product-action-section {
        flex-direction: column;
        gap: 15px;
    }
    .price-display {
        text-align: center;
    }
    .product-action-section > div:last-child {
        flex-direction: column;
        width: 100%;
    }
    .quantity-control {
        justify-content: center;
    }
    .add-to-cart-btn {
        width: 100%;
    }
}
</style>

<form id="modal_add_to_cart_form" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="price" value="0" id="modal_price">
    <input type="hidden" name="variant_price" value="0" id="modal_variant_price">
    <input type="hidden" name="variant_sku" value="" id="modal_variant_sku">

    <div class="product-modal-content">
        {{-- Header --}}
        <div class="product-modal-header">
            <div class="product-name text-white"><i class="bx bx-package"></i> {{ $product->name }}</div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        {{-- Body --}}
        <div class="product-modal-body">
            {{-- Image Section --}}
            <div class="product-image-section">
                <div class="product-image-wrapper">
                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}">
                </div>
            </div>

            {{-- Details Section --}}
            <div class="product-details-section">
                @if($product->short_description)
                <p class="product-description">{{ strip_tags($product->short_description) }}</p>
                @endif

                {{-- Attributes/Variants --}}
                @if(count($product->attribute_and_values) > 0)
                <div class="option-section">
                    <div class="option-label">{{ __('Select Options') }}</div>
                    <div class="attributes-grid">
                        @foreach ($product->attribute_and_values as $attributes)
                        <div>
                            <select name="{{ $attributes['attribute'] }}" class="variant-select attributes">
                                <option value="" selected disabled>{{ __('Select') }} {{ ucfirst($attributes['attribute']) }}</option>
                                @foreach($attributes['attribute_values'] as $value)
                                <option value="{{ $value['id'] }}">{{ $value['value'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Action Section --}}
            <div class="product-action-section">
                <div class="price-display">
                    <div class="price-label">{{ __('Price') }}</div>
                    <div class="price-value productPrice">{{ currency($product->actual_price) }}</div>
                    @if ($product->price != $product->actual_price)
                    <div class="price-original">{{ currency($product->price) }}</div>
                    @endif
                </div>

                <div>
                    <div class="variant-warning" id="variantWarning">
                        <i class="bx bx-info-circle"></i> {{ __('Please select all options') }}
                    </div>

                    <div class="quantity-control">
                        <button type="button" class="qty-btn" id="qty_minus">-</button>
                        <input type="number" class="qty-input" min="1" value="1" name="qty" id="modal_qty">
                        <button type="button" class="qty-btn" id="qty_plus">+</button>
                    </div>

                    <button class="add-to-cart-btn" type="button" id="modal_add_to_cart" {{ count($product->attribute_and_values) > 0 ? 'disabled' : '' }}>
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
    const currencySymbol = '{{ currency_icon() }}';
    const hasAttributes = {{ count($product->attribute_and_values) > 0 ? 'true' : 'false' }};
    const attributes = Object.values(@json($product->VariantsPriceAndSku));

    function formatCurrency(amount) {
        return currencySymbol + parseFloat(amount).toFixed(2);
    }

    function removeCurrency(str) {
        return parseFloat(str.replace(/[^0-9.-]+/g, ''));
    }

    function checkAllAttributesSelected() {
        const selects = document.querySelectorAll('.attributes');
        let allSelected = true;
        selects.forEach(function(select) {
            if (!select.value) {
                allSelected = false;
            }
        });
        return allSelected;
    }

    function updateButtonState() {
        const btn = document.getElementById('modal_add_to_cart');
        const warning = document.getElementById('variantWarning');

        if (!hasAttributes || checkAllAttributesSelected()) {
            btn.disabled = false;
            warning.classList.remove('show');
        } else {
            btn.disabled = true;
            warning.classList.add('show');
        }
    }

    // Attribute select changes
    document.querySelectorAll('.attributes').forEach(function(select) {
        select.addEventListener('change', function() {
            let attribute_values = [];
            document.querySelectorAll('.attributes').forEach(function(s) {
                if (s.value) attribute_values.push(s.value);
            });

            // Find matching variant
            const selected_variant = attributes.find(function(variant) {
                return variant['attribute_value_ids'].sort().toString() === attribute_values.sort().toString();
            });

            if (selected_variant && selected_variant.price) {
                document.getElementById('modal_variant_price').value = removeCurrency(selected_variant.currency_price);
                document.getElementById('modal_variant_sku').value = selected_variant.sku;
                document.getElementById('modal_price').value = removeCurrency(selected_variant.currency_price);
                document.querySelector('.productPrice').textContent = selected_variant.currency_price;
            }

            updateButtonState();
        });
    });

    // Quantity controls
    document.getElementById('qty_minus').addEventListener('click', function() {
        const input = document.getElementById('modal_qty');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
        }
    });

    document.getElementById('qty_plus').addEventListener('click', function() {
        const input = document.getElementById('modal_qty');
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
    });

    document.getElementById('modal_qty').addEventListener('change', function() {
        if (parseInt(this.value) < 1) this.value = 1;
    });

    // Add to cart button
    document.getElementById('modal_add_to_cart').addEventListener('click', function(e) {
        e.preventDefault();

        const variant_sku = document.getElementById('modal_variant_sku').value;

        if (hasAttributes && !variant_sku) {
            if (typeof toastr !== 'undefined') {
                toastr.error("{{ __('Please select all options') }}");
            }
            return;
        }

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
            btn.disabled = hasAttributes && !document.getElementById('modal_variant_sku').value;
        });
    });

    // Initialize state
    updateButtonState();
})();
</script>
