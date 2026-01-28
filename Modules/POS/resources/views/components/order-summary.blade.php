{{--
    Order Summary Component

    Usage:
    @include('pos::components.order-summary', [
        'items' => $cartItems,
        'subtotal' => 2340,
        'discount' => 0,
        'tax' => 117,
        'deliveryFee' => 0,
        'total' => 2457,
        'showItems' => true,
        'editable' => false
    ])
--}}

@php
    $items = $items ?? [];
    $subtotal = $subtotal ?? 0;
    $discount = $discount ?? 0;
    $tax = $tax ?? 0;
    $deliveryFee = $deliveryFee ?? 0;
    $total = $total ?? ($subtotal - $discount + $tax + $deliveryFee);
    $showItems = $showItems ?? true;
    $editable = $editable ?? false;
    $currency = currency_icon();
@endphp

<div class="order-summary-wrapper">
    @if($showItems && count($items) > 0)
    <div class="order-items-list">
        @foreach($items as $index => $item)
        <div class="order-item" data-index="{{ $index }}">
            <div class="order-item-info">
                <div class="order-item-name">
                    {{ $item['name'] ?? $item['menu_item_name'] ?? 'Item' }}
                    @if(isset($item['quantity']) && $item['quantity'] > 1)
                    <span class="item-qty-badge">x{{ $item['quantity'] }}</span>
                    @endif
                </div>
                @if(isset($item['addons']) && count($item['addons']) > 0)
                <div class="order-item-addons">
                    @foreach($item['addons'] as $addon)
                    <span class="addon-tag">+ {{ $addon['name'] ?? $addon }}</span>
                    @endforeach
                </div>
                @endif
                @if(isset($item['note']) && $item['note'])
                <div class="order-item-note">
                    <i class="bx bx-note"></i> {{ $item['note'] }}
                </div>
                @endif
            </div>
            <div class="order-item-price">
                {{ $currency }} {{ number_format($item['subtotal'] ?? ($item['price'] * ($item['quantity'] ?? 1)), 2) }}
            </div>
            @if($editable)
            <button type="button" class="btn-remove-item" onclick="removeOrderItem({{ $index }})">
                <i class="bx bx-x"></i>
            </button>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="order-summary-totals {{ $showItems ? 'has-items' : '' }}">
        <div class="summary-row summary-subtotal">
            <span>{{ __('Subtotal') }}</span>
            <span id="summarySubtotal">{{ $currency }} {{ number_format($subtotal, 2) }}</span>
        </div>

        @if($discount > 0)
        <div class="summary-row summary-discount">
            <span>{{ __('Discount') }}</span>
            <span id="summaryDiscount">- {{ $currency }} {{ number_format($discount, 2) }}</span>
        </div>
        @endif

        @if($tax > 0)
        <div class="summary-row summary-tax">
            <span>{{ __('Tax') }}</span>
            <span id="summaryTax">{{ $currency }} {{ number_format($tax, 2) }}</span>
        </div>
        @endif

        @if($deliveryFee > 0)
        <div class="summary-row summary-delivery">
            <span>{{ __('Delivery Fee') }}</span>
            <span id="summaryDelivery">{{ $currency }} {{ number_format($deliveryFee, 2) }}</span>
        </div>
        @endif

        <div class="summary-row summary-total">
            <span>{{ __('Total') }}</span>
            <span id="summaryTotal">{{ $currency }} {{ number_format($total, 2) }}</span>
        </div>
    </div>
</div>

<style>
.order-summary-wrapper {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
}

.order-items-list {
    max-height: 250px;
    overflow-y: auto;
    padding: 12px 16px;
}

.order-items-list::-webkit-scrollbar {
    width: 4px;
}

.order-items-list::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 2px;
}

.order-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item-info {
    flex: 1;
    min-width: 0;
}

.order-item-name {
    font-weight: 600;
    color: #232333;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.item-qty-badge {
    background: #696cff;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}

.order-item-addons {
    margin-top: 4px;
}

.addon-tag {
    display: inline-block;
    font-size: 11px;
    color: #697a8d;
    background: #fff;
    padding: 2px 6px;
    border-radius: 4px;
    margin-right: 4px;
    margin-top: 2px;
}

.order-item-note {
    font-size: 12px;
    color: #03c3ec;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.order-item-price {
    font-weight: 600;
    color: #232333;
    white-space: nowrap;
}

.btn-remove-item {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: #ff3e1d;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.2s;
}

.order-item:hover .btn-remove-item {
    opacity: 1;
}

.order-summary-totals {
    padding: 16px;
    background: #fff;
}

.order-summary-totals.has-items {
    border-top: 2px solid #e9ecef;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 14px;
}

.summary-row.summary-subtotal {
    color: #697a8d;
}

.summary-row.summary-discount {
    color: #71dd37;
}

.summary-row.summary-tax,
.summary-row.summary-delivery {
    color: #697a8d;
}

.summary-row.summary-total {
    font-size: 18px;
    font-weight: 700;
    color: #232333;
    padding-top: 12px;
    margin-top: 8px;
    border-top: 2px dashed #e9ecef;
}

/* Compact mode */
.order-summary-wrapper.compact .order-items-list {
    max-height: 150px;
}

.order-summary-wrapper.compact .order-item-name {
    font-size: 13px;
}

.order-summary-wrapper.compact .summary-row {
    font-size: 13px;
}

.order-summary-wrapper.compact .summary-row.summary-total {
    font-size: 16px;
}
</style>

<script>
function removeOrderItem(index) {
    // This should be handled by the parent component
    document.dispatchEvent(new CustomEvent('removeOrderItem', {
        detail: { index: index }
    }));
}

// Update summary values dynamically
function updateOrderSummary(data) {
    const currency = '{{ $currency }}';

    if (data.subtotal !== undefined) {
        document.getElementById('summarySubtotal').textContent =
            currency + ' ' + parseFloat(data.subtotal).toFixed(2);
    }

    if (data.discount !== undefined) {
        const discountEl = document.getElementById('summaryDiscount');
        if (discountEl) {
            discountEl.textContent = '- ' + currency + ' ' + parseFloat(data.discount).toFixed(2);
            discountEl.closest('.summary-row').style.display = data.discount > 0 ? 'flex' : 'none';
        }
    }

    if (data.tax !== undefined) {
        const taxEl = document.getElementById('summaryTax');
        if (taxEl) {
            taxEl.textContent = currency + ' ' + parseFloat(data.tax).toFixed(2);
        }
    }

    if (data.deliveryFee !== undefined) {
        const deliveryEl = document.getElementById('summaryDelivery');
        if (deliveryEl) {
            deliveryEl.textContent = currency + ' ' + parseFloat(data.deliveryFee).toFixed(2);
            deliveryEl.closest('.summary-row').style.display = data.deliveryFee > 0 ? 'flex' : 'none';
        }
    }

    if (data.total !== undefined) {
        document.getElementById('summaryTotal').textContent =
            currency + ' ' + parseFloat(data.total).toFixed(2);
    }
}
</script>
