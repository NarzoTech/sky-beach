{{--
    Amount Input Component with Quick Amount Buttons

    Usage:
    @include('pos::components.amount-input', [
        'total' => 2340,
        'name' => 'receive_amount',
        'id' => 'receiveAmount',
        'label' => 'Amount Received',
        'showQuickAmounts' => true,
        'fixedQuickAmounts' => [100, 200, 500, 1000, 2000],
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

    // Use fixed quick amounts or generate dynamic ones
    $fixedQuickAmounts = $fixedQuickAmounts ?? [100, 200, 500, 1000, 2000];
    $quickAmounts = $fixedQuickAmounts;
@endphp

<div class="amount-input-wrapper" data-total="{{ $total }}" id="{{ $id }}Wrapper">
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

    @if($showQuickAmounts)
    <div class="quick-amounts-container mt-3">
        <div class="quick-amounts-grid" id="{{ $id }}QuickAmounts">
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
                    class="btn btn-success exact-amount-btn w-100"
                    data-amount="{{ $total }}"
                    id="{{ $id }}ExactBtn">
                {{ __('Exact Amount') }}
            </button>
        </div>
    </div>
    @endif

    <div class="change-display mt-3" id="{{ $id }}ChangeDisplay" style="display: none;">
        <div class="change-card" id="{{ $id }}ChangeCard">
            <span class="change-label">{{ __('Change Due') }}</span>
            <span class="change-amount">{{ $currency }} <span class="change-value" id="{{ $id }}ChangeValue">0.00</span></span>
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
    max-width: 350px;
    margin: 0 auto;
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
    padding: 10px 8px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.quick-amount-btn:hover {
    background: #696cff;
    border-color: #696cff;
    color: white;
}

.quick-amounts-actions {
    max-width: 350px;
    margin: 0 auto;
}

.exact-amount-btn {
    padding: 12px 20px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    letter-spacing: 0.5px;
}

.change-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: #28a745;
    border-radius: 12px;
    color: white;
    max-width: 350px;
    margin: 0 auto;
}

.change-card.has-due {
    background: #dc3545;
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
        let total = parseFloat(wrapper.dataset.total) || 0;
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
                // Get current total from wrapper data attribute (may have been updated)
                const currentTotal = parseFloat(wrapper.dataset.total) || 0;
                input.value = currentTotal.toFixed(2);
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }

        // Calculate change on input
        if (input && changeDisplay) {
            input.addEventListener('input', function() {
                const received = parseFloat(this.value) || 0;
                // Get current total from data attribute (may have been updated dynamically)
                const currentTotal = parseFloat(wrapper.dataset.total) || 0;
                const change = received - currentTotal;

                if (received > 0 && currentTotal > 0) {
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
                    detail: { received: received, change: change, total: currentTotal }
                }));
            });
        }
    });
}

// Global function to update amount input total
function updateAmountInputTotal(inputId, newTotal) {
    const wrapper = document.getElementById(inputId + 'Wrapper');
    if (wrapper) {
        wrapper.dataset.total = newTotal;
        const exactBtn = wrapper.querySelector('.exact-amount-btn');
        if (exactBtn) {
            exactBtn.dataset.amount = newTotal;
        }
    }
}
</script>
