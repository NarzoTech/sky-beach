@php
    $orderTypeConfig = match($order->order_type) {
        'dine_in' => ['icon' => 'fa-chair', 'color' => 'primary', 'label' => __('Dine-in')],
        'take_away' => ['icon' => 'fa-shopping-bag', 'color' => 'success', 'label' => __('Take Away')],
        default => ['icon' => 'fa-receipt', 'color' => 'secondary', 'label' => __('Order')]
    };

    // Calculate grand_total if it's 0 or null
    $calculatedGrandTotal = $order->grand_total;
    if (empty($calculatedGrandTotal) || $calculatedGrandTotal == 0) {
        $subtotal = $order->details->sum('sub_total');
        $discount = $order->order_discount ?? 0;
        $tax = $order->total_tax ?? 0;
        $calculatedGrandTotal = $subtotal - $discount + $tax;
        if ($calculatedGrandTotal == 0 && $order->total_price > 0) {
            $calculatedGrandTotal = $order->total_price - $discount + $tax;
        }
    }

    // Status configuration
    $statusValue = $order->status;
    $statusClass = match(true) {
        $statusValue === 0 || $statusValue === 'processing' || $statusValue === 'pending' => 'warning',
        $statusValue === 1 || $statusValue === 'completed' => 'success',
        $statusValue === 2 || $statusValue === 'cancelled' => 'danger',
        default => 'info'
    };
    $statusIcon = match(true) {
        $statusValue === 0 || $statusValue === 'processing' || $statusValue === 'pending' => 'fa-hourglass-half',
        $statusValue === 1 || $statusValue === 'completed' => 'fa-check-circle',
        $statusValue === 2 || $statusValue === 'cancelled' => 'fa-times-circle',
        default => 'fa-info-circle'
    };
    $statusLabel = match(true) {
        $statusValue === 0 || $statusValue === 'processing' || $statusValue === 'pending' => __('Processing'),
        $statusValue === 1 || $statusValue === 'completed' => __('Completed'),
        $statusValue === 2 || $statusValue === 'cancelled' => __('Cancelled'),
        default => __('Pending')
    };
@endphp

