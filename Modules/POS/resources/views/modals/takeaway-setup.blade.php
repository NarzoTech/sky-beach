{{--
    Take-Away Setup Modal

    Purpose: Quick info collection for take-away orders
    Shows when user clicks checkout with Take-Away order type selected

    Data required:
    - $total: Order total amount
    - $itemCount: Number of items in cart
--}}

@php
    $total = $total ?? 0;
    $itemCount = $itemCount ?? 0;
@endphp

<div class="modal fade payment-modal" id="takeawaySetupModal" tabindex="-1" aria-labelledby="takeawaySetupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="takeawaySetupModalLabel">
                    <i class="bx bx-shopping-bag me-2"></i>{{ __('Take-Away Order') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="takeawaySetupForm">
                    {{-- Customer Info Section --}}
                    <div class="pm-section-title">
                        <i class="bx bx-user me-2"></i>{{ __('Customer Info') }}
                        <span class="text-muted fw-normal ms-2">({{ __('Optional') }})</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Customer Name') }}</label>
                            <input type="text"
                                   name="customer_name"
                                   class="form-control pm-input"
                                   placeholder="{{ __('Name for the order...') }}"
                                   autocomplete="off">
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Phone') }} <small class="text-muted">({{ __('for pickup notification') }})</small></label>
                            <input type="tel"
                                   name="customer_phone"
                                   class="form-control pm-input"
                                   placeholder="{{ __('Phone number...') }}"
                                   autocomplete="off">
                        </div>
                    </div>

                    <div class="pm-divider"></div>

                    {{-- Estimated Pickup Time --}}
                    <div class="pm-section-title">
                        <i class="bx bx-time-five me-2"></i>{{ __('Estimated Pickup Time') }}
                    </div>

                    <div class="pickup-time-selector mb-4">
                        <div class="pickup-time-options">
                            <label class="pickup-time-option active">
                                <input type="radio" name="pickup_time" value="10" checked>
                                <div class="pickup-time-box">
                                    <span class="time-value">10</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="15">
                                <div class="pickup-time-box">
                                    <span class="time-value">15</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="20">
                                <div class="pickup-time-box">
                                    <span class="time-value">20</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="30">
                                <div class="pickup-time-box">
                                    <span class="time-value">30</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="45">
                                <div class="pickup-time-box">
                                    <span class="time-value">45</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                        </div>
                        <div class="estimated-pickup-display mt-3">
                            {{ __('Ready by') }}: <strong id="estimatedPickupTime">--:--</strong>
                        </div>
                    </div>

                    <div class="pm-divider"></div>

                    {{-- Special Instructions --}}
                    <div class="pm-section-title">
                        <i class="bx bx-notepad me-2"></i>{{ __('Special Instructions') }}
                        <span class="text-muted fw-normal ms-2">({{ __('Optional') }})</span>
                    </div>

                    <textarea name="special_instructions"
                              class="form-control pm-input mb-3"
                              rows="2"
                              placeholder="{{ __('Any special requests or packaging instructions...') }}"></textarea>

                    <div class="pm-divider-dashed"></div>

                    {{-- Order Summary --}}
                    <div class="payment-summary-card">
                        <div class="summary-row">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span id="takeawaySubtotal">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row text-danger" id="takeawayDiscountRow" style="display: none;">
                            <span>{{ __('Discount') }}</span>
                            <span id="takeawayDiscountAmount">- {{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row" id="takeawayTaxRow">
                            <span>{{ __('Tax') }} (<span id="takeawayTaxRate">0</span>%)</span>
                            <span id="takeawayTaxAmount">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>{{ __('Total') }}</span>
                            <span id="takeawayTotalAmount">{{ currency_icon() }} 0.00</span>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer d-flex gap-2">
                <button type="button" class="btn btn-start-order flex-fill" onclick="placeTakeawayOrder(false)">
                    <i class="bx bx-check-circle me-1"></i>
                    {{ __('Place Order') }}
                    <small class="d-block" style="opacity: 0.7;">{{ __('Pay Later') }}</small>
                </button>
                <button type="button" class="btn btn-complete-payment flex-fill" onclick="placeTakeawayOrder(true)">
                    <i class="bx bx-credit-card me-1"></i>
                    {{ __('Pay Now') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pickup-time-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.pickup-time-option {
    cursor: pointer;
    margin: 0;
    flex: 1;
    min-width: 60px;
}

.pickup-time-option input {
    display: none;
}

.pickup-time-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    border: 2px solid var(--pm-border, #e0e0e0);
    border-radius: var(--pm-radius, 12px);
    background: #fff;
    transition: all 0.2s ease;
}

.pickup-time-box:hover {
    border-color: var(--pm-primary, #696cff);
    background: var(--pm-primary-light, #e8e8ff);
}

.pickup-time-option.active .pickup-time-box,
.pickup-time-option input:checked + .pickup-time-box {
    border-color: var(--pm-primary, #696cff);
    background: var(--pm-primary, #696cff);
    color: white;
}

.pickup-time-box .time-value {
    font-size: 20px;
    font-weight: 700;
    line-height: 1;
}

.pickup-time-box .time-unit {
    font-size: 11px;
    text-transform: uppercase;
    opacity: 0.8;
}

.estimated-pickup-display {
    background: var(--pm-primary-light, #e8e8ff);
    padding: 12px 16px;
    border-radius: var(--pm-radius, 12px);
    border: 1px solid var(--pm-primary, #696cff);
    color: var(--pm-dark, #232333);
    font-size: 14px;
}

.estimated-pickup-display strong {
    color: var(--pm-primary, #696cff);
    font-size: 16px;
}

#takeawaySetupModal .pm-section-title {
    display: flex;
    align-items: center;
}

@media (max-width: 576px) {
    .pickup-time-option {
        min-width: calc(33.33% - 10px);
    }
}
</style>

<script>
// Initialize Takeaway Setup Modal
function initTakeawaySetupModal(total, itemCount) {
    // Get subtotal, discount, and tax from POS summary
    const subtotal = parseFloat(document.getElementById('subtotal')?.value) || parseFloat(total);
    const discount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
    const taxRate = parseFloat(document.getElementById('taxRate')?.value) || 0;
    const taxAmount = parseFloat(document.getElementById('taxAmount')?.value) || 0;

    // Update subtotal display
    document.getElementById('takeawaySubtotal').textContent = currencyIcon + ' ' + subtotal.toFixed(2);

    // Show/hide discount row
    const discountRow = document.getElementById('takeawayDiscountRow');
    if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('takeawayDiscountAmount').textContent = '- ' + currencyIcon + ' ' + discount.toFixed(2);
    } else {
        discountRow.style.display = 'none';
    }

    // Always show and update tax row
    document.getElementById('takeawayTaxRate').textContent = taxRate;
    document.getElementById('takeawayTaxAmount').textContent = currencyIcon + ' ' + taxAmount.toFixed(2);

    // Update total display
    const totalAmount = document.getElementById('takeawayTotalAmount');
    if (totalAmount) {
        totalAmount.textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);
    }

    // Reset form
    const form = document.getElementById('takeawaySetupForm');
    if (form) {
        form.reset();
    }

    // Reset pickup time selection
    document.querySelectorAll('#takeawaySetupModal .pickup-time-option').forEach(opt => {
        opt.classList.remove('active');
    });
    const defaultPickup = document.querySelector('#takeawaySetupModal .pickup-time-option input[value="10"]');
    if (defaultPickup) {
        defaultPickup.checked = true;
        defaultPickup.closest('.pickup-time-option').classList.add('active');
    }

    // Update estimated pickup time
    updateEstimatedPickupTime(10);

    // Hide preloader
    if (typeof $ !== 'undefined') {
        $('.preloader_area').addClass('d-none');
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('takeawaySetupModal'));
    modal.show();
}

// Update estimated pickup time display
function updateEstimatedPickupTime(minutes) {
    const now = new Date();
    now.setMinutes(now.getMinutes() + parseInt(minutes));

    const hours = now.getHours();
    const mins = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    const displayMins = mins.toString().padStart(2, '0');

    const timeString = displayHours + ':' + displayMins + ' ' + ampm;
    document.getElementById('estimatedPickupTime').textContent = timeString;
}

// Handle pickup time selection
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#takeawaySetupModal .pickup-time-option input').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Update active state
            document.querySelectorAll('#takeawaySetupModal .pickup-time-option').forEach(opt => opt.classList.remove('active'));
            this.closest('.pickup-time-option').classList.add('active');

            // Update estimated time
            updateEstimatedPickupTime(this.value);
        });
    });
});

// Get takeaway form data
function getTakeawayFormData() {
    return {
        customer_name: document.querySelector('#takeawaySetupForm input[name="customer_name"]')?.value || '',
        customer_phone: document.querySelector('#takeawaySetupForm input[name="customer_phone"]')?.value || '',
        pickup_time: document.querySelector('#takeawaySetupForm input[name="pickup_time"]:checked')?.value || '15',
        special_instructions: document.querySelector('#takeawaySetupForm textarea[name="special_instructions"]')?.value || ''
    };
}

// Place takeaway order (with or without payment)
function placeTakeawayOrder(withPayment) {
    if (withPayment) {
        // Close this modal and proceed to payment
        const takeawayModal = bootstrap.Modal.getInstance(document.getElementById('takeawaySetupModal'));
        if (takeawayModal) {
            takeawayModal.hide();
        }
        // Proceed to payment modal
        proceedToPayment('take_away');
    } else {
        // Place order without payment (deferred)
        submitTakeawayOrderWithoutPayment();
    }
}

// Submit takeaway order without payment
function submitTakeawayOrderWithoutPayment() {
    const formData = getTakeawayFormData();
    const total = parseFloat(document.getElementById('takeawayTotalAmount')?.textContent) || 0;

    // Build form data for submission
    const submitData = new FormData();
    submitData.append('_token', '{{ csrf_token() }}');
    submitData.append('order_type', 'take_away');
    submitData.append('defer_payment', '1');
    submitData.append('total_amount', total);
    submitData.append('sale_date', new Date().toLocaleDateString('en-GB').replace(/\//g, '-'));
    submitData.append('customer_name', formData.customer_name);
    submitData.append('customer_phone', formData.customer_phone);
    submitData.append('estimated_prep_minutes', formData.pickup_time);
    submitData.append('special_instructions', formData.special_instructions);

    // Add tax data
    submitData.append('tax_rate', document.getElementById('taxRate')?.value || 0);
    submitData.append('total_tax', document.getElementById('taxAmount')?.value || 0);
    submitData.append('discount_amount', document.getElementById('discount_total_amount')?.value || 0);
    submitData.append('sub_total', document.getElementById('subtotal')?.value || document.getElementById('total')?.value || 0);

    // Show loading state
    const placeOrderBtn = document.querySelector('#takeawaySetupModal .btn-secondary');
    const originalText = placeOrderBtn.innerHTML;
    placeOrderBtn.innerHTML = '{{ __("Processing...") }}';
    placeOrderBtn.disabled = true;

    fetch('{{ route("admin.pos.checkout") }}', {
        method: 'POST',
        body: submitData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success || result.order) {
            // Close modal
            const takeawayModal = bootstrap.Modal.getInstance(document.getElementById('takeawaySetupModal'));
            if (takeawayModal) {
                takeawayModal.hide();
            }

            // Reset cart
            if (typeof getCart === 'function') {
                getCart();
            }

            // Update running orders count
            if (typeof updateRunningOrdersCount === 'function') {
                updateRunningOrdersCount();
            }

            // Get order ID from response
            const orderId = result.order ? result.order.id : (result.order_id || null);
            const successMessage = result.message || '{{ __("Take-away order placed successfully") }}';

            // Show success dialog with print option
            Swal.fire({
                icon: 'success',
                title: "{{ __('Order Placed') }}",
                text: successMessage,
                showCancelButton: true,
                showDenyButton: orderId ? true : false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                denyButtonColor: '#17a2b8',
                confirmButtonText: '<i class="fas fa-list me-1"></i> {{ __("View Orders") }}',
                cancelButtonText: '<i class="fas fa-plus me-1"></i> {{ __("New Order") }}',
                denyButtonText: '<i class="fas fa-print me-1"></i> {{ __("Print Receipt") }}',
                reverseButtons: true
            }).then((dialogResult) => {
                if (dialogResult.isConfirmed) {
                    // Show running orders modal
                    if (typeof openRunningOrders === 'function') {
                        openRunningOrders();
                    }
                } else if (dialogResult.isDenied && orderId) {
                    // Print receipt only
                    if (typeof printDineInReceipt === 'function') {
                        printDineInReceipt(orderId);
                    }
                }
                // If cancelled, just stay on POS for new order
            });
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.error(result.message || '{{ __("Failed to place order") }}');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('{{ __("An error occurred while placing the order") }}');
        }
    })
    .finally(() => {
        placeOrderBtn.innerHTML = originalText;
        placeOrderBtn.disabled = false;
    });
}
</script>
