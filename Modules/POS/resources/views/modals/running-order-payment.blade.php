{{--
    Running Order Payment Modal (Redesigned)

    Purpose: Payment for orders already started (dine-in deferred payments)
    Shows order summary with items, discount controls, and payment options

    Data required (passed via JavaScript):
    - orderId: The order ID
    - orderData: Order details including items, totals, table info
--}}

@php
    $accounts = $accounts ?? collect();
@endphp

<div class="modal fade payment-modal" id="runningOrderPaymentModal" tabindex="-1" aria-labelledby="runningOrderPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable rop-dialog">
        <div class="modal-content rop-modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bx bx-credit-card me-2"></i>{{ __('Checkout') }}
                    <span class="badge bg-white bg-opacity-25 ms-2 fw-normal" id="ropTableBadge" style="font-size: 12px;">--</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0" id="ropModalBody" style="min-height: 300px;">
                {{-- Loading State --}}
                <div class="payment-loading" id="ropLoading" style="display: flex !important; min-height: 300px;">
                    <div class="payment-loading-spinner"></div>
                    <div class="payment-loading-text">{{ __('Loading order...') }}</div>
                </div>

                {{-- Success State --}}
                <div class="payment-success" id="ropSuccess" style="display: none !important;">
                    <div class="payment-success-icon">
                        <i class="bx bx-check"></i>
                    </div>
                    <div class="payment-success-title">{{ __('Payment Complete!') }}</div>
                    <div class="payment-success-subtitle" id="ropSuccessMessage">{{ __('Table released successfully') }}</div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary me-2" onclick="printRunningOrderReceipt()">
                            <i class="bx bx-printer me-2"></i>{{ __('Print Receipt') }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="closeAndRefresh()">
                            <i class="bx bx-check me-2"></i>{{ __('Done') }}
                        </button>
                    </div>
                </div>

                {{-- Main Content --}}
                <div id="ropContent" style="display: none !important;">
                    <div class="row g-0">
                        {{-- Left Column: Order Details --}}
                        <div class="col-lg-5 border-end">
                            <div class="p-3">
                                {{-- Order Info Header --}}
                                <div class="rop-order-header mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted mb-1">{{ __('Order') }}</h6>
                                            <h4 class="mb-0" id="ropOrderNumber">#000000</h4>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary" id="ropOrderType">{{ __('Dine-In') }}</span>
                                            <div class="small text-muted mt-1" id="ropOrderTime">--:--</div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3 mt-2 text-muted small">
                                        <span><i class="bx bx-user me-1"></i><span id="ropWaiterName">--</span></span>
                                        <span><i class="bx bx-group me-1"></i><span id="ropGuestCount">0</span> {{ __('guests') }}</span>
                                    </div>
                                </div>

                                {{-- Order Items --}}
                                <div class="pm-section-title d-flex justify-content-between align-items-center">
                                    <span><i class="bx bx-food-menu me-2"></i>{{ __('Order Items') }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editRunningOrder()" id="ropEditBtn">
                                        <i class="bx bx-edit-alt me-1"></i>{{ __('Edit') }}
                                    </button>
                                </div>

                                <div class="rop-items-list" id="ropItemsList">
                                    {{-- Items will be populated via JavaScript --}}
                                </div>

                                {{-- Discount Section --}}
                                <div class="pm-divider"></div>
                                <div class="pm-section-title">
                                    <i class="bx bx-purchase-tag me-2"></i>{{ __('Discount') }}
                                </div>

                                <div class="rop-discount-section">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">{{ __('Amount') }}</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ currency_icon() }}</span>
                                                <input type="number"
                                                       class="form-control"
                                                       id="ropDiscountAmount"
                                                       value="0"
                                                       min="0"
                                                       step="0.01"
                                                       onchange="applyRopDiscount('amount')">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">{{ __('Percentage') }}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number"
                                                       class="form-control"
                                                       id="ropDiscountPercent"
                                                       value="0"
                                                       min="0"
                                                       max="100"
                                                       step="0.5"
                                                       onchange="applyRopDiscount('percent')">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tax Section --}}
                                <div class="pm-divider"></div>
                                <div class="pm-section-title">
                                    <i class="bx bx-receipt me-2"></i>{{ __('Tax') }}
                                </div>

                                <div class="rop-tax-section">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">{{ __('Tax Rate') }}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number"
                                                       class="form-control"
                                                       id="ropTaxRate"
                                                       value="{{ optional($posSettings)->pos_tax_rate ?? $setting->website_tax_rate ?? 0 }}"
                                                       min="0"
                                                       max="100"
                                                       step="0.5"
                                                       onchange="calculateRopTotals()">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">{{ __('Tax Amount') }}</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">{{ currency_icon() }}</span>
                                                <input type="number"
                                                       class="form-control"
                                                       id="ropTaxAmount"
                                                       value="0"
                                                       min="0"
                                                       step="0.01"
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Order Summary --}}
                                <div class="pm-divider-dashed"></div>
                                <div class="rop-summary">
                                    <div class="summary-row">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span id="ropSubtotal">{{ currency_icon() }} 0.00</span>
                                    </div>
                                    <div class="summary-row text-success" id="ropDiscountRow" style="display: none;">
                                        <span>{{ __('Discount') }}</span>
                                        <span id="ropDiscountDisplay">- {{ currency_icon() }} 0.00</span>
                                    </div>
                                    <div class="summary-row" id="ropTaxRow">
                                        <span>{{ __('Tax') }} (<span id="ropTaxRateDisplay">0</span>%)</span>
                                        <span id="ropTaxDisplay">{{ currency_icon() }} 0.00</span>
                                    </div>
                                    <div class="summary-row summary-total">
                                        <span>{{ __('Total Due') }}</span>
                                        <span id="ropGrandTotal">{{ currency_icon() }} 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Payment --}}
                        <div class="col-lg-7">
                            <div class="p-3">
                                {{-- Total Display --}}
                                <div class="rop-total-display mb-4">
                                    <div class="rop-total-amount" id="ropTotalAmount">{{ currency_icon() }} 0.00</div>
                                    <div class="rop-total-label">{{ __('TOTAL DUE') }}</div>
                                </div>

                                {{-- Payment Method Selection --}}
                                <div class="pm-section-title">{{ __('Payment Method') }}</div>

                                <div id="ropPaymentMethodContainer">
                                    @include('pos::components.payment-method-selector', [
                                        'selected' => 'cash',
                                        'name' => 'rop_payment_type',
                                        'showSplitOption' => true,
                                        'accounts' => $accounts ?? []
                                    ])
                                </div>

                                {{-- Split Payment Container --}}
                                <div class="rop-split-container d-none" id="ropSplitContainer">
                                    <div class="pm-divider"></div>
                                    <div class="pm-section-title d-flex justify-content-between align-items-center">
                                        <span>{{ __('Split Payments') }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRopSplitPayment()">
                                            <i class="bx bx-plus me-1"></i>{{ __('Add') }}
                                        </button>
                                    </div>
                                    <div class="rop-split-list" id="ropSplitList">
                                        {{-- Split payment rows --}}
                                    </div>
                                    <div class="split-total-bar mt-3">
                                        <div>
                                            <div class="split-total-label">{{ __('Total Payments') }}</div>
                                            <div class="split-remaining" id="ropSplitRemaining"></div>
                                        </div>
                                        <div class="split-total-amount">
                                            {{ currency_icon() }} <span id="ropSplitTotal">0.00</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Single Payment Container --}}
                                <div class="rop-single-container" id="ropSingleContainer">
                                    <div class="pm-divider"></div>

                                    {{-- Amount Input --}}
                                    <div class="pm-section-title">{{ __('Amount Received') }}</div>

                                    <div class="rop-amount-input-wrapper">
                                        <div class="rop-amount-input-container">
                                            <span class="currency-prefix">{{ currency_icon() }}</span>
                                            <input type="number"
                                                   id="ropAmountReceived"
                                                   class="rop-amount-input"
                                                   value="0"
                                                   step="0.01"
                                                   min="0"
                                                   onchange="calculateRopChange()"
                                                   oninput="calculateRopChange()">
                                        </div>

                                        {{-- Quick Amounts --}}
                                        <div class="rop-quick-amounts mt-3" id="ropQuickAmounts">
                                            {{-- Will be populated via JavaScript --}}
                                        </div>

                                        {{-- Exact Amount Button --}}
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary w-100 rop-exact-btn" onclick="setRopExactAmount()">
                                                <i class="bx bx-check me-1"></i>{{ __('EXACT AMOUNT') }}
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Change Display --}}
                                    <div class="rop-change-display mt-3" id="ropChangeDisplay">
                                        <div class="change-row">
                                            <span class="change-label">{{ __('Change Due') }}</span>
                                            <span class="change-amount" id="ropChangeAmount">{{ currency_icon() }} 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden Fields --}}
                <input type="hidden" id="ropOrderId" value="">
                <input type="hidden" id="ropSubtotalValue" value="0">
                <input type="hidden" id="ropTotalValue" value="0">
                <input type="hidden" id="ropIsSplit" value="0">
            </div>

            <div class="modal-footer rop-modal-footer" id="ropFooter">
                <button type="button" class="btn rop-btn-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                </button>
                <button type="button" class="btn rop-btn-complete" id="ropCompleteBtn" onclick="completeRunningOrderPayment()">
                    <i class="fas fa-check-circle me-2"></i>{{ __('Complete & Release Table') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Running Order Payment Modal - Theme Styles */
.rop-dialog {
    max-width: 1100px;
}

.rop-modal-content {
    border-radius: 16px;
    overflow: hidden;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.rop-modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 16px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.rop-btn-cancel {
    background: #fff;
    border: 1px solid #e9ecef;
    color: #697a8d;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s;
}

.rop-btn-cancel:hover {
    background: #f8f9fa;
    border-color: #d9dee3;
}

.rop-btn-complete {
    background: #696cff;
    border: none;
    color: #fff;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s;
}

.rop-btn-complete:hover {
    background: #5a5ee0;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
}

.rop-btn-complete:disabled {
    background: #a5a7ff;
    cursor: not-allowed;
}

/* Loading Spinner Styles */
.payment-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    min-height: 300px;
}

.payment-loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #696cff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.payment-loading-text {
    margin-top: 16px;
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

/* Success State Styles */
.payment-success {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    min-height: 300px;
}

.payment-success-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(113, 221, 55, 0.15);
    color: #71dd37;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    margin-bottom: 20px;
}

.payment-success-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
}

