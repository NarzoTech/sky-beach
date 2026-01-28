{{--
    Delivery Setup Modal

    Purpose: Collect required delivery information
    Shows when user clicks checkout with Delivery order type selected

    Data required:
    - $total: Order total amount
    - $itemCount: Number of items in cart
    - $deliveryFee: Delivery fee (optional, defaults to 0)
--}}

@php
    $deliveryFee = $deliveryFee ?? 0;
@endphp

<div class="modal fade payment-modal" id="deliverySetupModal" tabindex="-1" aria-labelledby="deliverySetupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="deliverySetupModalLabel">
                    {{ __('Delivery Order') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="deliverySetupForm">
                    {{-- Delivery Information Section --}}
                    <div class="section-title">
                        {{ __('Delivery Information') }}
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Phone Number') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="tel"
                                   name="delivery_phone"
                                   class="form-control"
                                   placeholder="{{ __('+880 1XX-XXXXXXX') }}"
                                   required
                                   autocomplete="off">
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Customer Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="delivery_name"
                                   class="form-control"
                                   placeholder="{{ __('Recipient name') }}"
                                   required
                                   autocomplete="off">
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Delivery Address') }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="delivery_address"
                                      class="form-control"
                                      rows="2"
                                      placeholder="{{ __('Full delivery address...') }}"
                                      required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Delivery Notes') }}
                                <span class="text-muted fw-normal">({{ __('Optional') }})</span>
                            </label>
                            <textarea name="delivery_notes"
                                      class="form-control"
                                      rows="2"
                                      placeholder="{{ __('Landmarks, building details, special instructions...') }}"></textarea>
                        </div>
                    </div>

                    <hr>

                    {{-- Order Summary with Delivery Fee --}}
                    <div class="delivery-summary">
                        <div class="summary-row">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span id="deliverySubtotal">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row text-danger" id="deliveryDiscountRow" style="display: none;">
                            <span>{{ __('Discount') }}</span>
                            <span id="deliveryDiscountAmount">- {{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row text-info">
                            <span>{{ __('Delivery Fee') }}</span>
                            <span id="deliveryFeeAmount">{{ currency_icon() }} {{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="summary-row" id="deliveryTaxRow">
                            <span>{{ __('Tax') }} (<span id="deliveryTaxRate">0</span>%)</span>
                            <span id="deliveryTaxAmount">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>{{ __('Total') }}</span>
                            <span id="deliveryGrandTotal">{{ currency_icon() }} 0.00</span>
                        </div>
                    </div>

                    {{-- Hidden field for delivery fee --}}
                    <input type="hidden" name="delivery_fee" id="deliveryFeeInput" value="{{ $deliveryFee }}">
                </form>
            </div>

            <div class="modal-footer d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" onclick="createDeliveryOrder(false)">
                    {{ __('Place Order (COD)') }}
                </button>
                <button type="button" class="btn btn-success flex-fill" onclick="createDeliveryOrder(true)">
                    {{ __('Pay Now') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.delivery-summary {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 10px;
    border: 1px solid #dee2e6;
}

.delivery-summary .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
}

.delivery-summary .summary-row.summary-total {
    border-top: 2px dashed #dee2e6;
    margin-top: 8px;
    padding-top: 12px;
    font-size: 18px;
    font-weight: 700;
    color: #333;
}
</style>

<script>
// Initialize Delivery Setup Modal
function initDeliverySetupModal(subtotal, itemCount, deliveryFee) {
    deliveryFee = deliveryFee || 0;

    // Get discount and tax from POS summary
    const discount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
    const taxRate = parseFloat(document.getElementById('taxRate')?.value) || 0;
    const taxAmount = parseFloat(document.getElementById('taxAmount')?.value) || 0;

    // Calculate total: subtotal - discount + delivery + tax
    const total = parseFloat(subtotal) + parseFloat(deliveryFee);

    // Update displays
    document.getElementById('deliverySubtotal').textContent = currencyIcon + ' ' + parseFloat(subtotal).toFixed(2);
    document.getElementById('deliveryFeeAmount').textContent = currencyIcon + ' ' + parseFloat(deliveryFee).toFixed(2);
    document.getElementById('deliveryGrandTotal').textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);
    document.getElementById('deliveryFeeInput').value = deliveryFee;

    // Show/hide discount row
    const discountRow = document.getElementById('deliveryDiscountRow');
    if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('deliveryDiscountAmount').textContent = '- ' + currencyIcon + ' ' + discount.toFixed(2);
    } else {
        discountRow.style.display = 'none';
    }

    // Always show and update tax row
    document.getElementById('deliveryTaxRate').textContent = taxRate;
    document.getElementById('deliveryTaxAmount').textContent = currencyIcon + ' ' + taxAmount.toFixed(2);

    // Reset form
    const form = document.getElementById('deliverySetupForm');
    if (form) {
        // Keep delivery fee hidden field
        const deliveryFeeVal = deliveryFee;
        form.reset();
        document.getElementById('deliveryFeeInput').value = deliveryFeeVal;
    }

    // Pre-fill from customer if selected
    const selectedCustomer = document.getElementById('customer_id');
    if (selectedCustomer && selectedCustomer.value) {
        const selectedOption = selectedCustomer.options[selectedCustomer.selectedIndex];
        const customerPhone = selectedOption.dataset.phone;
        const customerName = selectedOption.text.split(' - ')[0];

        if (customerName && customerName !== 'Walk-In Customer') {
            document.querySelector('#deliverySetupForm input[name="delivery_name"]').value = customerName;
        }
        if (customerPhone) {
            document.querySelector('#deliverySetupForm input[name="delivery_phone"]').value = customerPhone;
        }
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deliverySetupModal'));
    modal.show();
}

// Validate delivery form
function validateDeliveryForm() {
    const form = document.getElementById('deliverySetupForm');
    const phone = form.querySelector('input[name="delivery_phone"]').value.trim();
    const name = form.querySelector('input[name="delivery_name"]').value.trim();
    const address = form.querySelector('textarea[name="delivery_address"]').value.trim();

    if (!phone) {
        toastr.warning('{{ __("Please enter a phone number") }}');
        form.querySelector('input[name="delivery_phone"]').focus();
        return false;
    }

    if (!name) {
        toastr.warning('{{ __("Please enter customer name") }}');
        form.querySelector('input[name="delivery_name"]').focus();
        return false;
    }

    if (!address) {
        toastr.warning('{{ __("Please enter delivery address") }}');
        form.querySelector('textarea[name="delivery_address"]').focus();
        return false;
    }

    return true;
}

// Create delivery order (with or without payment)
function createDeliveryOrder(withPayment) {
    if (!validateDeliveryForm()) {
        return;
    }

    if (withPayment) {
        // Close this modal and proceed to payment
        const deliveryModal = bootstrap.Modal.getInstance(document.getElementById('deliverySetupModal'));
        if (deliveryModal) {
            deliveryModal.hide();
        }
        // Proceed to payment modal
        proceedToPayment('delivery');
    } else {
        // Create COD order
        submitDeliveryOrderCOD();
    }
}

// Submit delivery order as COD (Cash on Delivery)
function submitDeliveryOrderCOD() {
    const formData = getDeliveryFormData();

    // Build form data for submission
    const submitData = new FormData();
    submitData.append('_token', '{{ csrf_token() }}');
    submitData.append('order_type', 'delivery');
    submitData.append('defer_payment', '1');
    submitData.append('delivery_phone', formData.delivery_phone);
    submitData.append('delivery_name', formData.delivery_name);
    submitData.append('delivery_address', formData.delivery_address);
    submitData.append('delivery_notes', formData.delivery_notes);
    submitData.append('shipping_cost', formData.delivery_fee);
    submitData.append('sale_date', new Date().toLocaleDateString('en-GB').replace(/\//g, '-'));

    // Get totals from main POS display
    const subTotal = parseFloat(document.getElementById('subtotal')?.value) || parseFloat(document.getElementById('total')?.value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
    const deliveryFee = parseFloat(formData.delivery_fee) || 0;
    const taxRate = parseFloat(document.getElementById('taxRate')?.value) || 0;
    const taxAmount = parseFloat(document.getElementById('taxAmount')?.value) || 0;
    const grandTotal = parseFloat(document.getElementById('finalTotal')?.textContent.replace(/[^0-9.]/g, '')) || subTotal;

    submitData.append('sub_total', subTotal);
    submitData.append('discount_amount', discountAmount);
    submitData.append('tax_rate', taxRate);
    submitData.append('total_tax', taxAmount);
    submitData.append('total_amount', grandTotal + deliveryFee);

    // Show loading state
    const placeOrderBtn = document.querySelector('#deliverySetupModal .btn-secondary');
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
            const deliveryModal = bootstrap.Modal.getInstance(document.getElementById('deliverySetupModal'));
            if (deliveryModal) {
                deliveryModal.hide();
            }

            // Show success message
            if (typeof toastr !== 'undefined') {
                toastr.success(result.message || '{{ __("Delivery order placed successfully") }}');
            }

            // Reset cart
            if (typeof getCart === 'function') {
                getCart();
            }

            // Update running orders count
            if (typeof updateRunningOrdersCount === 'function') {
                updateRunningOrdersCount();
            }
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

// Get delivery form data
function getDeliveryFormData() {
    return {
        delivery_phone: document.querySelector('#deliverySetupForm input[name="delivery_phone"]')?.value || '',
        delivery_name: document.querySelector('#deliverySetupForm input[name="delivery_name"]')?.value || '',
        delivery_address: document.querySelector('#deliverySetupForm textarea[name="delivery_address"]')?.value || '',
        delivery_notes: document.querySelector('#deliverySetupForm textarea[name="delivery_notes"]')?.value || '',
        delivery_fee: document.getElementById('deliveryFeeInput')?.value || 0
    };
}
</script>
