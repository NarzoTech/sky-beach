{{--
    Unified Payment Modal

    Purpose: Single payment interface for all order types
    Can be opened directly or after setup modals

    Data required:
    - $total: Order total amount
    - $orderType: 'dine_in' or 'take_away'
    - $items: Collection of cart items (optional)
--}}

@php
    $accounts = $accounts ?? collect();
    $total = $total ?? 0;
    $orderType = $orderType ?? 'dine_in';
@endphp

<div class="modal fade payment-modal" id="unifiedPaymentModal" tabindex="-1" aria-labelledby="unifiedPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" id="paymentModalHeader">
                <h5 class="modal-title" id="unifiedPaymentModalLabel">
                    <i class="bx bx-credit-card me-2"></i>{{ __('Payment') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="paymentModalBody">
                {{-- Loading State --}}
                <div class="payment-loading d-none" id="paymentLoading">
                    <div class="payment-loading-spinner"></div>
                    <div class="payment-loading-text">{{ __('Processing payment...') }}</div>
                </div>

                {{-- Success State --}}
                <div class="payment-success d-none" id="paymentSuccess">
                    <div class="payment-success-icon">
                        <span class="checkmark">&#10003;</span>
                    </div>
                    <div class="payment-success-title">{{ __('Payment Complete!') }}</div>
                    <div class="payment-success-subtitle" id="paymentSuccessMessage">{{ __('Order placed successfully') }}</div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary me-2" onclick="printReceipt()">
                            {{ __('Print Receipt') }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="closePaymentAndClearCart()">
                            {{ __('Done') }}
                        </button>
                    </div>
                </div>

                {{-- Payment Form --}}
                <div id="paymentFormContainer">
                    <form id="unifiedPaymentForm">
                        {{-- Order Summary --}}
                        <div class="payment-summary-card mb-3">
                            <div class="summary-row">
                                <span class="text-muted">{{ __('Subtotal') }}</span>
                                <span id="paymentSubtotal">{{ currency_icon() }} 0.00</span>
                            </div>
                            <div class="summary-row text-success" id="paymentDiscountRow" style="display: none;">
                                <span>{{ __('Discount') }}</span>
                                <span id="paymentDiscountDisplay">- {{ currency_icon() }} 0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>{{ __('Tax') }} (<span id="paymentTaxRateDisplay">0</span>%)</span>
                                <span id="paymentTaxDisplay">{{ currency_icon() }} 0.00</span>
                            </div>
                        </div>

                        {{-- Total Display with Order Context --}}
                        <div class="payment-total-card" id="paymentTotalCard">
                            <div class="payment-total-amount" id="paymentTotalAmount">{{ currency_icon() }} 0.00</div>
                            <div class="payment-total-label">{{ __('TOTAL DUE') }}</div>
                            <div class="payment-order-context" id="paymentOrderContext">
                                <span class="order-type-badge badge-dine-in" id="paymentOrderTypeBadge">
                                    {{ __('Dine-In') }}
                                </span>
                                <span class="context-separator">|</span>
                                <span id="paymentItemCount">0 {{ __('items') }}</span>
                            </div>
                        </div>

                        <div class="pm-divider"></div>

                        {{-- Payment Method Selection --}}
                        <div class="pm-section-title">{{ __('Payment Method') }}</div>

                        <div id="paymentMethodContainer">
                            @include('pos::components.payment-method-selector', [
                                'selected' => 'cash',
                                'name' => 'payment_type',
                                'showSplitOption' => true
                            ])
                        </div>

                        {{-- Account Selection (for non-cash payments) --}}
                        <div class="account-selection-container mt-3 d-none" id="accountSelectionContainer">
                            <label class="form-label">{{ __('Select Account') }}</label>
                            <select class="form-select pm-input" name="account_id" id="paymentAccountSelect">
                                <option value="">{{ __('Select Account...') }}</option>
                                @foreach($accounts ?? [] as $account)
                                    @if($account->account_type !== 'cash')
                                    <option value="{{ $account->id }}"
                                            data-type="{{ $account->account_type }}">
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

                        {{-- Split Payment Container --}}
                        <div class="split-payment-container d-none" id="splitPaymentContainer">
                            <div class="pm-divider"></div>
                            <div class="pm-section-title d-flex justify-content-between align-items-center">
                                <span>{{ __('Split Payments') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSplitPayment()">
                                    <i class="bx bx-plus me-1"></i>{{ __('Add') }}
                                </button>
                            </div>

                            <div class="split-payments-list" id="splitPaymentsList">
                                {{-- Split payment rows will be added here --}}
                            </div>

                            <div class="split-total-bar">
                                <div>
                                    <div class="split-total-label">{{ __('Total Payments') }}</div>
                                    <div class="split-remaining" id="splitRemaining">
                                        {{ __('Remaining') }}: <span id="splitRemainingAmount">{{ currency_icon() }} 0.00</span>
                                    </div>
                                </div>
                                <div class="split-total-amount">
                                    <span id="splitTotalPaying">0.00</span>
                                </div>
                            </div>
                        </div>

                        {{-- Single Payment Container --}}
                        <div class="single-payment-container" id="singlePaymentContainer">
                            <div class="pm-divider"></div>

                            {{-- Amount Input --}}
                            @include('pos::components.amount-input', [
                                'total' => 0,
                                'name' => 'receive_amount',
                                'id' => 'paymentReceiveAmount'
                            ])

                            {{-- Change Display --}}
                            <div class="change-display mt-3" id="changeDisplayContainer">
                                <div class="change-display-row">
                                    <span>{{ __('Change Due') }}</span>
                                    <span class="change-amount" id="changeAmount">{{ currency_icon() }} 0.00</span>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden fields --}}
                        <input type="hidden" name="order_type" id="paymentOrderType" value="dine_in">
                        <input type="hidden" name="total_amount" id="paymentTotalHidden" value="0">
                        <input type="hidden" name="table_id" id="paymentTableId" value="">
                        <input type="hidden" name="guest_count" id="paymentGuestCount" value="">
                        <input type="hidden" name="waiter_id" id="paymentWaiterId" value="">
                        <input type="hidden" name="is_split_payment" id="isSplitPayment" value="0">
                    </form>
                </div>
            </div>

            <div class="modal-footer" id="paymentModalFooter">
                <button type="button" class="btn btn-complete-payment btn-lg w-100" id="completePaymentBtn" onclick="completePayment()">
                    <i class="bx bx-check-circle me-1"></i>{{ __('Complete Payment') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.payment-summary-card {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.payment-summary-card .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 14px;
}

.payment-total-card {
    background: var(--pm-primary, #696cff);
    color: white;
    padding: 30px;
    border-radius: var(--pm-radius, 12px);
    text-align: center;
}

.payment-total-amount {
    font-size: 48px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 8px;
}

.payment-total-label {
    font-size: 14px;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 16px;
}

.payment-order-context {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-size: 14px;
    opacity: 0.9;
}

.context-separator {
    opacity: 0.4;
}

/* Change Display */
.change-display {
    background: var(--pm-success-light);
    padding: 16px;
    border-radius: var(--pm-radius);
}

.change-display-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.change-display-row span:first-child {
    font-weight: 500;
    color: var(--pm-dark);
}

.change-amount {
    font-size: 24px;
    font-weight: 700;
    color: var(--pm-success);
}

.change-display.has-change {
    background: var(--pm-success-light);
}

.change-display.insufficient {
    background: var(--pm-danger-light);
}

.change-display.insufficient .change-amount {
    color: var(--pm-danger);
}

/* Order type badge colors in total card */
.payment-total-card .order-type-badge {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
}

/* Modal footer */
#unifiedPaymentModal .modal-footer {
    padding: 16px 24px;
}

/* Split payment styles in unified modal */
#splitPaymentContainer .split-payments-list {
    max-height: 280px;
    overflow-y: auto;
}

.split-payment-row {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 10px;
}

.split-payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
}

.split-payment-number {
    font-weight: 600;
    font-size: 13px;
    color: #495057;
}

.btn-remove-payment {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 18px;
    line-height: 1;
}

.btn-remove-payment:hover {
    background: #fee2e2;
}

.split-payment-methods {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
}

.split-method-option {
    cursor: pointer;
    margin: 0;
}

.split-method-option input[type="radio"] {
    display: none;
}

.split-method-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 8px 4px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: white;
    transition: all 0.2s ease;
    min-height: 50px;
}

.split-method-box i {
    font-size: 16px;
    margin-bottom: 2px;
    color: var(--method-color, #666);
}

.split-method-box span {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    color: #666;
}

.split-method-option:hover .split-method-box {
    border-color: var(--method-color, #696cff);
}

.split-method-option.active .split-method-box,
.split-method-option input:checked + .split-method-box {
    background: var(--method-color, #696cff);
    border-color: var(--method-color, #696cff);
}

.split-method-option.active .split-method-box i,
.split-method-option.active .split-method-box span,
.split-method-option input:checked + .split-method-box i,
.split-method-option input:checked + .split-method-box span {
    color: white;
}

.split-account-select select {
    font-size: 13px;
}

.split-amount-input .input-group-text {
    background: #e9ecef;
    border-color: #dee2e6;
    font-weight: 600;
    font-size: 13px;
}

.split-amount-input input {
    font-weight: 600;
    font-size: 16px;
    text-align: right;
}

.split-total-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--pm-primary, #696cff);
    color: white;
    padding: 12px 16px;
    border-radius: var(--pm-radius, 12px);
}

.split-total-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.8;
}

.split-remaining {
    font-size: 12px;
    margin-top: 2px;
}

.split-remaining.has-remaining {
    color: #ffc107;
}

.split-remaining.fully-paid {
    color: #71dd37;
}

.split-total-amount {
    font-size: 20px;
    font-weight: 700;
}

/* Responsive */
@media (max-width: 576px) {
    .payment-total-amount {
        font-size: 36px;
    }

    .payment-total-card {
        padding: 20px;
    }
}
</style>

<script>
// Currency icon (set from main page)
var currencyIcon = '{{ currency_icon() }}';
var currentOrderType = 'dine_in';
var currentTotal = 0;
var isSplitMode = false;
var splitPaymentIndex = 0;

// Update quick amount buttons dynamically
function updateQuickAmounts(total) {
    const container = document.querySelector('#singlePaymentContainer .quick-amounts-grid');
    if (!container) return;

    // Generate smart quick amounts
    const roundedTotal = Math.ceil(total);
    const suggestions = [];

    // Nearest 100
    const nearest100 = Math.ceil(roundedTotal / 100) * 100;
    if (nearest100 >= roundedTotal) suggestions.push(nearest100);

    // Nearest 500
    const nearest500 = Math.ceil(roundedTotal / 500) * 500;
    if (nearest500 > nearest100) suggestions.push(nearest500);

    // Nearest 1000
    const nearest1000 = Math.ceil(roundedTotal / 1000) * 1000;
    if (nearest1000 > nearest500) suggestions.push(nearest1000);

    // Add higher amounts
    if (nearest1000 + 500 > nearest1000) suggestions.push(nearest1000 + 500);
    if (nearest1000 + 1000 > nearest1000 + 500) suggestions.push(nearest1000 + 1000);

    // Remove duplicates and take top 5
    const uniqueSuggestions = [...new Set(suggestions)].slice(0, 5);

    // Generate buttons HTML
    let buttonsHtml = '';
    uniqueSuggestions.forEach(amount => {
        buttonsHtml += `
            <button type="button"
                    class="btn btn-outline-secondary quick-amount-btn"
                    onclick="setPaymentAmount(${amount})">
                ${amount.toLocaleString()}
            </button>
        `;
    });

    container.innerHTML = buttonsHtml;

    // Update exact amount button
    const exactBtn = document.querySelector('#singlePaymentContainer .exact-amount-btn');
    if (exactBtn) {
        exactBtn.setAttribute('onclick', `setPaymentAmount(${total})`);
    }
}

// Set payment amount from quick buttons
function setPaymentAmount(amount) {
    const amountInput = document.getElementById('paymentReceiveAmount');
    if (amountInput) {
        amountInput.value = amount.toFixed(2);
        calculateChange();
    }
}

// Initialize Payment Modal
function initPaymentModal(options) {
    options = options || {};
    currentTotal = parseFloat(options.total) || 0;
    currentOrderType = options.orderType || 'dine_in';
    const itemCount = options.itemCount || 0;

    console.log('initPaymentModal called with orderType:', currentOrderType, 'total:', currentTotal);

    // Hide previous setup modal if open
    const setupModals = ['dineInSetupModal', 'takeawaySetupModal'];
    setupModals.forEach(modalId => {
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    });

    // Show modal with loading state first
    document.getElementById('paymentLoading').classList.remove('d-none');
    document.getElementById('paymentSuccess').classList.add('d-none');
    document.getElementById('paymentFormContainer').classList.add('d-none');
    document.getElementById('paymentModalFooter').classList.add('d-none');

    // Update loading text
    const loadingText = document.querySelector('.payment-loading-text');
    if (loadingText) {
        loadingText.textContent = '{{ __("Preparing payment...") }}';
    }

    // Show payment modal immediately with loader
    const paymentModal = new bootstrap.Modal(document.getElementById('unifiedPaymentModal'));
    paymentModal.show();

    // Initialize content after a brief delay for smooth UX
    setTimeout(function() {
        // Get summary values from POS
        const subtotal = parseFloat(document.getElementById('subtotal')?.value) || currentTotal;
        const discount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
        const taxRate = parseFloat(document.getElementById('taxRate')?.value) || 0;
        const taxAmount = parseFloat(document.getElementById('taxAmount')?.value) || 0;

        // Update summary display
        document.getElementById('paymentSubtotal').textContent = currencyIcon + ' ' + subtotal.toFixed(2);
        document.getElementById('paymentTaxRateDisplay').textContent = taxRate;
        document.getElementById('paymentTaxDisplay').textContent = currencyIcon + ' ' + taxAmount.toFixed(2);

        // Show/hide discount row
        if (discount > 0) {
            document.getElementById('paymentDiscountRow').style.display = 'flex';
            document.getElementById('paymentDiscountDisplay').textContent = '- ' + currencyIcon + ' ' + discount.toFixed(2);
        } else {
            document.getElementById('paymentDiscountRow').style.display = 'none';
        }

        // Update total display
        document.getElementById('paymentTotalAmount').textContent = currencyIcon + ' ' + currentTotal.toFixed(2);
        document.getElementById('paymentTotalHidden').value = currentTotal;

        // Update order context
        updateOrderTypeBadge(currentOrderType);
        document.getElementById('paymentItemCount').textContent = itemCount + ' {{ __("items") }}';
        document.getElementById('paymentOrderType').value = currentOrderType;

        // Copy relevant data from setup forms
        if (currentOrderType === 'dine_in') {
            const tableId = document.getElementById('tableSelector_table_id')?.value || '';
            const guestCount = document.querySelector('#dineInSetupForm input[name="guest_count"]')?.value || '';
            const waiterId = document.querySelector('#dineInSetupForm input[name="waiter_id"]:checked')?.value || '';

            document.getElementById('paymentTableId').value = tableId;
            document.getElementById('paymentGuestCount').value = guestCount;
            document.getElementById('paymentWaiterId').value = waiterId;
        }

        // Reset to single payment mode
        resetToSinglePayment();

        // Update amount input with total
        const amountInput = document.getElementById('paymentReceiveAmount');
        if (amountInput) {
            amountInput.value = currentTotal.toFixed(2);
        }

        // Update amount input wrapper data-total for change calculation
        const amountWrapper = document.getElementById('paymentReceiveAmountWrapper');
        if (amountWrapper) {
            amountWrapper.dataset.total = currentTotal;
        }

        // Update exact amount button
        const exactBtn = document.getElementById('paymentReceiveAmountExactBtn');
        if (exactBtn) {
            exactBtn.dataset.amount = currentTotal;
        }

        // Update change display
        calculateChange();

        // Hide loading, show form
        document.getElementById('paymentLoading').classList.add('d-none');
        document.getElementById('paymentFormContainer').classList.remove('d-none');
        document.getElementById('paymentModalFooter').classList.remove('d-none');

        // Reset loading text for future use
        if (loadingText) {
            loadingText.textContent = '{{ __("Processing payment...") }}';
        }
    }, 300);
}

// Update order type badge appearance
function updateOrderTypeBadge(orderType) {
    const badge = document.getElementById('paymentOrderTypeBadge');
    if (!badge) return;

    badge.className = 'order-type-badge';

    switch(orderType) {
        case 'dine_in':
            badge.classList.add('badge-dine-in');
            badge.textContent = '{{ __("Dine-In") }}';
            break;
        case 'take_away':
            badge.classList.add('badge-takeaway');
            badge.textContent = '{{ __("Take-Away") }}';
            break;
        default:
            badge.textContent = orderType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
}

// Proceed to payment from setup modals
function proceedToPayment(orderType) {
    // Validate based on order type
    if (orderType === 'dine_in') {
        const tableId = document.getElementById('tableSelector_table_id')?.value;
        if (!tableId) {
            toastr.warning('{{ __("Please select a table") }}');
            return;
        }
        // Store table ID for order submission
        document.getElementById('table_id').value = tableId;
    } else if (orderType === 'take_away') {
        // Store customer info
        const customerName = document.querySelector('#takeawaySetupForm input[name="customer_name"]')?.value || '';
        const customerPhone = document.querySelector('#takeawaySetupForm input[name="customer_phone"]')?.value || '';
        const pickupTime = document.querySelector('#takeawaySetupForm input[name="pickup_time"]:checked')?.value || '15';

        // Store in hidden fields on main form if they exist
        const mainCustomerName = document.querySelector('input#customer_name, input[name="customer_name"]:not(#takeawaySetupForm input)');
        const mainCustomerPhone = document.querySelector('input#customer_phone, input[name="customer_phone"]:not(#takeawaySetupForm input)');
        if (mainCustomerName) mainCustomerName.value = customerName;
        if (mainCustomerPhone) mainCustomerPhone.value = customerPhone;
    }

    // Get current cart total from finalTotal (which includes any discounts)
    const total = parseFloat(document.getElementById('finalTotal')?.textContent.replace(/[^0-9.]/g, '')) || 0;
    const itemCount = parseInt(document.getElementById('titems')?.textContent) || 0;

    let finalTotal = total;

    // Close setup modal
    const setupModalId = orderType === 'dine_in' ? 'dineInSetupModal' : 'takeawaySetupModal';
    const setupModal = bootstrap.Modal.getInstance(document.getElementById(setupModalId));
    if (setupModal) {
        setupModal.hide();
    }

    // Set order type in main form
    document.getElementById('order_type').value = orderType;

    // Initialize payment modal
    initPaymentModal({
        total: finalTotal,
        orderType: orderType,
        itemCount: itemCount
    });
}

// Reset to single payment mode
function resetToSinglePayment() {
    isSplitMode = false;
    document.getElementById('isSplitPayment').value = '0';
    document.getElementById('splitPaymentContainer').classList.add('d-none');
    document.getElementById('singlePaymentContainer').classList.remove('d-none');
    document.getElementById('splitPaymentsList').innerHTML = '';
    splitPaymentIndex = 0;

    // Reset payment method to cash
    document.querySelectorAll('#paymentMethodContainer .payment-method-option').forEach(opt => {
        opt.classList.remove('active');
        const radio = opt.querySelector('input');
        if (radio) radio.checked = false;
    });
    const cashOption = document.querySelector('#paymentMethodContainer .payment-method-option input[value="cash"]');
    if (cashOption) {
        cashOption.checked = true;
        cashOption.closest('.payment-method-option').classList.add('active');
    }

    // Hide account selection
    document.getElementById('accountSelectionContainer').classList.add('d-none');
}

// Enable split payment mode
function enableSplitPayment() {
    isSplitMode = true;
    document.getElementById('isSplitPayment').value = '1';
    document.getElementById('splitPaymentContainer').classList.remove('d-none');
    document.getElementById('singlePaymentContainer').classList.add('d-none');

    // Add first two payment rows
    addSplitPayment(currentTotal / 2);
    addSplitPayment(currentTotal / 2);
}

// Add split payment row
function addSplitPayment(amount) {
    amount = amount || 0;
    const container = document.getElementById('splitPaymentsList');
    const index = splitPaymentIndex++;

    const html = `
        <div class="split-payment-row" data-index="${index}">
            <div class="split-payment-header">
                <span class="split-payment-number">{{ __('Payment') }} #${index + 1}</span>
                <button type="button" class="btn-remove-payment" onclick="removeSplitPayment(${index})">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="split-payment-content">
                <div class="split-payment-methods">
                    <label class="split-method-option active">
                        <input type="radio" name="payment_type[${index}]" value="cash" checked onchange="onSplitPaymentTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #71dd37">
                            <i class="bx bx-money"></i>
                            <span>{{ __('Cash') }}</span>
                        </div>
                    </label>
                    <label class="split-method-option">
                        <input type="radio" name="payment_type[${index}]" value="card" onchange="onSplitPaymentTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #696cff">
                            <i class="bx bx-credit-card"></i>
                            <span>{{ __('Card') }}</span>
                        </div>
                    </label>
                    <label class="split-method-option">
                        <input type="radio" name="payment_type[${index}]" value="bank" onchange="onSplitPaymentTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #03c3ec">
                            <i class="bx bx-building"></i>
                            <span>{{ __('Bank') }}</span>
                        </div>
                    </label>
                    <label class="split-method-option">
                        <input type="radio" name="payment_type[${index}]" value="mobile_banking" onchange="onSplitPaymentTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #ffab00">
                            <i class="bx bx-mobile"></i>
                            <span>{{ __('Mobile') }}</span>
                        </div>
                    </label>
                </div>
                <div class="split-account-select mt-2" style="display: none;">
                    <select class="form-select form-select-sm" name="account_id[${index}]">
                        <option value="">{{ __('Select Account...') }}</option>
                        @foreach($accounts ?? [] as $account)
                            @if($account->account_type !== 'cash')
                            <option value="{{ $account->id }}" data-type="{{ $account->account_type }}">
                                {{ $account->bank_account_number ?? $account->card_number ?? $account->mobile_number ?? $account->name }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="split-amount-input mt-2">
                    <div class="input-group">
                        <span class="input-group-text">{{ currency_icon() }}</span>
                        <input type="number" name="paying_amount[${index}]" class="form-control split-amount" value="${amount.toFixed(2)}" step="0.01" min="0" onchange="calculateSplitTotal()" oninput="calculateSplitTotal()">
                    </div>
                </div>
            </div>
        </div>
    `;

    // Create temp element to insert the HTML
    const temp = document.createElement('div');
    temp.innerHTML = html;

    // Append with animation
    const row = temp.firstElementChild;
    row.style.animation = 'slideUp 0.3s ease';
    container.appendChild(row);

    // Calculate totals
    calculateSplitTotal();
}

// Handle split payment type change
function onSplitPaymentTypeChange(index, type) {
    const row = document.querySelector(`.split-payment-row[data-index="${index}"]`);
    if (!row) return;

    // Update active state on method buttons
    row.querySelectorAll('.split-method-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.querySelector(`input[value="${type}"]`)) {
            opt.classList.add('active');
        }
    });

    // Show/hide account select
    const accountSelect = row.querySelector('.split-account-select');
    if (type === 'cash') {
        accountSelect.style.display = 'none';
        accountSelect.querySelector('select').value = '';
    } else {
        accountSelect.style.display = 'block';
        // Filter options by type
        accountSelect.querySelectorAll('option').forEach(opt => {
            if (opt.value === '') {
                opt.style.display = 'block';
            } else {
                opt.style.display = opt.dataset.type === type ? 'block' : 'none';
            }
        });
    }
}

// Calculate split payment total
function calculateSplitTotal() {
    let total = 0;
    document.querySelectorAll('#splitPaymentsList .split-amount').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const remaining = currentTotal - total;
    const remainingEl = document.getElementById('splitRemaining');
    const remainingAmount = document.getElementById('splitRemainingAmount');

    if (remainingAmount) {
        remainingAmount.textContent = currencyIcon + ' ' + Math.abs(remaining).toFixed(2);
    }

    if (remainingEl) {
        if (remaining <= 0.01) {
            remainingEl.classList.remove('has-remaining');
            remainingEl.classList.add('fully-paid');
            remainingEl.innerHTML = '<i class="bx bx-check-circle text-success"></i> {{ __("Fully Covered") }}';
        } else {
            remainingEl.classList.add('has-remaining');
            remainingEl.classList.remove('fully-paid');
            remainingEl.innerHTML = '{{ __("Remaining") }}:<br><span id="splitRemainingAmount">' + currencyIcon + ' ' + remaining.toFixed(2) + '</span>';
        }
    }

    // Enable/disable complete button
    document.getElementById('completePaymentBtn').disabled = remaining > 0.01;

    // Dispatch event
    document.dispatchEvent(new CustomEvent('splitTotalChanged', {
        detail: { total: total, remaining: remaining }
    }));
}

// Remove split payment row
function removeSplitPayment(index) {
    const row = document.querySelector(`.split-payment-row[data-index="${index}"]`);
    if (row) {
        row.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => {
            row.remove();
            calculateSplitTotal();
        }, 300);
    }
}

// Calculate change for single payment
function calculateChange() {
    const received = parseFloat(document.getElementById('paymentReceiveAmount')?.value) || 0;
    const change = received - currentTotal;

    const changeDisplay = document.getElementById('changeDisplayContainer');
    const changeAmount = document.getElementById('changeAmount');

    if (change >= 0) {
        changeDisplay.classList.remove('insufficient');
        changeDisplay.classList.add('has-change');
        changeAmount.textContent = currencyIcon + ' ' + change.toFixed(2);
    } else {
        changeDisplay.classList.add('insufficient');
        changeDisplay.classList.remove('has-change');
        changeAmount.textContent = '- ' + currencyIcon + ' ' + Math.abs(change).toFixed(2);
    }

    // Enable/disable complete button
    const completeBtn = document.getElementById('completePaymentBtn');
    if (received >= currentTotal) {
        completeBtn.disabled = false;
    } else {
        // Allow for card/bank payments even with exact amount
        const paymentType = document.querySelector('#unifiedPaymentForm input[name="payment_type"]:checked')?.value;
        if (paymentType !== 'cash' && received > 0) {
            completeBtn.disabled = false;
        } else {
            completeBtn.disabled = change < 0;
        }
    }
}

// Complete payment
function completePayment() {
    const form = document.getElementById('unifiedPaymentForm');
    const formData = new FormData();

    // Add CSRF token
    formData.append('_token', '{{ csrf_token() }}');

    // Add basic form data
    formData.append('order_type', document.getElementById('paymentOrderType').value);
    formData.append('total_amount', document.getElementById('paymentTotalHidden').value);
    formData.append('table_id', document.getElementById('paymentTableId').value);
    formData.append('guest_count', document.getElementById('paymentGuestCount').value);
    formData.append('waiter_id', document.getElementById('paymentWaiterId').value);
    formData.append('sale_date', new Date().toLocaleDateString('en-GB').replace(/\//g, '-')); // DD-MM-YYYY format

    // Add tax and discount data from POS summary
    formData.append('tax_rate', document.getElementById('taxRate')?.value || 0);
    formData.append('total_tax', document.getElementById('taxAmount')?.value || 0);
    formData.append('tax_amount', document.getElementById('taxAmount')?.value || 0);
    formData.append('discount_amount', document.getElementById('discount_total_amount')?.value || 0);
    formData.append('sub_total', document.getElementById('subtotal')?.value || document.getElementById('total')?.value || 0);

    // Add order type specific data
    if (currentOrderType === 'take_away') {
        const takeawayData = getTakeawayFormData();
        Object.keys(takeawayData).forEach(key => {
            formData.append(key, takeawayData[key]);
        });
    }

    // Handle payments - Backend expects arrays: payment_type[], paying_amount[], account_id[]
    if (isSplitMode) {
        // Split payments - collect from each row
        let index = 0;
        document.querySelectorAll('#splitPaymentsList .split-payment-row').forEach((row) => {
            const paymentType = row.querySelector('input[name^="payment_type"]:checked')?.value || 'cash';
            const amount = parseFloat(row.querySelector('.split-amount')?.value) || 0;
            const accountId = row.querySelector('select[name^="account_id"]')?.value || '';

            if (amount > 0) {
                formData.append('payment_type[' + index + ']', paymentType);
                formData.append('paying_amount[' + index + ']', amount);
                formData.append('account_id[' + index + ']', accountId);
                index++;
            }
        });

        // Calculate totals for split payment
        let totalReceived = 0;
        document.querySelectorAll('#splitPaymentsList .split-amount').forEach(input => {
            totalReceived += parseFloat(input.value) || 0;
        });
        formData.append('receive_amount', totalReceived);
        formData.append('return_amount', Math.max(0, totalReceived - currentTotal));
    } else {
        // Single payment - send as array with index 0
        const paymentType = document.querySelector('#unifiedPaymentForm input[name="payment_type"]:checked')?.value || 'cash';
        const receiveAmount = parseFloat(document.getElementById('paymentReceiveAmount')?.value) || 0;
        const accountId = document.getElementById('paymentAccountSelect')?.value || '';

        formData.append('payment_type[0]', paymentType);
        formData.append('paying_amount[0]', receiveAmount);
        formData.append('account_id[0]', accountId);
        formData.append('receive_amount', receiveAmount);
        formData.append('return_amount', Math.max(0, receiveAmount - currentTotal));
    }

    // Show loading
    document.getElementById('paymentFormContainer').classList.add('d-none');
    document.getElementById('paymentModalFooter').classList.add('d-none');
    document.getElementById('paymentLoading').classList.remove('d-none');

    // Submit payment
    fetch('{{ route("admin.pos.checkout") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        document.getElementById('paymentLoading').classList.add('d-none');

        if (result.success) {
            // Show success
            document.getElementById('paymentSuccess').classList.remove('d-none');
            document.getElementById('paymentSuccessMessage').textContent =
                result.message || '{{ __("Order placed successfully") }}';

            // Store order ID for receipt printing
            window.lastOrderId = result.order_id;

            // Award loyalty points if customer has phone
            awardLoyaltyPointsForOrder(result.order_id, currentTotal);

            // Reset cart
            if (typeof getCart === 'function') {
                getCart();
            }

            // Update running orders count
            if (typeof updateRunningOrdersCount === 'function') {
                updateRunningOrdersCount();
            }

            // Auto-print receipt if enabled
            if (result.auto_print && result.order_id) {
                printReceipt(result.order_id);
            }
        } else {
            // Show form again on error
            document.getElementById('paymentFormContainer').classList.remove('d-none');
            document.getElementById('paymentModalFooter').classList.remove('d-none');
            toastr.error(result.message || '{{ __("Payment failed") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('paymentLoading').classList.add('d-none');
        document.getElementById('paymentFormContainer').classList.remove('d-none');
        document.getElementById('paymentModalFooter').classList.remove('d-none');
        toastr.error('{{ __("An error occurred") }}');
    });
}

// Print POS receipt for thermal printer
function printReceipt(orderId) {
    orderId = orderId || window.lastOrderId;
    if (!orderId) {
        toastr.warning('{{ __("No order to print") }}');
        return;
    }

    // Open POS receipt in print-friendly window (80mm thermal printer format)
    const printUrl = '{{ route("admin.pos.running-orders.receipt", ["id" => "__ID__"]) }}'.replace('__ID__', orderId) + '?auto=1';
    const printWindow = window.open(printUrl, 'pos_receipt', 'width=350,height=600,scrollbars=yes,resizable=yes');

    // Focus on print window
    if (printWindow) {
        printWindow.focus();
    }
}

// Award loyalty points for completed order
function awardLoyaltyPointsForOrder(orderId, orderTotal) {
    // Get customer phone from various sources
    let customerPhone = null;

    // Try from customer selector
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect && customerSelect.value && customerSelect.value !== 'walk-in-customer') {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        customerPhone = selectedOption?.dataset?.phone;
    }

    // If no phone, skip awarding points
    if (!customerPhone) {
        console.log('No customer phone found, skipping loyalty points');
        return;
    }

    // Award points via API
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('phone', customerPhone);
    formData.append('sale_id', orderId);
    formData.append('order_total', orderTotal);

    fetch('{{ route("admin.pos.loyalty.award") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.points_earned > 0) {
            // Show points earned notification
            toastr.info('{{ __("Customer earned") }} ' + result.points_earned + ' {{ __("loyalty points!") }}');

            // Update success message to include points
            const successMsg = document.getElementById('paymentSuccessMessage');
            if (successMsg) {
                successMsg.innerHTML = successMsg.textContent +
                    '<br><small class="text-info"><i class="bx bx-star"></i> +' + result.points_earned + ' {{ __("points earned") }}</small>';
            }
        }
    })
    .catch(error => {
        console.log('Loyalty points error (non-critical):', error);
    });
}

// Close payment modal and clear cart
function closePaymentAndClearCart() {
    // Close the modal
    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('unifiedPaymentModal'));
    if (paymentModal) {
        paymentModal.hide();
    }

    // Clear order ID
    window.lastOrderId = null;

    // Clear the cart by calling the cart-clear route and reload page
    fetch('{{ route("admin.cart-clear") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(() => {
        // Reload the page to show empty cart
        window.location.reload();
    })
    .catch(() => {
        // Still reload on error
        window.location.reload();
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Amount input change
    const amountInput = document.getElementById('paymentReceiveAmount');
    if (amountInput) {
        amountInput.addEventListener('input', calculateChange);
        amountInput.addEventListener('change', calculateChange);
    }

    // Payment method change
    document.addEventListener('paymentMethodChanged', function(e) {
        const paymentType = e.detail.type;
        const accountContainer = document.getElementById('accountSelectionContainer');
        const accountSelect = document.getElementById('paymentAccountSelect');

        if (paymentType === 'cash') {
            accountContainer.classList.add('d-none');
            accountSelect.value = '';
        } else if (paymentType === 'split') {
            enableSplitPayment();
        } else {
            accountContainer.classList.remove('d-none');
            // Filter account options by type
            accountSelect.querySelectorAll('option').forEach(opt => {
                if (opt.value === '') {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = opt.dataset.type === paymentType ? 'block' : 'none';
                }
            });
            accountSelect.value = '';
        }

        calculateChange();
    });

    // Split total changed
    document.addEventListener('splitTotalChanged', function(e) {
        const total = e.detail.total;
        const remaining = currentTotal - total;

        document.getElementById('splitRemainingAmount').textContent =
            currencyIcon + ' ' + Math.abs(remaining).toFixed(2);

        const remainingEl = document.getElementById('splitRemaining');
        if (remaining <= 0) {
            remainingEl.classList.remove('has-remaining');
            remainingEl.classList.add('fully-paid');
            remainingEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> {{ __("Fully Covered") }}';
        } else {
            remainingEl.classList.add('has-remaining');
            remainingEl.classList.remove('fully-paid');
            remainingEl.innerHTML = '{{ __("Remaining") }}: <span id="splitRemainingAmount">' +
                currencyIcon + ' ' + remaining.toFixed(2) + '</span>';
        }

        // Enable/disable complete button
        document.getElementById('completePaymentBtn').disabled = remaining > 0.01;
    });

    // Quick amount buttons
    document.addEventListener('quickAmountSelected', function(e) {
        const amount = e.detail.amount;
        const amountInput = document.getElementById('paymentReceiveAmount');
        if (amountInput) {
            amountInput.value = amount.toFixed(2);
            calculateChange();
        }
    });
});
</script>
