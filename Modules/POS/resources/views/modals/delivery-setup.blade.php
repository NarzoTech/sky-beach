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
            <div class="modal-header header-delivery">
                <h5 class="modal-title" id="deliverySetupModalLabel">
                    <i class="bx bx-cycling"></i>
                    {{ __('Delivery Order') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="deliverySetupForm">
                    {{-- Delivery Information Section --}}
                    <div class="pm-section-title">
                        <i class="bx bx-map-pin me-2"></i>{{ __('Delivery Information') }}
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Phone Number') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input type="tel"
                                       name="delivery_phone"
                                       class="form-control pm-input"
                                       placeholder="{{ __('+880 1XX-XXXXXXX') }}"
                                       required
                                       autocomplete="off">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Customer Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text"
                                       name="delivery_name"
                                       class="form-control pm-input"
                                       placeholder="{{ __('Recipient name') }}"
                                       required
                                       autocomplete="off">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                {{ __('Delivery Address') }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="delivery_address"
                                      class="form-control pm-textarea"
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
                                      class="form-control pm-textarea"
                                      rows="2"
                                      placeholder="{{ __('Landmarks, building details, special instructions...') }}"></textarea>
                        </div>
                    </div>

                    <div class="pm-divider-dashed"></div>

                    {{-- Order Summary with Delivery Fee --}}
                    <div class="delivery-order-summary">
                        <div class="summary-row">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span id="deliverySubtotal">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="summary-row delivery-fee-row">
                            <span>
                                <i class="bx bx-cycling me-1"></i>
                                {{ __('Delivery Fee') }}
                            </span>
                            <span id="deliveryFeeAmount">{{ currency_icon() }} {{ number_format($deliveryFee, 2) }}</span>
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

            <div class="modal-footer d-flex justify-content-between gap-2">
                <button type="button" class="btn btn-cod flex-fill" onclick="createCODOrder()">
                    <i class="bx bx-money me-2"></i>
                    {{ __('Cash on Delivery') }}
                </button>
                <button type="button" class="btn btn-complete-payment flex-fill" onclick="proceedToPayment('delivery')">
                    <i class="bx bx-credit-card me-2"></i>
                    {{ __('Pay Now') }}
                    <small class="d-block" id="deliveryPayNowAmount"></small>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.delivery-order-summary {
    background: var(--pm-gray-light);
    padding: 16px;
    border-radius: var(--pm-radius);
}

.delivery-order-summary .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.delivery-order-summary .summary-row.delivery-fee-row {
    color: var(--pm-info);
    font-weight: 500;
}

.delivery-order-summary .summary-row.summary-total {
    border-top: 2px dashed var(--pm-border);
    margin-top: 8px;
    padding-top: 12px;
    font-size: 18px;
    font-weight: 700;
    color: var(--pm-dark);
}

#deliverySetupModal .modal-footer {
    padding: 16px 24px;
}

#deliverySetupModal .btn-cod {
    background: var(--pm-warning);
    border: none;
    color: var(--pm-dark);
    padding: 14px 20px;
    font-weight: 700;
    border-radius: var(--pm-radius);
    transition: all 0.3s ease;
}

#deliverySetupModal .btn-cod:hover {
    background: #e69a00;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 171, 0, 0.4);
}

#deliverySetupModal .btn-complete-payment {
    padding: 14px 20px;
}

#deliverySetupModal .pm-textarea {
    min-height: 60px;
}

/* Required field styling */
#deliverySetupForm input:required:invalid,
#deliverySetupForm textarea:required:invalid {
    border-color: var(--pm-border);
}

#deliverySetupForm input:required:invalid:focus,
#deliverySetupForm textarea:required:invalid:focus {
    border-color: var(--pm-info);
    box-shadow: 0 0 0 3px rgba(3, 195, 236, 0.1);
}
</style>

<script>
// Initialize Delivery Setup Modal
function initDeliverySetupModal(subtotal, itemCount, deliveryFee) {
    deliveryFee = deliveryFee || 0;
    const total = parseFloat(subtotal) + parseFloat(deliveryFee);

    // Update displays
    document.getElementById('deliverySubtotal').textContent = currencyIcon + ' ' + parseFloat(subtotal).toFixed(2);
    document.getElementById('deliveryFeeAmount').textContent = currencyIcon + ' ' + parseFloat(deliveryFee).toFixed(2);
    document.getElementById('deliveryGrandTotal').textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);
    document.getElementById('deliveryPayNowAmount').textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);
    document.getElementById('deliveryFeeInput').value = deliveryFee;

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

// Create Cash on Delivery order
function createCODOrder() {
    if (!validateDeliveryForm()) {
        return;
    }

    const formData = getDeliveryFormData();
    formData.payment_type = 'cod';
    formData.payment_status = 'pending';

    const modal = bootstrap.Modal.getInstance(document.getElementById('deliverySetupModal'));

    // Create delivery order with COD
    const submitData = new FormData();
    submitData.append('_token', '{{ csrf_token() }}');
    submitData.append('order_type', 'delivery');
    submitData.append('payment_type', 'cod');
    submitData.append('payment_status', 'pending');
    submitData.append('defer_payment', '1');
    submitData.append('delivery_phone', formData.delivery_phone);
    submitData.append('delivery_name', formData.delivery_name);
    submitData.append('delivery_address', formData.delivery_address);
    submitData.append('delivery_notes', formData.delivery_notes);
    submitData.append('delivery_fee', formData.delivery_fee);

    // Get totals from main POS display
    const subTotal = parseFloat(document.getElementById('tprice')?.textContent.replace(/[^0-9.]/g, '')) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
    const deliveryFee = parseFloat(formData.delivery_fee) || 0;
    const totalAmount = parseFloat(document.getElementById('finalTotal')?.textContent.replace(/[^0-9.]/g, '')) || subTotal;

    submitData.append('sub_total', subTotal);
    submitData.append('discount_amount', discountAmount);
    submitData.append('total_amount', totalAmount + deliveryFee);
    submitData.append('sale_date', new Date().toLocaleDateString('en-GB').replace(/\//g, '-')); // DD-MM-YYYY

    fetch('{{ route("admin.pos.create-delivery-order") }}', {
        method: 'POST',
        body: submitData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            modal.hide();
            toastr.success(result.message || '{{ __("Delivery order created successfully") }}');

            // Reset cart
            if (typeof getCart === 'function') {
                getCart();
            }

            // Show receipt or print option
            if (result.order_id && typeof showOrderReceipt === 'function') {
                showOrderReceipt(result.order_id);
            }
        } else {
            toastr.error(result.message || '{{ __("Failed to create order") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('{{ __("An error occurred") }}');
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
