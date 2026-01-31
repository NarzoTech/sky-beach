{{--
    Dine-In Setup Modal

    Purpose: Collect table, guest count, and waiter information before payment/start order
    Shows when user clicks checkout with Dine-In order type selected

    Data required:
    - $availableTables: Collection of RestaurantTable models
    - $waiters: Collection of Employee models
    - $total: Order total amount
    - $itemCount: Number of items in cart
--}}

@php
    $availableTables = $availableTables ?? collect();
    $waiters = $waiters ?? collect();
    $total = $total ?? 0;
    $itemCount = $itemCount ?? 0;
@endphp

<div class="modal fade payment-modal" id="dineInSetupModal" tabindex="-1" aria-labelledby="dineInSetupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header header-dine-in">
                <h5 class="modal-title" id="dineInSetupModalLabel">
                    <i class="bx bx-restaurant"></i>
                    {{ __('Dine-In Order') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="dineInSetupForm">
                    {{-- Table Selection Section --}}
                    <div class="pm-section-title">
                        <i class="bx bx-chair me-2"></i>{{ __('Select Table') }}
                    </div>

                    <div id="dineInTableSelector">
                        @include('pos::components.table-selector', [
                            'tables' => $availableTables ?? collect(),
                            'selected' => null,
                            'name' => 'table_id'
                        ])
                    </div>

                    <div class="pm-divider"></div>

                    {{-- Guest Count Section --}}
                    <div class="pm-section-title">
                        <i class="bx bx-group me-2"></i>{{ __('Number of Guests') }}
                    </div>

                    @include('pos::components.guest-count-selector', [
                        'selected' => 2,
                        'maxGuests' => 8,
                        'name' => 'guest_count'
                    ])

                    <div class="pm-divider"></div>

                    {{-- Waiter Assignment Section --}}
                    <div class="pm-section-title">
                        <i class="bx bx-user me-2"></i>{{ __('Assign Waiter') }}
                        <span class="text-muted fw-normal">({{ __('Optional') }})</span>
                    </div>

                    @include('pos::components.waiter-selector', [
                        'waiters' => $waiters ?? collect(),
                        'selected' => null,
                        'name' => 'waiter_id',
                        'required' => false
                    ])

                    <div class="pm-divider-dashed"></div>

                    {{-- Order Summary --}}
                    <div class="dine-in-order-summary">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ __('Items') }}</span>
                            <span class="fw-semibold" id="dineInItemCount">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span id="dineInSubtotal">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 text-danger" id="dineInDiscountRow" style="display: none;">
                            <span>{{ __('Discount') }}</span>
                            <span id="dineInDiscountAmount">- {{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" id="dineInTaxRow">
                            <span>{{ __('Tax') }} (<span id="dineInTaxRate">0</span>%)</span>
                            <span id="dineInTaxAmount">{{ currency_icon() }} 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 2px dashed #dee2e6;">
                            <span class="fs-5 fw-semibold">{{ __('Total') }}</span>
                            <span class="fs-4 fw-bold text-primary" id="dineInOrderTotal">{{ currency_icon() }} 0.00</span>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-start-order flex-fill me-2" onclick="startDineInOrder()">
                    <i class="bx bx-play-circle me-2"></i>
                    {{ __('Start Order') }}
                    <small class="d-block text-white-50">{{ __('Pay Later') }}</small>
                </button>
                <button type="button" class="btn btn-complete-payment flex-fill" onclick="proceedToPayment('dine_in')">
                    <i class="bx bx-credit-card me-2"></i>
                    {{ __('Pay Now') }}
                    <small class="d-block" id="dineInPayNowAmount"></small>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.dine-in-order-summary {
    background: var(--pm-gray-light);
    padding: 16px;
    border-radius: var(--pm-radius);
}

#dineInSetupModal .modal-footer {
    padding: 16px 24px;
}

#dineInSetupModal .btn-start-order {
    background: linear-gradient(135deg, #697a8d, #5a6a7d);
    border: none;
    color: white;
    padding: 14px 20px;
    border-radius: var(--pm-radius);
    transition: all 0.3s ease;
}

#dineInSetupModal .btn-start-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 122, 141, 0.4);
}

#dineInSetupModal .btn-complete-payment {
    padding: 14px 20px;
}

#dineInSetupModal .pm-section-title {
    display: flex;
    align-items: center;
}
</style>