.payment-success-subtitle {
    font-size: 14px;
    color: #6c757d;
}

/* Running Order Payment Modal Styles */
.rop-order-header {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.rop-items-list {
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 12px;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 0 12px;
}

.rop-items-list::-webkit-scrollbar {
    width: 4px;
}

.rop-items-list::-webkit-scrollbar-thumb {
    background: #696cff;
    border-radius: 2px;
}

.rop-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 0;
    border-bottom: 1px solid #f0f1f5;
}

.rop-item:last-child {
    border-bottom: none;
}

.rop-item-info {
    flex: 1;
}

.rop-item-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
}

.rop-item-qty {
    font-size: 12px;
    color: #696cff;
    font-weight: 600;
    background: rgba(105, 108, 255, 0.1);
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 4px;
}

.rop-item-addons {
    font-size: 11px;
    color: #03c3ec;
    margin-top: 4px;
}

.rop-item-price {
    font-weight: 700;
    color: #2d3748;
    white-space: nowrap;
    font-size: 14px;
}

.rop-discount-section,
.rop-tax-section {
    background: #f8f9fa;
    padding: 14px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.rop-summary {
    background: #f8f9fa;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.rop-summary .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
    color: #697a8d;
}

.rop-summary .summary-row.summary-total {
    font-size: 18px;
    font-weight: 700;
    color: #696cff;
    padding-top: 14px;
    margin-top: 10px;
    border-top: 2px dashed #e9ecef;
}

/* Total Display */
.rop-total-display {
    background: #696cff;
    color: white;
    padding: 28px 24px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(105, 108, 255, 0.3);
}

.rop-total-amount {
    font-size: 42px;
    font-weight: 800;
    line-height: 1.2;
}

.rop-total-label {
    font-size: 12px;
    opacity: 0.85;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 6px;
    font-weight: 500;
}

/* Amount Input */
.rop-amount-input-wrapper {
    max-width: 400px;
    margin: 0 auto;
}

.rop-amount-input-container {
    position: relative;
}

.rop-amount-input-container .currency-prefix {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    font-weight: 600;
    color: #697a8d;
}

.rop-amount-input {
    width: 100%;
    padding: 16px 16px 16px 50px;
    font-size: 32px;
    font-weight: 700;
    text-align: center;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    -moz-appearance: textfield;
    color: #2d3748;
    transition: all 0.2s;
}

.rop-amount-input::-webkit-outer-spin-button,
.rop-amount-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
}

