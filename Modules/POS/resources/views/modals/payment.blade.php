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
    <div class="modal-dialog modal-lg unified-payment-dialog">
        <div class="modal-content unified-payment-content">
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

                        {{-- Split Payment Container --}}
                        <div class="split-payment-container d-none" id="splitPaymentContainer">
                            <div class="pm-divider"></div>
                            <div class="pm-section-title d-flex justify-content-between align-items-center">
                                <span>{{ __('Split Payments') }}</span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="resetToSinglePayment()">
                                        <i class="bx bx-x me-1"></i>{{ __('Cancel') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSplitBtn" onclick="addSplitPayment()">
                                        <i class="bx bx-plus me-1"></i>{{ __('Add') }}
                                    </button>
                                </div>
                            </div>

                            <div class="split-payments-list" id="splitPaymentsList">
                                {{-- Compact split payment rows will be added here --}}
                            </div>

                            <div class="split-total-bar" id="splitTotalBar">
                                <div>
                                    <div class="split-total-label">{{ __('Total Payments') }}</div>
                                    <div class="split-remaining" id="splitRemaining">
                                        {{ __('Remaining') }}: <span id="splitRemainingAmount">{{ currency_icon() }} 0.00</span>
                                    </div>
                                </div>
                                <div class="split-total-amount">
                                    {{ currency_icon() }} <span id="splitTotalPaying">0.00</span>
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

                            {{-- Change display is handled by the amount-input component above --}}
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
/* Dialog positioning - account for POS footer */
.unified-payment-dialog {
    max-width: 800px;
    margin-top: 0.5rem;
    margin-bottom: calc(var(--pos-footer-height, 70px) + 0.5rem);
}

/* Flex column layout for sticky footer */
.unified-payment-content {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - var(--pos-footer-height, 70px) - 1rem);
}

/* Scrollable body */
#unifiedPaymentModal .modal-body {
    flex: 1 1 auto;
    overflow-x: hidden;
    overflow-y: auto;
}

/* Sticky footer */
#unifiedPaymentModal .modal-footer {
    flex-shrink: 0;
    padding: 16px 24px;
}

.payment-summary-card {
    background: #f8f9fa;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.payment-summary-card .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 14px;
}

.payment-total-card {
    background: var(--pm-primary, #696cff);
    color: white;
    padding: 16px 20px;
    border-radius: var(--pm-radius, 12px);
    text-align: center;
}

.payment-total-amount {
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 4px;
}

.payment-total-label {
    font-size: 12px;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 8px;
}

.payment-order-context {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-size: 13px;
    opacity: 0.9;
}

.context-separator {
    opacity: 0.4;
}


/* Order type badge colors in total card */
.payment-total-card .order-type-badge {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
}

/* Split payment styles - compact rows */
#splitPaymentContainer .split-payments-list {
    max-height: 300px;
    overflow-y: auto;
}

.split-payment-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 12px;
    margin-bottom: 8px;
}

.split-row-number {
    font-weight: 700;
    font-size: 13px;
    color: #696cff;
    min-width: 24px;
}

.split-row-method {
    min-width: 100px;
}

.split-row-method select {
    font-size: 13px;
    padding: 6px 10px;
    border-radius: 6px;
}

.split-row-account {
    min-width: 130px;
}

.split-row-account select {
    font-size: 13px;
    padding: 6px 10px;
    border-radius: 6px;
}

.split-row-amount {
    flex: 1;
    min-width: 120px;
}

