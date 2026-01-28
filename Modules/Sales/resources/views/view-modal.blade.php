@php
    // Parse notes JSON if it contains customer info (from website orders)
    $notesData = null;
    $remarkText = $sale->sale_note;
    if ($sale->notes && is_string($sale->notes)) {
        $decoded = json_decode($sale->notes, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $notesData = $decoded;
            $remarkText = $decoded['remark'] ?? $sale->sale_note;
        }
    }

    // Get customer info - prefer notes data for website orders
    $customerName = $notesData['customer_name'] ?? $sale->customer->name ?? 'Guest';
    $customerPhone = $notesData['customer_phone'] ?? $sale->customer->phone ?? '';
    $customerEmail = $notesData['customer_email'] ?? $sale->customer->email ?? '';
    $orderSource = $notesData['source'] ?? null;
@endphp

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title"><i class="fas fa-file-invoice me-2"></i>{{ __('View Sale') }} - #{{ $sale->invoice }}</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <!-- Order Info Cards -->
    <div class="row g-3 mb-4">
        <!-- Business Info -->
        <div class="col-md-4">
            <div class="card h-100 border-0 bg-light">
                <div class="card-body py-2">
                    <h6 class="text-muted mb-2"><i class="fas fa-store me-1"></i>{{ __('Business') }}</h6>
                    <strong>{{ $setting->app_name }}</strong>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-md-4">
            <div class="card h-100 border-0 bg-light">
                <div class="card-body py-2">
                    <h6 class="text-muted mb-2"><i class="fas fa-user me-1"></i>{{ __('Customer') }}</h6>
                    <strong>{{ $customerName }}</strong>
                    @if($customerPhone)
                        <br><small><i class="fas fa-phone me-1"></i>{{ $customerPhone }}</small>
                    @endif
                    @if($customerEmail)
                        <br><small><i class="fas fa-envelope me-1"></i>{{ $customerEmail }}</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="col-md-4">
            <div class="card h-100 border-0 bg-light">
                <div class="card-body py-2">
                    <h6 class="text-muted mb-2"><i class="fas fa-info-circle me-1"></i>{{ __('Order Details') }}</h6>
                    <div class="small">
                        <div><strong>{{ __('Invoice') }}:</strong> {{ $sale->invoice }}</div>
                        <div><strong>{{ __('Date') }}:</strong> {{ formatDate($sale->order_date) }}</div>
                        <div><strong>{{ __('By') }}:</strong> {{ $sale->createdBy->name ?? '-' }}</div>
                        @if($sale->order_type)
                            <div>
                                <strong>{{ __('Type') }}:</strong>
                                <span class="badge bg-{{ $sale->order_type == 'dine_in' ? 'primary' : ($sale->order_type == 'take_away' ? 'success' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $sale->order_type)) }}
                                </span>
                            </div>
                        @endif
                        @if($orderSource)
                            <div><strong>{{ __('Source') }}:</strong> <span class="badge bg-secondary">{{ ucfirst($orderSource) }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table & Waiter Info (if dine-in) -->
    @if($sale->table || $sale->waiter)
    <div class="row g-3 mb-3">
        @if($sale->table)
        <div class="col-md-6">
            <div class="alert alert-info py-2 mb-0">
                <i class="fas fa-chair me-2"></i><strong>{{ __('Table') }}:</strong> {{ $sale->table->name }}
                @if($sale->guest_count)
                    <span class="ms-2">| <i class="fas fa-users me-1"></i>{{ $sale->guest_count }} {{ __('guests') }}</span>
                @endif
            </div>
        </div>
        @endif
        @if($sale->waiter)
        <div class="col-md-6">
            <div class="alert alert-warning py-2 mb-0">
                <i class="fas fa-user-tie me-2"></i><strong>{{ __('Waiter') }}:</strong> {{ $sale->waiter->name }}
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Items Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 10%;">{{ __('Image') }}</th>
                    <th style="width: 40%;">{{ __('Item') }}</th>
                    <th style="width: 15%;" class="text-center">{{ __('Qty') }}</th>
                    <th style="width: 15%;" class="text-end">{{ __('Price') }}</th>
                    <th style="width: 15%;" class="text-end">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $subTotal = 0; @endphp
                @forelse ($sale->details as $index => $detail)
                    @php
                        // Determine item name and image based on type
                        $itemName = 'Unknown Item';
                        $itemImage = null;
                        $itemType = '';

                        if ($detail->combo_id && $detail->combo) {
                            $itemName = $detail->combo_name ?? $detail->combo->name ?? 'Combo';
                            $itemImage = $detail->combo->image ?? null;
                            $itemType = 'Combo';
                        } elseif ($detail->menu_item_id && $detail->menuItem) {
                            $itemName = $detail->menuItem->name ?? 'Menu Item';
                            $itemImage = $detail->menuItem->image ?? null;
                            $itemType = 'Menu';
                        } elseif ($detail->ingredient_id && $detail->ingredient) {
                            $itemName = $detail->ingredient->name ?? 'Product';
                            $itemImage = $detail->ingredient->single_image ?? null;
                            $itemType = 'Product';
                        } elseif ($detail->service_id && $detail->service) {
                            $itemName = $detail->service->name ?? 'Service';
                            $itemImage = $detail->service->single_image ?? null;
                            $itemType = 'Service';
                        }

                        $subTotal += $detail->sub_total;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($itemImage)
                                <img src="{{ asset($itemImage) }}" alt="{{ $itemName }}"
                                    style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                            @else
                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $itemName }}</strong>
                            @if($itemType)
                                <br><small class="badge bg-secondary">{{ $itemType }}</small>
                            @endif
                            @if($detail->attributes)
                                <br><small class="text-muted">{{ $detail->attributes }}</small>
                            @endif
                            @if(!empty($detail->addons))
                                <div class="mt-1">
                                    @foreach($detail->addons as $addon)
                                        <small class="text-info d-block">
                                            <i class="fas fa-plus-circle me-1"></i>{{ $addon['name'] }}
                                            @if(isset($addon['qty']) && $addon['qty'] > 1) x{{ $addon['qty'] }} @endif
                                            ({{ currency($addon['price'] ?? 0) }})
                                        </small>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $detail->quantity }}</td>
                        <td class="text-end">{{ currency($detail->price) }}</td>
                        <td class="text-end">{{ currency($detail->sub_total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <br>{{ __('No items found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="row">
        <div class="col-md-6">
            @if($remarkText)
            <div class="card border-0 bg-light">
                <div class="card-body py-2">
                    <h6 class="text-muted mb-1"><i class="fas fa-sticky-note me-1"></i>{{ __('Remark') }}</h6>
                    <p class="mb-0">{{ $remarkText }}</p>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-6">
            <table class="table table-sm mb-0">
                <tr>
                    <th>{{ __('Subtotal') }}</th>
                    <td class="text-end">{{ currency($subTotal) }}</td>
                </tr>
                @if($sale->order_discount > 0)
                <tr>
                    <th>{{ __('Discount') }}</th>
                    <td class="text-end text-danger">- {{ currency($sale->order_discount) }}</td>
                </tr>
                @endif
                @if($sale->total_tax > 0)
                <tr>
                    <th>{{ __('Tax') }}</th>
                    <td class="text-end">{{ currency($sale->total_tax) }}</td>
                </tr>
                @endif
                <tr class="table-dark">
                    <th>{{ __('Grand Total') }}</th>
                    <td class="text-end"><strong>{{ currency($sale->grand_total) }}</strong></td>
                </tr>
                <tr class="table-success">
                    <th>{{ __('Paid') }}</th>
                    <td class="text-end">{{ currency($sale->paid_amount) }}</td>
                </tr>
                @if($sale->due_amount > 0)
                <tr class="table-danger">
                    <th>{{ __('Due') }}</th>
                    <td class="text-end"><strong>{{ currency($sale->due_amount) }}</strong></td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    @if($sale->due_amount > 0)
    <button type="button" class="btn btn-success receive-payment" data-id="{{ $sale->id }}" data-bs-dismiss="modal">
        <i class="fas fa-money-bill me-1"></i>{{ __('Receive Payment') }}
    </button>
    @endif
    <a href="{{ route('admin.sales.invoice', $sale->id) }}" class="btn btn-primary" target="_blank">
        <i class="fas fa-print me-1"></i>{{ __('Print Invoice') }}
    </a>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="fas fa-times me-1"></i>{{ __('Close') }}
    </button>
</div>
