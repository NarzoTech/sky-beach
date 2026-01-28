{{--
    Payment Method Selector Component

    Usage:
    @include('pos::components.payment-method-selector', [
        'selected' => 'cash',
        'name' => 'payment_type',
        'index' => 0,
        'showSplitOption' => true
    ])
--}}

@php
    $selected = $selected ?? 'cash';
    $name = $name ?? 'payment_type';
    $index = isset($index) ? (int) $index : null;
    $showSplitOption = filter_var($showSplitOption ?? false, FILTER_VALIDATE_BOOLEAN);
    $inputName = $index !== null ? "{$name}[{$index}]" : $name;
    $accounts = $accounts ?? [];

    $paymentMethods = [
        'cash' => ['icon' => 'bx bx-money', 'label' => __('Cash'), 'color' => '#71dd37'],
        'card' => ['icon' => 'bx bx-credit-card', 'label' => __('Card'), 'color' => '#696cff'],
        'bank' => ['icon' => 'bx bx-building', 'label' => __('Bank'), 'color' => '#03c3ec'],
        'mobile_banking' => ['icon' => 'bx bx-mobile', 'label' => __('Mobile'), 'color' => '#ffab00'],
    ];
@endphp

<div class="payment-method-selector" data-index="{{ $index }}">
    <div class="payment-methods-grid">
        @foreach($paymentMethods as $type => $method)
        <label class="payment-method-option {{ $selected === $type ? 'active' : '' }}" data-type="{{ $type }}">
            <input type="radio"
                   name="{{ $inputName }}"
                   value="{{ $type }}"
                   {{ $selected === $type ? 'checked' : '' }}
                   class="payment-type-radio">
            <div class="payment-method-box" style="--method-color: {{ $method['color'] }}">
                <i class="{{ $method['icon'] }} payment-icon"></i>
                <span class="payment-label">{{ $method['label'] }}</span>
            </div>
        </label>
        @endforeach
    </div>

    {{-- Account Selection (for non-cash payments) --}}
    <div class="account-selection mt-3" style="display: none;">
        <label class="form-label fw-semibold">{{ __('Select Account') }}</label>
        <select class="form-select account-select" name="account_id[{{ $index }}]">
            <option value="">{{ __('Select Account...') }}</option>
            @foreach($accounts ?? [] as $account)
                @if($account->account_type !== 'cash')
                <option value="{{ $account->id }}"
                        data-type="{{ $account->account_type }}"
                        style="display: none;">
                    @if($account->account_type === 'bank')
                        {{ $account->bank_account_number }} ({{ $account->bank->name ?? 'Bank' }})
                    @elseif($account->account_type === 'card')
                        {{ $account->card_number }} ({{ $account->card_type ?? 'Card' }})
                    @elseif($account->account_type === 'mobile_banking')
                        {{ $account->mobile_number }} ({{ $account->mobile_bank_name ?? 'Mobile' }})
                    @else
                        {{ $account->name ?? $account->account_type }}
                    @endif
                </option>
                @endif
            @endforeach
        </select>
    </div>

    @if($showSplitOption)
    <div class="text-center mt-3">
        <button type="button" class="btn btn-outline-primary btn-sm add-split-payment-btn">
            <i class="bx bx-plus me-1"></i>{{ __('Split Payment') }}
        </button>
    </div>
    @endif
</div>

<style>
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

@media (max-width: 576px) {
    .payment-methods-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.payment-method-option {
    cursor: pointer;
    margin: 0;
}

.payment-method-option input[type="radio"] {
    display: none;
}

.payment-method-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.2s ease;
    background: #fff;
    min-height: 90px;
}

.payment-method-box:hover {
    border-color: var(--method-color, #696cff);
    background: rgba(105, 108, 255, 0.05);
    transform: translateY(-2px);
}

.payment-method-option.active .payment-method-box,
.payment-method-option input:checked + .payment-method-box {
    border-color: var(--method-color, #696cff);
    background: var(--method-color, #696cff);
    color: white;
    box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3);
}

.payment-icon {
    font-size: 28px;
    margin-bottom: 6px;
}

.payment-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.account-selection {
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize payment method selectors
    initPaymentMethodSelector();
});

function initPaymentMethodSelector() {
    document.querySelectorAll('.payment-method-selector').forEach(function(selector) {
        const radios = selector.querySelectorAll('.payment-type-radio');
        const accountSelection = selector.querySelector('.account-selection');
        const accountSelect = selector.querySelector('.account-select');

        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Update active state
                selector.querySelectorAll('.payment-method-option').forEach(opt => opt.classList.remove('active'));
                this.closest('.payment-method-option').classList.add('active');

                // Show/hide account selection
                const selectedType = this.value;
                if (selectedType !== 'cash' && accountSelection) {
                    accountSelection.style.display = 'block';
                    // Filter account options by type
                    if (accountSelect) {
                        accountSelect.querySelectorAll('option').forEach(function(opt) {
                            if (opt.value === '') {
                                opt.style.display = 'block';
                            } else {
                                opt.style.display = opt.dataset.type === selectedType ? 'block' : 'none';
                            }
                        });
                        accountSelect.value = '';
                    }
                } else if (accountSelection) {
                    accountSelection.style.display = 'none';
                }

                // Trigger custom event
                selector.dispatchEvent(new CustomEvent('paymentMethodChanged', {
                    detail: { type: selectedType, index: selector.dataset.index }
                }));
            });
        });

        // Initialize on load
        const checkedRadio = selector.querySelector('.payment-type-radio:checked');
        if (checkedRadio) {
            checkedRadio.dispatchEvent(new Event('change'));
        }
    });
}
</script>