.rop-amount-input:focus {
    border-color: #696cff;
    outline: none;
    box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.15);
}

/* Quick Amounts */
.rop-quick-amounts {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
}

.rop-quick-btn {
    padding: 10px 8px;
    font-size: 14px;
    font-weight: 600;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #697a8d;
}

.rop-quick-btn:hover {
    border-color: #696cff;
    background: rgba(105, 108, 255, 0.08);
    color: #696cff;
    transform: translateY(-2px);
}

.rop-exact-btn {
    padding: 12px 20px;
    font-weight: 700;
    background: #696cff;
    border: none;
    border-radius: 10px;
    color: #fff;
    transition: all 0.2s;
}

.rop-exact-btn:hover {
    background: #5a5ee0;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
}

/* Change Display */
.rop-change-display {
    background: rgba(113, 221, 55, 0.1);
    padding: 16px 20px;
    border-radius: 12px;
    border: 1px solid rgba(113, 221, 55, 0.2);
}

.rop-change-display .change-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rop-change-display .change-label {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
}

.rop-change-display .change-amount {
    font-size: 24px;
    font-weight: 700;
    color: #71dd37;
}

.rop-change-display.insufficient {
    background: rgba(255, 62, 29, 0.1);
    border-color: rgba(255, 62, 29, 0.2);
}

.rop-change-display.insufficient .change-amount {
    color: #ff3e1d;
}

/* Split Payment Styles */
.rop-split-container {
    margin-top: 16px;
}

.rop-split-list {
    max-height: 300px;
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

.split-amount-input .input-group {
    max-width: 100%;
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
    background: #696cff;
    color: white;
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
}

.split-total-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.9;
    font-weight: 500;
}

.split-remaining {
    font-size: 12px;
    margin-top: 4px;
}

.split-remaining.has-remaining {
    color: #ffab00;
}

.split-remaining.fully-paid {
    color: #b4ffb4;
}

.split-total-amount {
    font-size: 22px;
    font-weight: 700;
}

/* Responsive - Tablet (768px - 1199px) */
@media (min-width: 768px) and (max-width: 1199px) {
    .rop-dialog {
        max-width: 95%;
        margin: 1rem auto;
    }

    .rop-total-amount {
        font-size: 36px;
    }

    .rop-amount-input {
        font-size: 26px;
        padding: 14px 14px 14px 45px;
    }

    .rop-quick-amounts {
        grid-template-columns: repeat(5, 1fr);
    }

    .rop-quick-btn {
        padding: 8px 6px;
        font-size: 13px;
    }
}

