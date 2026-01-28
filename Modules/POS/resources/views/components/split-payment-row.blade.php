{{--
    Split Payment Row Component

    Usage:
    @include('pos::components.split-payment-row', [
        'index' => 0,
        'amount' => 1000,
        'paymentType' => 'cash',
        'accountId' => null,
        'canRemove' => false
    ])
--}}

@php
    $index = isset($index) ? (int) $index : 0;
    $amount = isset($amount) ? (float) $amount : 0;
    $paymentType = $paymentType ?? 'cash';
    $accountId = $accountId ?? null;
    $canRemove = isset($canRemove) ? filter_var($canRemove, FILTER_VALIDATE_BOOLEAN) : true;
    $accounts = $accounts ?? [];

    $paymentMethods = [
        'cash' => ['icon' => 'bx bx-money', 'label' => __('Cash'), 'color' => '#71dd37'],
        'card' => ['icon' => 'bx bx-credit-card', 'label' => __('Card'), 'color' => '#696cff'],
        'bank' => ['icon' => 'bx bx-building', 'label' => __('Bank'), 'color' => '#03c3ec'],
        'mobile_banking' => ['icon' => 'bx bx-mobile', 'label' => __('Mobile'), 'color' => '#ffab00'],
    ];
@endphp

<div class="split-payment-row" data-index="{{ $index }}">
    <div class="split-payment-header">
        <span class="split-payment-number">{{ __('Payment') }} #{{ $index + 1 }}</span>
        @if($canRemove)
        <button type="button" class="btn-remove-payment" onclick="removeSplitPayment({{ $index }})">
            <i class="bx bx-x"></i>
        </button>
        @endif
    </div>

    <div class="split-payment-content">
        <div class="split-payment-methods">
            @foreach($paymentMethods as $type => $method)
            <label class="split-method-option {{ $paymentType === $type ? 'active' : '' }}">
                <input type="radio"
                       name="payment_type[{{ $index }}]"
                       value="{{ $type }}"
                       {{ $paymentType === $type ? 'checked' : '' }}
                       onchange="onSplitPaymentTypeChange({{ $index }}, this.value)">
                <div class="split-method-box" style="--method-color: {{ $method['color'] }}">
                    <i class="{{ $method['icon'] }}"></i>
                    <span>{{ $method['label'] }}</span>
                </div>
            </label>
            @endforeach
        </div>

        <div class="split-account-select mt-2" style="{{ $paymentType === 'cash' ? 'display: none;' : '' }}">
            <select class="form-select form-select-sm" name="account_id[{{ $index }}]">
                <option value="">{{ __('Select Account...') }}</option>
                @foreach($accounts ?? [] as $account)
                    @if($account->account_type !== 'cash')
                    <option value="{{ $account->id }}"
                            data-type="{{ $account->account_type }}"
                            {{ $accountId == $account->id ? 'selected' : '' }}
                            style="{{ $account->account_type !== $paymentType ? 'display: none;' : '' }}">
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

        <div class="split-amount-input mt-2">
            <div class="input-group">
                <span class="input-group-text">{{ currency_icon() }}</span>
                <input type="number"
                       name="paying_amount[{{ $index }}]"
                       class="form-control split-amount"
                       value="{{ number_format($amount, 2, '.', '') }}"
                       step="0.01"
                       min="0"
                       placeholder="0.00"
                       onchange="calculateSplitTotal()"
                       oninput="calculateSplitTotal()">
            </div>
        </div>
    </div>
</div>

<style>
.split-payment-row {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
}

.split-payment-row:hover {
    border-color: #696cff;
}

.split-payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.split-payment-number {
    font-size: 13px;
    font-weight: 600;
    color: #697a8d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-remove-payment {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: #ff3e1d;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-remove-payment:hover {
    background: #e63617;
    transform: scale(1.1);
}

.split-payment-methods {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}