<div class="order-details-content" data-order-id="{{ $order->id }}" data-created-at="{{ $order->created_at->timestamp }}">
    <!-- Order Info Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="info-card primary">
                <div class="d-flex align-items-center gap-3">
                    <div class="info-icon">
                        <i class="fas {{ $orderTypeConfig['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="info-label">{{ $orderTypeConfig['label'] }}</div>
                        @if($order->order_type == 'dine_in' && $order->table)
                            <div class="info-value primary">{{ $order->table->name }}</div>
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i>{{ $order->guest_count ?? 1 }} / {{ $order->table->capacity ?? '-' }} {{ __('seats') }}
                            </small>
                        @else
                            <div class="info-value">{{ $orderTypeConfig['label'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="info-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div>
                        <div class="info-label">{{ __('Invoice') }}</div>
                        <div class="info-value primary">#{{ $order->invoice }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="info-label">{{ __('Started') }}</div>
                        <div class="info-value">{{ $order->created_at->format('h:i A') }}</div>
                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Duration & Status -->
    <div class="duration-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="info-icon" style="background: #f0f1ff;">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                    <div>
                        <div class="info-label">{{ __('Order Duration') }}</div>
                        <div class="duration-display" id="orderDuration">
                            <span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="info-label">{{ __('Started At') }}</div>
                <div class="info-value" id="orderStartTime">{{ $order->created_at->format('h:i:s A') }}</div>
            </div>
            <div class="col-md-4 text-end">
                <span class="status-badge bg-{{ $statusClass }} text-white">
                    <i class="fas {{ $statusIcon }} me-1"></i>{{ $statusLabel }}
                </span>
            </div>
        </div>
    </div>

    <!-- Cooking Progress -->
    @if($order->estimated_prep_minutes)
    @php
        $prepEndTime = $order->created_at->addMinutes($order->estimated_prep_minutes);
        $now = now();
        $elapsedMinutes = $order->created_at->diffInMinutes($now);
        $totalPrepTime = $order->estimated_prep_minutes;
        $progressPercent = min(100, ($elapsedMinutes / $totalPrepTime) * 100);
        $isReady = $now->gte($prepEndTime);
        $remainingMinutes = $isReady ? 0 : ceil($now->diffInMinutes($prepEndTime, false));
        $overdueMinutes = $isReady ? $now->diffInMinutes($prepEndTime) : 0;
    @endphp
    <div class="progress-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="progress-icon {{ $isReady ? 'ready' : 'cooking' }}">
                    <i class="fas {{ $isReady ? 'fa-check' : 'fa-fire' }} fa-lg"></i>
                </div>
                <div>
                    <div class="info-label">{{ __('Cooking Progress') }}</div>
                    @if($isReady)
                        <div class="info-value text-success">
                            <i class="fas fa-check me-1"></i>{{ __('Ready to Serve') }}
                            <small class="text-muted ms-1">(+{{ $overdueMinutes }} min)</small>
                        </div>
                    @else
                        <div class="info-value text-warning">
                            <i class="fas fa-clock me-1"></i>{{ $remainingMinutes }} {{ __('min remaining') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-end">
                <div class="info-label">{{ __('Est. Prep Time') }}</div>
                <div class="info-value">{{ $order->estimated_prep_minutes }} {{ __('min') }}</div>
            </div>
        </div>
        <div class="progress" style="height: 6px; border-radius: 3px;">
            <div class="progress-bar {{ $isReady ? 'bg-success' : 'bg-warning progress-bar-striped progress-bar-animated' }}"
                 role="progressbar" style="width: {{ $progressPercent }}%"></div>
        </div>
    </div>
    @endif

    <!-- Staff Info -->
    <div class="d-flex flex-wrap gap-3 mb-4">
        @if($order->customer_id && $order->customer)
        <div class="staff-badge">
            <i class="fas fa-user"></i>
            <div>
                <small class="text-muted d-block">{{ __('Customer') }}</small>
                <strong>{{ $order->customer->name ?? 'Guest' }}</strong>
                @if($order->customer->phone)
                    <small class="text-muted d-block">{{ $order->customer->phone }}</small>
                @endif
            </div>
        </div>
        @endif
        @if($order->waiter)
        <div class="staff-badge">
            <i class="fas fa-user-tie"></i>
            <div>
                <small class="text-muted d-block">{{ __('Waiter') }}</small>
                <strong>{{ $order->waiter->name }}</strong>
                @if($order->waiter->designation)
                    <small class="text-muted d-block">{{ $order->waiter->designation }}</small>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Order Items -->
    <div class="items-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>{{ __('Order Items') }}</span>
            <span class="badge bg-secondary">{{ $order->details->count() }} {{ __('items') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table items-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>{{ __('Item') }}</th>
                        <th class="text-center" style="width: 140px;">{{ __('Qty') }}</th>
                        <th class="text-end" style="width: 100px;">{{ __('Price') }}</th>
                        <th class="text-end" style="width: 100px;">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $detail)
                    <tr class="order-item-row" data-detail-id="{{ $detail->id }}" data-price="{{ $detail->price }}">
                        <td class="text-center">
                            <button type="button" class="remove-btn" onclick="removeOrderItem({{ $order->id }}, {{ $detail->id }})" title="{{ __('Remove') }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                        <td>
                            <strong>{{ $detail->menuItem->name ?? ($detail->service->name ?? 'Unknown Item') }}</strong>
                            @if($detail->variant_id && $detail->attributes)
                            <br><small class="text-muted"><i class="fas fa-tag me-1"></i>{{ $detail->attributes }}</small>
                            @endif
                            @if(!empty($detail->addons) && is_array($detail->addons))
                            <br><small class="text-muted">
                                <i class="fas fa-plus-circle me-1"></i>
                                @foreach($detail->addons as $addon)
                                    {{ is_array($addon) ? ($addon['name'] ?? '') : $addon }}@if(!$loop->last), @endif
                                @endforeach
                            </small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="qty-control">
                                <button class="qty-btn" type="button" onclick="updateItemQty({{ $order->id }}, {{ $detail->id }}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="qty-input" value="{{ $detail->quantity }}" min="1"
                                       data-detail-id="{{ $detail->id }}"
                                       onchange="updateItemQtyDirect({{ $order->id }}, {{ $detail->id }}, this.value)">
                                <button class="qty-btn" type="button" onclick="updateItemQty({{ $order->id }}, {{ $detail->id }}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td class="text-end">{{ currency($detail->price) }}</td>
                        <td class="text-end fw-semibold">{{ currency($detail->sub_total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">{{ __('Subtotal') }}</td>
                        <td class="text-end fw-semibold" id="orderSubtotal">{{ currency($order->total_price) }}</td>
                    </tr>
                    @if($order->order_discount > 0)
                    <tr class="text-danger">
                        <td colspan="4" class="text-end">{{ __('Discount') }}</td>
                        <td class="text-end">-{{ currency($order->order_discount) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="text-end">{{ __('Tax') }} @if(($order->tax_rate ?? 0) > 0)({{ $order->tax_rate }}%)@endif</td>
                        <td class="text-end">{{ currency($order->total_tax ?? 0) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>{{ __('Grand Total') }}</strong></td>
                        <td class="text-end total-amount" id="orderGrandTotal">{{ currency($calculatedGrandTotal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Notes -->
    @if($order->sale_note || $order->staff_note)
    <div class="progress-card mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-sticky-note text-muted"></i>
            <strong>{{ __('Notes') }}</strong>
        </div>
        @if($order->sale_note)
        <p class="mb-1 text-muted"><strong>{{ __('Sale Note') }}:</strong> {{ $order->sale_note }}</p>
        @endif
        @if($order->staff_note)
        <p class="mb-0 text-muted"><strong>{{ __('Staff Note') }}:</strong> {{ $order->staff_note }}</p>
        @endif
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-back" onclick="backToRunningOrders()">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
            </button>
            <button type="button" class="btn btn-cancel" onclick="cancelRunningOrder({{ $order->id }})">
                <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
            </button>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-add" onclick="addItemsToOrder({{ $order->id }})">
                <i class="fas fa-plus me-1"></i>{{ __('Add Items') }}
            </button>
            <button type="button" class="btn btn-pay" onclick="showPaymentModal({{ $order->id }}, {{ $calculatedGrandTotal }}, '{{ $order->invoice }}', '{{ $order->table->name ?? '' }}')">
                <i class="fas fa-cash-register me-1"></i>{{ __('Complete & Pay') }}
            </button>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<input type="hidden" id="current-order-id" value="{{ $order->id }}">
<input type="hidden" id="current-order-total" value="{{ $calculatedGrandTotal }}">
<input type="hidden" id="order-created-timestamp" value="{{ $order->created_at->timestamp }}">

<script>
// Real-time duration update
(function() {
    const createdTimestamp = {{ $order->created_at->timestamp }};

    function updateDuration() {
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
            if (elapsed > 3600) {
                durationEl.style.color = '#dc3545';
            } else if (elapsed > 1800) {
                durationEl.style.color = '#ffab00';
            }
        }
    }

    updateDuration();
    window.orderClockInterval = setInterval(updateDuration, 1000);
})();

$('#order-details-modal').on('hidden.bs.modal', function() {
    if (window.orderClockInterval) {
        clearInterval(window.orderClockInterval);
    }
});
</script>
