<div class="order-details-content">
    <!-- Order Header -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chair fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0">{{ $order->table->name ?? 'No Table' }}</h5>
                            <small class="text-muted">{{ $order->table->capacity ?? '-' }} {{ __('seats') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">{{ __('Invoice') }}</small>
                            <h6 class="mb-0">#{{ $order->invoice }}</h6>
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

    <!-- Customer Info -->
    @if($order->customer_id)
    <div class="alert alert-info py-2 mb-3">
        <i class="fas fa-user me-2"></i>
        <strong>{{ __('Customer') }}:</strong> {{ $order->customer->name ?? 'Guest' }}
        @if($order->customer->phone)
        - {{ $order->customer->phone }}
        @endif
    </div>
    @endif

    <!-- Order Items -->
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('Qty') }}</th>
                    <th class="text-end" style="width: 100px;">{{ __('Price') }}</th>
                    <th class="text-end" style="width: 100px;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->details as $detail)
                <tr>
                    <td>
                        {{ $detail->menuItem->name ?? ($detail->service->name ?? 'Unknown Item') }}
                        @if($detail->variant_id && $detail->attributes)
                        <br><small class="text-muted">{{ $detail->attributes }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-end">{{ currency($detail->price) }}</td>
                    <td class="text-end">{{ currency($detail->sub_total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>{{ __('Subtotal') }}:</strong></td>
                    <td class="text-end">{{ currency($order->total_price) }}</td>
                </tr>
                @if($order->order_discount > 0)
                <tr class="text-danger">
                    <td colspan="3" class="text-end">{{ __('Discount') }}:</td>
                    <td class="text-end">-{{ currency($order->order_discount) }}</td>
                </tr>
                @endif
                @if($order->total_tax > 0)
                <tr>
                    <td colspan="3" class="text-end">{{ __('Tax') }}:</td>
                    <td class="text-end">{{ currency($order->total_tax) }}</td>
                </tr>
                @endif
                <tr class="table-primary">
                    <td colspan="3" class="text-end"><strong>{{ __('Grand Total') }}:</strong></td>
                    <td class="text-end"><strong>{{ currency($order->grand_total) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Order Duration -->
    <div class="alert alert-secondary py-2 mb-3">
        <div class="d-flex justify-content-between">
            <span>
                <i class="fas fa-hourglass-half me-2"></i>
                {{ __('Order Duration') }}: <strong>{{ $order->created_at->diffForHumans(null, true) }}</strong>
            </span>
            <span class="badge bg-{{ $order->status == 'processing' ? 'warning' : 'info' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <!-- Notes -->
    @if($order->sale_note || $order->staff_note)
    <div class="card mb-3">
        <div class="card-header py-2">
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
    <div class="d-flex gap-2 justify-content-end mt-3">
        <button type="button" class="btn btn-danger" onclick="cancelRunningOrder({{ $order->id }})">
            <i class="fas fa-times me-1"></i>{{ __('Cancel Order') }}
        </button>
        <button type="button" class="btn btn-warning" onclick="addItemsToOrder({{ $order->id }})">
            <i class="fas fa-plus me-1"></i>{{ __('Add Items') }}
        </button>
        <button type="button" class="btn btn-success" onclick="showPaymentModal({{ $order->id }}, {{ $order->grand_total }})">
            <i class="fas fa-check me-1"></i>{{ __('Complete & Pay') }}
        </button>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<input type="hidden" id="current-order-id" value="{{ $order->id }}">
<input type="hidden" id="current-order-total" value="{{ $order->grand_total }}">