@media (max-width: 576px) {
    .split-payment-methods {
        grid-template-columns: repeat(2, 1fr);
    }
}

.split-method-option {
    cursor: pointer;
    margin: 0;
}

.split-method-option input {
    display: none;
}

.split-method-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 10px 8px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    transition: all 0.2s;
}

.split-method-box i {
    font-size: 20px;
    margin-bottom: 4px;
}

.split-method-box span {
    font-size: 11px;
    font-weight: 600;
}

.split-method-option:hover .split-method-box {
    border-color: var(--method-color);
}

.split-method-option.active .split-method-box,
.split-method-option input:checked + .split-method-box {
    border-color: var(--method-color);
    background: var(--method-color);
    color: white;
}

.split-amount-input .input-group-text {
    background: #696cff;
    color: white;
    border-color: #696cff;
    font-weight: 600;
}

.split-amount {
    font-size: 18px;
    font-weight: 600;
    text-align: right;
}
</style>

<script>
function onSplitPaymentTypeChange(index, type) {
    const row = document.querySelector(`.split-payment-row[data-index="${index}"]`);
    if (!row) return;

    const accountSelect = row.querySelector('.split-account-select');
    const selectElement = row.querySelector('select[name^="account_id"]');

    // Update active state on method buttons
    row.querySelectorAll('.split-method-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.querySelector('input').value === type) {
            opt.classList.add('active');
        }
    });

    // Show/hide account select
    if (type === 'cash') {
        accountSelect.style.display = 'none';
        selectElement.value = '';
    } else {
        accountSelect.style.display = 'block';
        // Filter options by type
        selectElement.querySelectorAll('option').forEach(opt => {
            if (opt.value === '') {
                opt.style.display = 'block';
            } else {
                opt.style.display = opt.dataset.type === type ? 'block' : 'none';
            }
        });
        selectElement.value = '';
    }
}

function removeSplitPayment(index) {
    const row = document.querySelector(`.split-payment-row[data-index="${index}"]`);
    if (row) {
        row.style.animation = 'fadeOut 0.2s ease';
        setTimeout(() => {
            row.remove();
            reindexSplitPayments();
            calculateSplitTotal();
        }, 200);
    }
}

function reindexSplitPayments() {
    document.querySelectorAll('.split-payment-row').forEach((row, newIndex) => {
        row.dataset.index = newIndex;
        row.querySelector('.split-payment-number').textContent = `{{ __("Payment") }} #${newIndex + 1}`;

        // Update input names
        row.querySelectorAll('input[name^="payment_type"]').forEach(input => {
            input.name = `payment_type[${newIndex}]`;
        });
        row.querySelectorAll('select[name^="account_id"]').forEach(select => {
            select.name = `account_id[${newIndex}]`;
        });
        row.querySelectorAll('input[name^="paying_amount"]').forEach(input => {
            input.name = `paying_amount[${newIndex}]`;
        });

        // Update remove button
        const removeBtn = row.querySelector('.btn-remove-payment');
        if (removeBtn) {
            removeBtn.setAttribute('onclick', `removeSplitPayment(${newIndex})`);
        }

        // Update radio onchange
        row.querySelectorAll('.split-method-option input').forEach(radio => {
            radio.setAttribute('onchange', `onSplitPaymentTypeChange(${newIndex}, this.value)`);
        });
    });
}

function calculateSplitTotal() {
    let total = 0;
    document.querySelectorAll('.split-payment-row .split-amount').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    // Update total paying display
    const totalPayingDisplay = document.getElementById('splitTotalPaying');
    if (totalPayingDisplay) {
        totalPayingDisplay.textContent = total.toFixed(2);
    }

    // Trigger event
    document.dispatchEvent(new CustomEvent('splitTotalChanged', {
        detail: { total: total }
    }));

    return total;
}

// Add fade out animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(-20px); }
    }
`;
document.head.appendChild(style);
</script>
