{{--
    Amount Input Component with Quick Amount Buttons

    Usage:
    @include('pos::components.amount-input', [
        'total' => 2340,
        'name' => 'receive_amount',
        'id' => 'receiveAmount',
        'label' => 'Amount Received',
        'showQuickAmounts' => true,
        'readonly' => false
    ])
--}}

@php
    $total = $total ?? 0;
    $name = $name ?? 'receive_amount';
    $id = $id ?? $name;
    $label = $label ?? __('Amount Received');
    $showQuickAmounts = $showQuickAmounts ?? true;
    $readonly = $readonly ?? false;
    $currency = currency_icon();

    // Generate smart quick amounts
    $quickAmounts = [];
    if ($showQuickAmounts && $total > 0) {
        $roundedTotal = ceil($total);

        // Nearest 100
        $nearest100 = ceil($roundedTotal / 100) * 100;
        if ($nearest100 >= $roundedTotal) $quickAmounts[] = $nearest100;

        // Nearest 500
        $nearest500 = ceil($roundedTotal / 500) * 500;
        if ($nearest500 > $nearest100) $quickAmounts[] = $nearest500;

        // Nearest 1000
        $nearest1000 = ceil($roundedTotal / 1000) * 1000;
        if ($nearest1000 > $nearest500) $quickAmounts[] = $nearest1000;

        // Add some higher amounts
        if ($nearest1000 + 500 > $nearest1000) $quickAmounts[] = $nearest1000 + 500;
        if ($nearest1000 + 1000 > $nearest1000 + 500) $quickAmounts[] = $nearest1000 + 1000;

        // Remove duplicates and sort
        $quickAmounts = array_unique($quickAmounts);
        sort($quickAmounts);
        $quickAmounts = array_slice($quickAmounts, 0, 5);
    }
@endphp

<div class="amount-input-wrapper" data-total="{{ $total }}">
    @if($label)
    <label class="form-label fw-semibold text-center d-block mb-2">{{ $label }}</label>
    @endif

    <div class="amount-input-container">
        <span class="currency-prefix">{{ $currency }}</span>
        <input type="number"
               name="{{ $name }}"
               id="{{ $id }}"
               class="amount-input"
               value="{{ number_format($total, 2, '.', '') }}"
               step="0.01"
               min="0"
               {{ $readonly ? 'readonly' : '' }}
               autocomplete="off">
    </div>

    @if($showQuickAmounts && count($quickAmounts) > 0)
    <div class="quick-amounts-container mt-3">
        <div class="quick-amounts-grid">
            @foreach($quickAmounts as $amount)
            <button type="button"
                    class="btn btn-outline-secondary quick-amount-btn"
                    data-amount="{{ $amount }}">
                {{ number_format($amount, 0) }}
            </button>
            @endforeach
        </div>
        <div class="quick-amounts-actions mt-2">
            <button type="button"
                    class="btn btn-primary exact-amount-btn w-100"
                    data-amount="{{ $total }}">
                <i class="bx bx-check me-1"></i>{{ __('EXACT AMOUNT') }}
            </button>
        </div>
    </div>
    @endif

    <div class="change-display mt-3" style="display: none;">
        <div class="change-card">
            <span class="change-label">{{ __('Change Due') }}</span>
            <span class="change-amount">{{ $currency }} <span class="change-value">0.00</span></span>
        </div>
    </div>
</div>

<style>
.amount-input-container {
    position: relative;
    max-width: 350px;
    margin: 0 auto;
}

.currency-prefix {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 24px;
    font-weight: 600;
    color: #697a8d;
    z-index: 1;
}

.amount-input {
    width: 100%;
    padding: 20px 20px 20px 60px;
    font-size: 36px;
    font-weight: 700;
    text-align: center;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background: #fff;
    transition: all 0.2s ease;
    -moz-appearance: textfield;
}

.amount-input::-webkit-outer-spin-button,
.amount-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.amount-input:focus {
    border-color: #696cff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
}

.amount-input[readonly] {
    background: #f5f5f9;
    cursor: not-allowed;
}

.quick-amounts-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
}

@media (max-width: 576px) {
    .quick-amounts-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .amount-input {
        font-size: 28px;
        padding: 16px 16px 16px 50px;
    }

    .currency-prefix {
        font-size: 20px;
        left: 15px;
    }
}

.quick-amount-btn {
    padding: 12px 8px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.quick-amount-btn:hover {
    background: #696cff;
    border-color: #696cff;
    color: white;
    transform: translateY(-2px);
}

.exact-amount-btn {
    padding: 14px 20px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 8px;
    letter-spacing: 0.5px;
}

.change-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: linear-gradient(135deg, #71dd37, #5cb82f);
    border-radius: 12px;
    color: white;
    max-width: 350px;
    margin: 0 auto;
}

.change-card.has-due {
    background: linear-gradient(135deg, #ff3e1d, #e63617);
}

.change-label {
    font-size: 14px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.change-amount {
    font-size: 24px;
    font-weight: 700;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initAmountInputs();
});

function initAmountInputs() {
    document.querySelectorAll('.amount-input-wrapper').forEach(function(wrapper) {
        const input = wrapper.querySelector('.amount-input');
        const total = parseFloat(wrapper.dataset.total) || 0;
        const changeDisplay = wrapper.querySelector('.change-display');
        const changeValue = wrapper.querySelector('.change-value');
        const changeCard = wrapper.querySelector('.change-card');

        // Quick amount buttons
        wrapper.querySelectorAll('.quick-amount-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const amount = parseFloat(this.dataset.amount);
                input.value = amount.toFixed(2);
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });

        // Exact amount button
        const exactBtn = wrapper.querySelector('.exact-amount-btn');
        if (exactBtn) {
            exactBtn.addEventListener('click', function() {
                const amount = parseFloat(this.dataset.amount);
                input.value = amount.toFixed(2);
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }

        // Calculate change on input
        if (input && changeDisplay) {
            input.addEventListener('input', function() {
                const received = parseFloat(this.value) || 0;
                const change = received - total;

                if (received > 0) {
                    changeDisplay.style.display = 'block';
                    changeValue.textContent = Math.abs(change).toFixed(2);

                    if (change >= 0) {
                        changeCard.classList.remove('has-due');
                        wrapper.querySelector('.change-label').textContent = '{{ __("Change Due") }}';
                    } else {
                        changeCard.classList.add('has-due');
                        wrapper.querySelector('.change-label').textContent = '{{ __("Amount Due") }}';
                    }
                } else {
                    changeDisplay.style.display = 'none';
                }

                // Trigger custom event
                wrapper.dispatchEvent(new CustomEvent('amountChanged', {
                    detail: { received: received, change: change, total: total }
                }));
            });
        }
    });
}

// Global function to update amount input total
function updateAmountInputTotal(wrapperId, newTotal) {
    const wrapper = document.getElementById(wrapperId) || document.querySelector(`[data-total="${wrapperId}"]`);
    if (wrapper) {
        wrapper.dataset.total = newTotal;
        const exactBtn = wrapper.querySelector('.exact-amount-btn');
        if (exactBtn) {
            exactBtn.dataset.amount = newTotal;
        }
        // Trigger recalculation
        const input = wrapper.querySelector('.amount-input');
        if (input) {
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
}
</script>