/* Responsive - Small tablets and below */
@media (max-width: 992px) {
    .rop-total-amount {
        font-size: 32px;
    }

    .rop-amount-input {
        font-size: 24px;
    }

    .rop-quick-amounts {
        grid-template-columns: repeat(3, 1fr);
    }

    .split-payment-methods {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Mobile */
@media (max-width: 767px) {
    #runningOrderPaymentModal .modal-body > #ropContent > .row {
        flex-direction: column;
    }

    #runningOrderPaymentModal .col-lg-5,
    #runningOrderPaymentModal .col-lg-7 {
        border-right: none !important;
        border-bottom: 1px solid #e9ecef;
    }

    .rop-total-display {
        padding: 20px 16px;
    }

    .rop-total-amount {
        font-size: 28px;
    }

    .rop-amount-input {
        font-size: 20px;
        padding: 12px 12px 12px 40px;
    }

    .rop-amount-input-container .currency-prefix {
        font-size: 16px;
        left: 12px;
    }

    .rop-modal-footer {
        flex-direction: column;
        gap: 10px;
    }

    .rop-btn-cancel,
    .rop-btn-complete {
        width: 100%;
    }
}

/* Section Titles */
#runningOrderPaymentModal .pm-section-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #697a8d;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
}

#runningOrderPaymentModal .pm-section-title i {
    color: #696cff;
}

#runningOrderPaymentModal .pm-divider {
    height: 1px;
    background: #e9ecef;
    margin: 16px 0;
}

#runningOrderPaymentModal .pm-divider-dashed {
    height: 0;
    border-bottom: 2px dashed #e9ecef;
    margin: 16px 0;
}

/* Edit Button */
#runningOrderPaymentModal .btn-outline-primary {
    border-color: #696cff;
    color: #696cff;
}

#runningOrderPaymentModal .btn-outline-primary:hover {
    background: #696cff;
    color: #fff;
}
</style>

<script>
// Running Order Payment Modal Variables
var ropCurrentOrder = null;
var ropSplitIndex = 0;
var ropIsSplitMode = false;

// Open Running Order Payment Modal
function openRunningOrderPayment(orderId) {
    // Reset state
    ropCurrentOrder = null;
    ropSplitIndex = 0;
    ropIsSplitMode = false;

    // Show loading, hide content
    showRopLoading();

    // Store order ID
    document.getElementById('ropOrderId').value = orderId;

    // Show modal
    const modalEl = document.getElementById('runningOrderPaymentModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    modal.show();

    // Load order data
    loadRunningOrderData(orderId);
}

// Helper functions for showing/hiding states
function showRopLoading(message) {
    const loadingEl = document.getElementById('ropLoading');
    const contentEl = document.getElementById('ropContent');
    const successEl = document.getElementById('ropSuccess');
    const footerEl = document.getElementById('ropFooter');

    if (message) {
        document.querySelector('#ropLoading .payment-loading-text').textContent = message;
    } else {
        document.querySelector('#ropLoading .payment-loading-text').textContent = '{{ __("Loading order...") }}';
    }

    loadingEl.style.setProperty('display', 'flex', 'important');
    contentEl.style.setProperty('display', 'none', 'important');
    successEl.style.setProperty('display', 'none', 'important');
    footerEl.style.setProperty('display', 'none', 'important');
}

function showRopContent() {
    const loadingEl = document.getElementById('ropLoading');
    const contentEl = document.getElementById('ropContent');
    const successEl = document.getElementById('ropSuccess');
    const footerEl = document.getElementById('ropFooter');

    loadingEl.style.setProperty('display', 'none', 'important');
    contentEl.style.setProperty('display', 'block', 'important');
    successEl.style.setProperty('display', 'none', 'important');
    footerEl.style.setProperty('display', 'flex', 'important');
}

function showRopSuccess(message) {
    const loadingEl = document.getElementById('ropLoading');
    const contentEl = document.getElementById('ropContent');
    const successEl = document.getElementById('ropSuccess');
    const footerEl = document.getElementById('ropFooter');

    if (message) {
        document.getElementById('ropSuccessMessage').textContent = message;
    }

    loadingEl.style.setProperty('display', 'none', 'important');
    contentEl.style.setProperty('display', 'none', 'important');
    successEl.style.setProperty('display', 'flex', 'important');
    footerEl.style.setProperty('display', 'none', 'important');
}

// Reset modal state when hidden
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('runningOrderPaymentModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function() {
            // Reset to loading state for next open
            showRopLoading();
        });
    }
});

// Load order data via AJAX
function loadRunningOrderData(orderId) {
    const url = '{{ route("admin.pos.running-orders.details", ["id" => "__ID__"]) }}'.replace('__ID__', orderId);
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                ropCurrentOrder = data.order;
                populateRunningOrderModal(data.order);
            } else {
                toastr.error(data.message || '{{ __("Failed to load order") }}');
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('runningOrderPaymentModal'));
                if (modalInstance) modalInstance.hide();
            }
        })
        .catch(error => {
            console.error('Error loading order:', error);
            toastr.error('{{ __("Failed to load order details") }}: ' + error.message);
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('runningOrderPaymentModal'));
            if (modalInstance) modalInstance.hide();
        });
}

