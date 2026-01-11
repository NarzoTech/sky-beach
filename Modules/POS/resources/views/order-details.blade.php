<div class="order-details-content" data-order-id="{{ $order->id }}" data-created-at="{{ $order->created_at->timestamp }}">
    <!-- Order Header -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <div class="table-icon-wrapper me-3">
                            <i class="fas fa-chair fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $order->table->name ?? 'No Table' }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i>{{ $order->table->capacity ?? '-' }} {{ __('seats') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">{{ __('Invoice') }}</small>
                            <h6 class="mb-0 text-primary">#{{ $order->invoice }}</h6>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ __('Started') }}</small>
                            <h6 class="mb-0">{{ $order->created_at->format('h:i A') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Clock & Duration -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x text-warning me-3 fa-spin-pulse"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('Order Duration') }}</small>
                                <h4 class="mb-0 order-duration-display" id="orderDuration">
                                    <span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>
                                </h4>
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">{{ __('Current Time') }}</small>
                            <h5 class="mb-0 text-primary" id="currentTime">--:--:--</h5>
                        </div>
                        <div>
                            <span class="badge bg-{{ $order->status == 'processing' ? 'warning' : 'info' }} fs-6 px-3 py-2">
                                <i class="fas fa-spinner fa-spin me-1"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    @if($order->customer_id && $order->customer)
    <div class="alert alert-info py-2 mb-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-user me-2"></i>
            <strong>{{ __('Customer') }}:</strong> {{ $order->customer->name ?? 'Guest' }}
            @if($order->customer->phone)
            - <i class="fas fa-phone ms-2 me-1"></i>{{ $order->customer->phone }}
            @endif
        </span>
        @if(isset($customerLoyalty) && $customerLoyalty)
        <span class="badge bg-success">
            <i class="fas fa-star me-1"></i>{{ $customerLoyalty['total_points'] ?? 0 }} pts
        </span>
        @endif
    </div>
    @endif

    <!-- Order Items - Editable -->
    <div class="card mb-3">
        <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>{{ __('Order Items') }}</span>
            <span class="badge bg-light text-dark">{{ $order->details->count() }} {{ __('items') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="orderItemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>{{ __('Item') }}</th>
                            <th class="text-center" style="width: 150px;">{{ __('Quantity') }}</th>
                            <th class="text-end" style="width: 100px;">{{ __('Price') }}</th>
                            <th class="text-end" style="width: 100px;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $detail)
                        <tr class="order-item-row" data-detail-id="{{ $detail->id }}" data-price="{{ $detail->price }}">
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"
                                        onclick="removeOrderItem({{ $order->id }}, {{ $detail->id }})"
                                        title="{{ __('Remove Item') }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                            <td>
                                <strong>{{ $detail->menuItem->name ?? ($detail->service->name ?? 'Unknown Item') }}</strong>
                                @if($detail->variant_id && $detail->attributes)
                                <br><small class="text-muted"><i class="fas fa-tag me-1"></i>{{ $detail->attributes }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="input-group input-group-sm qty-control-group">
                                    <button class="btn btn-outline-secondary qty-btn" type="button"
                                            onclick="updateItemQty({{ $order->id }}, {{ $detail->id }}, -1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control text-center item-qty"
                                           value="{{ $detail->quantity }}"
                                           min="1"
                                           data-detail-id="{{ $detail->id }}"
                                           onchange="updateItemQtyDirect({{ $order->id }}, {{ $detail->id }}, this.value)">
                                    <button class="btn btn-outline-secondary qty-btn" type="button"
                                            onclick="updateItemQty({{ $order->id }}, {{ $detail->id }}, 1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-end">{{ currency($detail->price) }}</td>
                            <td class="text-end item-total">{{ currency($detail->sub_total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>{{ __('Subtotal') }}:</strong></td>
                            <td class="text-end" id="orderSubtotal">{{ currency($order->total_price) }}</td>
                        </tr>
                        @if($order->order_discount > 0)
                        <tr class="text-danger">
                            <td colspan="4" class="text-end">{{ __('Discount') }}:</td>
                            <td class="text-end">-{{ currency($order->order_discount) }}</td>
                        </tr>
                        @endif
                        @if($order->total_tax > 0)
                        <tr>
                            <td colspan="4" class="text-end">{{ __('Tax') }}:</td>
                            <td class="text-end">{{ currency($order->total_tax) }}</td>
                        </tr>
                        @endif
                        <tr class="table-success">
                            <td colspan="4" class="text-end"><strong class="fs-5">{{ __('Grand Total') }}:</strong></td>
                            <td class="text-end"><strong class="fs-5" id="orderGrandTotal">{{ currency($order->grand_total) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($order->sale_note || $order->staff_note)
    <div class="card mb-3 border-info">
        <div class="card-header py-2 bg-info text-white">
            <i class="fas fa-sticky-note me-2"></i>{{ __('Notes') }}
        </div>
        <div class="card-body py-2">
            @if($order->sale_note)
            <p class="mb-1"><strong>{{ __('Sale Note') }}:</strong> {{ $order->sale_note }}</p>
            @endif
            @if($order->staff_note)
            <p class="mb-0"><strong>{{ __('Staff Note') }}:</strong> {{ $order->staff_note }}</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="d-flex gap-2 justify-content-between mt-3">
        <button type="button" class="btn btn-outline-danger" onclick="cancelRunningOrder({{ $order->id }})">
            <i class="fas fa-times me-1"></i>{{ __('Cancel Order') }}
        </button>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning" onclick="addItemsToOrder({{ $order->id }})">
                <i class="fas fa-plus me-1"></i>{{ __('Add More Items') }}
            </button>
            <button type="button" class="btn btn-success btn-lg" onclick="showPaymentModal({{ $order->id }}, {{ $order->grand_total }}, '{{ $order->invoice }}', '{{ $order->table->name ?? 'N/A' }}')">
                <i class="fas fa-cash-register me-1"></i>{{ __('Complete & Pay') }}
            </button>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<input type="hidden" id="current-order-id" value="{{ $order->id }}">
<input type="hidden" id="current-order-total" value="{{ $order->grand_total }}">
<input type="hidden" id="order-created-timestamp" value="{{ $order->created_at->timestamp }}">

<style>
.order-details-content .qty-control-group {
    max-width: 130px;
    margin: 0 auto;
}
.order-details-content .qty-control-group .qty-btn {
    padding: 0.25rem 0.5rem;
}
.order-details-content .qty-control-group .item-qty {
    font-weight: bold;
}
.order-details-content .remove-item-btn {
    opacity: 0.5;
    transition: opacity 0.2s;
}
.order-details-content .order-item-row:hover .remove-item-btn {
    opacity: 1;
}
.order-duration-display {
    font-family: 'Courier New', monospace;
    letter-spacing: 2px;
}
.table-icon-wrapper {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
@keyframes pulse-warning {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
.fa-spin-pulse {
    animation: pulse-warning 1s ease-in-out infinite;
}
</style>

<script>
// Real-time clock update
(function() {
    const createdTimestamp = {{ $order->created_at->timestamp }};

    function updateClock() {
        const now = new Date();
        const elapsed = Math.floor(now.getTime() / 1000) - createdTimestamp;

        const hours = Math.floor(elapsed / 3600);
        const minutes = Math.floor((elapsed % 3600) / 60);
        const seconds = elapsed % 60;

        const durationEl = document.getElementById('orderDuration');
        if (durationEl) {
            durationEl.querySelector('.hours').textContent = String(hours).padStart(2, '0');
            durationEl.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
            durationEl.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');

            // Change color based on duration
            if (elapsed > 3600) { // More than 1 hour
                durationEl.classList.remove('text-warning', 'text-dark');
                durationEl.classList.add('text-danger');
            } else if (elapsed > 1800) { // More than 30 minutes
                durationEl.classList.remove('text-dark', 'text-danger');
                durationEl.classList.add('text-warning');
            }
        }

        // Update current time
        const currentTimeEl = document.getElementById('currentTime');
        if (currentTimeEl) {
            currentTimeEl.textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        }
    }

    // Update immediately and then every second
    updateClock();
    window.orderClockInterval = setInterval(updateClock, 1000);
})();

// Clean up interval when modal is closed
$('#order-details-modal').on('hidden.bs.modal', function() {
    if (window.orderClockInterval) {
        clearInterval(window.orderClockInterval);
    }
});
</script>