<script>
// Initialize Dine-In Setup Modal
function initDineInSetupModal(total, itemCount) {
    // Get subtotal, discount, and tax from POS summary
    const subtotal = parseFloat(document.getElementById('subtotal')?.value) || parseFloat(total);
    const discount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
    const taxRate = parseFloat(document.getElementById('taxRate')?.value) || 0;
    const taxAmount = parseFloat(document.getElementById('taxAmount')?.value) || 0;

    document.getElementById('dineInItemCount').textContent = itemCount;
    document.getElementById('dineInSubtotal').textContent = currencyIcon + ' ' + subtotal.toFixed(2);

    // Show/hide discount row
    const discountRow = document.getElementById('dineInDiscountRow');
    if (discount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('dineInDiscountAmount').textContent = '- ' + currencyIcon + ' ' + discount.toFixed(2);
    } else {
        discountRow.style.display = 'none';
    }

    // Always show and update tax row
    document.getElementById('dineInTaxRate').textContent = taxRate;
    document.getElementById('dineInTaxAmount').textContent = currencyIcon + ' ' + taxAmount.toFixed(2);

    document.getElementById('dineInOrderTotal').textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);
    document.getElementById('dineInPayNowAmount').textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);

    // Reset form
    const form = document.getElementById('dineInSetupForm');
    if (form) {
        form.reset();
    }

    // Reset table selection
    document.querySelectorAll('#dineInTableSelector .table-option').forEach(opt => {
        opt.classList.remove('active');
        const radio = opt.querySelector('input[type="radio"]');
        if (radio) radio.checked = false;
    });

    // Reset guest count to 2
    const guestInput = document.querySelector('#dineInSetupForm input[name="guest_count"]');
    if (guestInput) {
        guestInput.value = 2;
        document.querySelectorAll('.guest-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.count === '2') {
                btn.classList.add('active');
            }
        });
    }

    // Reset waiter selection
    document.querySelectorAll('#dineInSetupForm .waiter-option').forEach(opt => {
        opt.classList.remove('active');
    });
    const noWaiterOption = document.querySelector('#dineInSetupForm .waiter-option input[value=""]');
    if (noWaiterOption) {
        noWaiterOption.checked = true;
        noWaiterOption.closest('.waiter-option').classList.add('active');
    }

    // Hide preloader
    if (typeof $ !== 'undefined') {
        $('.preloader_area').addClass('d-none');
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('dineInSetupModal'));
    modal.show();
}

// Start order without payment (pay later)
function startDineInOrder() {
    const tableId = document.getElementById('tableSelector_table_id')?.value;
    const guestCount = document.querySelector('#dineInSetupForm input[name="guest_count"]')?.value || 2;
    const waiterId = document.querySelector('#dineInSetupForm input[name="waiter_id"]:checked')?.value || '';

    if (!tableId) {
        toastr.warning('{{ __("Please select a table") }}');
        return;
    }

    // Update hidden fields in main form
    document.getElementById('table_id').value = tableId;

    // Create order with deferred payment
    createDineInOrder({
        table_id: tableId,
        guest_count: guestCount,
        waiter_id: waiterId,
        payment_status: 'pending'
    });
}

// Create dine-in order
function createDineInOrder(data) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('table_id', data.table_id);
    formData.append('guest_count', data.guest_count);
    formData.append('waiter_id', data.waiter_id || '');
    formData.append('order_type', 'dine_in');
    formData.append('payment_status', data.payment_status || 'pending');
    formData.append('defer_payment', '1');

    // Get totals from main POS display
    const subTotal = parseFloat(document.getElementById('tprice')?.textContent.replace(/[^0-9.]/g, '')) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_total_amount')?.value) || 0;
    const totalAmount = parseFloat(document.getElementById('finalTotal')?.textContent.replace(/[^0-9.]/g, '')) || subTotal;

    formData.append('sub_total', subTotal);
    formData.append('discount_amount', discountAmount);
    formData.append('total_amount', totalAmount);
    formData.append('sale_date', new Date().toLocaleDateString('en-GB').replace(/\//g, '-')); // DD-MM-YYYY

    // Show loading
    const modal = bootstrap.Modal.getInstance(document.getElementById('dineInSetupModal'));

    fetch('{{ route("admin.pos.start-dine-in-order") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            modal.hide();
            toastr.success(result.message || '{{ __("Order started successfully") }}');

            // Update table display
            updateSelectedTable(data.table_id, result.table_name);

            // Refresh cart
            if (typeof getCart === 'function') {
                getCart();
            }

            // Refresh running orders count
            if (typeof updateRunningOrdersCount === 'function') {
                updateRunningOrdersCount();
            }
        } else {
            toastr.error(result.message || '{{ __("Failed to start order") }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('{{ __("An error occurred") }}');
    });
}

// Listen for table selection changes to update guest count max
document.addEventListener('tableSelected', function(e) {
    const tableCapacity = e.detail.capacity;
    const availableSeats = e.detail.availableSeats;

    // Update guest count max
    if (typeof setGuestCountMax === 'function') {
        setGuestCountMax(availableSeats || tableCapacity);
    }
});

// Update the selected table display on the main POS page
function updateSelectedTable(tableId, tableName) {
    // Update hidden input
    const tableInput = document.getElementById('table_id');
    if (tableInput) {
        tableInput.value = tableId;
    }

    // Update button display
    const selectedTableText = document.getElementById('selectedTableText');
    if (selectedTableText) {
        selectedTableText.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + tableName;
    }

    const selectedTableBadge = document.getElementById('selectedTableBadge');
    if (selectedTableBadge) {
        selectedTableBadge.style.display = 'inline-block';
    }

    const openTableModal = document.getElementById('openTableModal');
    if (openTableModal) {
        openTableModal.classList.add('table-selected');
    }

    // Update payment button state
    if (typeof updatePaymentButtonState === 'function') {
        updatePaymentButtonState();
    }
}
</script>