.split-row-amount .input-group-text {
    background: var(--pm-primary, #696cff);
    color: white;
    border-color: var(--pm-primary, #696cff);
    font-weight: 600;
    font-size: 14px;
    padding: 8px 12px;
}

.split-row-amount input {
    font-weight: 700;
    font-size: 16px;
    text-align: right;
    padding: 8px 12px;
    border-color: #dee2e6;
}

.split-row-amount input:focus {
    border-color: var(--pm-primary, #696cff);
    box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.15);
}

.btn-remove-split {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 18px;
    line-height: 1;
    flex-shrink: 0;
}

.btn-remove-split:hover {
    background: #fee2e2;
}

.split-total-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-radius: var(--pm-radius, 12px);
    margin-top: 8px;
}

.split-total-bar.state-remaining {
    background: var(--pm-warning, #ffab00);
    color: #232333;
}

.split-total-bar.state-exact {
    background: var(--pm-success, #71dd37);
    color: white;
}

.split-total-bar.state-overpaid {
    background: #ff6b35;
    color: white;
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
    font-weight: 600;
}

.split-total-amount {
    font-size: 20px;
    font-weight: 700;
}

#addSplitBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 576px) {
    .split-payment-row {
        flex-wrap: wrap;
    }
    .split-row-method,
    .split-row-account {
        min-width: calc(50% - 20px);
    }
    .split-row-amount {
        min-width: calc(100% - 40px);
    }
}

/* Responsive */
@media (max-width: 576px) {
    .payment-total-amount {
        font-size: 26px;
    }

    .payment-total-card {
        padding: 12px 16px;
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

// Max split payment rows
var MAX_SPLIT_ROWS = 4;

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

    // Hide component's account selection
    const compAccountSel = document.querySelector('#paymentMethodContainer .account-selection');
    if (compAccountSel) compAccountSel.style.display = 'none';

    // Re-enable complete button
    document.getElementById('completePaymentBtn').disabled = false;

    // Recalculate change for single mode
    calculateChange();
}

// Enable split payment mode
function enableSplitPayment() {
    if (isSplitMode) return; // Guard against double-click

    isSplitMode = true;
    document.getElementById('isSplitPayment').value = '1';
    document.getElementById('splitPaymentContainer').classList.remove('d-none');
    document.getElementById('singlePaymentContainer').classList.add('d-none');
    document.getElementById('splitPaymentsList').innerHTML = '';
    splitPaymentIndex = 0;

    // Hide component's account selection
    const accountSelection = document.querySelector('#paymentMethodContainer .account-selection');
    if (accountSelection) accountSelection.style.display = 'none';

    // Add first two payment rows with 50/50 split
    addSplitPayment(Math.round(currentTotal / 2 * 100) / 100);
    addSplitPayment(Math.round(currentTotal / 2 * 100) / 100);
}

// Add split payment row (compact single-line)
function addSplitPayment(amount) {
    const container = document.getElementById('splitPaymentsList');
    const rowCount = container.querySelectorAll('.split-payment-row').length;

    // Enforce max limit
    if (rowCount >= MAX_SPLIT_ROWS) {
        toastr.warning('{{ __("Maximum") }} ' + MAX_SPLIT_ROWS + ' {{ __("split payments allowed") }}');
        return;
    }

    amount = amount || 0;
    const index = splitPaymentIndex++;

    const html = `
        <div class="split-payment-row" data-index="${index}">
            <span class="split-row-number">#${rowCount + 1}</span>
            <div class="split-row-method">
                <select class="form-select form-select-sm" name="split_type_${index}" onchange="onSplitMethodChange(${index}, this.value)">
                    <option value="cash" selected>{{ __('Cash') }}</option>
                    <option value="card">{{ __('Card') }}</option>
                    <option value="bank">{{ __('Bank') }}</option>
                    <option value="mobile_banking">{{ __('Mobile') }}</option>
                </select>
            </div>
            <div class="split-row-account" style="display: none;">
                <select class="form-select form-select-sm" name="split_account_${index}">
                    <option value="">{{ __('Account...') }}</option>
                    @foreach($accounts ?? [] as $account)
                        @if($account->account_type !== 'cash')
                        <option value="{{ $account->id }}" data-type="{{ $account->account_type }}">
                            {{ $account->bank_account_number ?? $account->card_number ?? $account->mobile_number ?? $account->name }}
                        </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="split-row-amount">
                <div class="input-group">
                    <span class="input-group-text">{{ currency_icon() }}</span>
                    <input type="number" name="split_amount_${index}" class="form-control split-amount"
                           value="${amount.toFixed(2)}" step="0.01" min="0"
                           oninput="calculateSplitTotal()" onchange="calculateSplitTotal()">
                </div>
            </div>
            <button type="button" class="btn-remove-split" onclick="removeSplitPayment(${index})" title="{{ __('Remove') }}">
                <i class="bx bx-x"></i>
            </button>
        </div>
    `;

    const temp = document.createElement('div');
    temp.innerHTML = html.trim();
    const row = temp.firstElementChild;
    row.style.animation = 'slideUp 0.3s ease';
    container.appendChild(row);

    // Update Add button state
    updateAddSplitBtnState();

    // Calculate totals
    calculateSplitTotal();
}

// Update Add button disabled state
function updateAddSplitBtnState() {
    const container = document.getElementById('splitPaymentsList');
    const rowCount = container.querySelectorAll('.split-payment-row').length;
    const addBtn = document.getElementById('addSplitBtn');
    if (addBtn) {
        addBtn.disabled = rowCount >= MAX_SPLIT_ROWS;
    }
}

// Handle split row method change
function onSplitMethodChange(index, type) {
    const row = document.querySelector(`.split-payment-row[data-index="${index}"]`);
    if (!row) return;

    const accountDiv = row.querySelector('.split-row-account');
    const accountSelect = accountDiv.querySelector('select');

    if (type === 'cash') {
        accountDiv.style.display = 'none';
        accountSelect.value = '';
    } else {
        accountDiv.style.display = 'block';
        // Filter account options by type
        accountSelect.querySelectorAll('option').forEach(opt => {
            if (opt.value === '') {
                opt.style.display = 'block';
            } else {
                opt.style.display = opt.dataset.type === type ? 'block' : 'none';
            }
        });
        accountSelect.value = '';
    }
}

// Calculate split payment total
function calculateSplitTotal() {
    let total = 0;
    document.querySelectorAll('#splitPaymentsList .split-amount').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const remaining = currentTotal - total;
    const totalBar = document.getElementById('splitTotalBar');
    const remainingEl = document.getElementById('splitRemaining');
    const totalPayingEl = document.getElementById('splitTotalPaying');

    // Update total paying display
    if (totalPayingEl) {
        totalPayingEl.textContent = total.toFixed(2);
    }

    // Update status bar color and message
    if (totalBar) {
        totalBar.classList.remove('state-remaining', 'state-exact', 'state-overpaid');
    }

    if (remainingEl) {
        if (remaining > 0.01) {
            // Under-paid
            if (totalBar) totalBar.classList.add('state-remaining');
            remainingEl.innerHTML = '{{ __("Remaining") }}: ' + currencyIcon + ' ' + remaining.toFixed(2);
        } else if (remaining < -0.01) {
            // Over-paid
            if (totalBar) totalBar.classList.add('state-overpaid');
            remainingEl.innerHTML = '<i class="bx bx-error me-1"></i>{{ __("Overpaid by") }} ' + currencyIcon + ' ' + Math.abs(remaining).toFixed(2);
        } else {
            // Exact
            if (totalBar) totalBar.classList.add('state-exact');
            remainingEl.innerHTML = '<i class="bx bx-check-circle me-1"></i>{{ __("Fully Covered") }}';
        }
    }

    // Enable complete button only if fully covered (allow slight overpayment)
    document.getElementById('completePaymentBtn').disabled = remaining > 0.01;
}

// Remove split payment row
function removeSplitPayment(index) {
    const container = document.getElementById('splitPaymentsList');
    const rows = container.querySelectorAll('.split-payment-row');

    // Prevent removing if only 2 rows left
    if (rows.length <= 2) {
        toastr.warning('{{ __("Minimum 2 split payments required. Use Cancel to exit split mode.") }}');
        return;
    }

    const row = container.querySelector(`.split-payment-row[data-index="${index}"]`);
    if (row) {
        row.remove();
        // Re-number remaining rows
        container.querySelectorAll('.split-payment-row').forEach((r, i) => {
            r.querySelector('.split-row-number').textContent = '#' + (i + 1);
        });
        updateAddSplitBtnState();
        calculateSplitTotal();
    }
}

// Calculate change for single payment
function calculateChange() {
    const received = parseFloat(document.getElementById('paymentReceiveAmount')?.value) || 0;
    const change = received - currentTotal;

    // Update the amount-input component's change display
    const changeDisplay = document.getElementById('paymentReceiveAmountChangeDisplay');
    const changeValue = document.getElementById('paymentReceiveAmountChangeValue');
    const changeCard = document.getElementById('paymentReceiveAmountChangeCard');

    if (changeDisplay && changeValue && changeCard) {
        if (received > 0 && currentTotal > 0) {
            changeDisplay.style.display = 'block';
            changeValue.textContent = Math.abs(change).toFixed(2);

            if (change >= 0) {
                changeCard.classList.remove('has-due');
                changeCard.querySelector('.change-label').textContent = '{{ __("Change Due") }}';
            } else {
                changeCard.classList.add('has-due');
                changeCard.querySelector('.change-label').textContent = '{{ __("Amount Due") }}';
            }
        } else {
            changeDisplay.style.display = 'none';
        }
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
        // Split payments - collect from each compact row
        let index = 0;
        document.querySelectorAll('#splitPaymentsList .split-payment-row').forEach((row) => {
            const paymentType = row.querySelector('select[name^="split_type_"]')?.value || 'cash';
            const amount = parseFloat(row.querySelector('.split-amount')?.value) || 0;
            const accountId = row.querySelector('select[name^="split_account_"]')?.value || '';

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
        // Read account from the payment-method-selector component's own select
        const accountId = document.querySelector('#paymentMethodContainer .account-select')?.value || '';

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

            // Show loyalty points notification (points earned server-side)
            if (result.points_earned > 0) {
                toastr.info('{{ __("Customer earned") }} ' + result.points_earned + ' {{ __("loyalty points!") }}');
                const successMsg = document.getElementById('paymentSuccessMessage');
                if (successMsg) {
                    successMsg.innerHTML = successMsg.textContent +
                        '<br><small class="text-info"><i class="bx bx-star"></i> +' + result.points_earned + ' {{ __("points earned") }}</small>';
                }
            }

            // Clear cart
            $(".product-table tbody").html('');
            $('#titems').text(0);
            $('#discount_total_amount').val(0);
            $('#tds').text(0);
            if (typeof totalSummery === 'function') {
                totalSummery();
            }
            $("#customer_id").val('').trigger('change');
            $('#discount_type').val(0).trigger('change');
            $('.dis-form').hide();

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

    // Payment method change — component handles its own account selection
    document.addEventListener('paymentMethodChanged', function(e) {
        const paymentType = e.detail.type;

        if (paymentType === 'split') {
            enableSplitPayment();
        } else {
            // If we were in split mode, exit it
            if (isSplitMode) {
                resetToSinglePayment();
            }
        }

        calculateChange();
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
