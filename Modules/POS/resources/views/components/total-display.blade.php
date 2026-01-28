{{--
    Total Display Component

    Usage:
    @include('pos::components.total-display', [
        'total' => 2340,
        'label' => 'Total Due',
        'subtitle' => 'Dine-In - Table 5 - 3 items',
        'variant' => 'dark', // dark, primary, success, warning
        'size' => 'large' // small, medium, large
    ])
--}}

@php
    $total = $total ?? 0;
    $label = $label ?? __('Total Due');
    $subtitle = $subtitle ?? null;
    $variant = $variant ?? 'dark';
    $size = $size ?? 'large';
    $id = $id ?? 'totalDisplay';
    $currency = currency_icon();

    $variantClasses = [
        'dark' => 'total-display-dark',
        'primary' => 'total-display-primary',
        'success' => 'total-display-success',
        'warning' => 'total-display-warning',
        'danger' => 'total-display-danger',
    ];

    $sizeClasses = [
        'small' => 'total-display-sm',
        'medium' => 'total-display-md',
        'large' => 'total-display-lg',
    ];
@endphp

<div class="total-display {{ $variantClasses[$variant] ?? 'total-display-dark' }} {{ $sizeClasses[$size] ?? 'total-display-lg' }}" id="{{ $id }}">
    <div class="total-amount-wrapper">
        <span class="total-currency">{{ $currency }}</span>
        <span class="total-amount" id="{{ $id }}Amount">{{ number_format($total, 2) }}</span>
    </div>
    <div class="total-label">{{ $label }}</div>
    @if($subtitle)
    <div class="total-subtitle">{{ $subtitle }}</div>
    @endif
</div>

<style>
.total-display {
    text-align: center;
    padding: 30px 20px;
    border-radius: 12px;
    color: white;
}

/* Variants - Flat colors */
.total-display-dark {
    background: #2c3e50;
}

.total-display-primary {
    background: #5f61e6;
}

.total-display-success {
    background: #28a745;
}

.total-display-warning {
    background: #f39c12;
}

.total-display-danger {
    background: #dc3545;
}

/* Sizes */
.total-display-lg .total-amount-wrapper {
    margin-bottom: 8px;
}

.total-display-lg .total-currency {
    font-size: 28px;
    font-weight: 600;
    vertical-align: top;
    margin-right: 4px;
}

.total-display-lg .total-amount {
    font-size: 48px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -1px;
}

.total-display-lg .total-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.total-display-lg .total-subtitle {
    font-size: 13px;
    opacity: 0.7;
}

/* Medium size */
.total-display-md {
    padding: 20px 16px;
}

.total-display-md .total-currency {
    font-size: 18px;
    font-weight: 600;
    vertical-align: top;
    margin-right: 2px;
}

.total-display-md .total-amount {
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
}

.total-display-md .total-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    opacity: 0.9;
    margin-top: 4px;
}

.total-display-md .total-subtitle {
    font-size: 12px;
    opacity: 0.7;
    margin-top: 4px;
}

/* Small size */
.total-display-sm {
    padding: 16px 12px;
    border-radius: 10px;
}

.total-display-sm .total-currency {
    font-size: 16px;
    font-weight: 600;
    vertical-align: middle;
    margin-right: 2px;
}

.total-display-sm .total-amount {
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
}

.total-display-sm .total-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.9;
    margin-top: 4px;
}

.total-display-sm .total-subtitle {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 2px;
}

/* Animation for amount changes */
.total-amount.updating {
    animation: pulse 0.3s ease;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Responsive */
@media (max-width: 576px) {
    .total-display-lg .total-amount {
        font-size: 36px;
    }

    .total-display-lg .total-currency {
        font-size: 20px;
    }
}
</style>

<script>
// Function to update total display dynamically
function updateTotalDisplay(newTotal, elementId = 'displayTotalAmount') {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.add('updating');
        element.textContent = parseFloat(newTotal).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        setTimeout(() => element.classList.remove('updating'), 300);
    }
}
</script>