// Populate modal with order data
function populateRunningOrderModal(order) {
    // Hide loading, show content
    showRopContent();

    // Order info - invoice field name is 'invoice' not 'invoice_no'
    document.getElementById('ropOrderNumber').textContent = '#' + (order.invoice || order.invoice_no || order.id);
    document.getElementById('ropTableBadge').textContent = order.table?.name || '--';
    document.getElementById('ropWaiterName').textContent = order.waiter?.name || '{{ __("No waiter") }}';
    document.getElementById('ropGuestCount').textContent = order.guest_count || 1;
    // Format the order time
    let orderTime = '--';
    if (order.created_at) {
        const date = new Date(order.created_at);
        orderTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    document.getElementById('ropOrderTime').textContent = orderTime;

    // Order type badge
    const orderTypeLabels = {
        'dine_in': '{{ __("Dine-In") }}',
        'take_away': '{{ __("Take-Away") }}'
    };
    document.getElementById('ropOrderType').textContent = orderTypeLabels[order.order_type] || order.order_type;

    // Populate items list
    const itemsList = document.getElementById('ropItemsList');
    let itemsHtml = '';

    if (order.details && order.details.length > 0) {
        order.details.forEach(item => {
            const itemName = item.menu_item?.name || item.service?.name || item.ingredient?.name || 'Item';
            const quantity = item.quantity || item.qty || 1;
            const unitPrice = parseFloat(item.price || 0);
            const price = parseFloat(item.sub_total || item.amount || (unitPrice * quantity) || 0);
            let addons = [];
            try {
                addons = item.addons ? (typeof item.addons === 'string' ? JSON.parse(item.addons) : item.addons) : [];
            } catch(e) { addons = []; }

            let addonsHtml = '';
            if (addons.length > 0) {
                addonsHtml = '<div class="rop-item-addons">';
                addons.forEach(addon => {
                    addonsHtml += '+ ' + addon.name + ' ';
                });
                addonsHtml += '</div>';
            }

            itemsHtml += `
                <div class="rop-item">
                    <div class="rop-item-info">
                        <div class="rop-item-name">${itemName}</div>
                        <div class="rop-item-qty">x${quantity}</div>
                        ${addonsHtml}
                    </div>
                    <div class="rop-item-price">${currencyIcon} ${price.toFixed(2)}</div>
                </div>
            `;
        });
    } else {
        itemsHtml = '<div class="text-center text-muted py-3">{{ __("No items") }}</div>';
    }
    itemsList.innerHTML = itemsHtml;

    // Set amounts - map from Sale model field names
    const subtotal = parseFloat(order.total_price || order.sub_total || 0);
    const discount = parseFloat(order.order_discount || order.discount_amount || 0);
    const tax = parseFloat(order.total_tax || order.vat_amount || 0);
    const total = parseFloat(order.grand_total || order.total_amount || subtotal - discount + tax || 0);

    document.getElementById('ropSubtotal').textContent = currencyIcon + ' ' + subtotal.toFixed(2);
    document.getElementById('ropSubtotalValue').value = subtotal;

    // Show/hide discount row
    if (discount > 0) {
        document.getElementById('ropDiscountRow').style.display = 'flex';
        document.getElementById('ropDiscountDisplay').textContent = '- ' + currencyIcon + ' ' + discount.toFixed(2);
        document.getElementById('ropDiscountAmount').value = discount.toFixed(2);
    } else {
        document.getElementById('ropDiscountRow').style.display = 'none';
    }

    // Always show tax row and update values
    const taxRate = parseFloat(order.tax_rate) || parseFloat(document.getElementById('ropTaxRate')?.value) || 0;
    document.getElementById('ropTaxRateDisplay').textContent = taxRate;
    document.getElementById('ropTaxDisplay').textContent = currencyIcon + ' ' + tax.toFixed(2);
    document.getElementById('ropTaxRate').value = taxRate;
    document.getElementById('ropTaxAmount').value = tax.toFixed(2);

    // Grand total
    document.getElementById('ropGrandTotal').textContent = currencyIcon + ' ' + total.toFixed(2);
    document.getElementById('ropTotalAmount').textContent = currencyIcon + ' ' + total.toFixed(2);
    document.getElementById('ropTotalValue').value = total;

    // Set amount received to total
    document.getElementById('ropAmountReceived').value = total.toFixed(2);

    // Generate quick amounts
    generateRopQuickAmounts(total);

    // Reset payment method to cash
    resetRopPaymentMethod();

    // Calculate change
    calculateRopChange();
}

// Generate quick amount buttons
function generateRopQuickAmounts(total) {
    const container = document.getElementById('ropQuickAmounts');
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

    // Generate buttons
    let html = '';
    uniqueSuggestions.forEach(amount => {
        html += `<button type="button" class="rop-quick-btn" onclick="setRopAmount(${amount})">${amount.toLocaleString()}</button>`;
    });

    container.innerHTML = html;
}

// Set amount from quick button
function setRopAmount(amount) {
    document.getElementById('ropAmountReceived').value = amount.toFixed(2);
    calculateRopChange();
}

// Set exact amount
function setRopExactAmount() {
    const total = parseFloat(document.getElementById('ropTotalValue').value) || 0;
    document.getElementById('ropAmountReceived').value = total.toFixed(2);
    calculateRopChange();
}

// Calculate change
function calculateRopChange() {
    const total = parseFloat(document.getElementById('ropTotalValue').value) || 0;
    const received = parseFloat(document.getElementById('ropAmountReceived').value) || 0;
    const change = received - total;

    const changeDisplay = document.getElementById('ropChangeDisplay');
    const changeAmount = document.getElementById('ropChangeAmount');

    if (change >= 0) {
        changeDisplay.classList.remove('insufficient');
        changeAmount.textContent = currencyIcon + ' ' + change.toFixed(2);
        document.querySelector('.rop-change-display .change-label').textContent = '{{ __("Change Due") }}';
        document.getElementById('ropCompleteBtn').disabled = false;
    } else {
        changeDisplay.classList.add('insufficient');
        changeAmount.textContent = '- ' + currencyIcon + ' ' + Math.abs(change).toFixed(2);
        document.querySelector('.rop-change-display .change-label').textContent = '{{ __("Amount Due") }}';

        // Allow non-cash payments without full amount
        const paymentType = document.querySelector('input[name="rop_payment_type"]:checked')?.value;
        document.getElementById('ropCompleteBtn').disabled = (paymentType === 'cash' && change < 0);
    }
}

// Apply discount
function applyRopDiscount(type) {
    const subtotal = parseFloat(document.getElementById('ropSubtotalValue').value) || 0;
    let discountAmount = 0;

    if (type === 'amount') {
        discountAmount = parseFloat(document.getElementById('ropDiscountAmount').value) || 0;
        // Update percent field
        const percent = subtotal > 0 ? (discountAmount / subtotal) * 100 : 0;
        document.getElementById('ropDiscountPercent').value = percent.toFixed(1);
    } else {
        const percent = parseFloat(document.getElementById('ropDiscountPercent').value) || 0;
        discountAmount = (subtotal * percent) / 100;
        document.getElementById('ropDiscountAmount').value = discountAmount.toFixed(2);
    }

    // Recalculate totals with tax
    calculateRopTotals();
}

// Calculate totals including tax
function calculateRopTotals() {
    const subtotal = parseFloat(document.getElementById('ropSubtotalValue').value) || 0;
    const discountAmount = parseFloat(document.getElementById('ropDiscountAmount').value) || 0;
    const taxRate = parseFloat(document.getElementById('ropTaxRate').value) || 0;

    // Calculate tax on discounted amount
    const taxableAmount = subtotal - discountAmount;
    const taxAmount = (taxableAmount * taxRate) / 100;
    const newTotal = taxableAmount + taxAmount;

    // Update tax amount display
    document.getElementById('ropTaxAmount').value = taxAmount.toFixed(2);

    // Update discount display
    if (discountAmount > 0) {
        document.getElementById('ropDiscountRow').style.display = 'flex';
        document.getElementById('ropDiscountDisplay').textContent = '- ' + currencyIcon + ' ' + discountAmount.toFixed(2);
    } else {
        document.getElementById('ropDiscountRow').style.display = 'none';
    }

    // Always update tax display
    document.getElementById('ropTaxRateDisplay').textContent = taxRate;
    document.getElementById('ropTaxDisplay').textContent = currencyIcon + ' ' + taxAmount.toFixed(2);

    // Update totals
    document.getElementById('ropGrandTotal').textContent = currencyIcon + ' ' + newTotal.toFixed(2);
    document.getElementById('ropTotalAmount').textContent = currencyIcon + ' ' + newTotal.toFixed(2);
    document.getElementById('ropTotalValue').value = newTotal;

    // Update amount received if it was set to exact
    document.getElementById('ropAmountReceived').value = newTotal.toFixed(2);

    // Regenerate quick amounts
    generateRopQuickAmounts(newTotal);

    // Recalculate change
    calculateRopChange();
}

// Reset payment method to cash
function resetRopPaymentMethod() {
    ropIsSplitMode = false;
    document.getElementById('ropIsSplit').value = '0';
    document.getElementById('ropSplitContainer').classList.add('d-none');
    document.getElementById('ropSingleContainer').classList.remove('d-none');

    // Hide component's account selection
    const accountSelection = document.querySelector('#ropPaymentMethodContainer .account-selection');
    if (accountSelection) accountSelection.style.display = 'none';

    document.getElementById('ropSplitList').innerHTML = '';
    ropSplitIndex = 0;

    // Reset to cash
    document.querySelectorAll('#ropPaymentMethodContainer .payment-method-option').forEach(opt => {
        opt.classList.remove('active');
        const radio = opt.querySelector('input');
        if (radio) radio.checked = false;
    });

    const cashOption = document.querySelector('#ropPaymentMethodContainer input[value="cash"]');
    if (cashOption) {
        cashOption.checked = true;
        cashOption.closest('.payment-method-option').classList.add('active');
    }
}

// Handle payment method change for ROP modal
document.addEventListener('DOMContentLoaded', function() {
    // Listen for payment method changes in ROP modal
    document.querySelectorAll('#ropPaymentMethodContainer .payment-method-option input').forEach(radio => {
        radio.addEventListener('change', function() {
            const paymentType = this.value;
            const accountSelection = document.querySelector('#ropPaymentMethodContainer .account-selection');
            const accountSelect = document.querySelector('#ropPaymentMethodContainer .account-select');

            // Update active state
            document.querySelectorAll('#ropPaymentMethodContainer .payment-method-option').forEach(opt => {
                opt.classList.remove('active');
            });
            this.closest('.payment-method-option').classList.add('active');

            if (paymentType === 'split') {
                enableRopSplitPayment();
                if (accountSelection) accountSelection.style.display = 'none';
            } else if (paymentType === 'cash') {
                if (accountSelection) accountSelection.style.display = 'none';
                document.getElementById('ropSplitContainer').classList.add('d-none');
                document.getElementById('ropSingleContainer').classList.remove('d-none');
                ropIsSplitMode = false;
            } else {
                // Card, Bank, Mobile - show account selection (handled by component)
                document.getElementById('ropSplitContainer').classList.add('d-none');
                document.getElementById('ropSingleContainer').classList.remove('d-none');
                ropIsSplitMode = false;

                // Component handles account filtering, just make sure it's visible
                if (accountSelection) {
                    accountSelection.style.display = 'block';
                    // Filter accounts by type
                    if (accountSelect) {
                        accountSelect.querySelectorAll('option').forEach(opt => {
                            if (opt.value === '') {
                                opt.style.display = 'block';
                            } else {
                                opt.style.display = opt.dataset.type === paymentType ? 'block' : 'none';
                            }
                        });
                        accountSelect.value = '';
                    }
                }
            }

            calculateRopChange();
        });
    });

    // Handle Split Payment button click in ROP modal
    const ropSplitBtn = document.querySelector('#ropPaymentMethodContainer .add-split-payment-btn');
    if (ropSplitBtn) {
        ropSplitBtn.addEventListener('click', function() {
            enableRopSplitPayment();
        });
    }
});

// Enable split payment mode
function enableRopSplitPayment() {
    ropIsSplitMode = true;
    document.getElementById('ropIsSplit').value = '1';
    document.getElementById('ropSplitContainer').classList.remove('d-none');
    document.getElementById('ropSingleContainer').classList.add('d-none');

    // Hide component's account selection
    const accountSelection = document.querySelector('#ropPaymentMethodContainer .account-selection');
    if (accountSelection) accountSelection.style.display = 'none';

    // Clear existing and add initial splits
    document.getElementById('ropSplitList').innerHTML = '';
    ropSplitIndex = 0;

    const total = parseFloat(document.getElementById('ropTotalValue').value) || 0;
    addRopSplitPayment(total / 2);
    addRopSplitPayment(total / 2);
}

// Add split payment row
function addRopSplitPayment(amount) {
    amount = amount || 0;
    const container = document.getElementById('ropSplitList');
    const index = ropSplitIndex++;

    const html = `
        <div class="split-payment-row" data-index="${index}">
            <div class="split-payment-header">
                <span class="split-payment-number">{{ __("Payment") }} #${index + 1}</span>
                <button type="button" class="btn-remove-payment" onclick="removeRopSplit(${index})">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="split-payment-content">
                <div class="split-payment-methods">
                    <label class="split-method-option active">
                        <input type="radio" name="rop_split_type_${index}" value="cash" checked onchange="onRopSplitTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #71dd37">
                            <i class="bx bx-money"></i>
                            <span>{{ __("Cash") }}</span>
                        </div>
                    </label>
                    <label class="split-method-option">
                        <input type="radio" name="rop_split_type_${index}" value="card" onchange="onRopSplitTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #696cff">
                            <i class="bx bx-credit-card"></i>
                            <span>{{ __("Card") }}</span>
                        </div>
                    </label>
                    <label class="split-method-option">
                        <input type="radio" name="rop_split_type_${index}" value="bank" onchange="onRopSplitTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #03c3ec">
                            <i class="bx bx-building"></i>
                            <span>{{ __("Bank") }}</span>
                        </div>
                    </label>
                    <label class="split-method-option">
                        <input type="radio" name="rop_split_type_${index}" value="mobile_banking" onchange="onRopSplitTypeChange(${index}, this.value)">
                        <div class="split-method-box" style="--method-color: #ffab00">
                            <i class="bx bx-mobile"></i>
                            <span>{{ __("Mobile") }}</span>
                        </div>
                    </label>
                </div>
                <div class="split-account-select mt-2" style="display: none;">
                    <select class="form-select form-select-sm" name="rop_split_account_${index}">
                        <option value="">{{ __("Select Account...") }}</option>
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
                        <input type="number" name="rop_split_amount_${index}" class="form-control rop-split-amount" value="${amount.toFixed(2)}" step="0.01" min="0" onchange="calculateRopSplitTotal()" oninput="calculateRopSplitTotal()">
                    </div>
                </div>
            </div>
        </div>
    `;

    const temp = document.createElement('div');
    temp.innerHTML = html;
    container.appendChild(temp.firstElementChild);

    calculateRopSplitTotal();
}

// Remove split payment row
function removeRopSplit(index) {
    const row = document.querySelector(`#ropSplitList .split-payment-row[data-index="${index}"]`);
    if (row) {
        row.remove();
        calculateRopSplitTotal();
    }
}

// Handle split payment type change
function onRopSplitTypeChange(index, type) {
    const row = document.querySelector(`#ropSplitList .split-payment-row[data-index="${index}"]`);
    if (!row) return;

    const accountSelect = row.querySelector('.split-account-select');
    const selectElement = row.querySelector('select');

    // Update active state
    row.querySelectorAll('.split-method-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.querySelector('input').value === type) {
            opt.classList.add('active');
        }
    });

    if (type === 'cash') {
        accountSelect.style.display = 'none';
    } else {
        accountSelect.style.display = 'block';
        // Filter options
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

// Calculate split total
function calculateRopSplitTotal() {
    let total = 0;
    document.querySelectorAll('#ropSplitList .rop-split-amount').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const orderTotal = parseFloat(document.getElementById('ropTotalValue').value) || 0;
    const remaining = orderTotal - total;

    document.getElementById('ropSplitTotal').textContent = total.toFixed(2);

    const remainingEl = document.getElementById('ropSplitRemaining');
    if (remaining <= 0.01) {
        remainingEl.innerHTML = '<i class="bx bx-check-circle me-1"></i> {{ __("Fully Covered") }}';
        remainingEl.classList.remove('has-remaining');
        remainingEl.classList.add('fully-paid');
        document.getElementById('ropCompleteBtn').disabled = false;
    } else {
        remainingEl.innerHTML = '{{ __("Remaining") }}: ' + currencyIcon + ' ' + remaining.toFixed(2);
        remainingEl.classList.add('has-remaining');
        remainingEl.classList.remove('fully-paid');
        document.getElementById('ropCompleteBtn').disabled = true;
    }
}

// Edit running order
function editRunningOrder() {
    const orderId = document.getElementById('ropOrderId').value;
    if (orderId && ropCurrentOrder) {
        // Close payment modal
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('runningOrderPaymentModal'));
        if (modalInstance) modalInstance.hide();

        // Check if we're in POS context (loadOrderToCart exists)
        if (typeof loadOrderToCart === 'function') {
            // POS edit flow
            loadOrderToCart(orderId);
        } else {
            // Sales page - redirect to edit page
            window.location.href = '{{ url("admin/sales") }}/' + orderId + '/edit';
        }
    }
}

