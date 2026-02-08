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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="deliverySetupModalLabel">
                    <i class="bx bx-package me-2"></i>{{ __('Delivery Order') }}
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
                            <div class="input-group">
                                <input type="tel"
                                       name="delivery_phone"
                                       id="deliveryModalPhone"
                                       class="form-control"
                                       placeholder="{{ __('+880 1XX-XXXXXXX') }}"
                                       required
                                       autocomplete="off">
                                <span class="input-group-text d-none" id="phoneLookupStatus">
                                    <span class="spinner-border spinner-border-sm" id="phoneLookupSpinner"></span>
                                    <i class="bx bx-check text-success d-none" id="phoneLookupFound"></i>
                                    <i class="bx bx-x text-muted d-none" id="phoneLookupNotFound"></i>
                                </span>
                            </div>
                            <small class="text-muted" id="phoneLookupHint">{{ __('Enter phone to auto-fill customer details') }}</small>
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

#phoneLookupStatus {
    background: #f8f9fa;
    border-left: none;
}

#phoneLookupHint {
    font-size: 11px;
    display: block;
    margin-top: 4px;
}

#phoneLookupHint.found {
    color: #198754 !important;
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

    // Pre-fill from delivery info row (if user already entered values there)
    const deliveryPhoneInput = document.getElementById('delivery_phone');
    const deliveryAddressInput = document.getElementById('delivery_address');

    if (deliveryPhoneInput && deliveryPhoneInput.value.trim()) {
        document.querySelector('#deliverySetupForm input[name="delivery_phone"]').value = deliveryPhoneInput.value.trim();
    }
    if (deliveryAddressInput && deliveryAddressInput.value.trim()) {
        document.querySelector('#deliverySetupForm textarea[name="delivery_address"]').value = deliveryAddressInput.value.trim();
    }

    // Pre-fill from customer if selected (and not already filled from delivery row)
    const selectedCustomer = document.getElementById('customer_id');
    if (selectedCustomer && selectedCustomer.value) {
        const selectedOption = selectedCustomer.options[selectedCustomer.selectedIndex];
        const customerPhone = selectedOption.dataset.phone;
        const customerName = selectedOption.text.split(' - ')[0];
        const customerAddress = selectedOption.dataset.address;

        if (customerName && customerName !== 'Walk-In Customer') {
            const nameInput = document.querySelector('#deliverySetupForm input[name="delivery_name"]');
            if (!nameInput.value.trim()) {
                nameInput.value = customerName;
            }
        }
        if (customerPhone) {
            const phoneInput = document.querySelector('#deliverySetupForm input[name="delivery_phone"]');
            if (!phoneInput.value.trim()) {
                phoneInput.value = customerPhone;
            }
        }
        if (customerAddress) {
            const addressInput = document.querySelector('#deliverySetupForm textarea[name="delivery_address"]');
            if (!addressInput.value.trim()) {
                addressInput.value = customerAddress;
            }
        }
    }

    // Hide preloader
    if (typeof $ !== 'undefined') {
        $('.preloader_area').addClass('d-none');
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
            const successMessage = result.message || '{{ __("Delivery order placed successfully") }}';

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

// Phone lookup functionality
let phoneLookupTimeout = null;
let lastLookedUpPhone = '';

function lookupCustomerByPhone(phone) {
    // Don't lookup if same phone or too short
    if (phone === lastLookedUpPhone || phone.length < 5) {
        return;
    }

    lastLookedUpPhone = phone;
    const statusEl = document.getElementById('phoneLookupStatus');
    const spinnerEl = document.getElementById('phoneLookupSpinner');
    const foundEl = document.getElementById('phoneLookupFound');
    const notFoundEl = document.getElementById('phoneLookupNotFound');
    const hintEl = document.getElementById('phoneLookupHint');

    // Show loading
    statusEl.classList.remove('d-none');
    spinnerEl.classList.remove('d-none');
    foundEl.classList.add('d-none');
    notFoundEl.classList.add('d-none');
    hintEl.textContent = '{{ __("Searching...") }}';
    hintEl.classList.remove('found');

    fetch('{{ route("admin.pos.customer-by-phone") }}?phone=' + encodeURIComponent(phone), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        spinnerEl.classList.add('d-none');

        if (result.success && result.customer) {
            foundEl.classList.remove('d-none');
            hintEl.textContent = '{{ __("Customer found:") }} ' + result.customer.name;
            hintEl.classList.add('found');

            // Auto-fill name and address
            const nameInput = document.querySelector('#deliverySetupForm input[name="delivery_name"]');
            const addressInput = document.querySelector('#deliverySetupForm textarea[name="delivery_address"]');

            if (nameInput && !nameInput.value.trim()) {
                nameInput.value = result.customer.name;
            }
            if (addressInput && !addressInput.value.trim() && result.customer.address) {
                addressInput.value = result.customer.address;
            }

            // Show success toast
            if (typeof toastr !== 'undefined') {
                toastr.info('{{ __("Customer details auto-filled") }}');
            }
        } else {
            notFoundEl.classList.remove('d-none');
            hintEl.textContent = '{{ __("No customer found with this number") }}';
            hintEl.classList.remove('found');
        }
    })
    .catch(error => {
        console.error('Phone lookup error:', error);
        spinnerEl.classList.add('d-none');
        notFoundEl.classList.remove('d-none');
        hintEl.textContent = '{{ __("Enter phone to auto-fill customer details") }}';
        hintEl.classList.remove('found');
    });
}

// Initialize phone lookup on modal shown
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('deliveryModalPhone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();

            // Clear previous timeout
            if (phoneLookupTimeout) {
                clearTimeout(phoneLookupTimeout);
            }

            // Reset status if phone is too short
            if (phone.length < 5) {
                document.getElementById('phoneLookupStatus').classList.add('d-none');
                document.getElementById('phoneLookupHint').textContent = '{{ __("Enter phone to auto-fill customer details") }}';
                document.getElementById('phoneLookupHint').classList.remove('found');
                lastLookedUpPhone = '';
                return;
            }

            // Debounce: wait 500ms after user stops typing
            phoneLookupTimeout = setTimeout(function() {
                lookupCustomerByPhone(phone);
            }, 500);
        });

        // Also trigger on blur for immediate lookup
        phoneInput.addEventListener('blur', function() {
            const phone = this.value.trim();
            if (phone.length >= 5 && phone !== lastLookedUpPhone) {
                if (phoneLookupTimeout) {
                    clearTimeout(phoneLookupTimeout);
                }
                lookupCustomerByPhone(phone);
            }
        });
    }

    // Reset lookup state when modal is hidden
    const deliveryModal = document.getElementById('deliverySetupModal');
    if (deliveryModal) {
        deliveryModal.addEventListener('hidden.bs.modal', function() {
            lastLookedUpPhone = '';
            document.getElementById('phoneLookupStatus').classList.add('d-none');
            document.getElementById('phoneLookupHint').textContent = '{{ __("Enter phone to auto-fill customer details") }}';
            document.getElementById('phoneLookupHint').classList.remove('found');
        });
    }
});
</script>