// Complete payment
function completeRunningOrderPayment() {
    const orderId = document.getElementById('ropOrderId').value;
    if (!orderId) {
        toastr.error('{{ __("No order selected") }}');
        return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('discount_amount', document.getElementById('ropDiscountAmount').value || 0);
    formData.append('tax_rate', document.getElementById('ropTaxRate').value || 0);
    formData.append('tax_amount', document.getElementById('ropTaxAmount').value || 0);
    formData.append('receive_amount', document.getElementById('ropAmountReceived').value || 0);
    formData.append('total_amount', document.getElementById('ropTotalValue').value || 0);

    if (ropIsSplitMode) {
        // Collect split payments as arrays (backend expects arrays)
        let index = 0;
        document.querySelectorAll('#ropSplitList .split-payment-row').forEach(row => {
            const rowIndex = row.dataset.index;
            const type = row.querySelector(`input[name="rop_split_type_${rowIndex}"]:checked`)?.value || 'cash';
            const amount = parseFloat(row.querySelector('.rop-split-amount')?.value) || 0;
            const accountId = row.querySelector(`select[name="rop_split_account_${rowIndex}"]`)?.value || '';

            if (amount > 0) {
                formData.append('payment_type[' + index + ']', type);
                formData.append('paying_amount[' + index + ']', amount);
                formData.append('account_id[' + index + ']', accountId);
                index++;
            }
        });
        formData.append('is_split', '1');
    } else {
        // Single payment - send as arrays with index 0 (backend expects arrays)
        const paymentType = document.querySelector('#ropPaymentMethodContainer input[name="rop_payment_type"]:checked')?.value || 'cash';
        const accountSelect = document.querySelector('#ropPaymentMethodContainer .account-select');
        const accountId = accountSelect?.value || '';
        const amountReceived = document.getElementById('ropAmountReceived')?.value || 0;

        formData.append('payment_type[0]', paymentType);
        formData.append('paying_amount[0]', amountReceived);
        formData.append('account_id[0]', accountId);
    }

    // Show loading
    showRopLoading('{{ __("Processing payment...") }}');

    // Submit payment
    const completeUrl = '{{ route("admin.pos.running-orders.complete", ["id" => "__ID__"]) }}'.replace('__ID__', orderId);
    fetch(completeUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Show success
            showRopSuccess(result.message || '{{ __("Payment completed successfully") }}');

            // Store for receipt
            window.lastCompletedOrderId = orderId;

            // Refresh running orders
            if (typeof loadRunningOrders === 'function') {
                loadRunningOrders();
            }
            if (typeof updateRunningOrdersCount === 'function') {
                updateRunningOrdersCount();
            }
        } else {
            // Show form again
            showRopContent();
            toastr.error(result.message || '{{ __("Payment failed") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showRopContent();
        toastr.error('{{ __("An error occurred") }}');
    });
}

// Print POS receipt for thermal printer
function printRunningOrderReceipt() {
    const orderId = window.lastCompletedOrderId || document.getElementById('ropOrderId').value;
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

// Close and refresh
function closeAndRefresh() {
    // Refresh the running orders list
    if (typeof loadRunningOrders === 'function') {
        loadRunningOrders();
    }
}
</script>
