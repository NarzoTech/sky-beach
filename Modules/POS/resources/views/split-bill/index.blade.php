@extends('admin.layouts.master')

@section('title', __('Split Bill') . ' - Order #' . $order->id)

@push('css')
<style>
    .split-container {
        display: flex;
        gap: 20px;
        min-height: calc(100vh - 200px);
    }
    .order-items-panel {
        flex: 1;
        max-width: 400px;
    }
    .splits-panel {
        flex: 2;
    }
    .item-card {
        padding: 12px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: grab;
        transition: all 0.2s;
    }
    .item-card:hover {
        border-color: #007bff;
        background: #f8f9fa;
    }
    .item-card.dragging {
        opacity: 0.5;
    }
    .item-card.assigned {
        opacity: 0.5;
        border-style: dashed;
    }
    .split-card {
        border: 2px solid #28a745;
        border-radius: 15px;
        margin-bottom: 20px;
        min-height: 200px;
    }
    .split-header {
        background: #28a745;
        color: white;
        padding: 15px;
        border-radius: 12px 12px 0 0;
    }
    .split-body {
        padding: 15px;
        min-height: 150px;
    }
    .split-body.drag-over {
        background: #e8f5e9;
    }
    .split-item {
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .split-footer {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }
    .split-total {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .drop-zone-hint {
        text-align: center;
        color: #aaa;
        padding: 30px;
        border: 2px dashed #ddd;
        border-radius: 10px;
    }
    .qty-adjuster {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .qty-adjuster input {
        width: 50px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-receipt me-2"></i>{{ __('Split Bill') }} - Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </h3>
                    <p class="text-muted mb-0">
                        @if($order->table)
                        {{ $order->table->name }} |
                        @endif
                        Total: <strong>{{ number_format($order->total, 2) }}</strong>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-outline-secondary me-2" onclick="resetSplits()">
                        <i class="fas fa-undo me-1"></i>{{ __('Reset') }}
                    </button>
                    <button class="btn btn-primary me-2" onclick="splitEqually()">
                        <i class="fas fa-equals me-1"></i>{{ __('Split Equally') }}
                    </button>
                    <a href="{{ route('admin.pos.running-orders.details', $order->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="split-container">
            <!-- Order Items Panel -->
            <div class="order-items-panel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body" id="order-items">
                        @foreach($order->details->where('is_voided', false) as $item)
                        <div class="item-card" draggable="true"
                             data-item-id="{{ $item->id }}"
                             data-item-name="{{ $item->menuItem->name ?? $item->service->name ?? 'Item' }}"
                             data-item-price="{{ $item->sub_total }}"
                             data-item-qty="{{ $item->quantity }}"
                             data-available-qty="{{ $item->quantity }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $item->quantity }}x</strong>
                                    {{ $item->menuItem->name ?? $item->service->name ?? 'Item' }}
                                </div>
                                <span class="badge bg-primary">{{ number_format($item->sub_total, 2) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <button class="btn btn-success w-100" onclick="addSplit()">
                            <i class="fas fa-plus me-1"></i>{{ __('Add Split') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Splits Panel -->
            <div class="splits-panel" id="splits-container">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-hand-point-up fa-3x mb-3"></i>
                    <h5>{{ __('Click "Add Split" to create bill splits') }}</h5>
                    <p>{{ __('Then drag items to each split, or use "Split Equally"') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Split Equally Modal -->
<div class="modal fade" id="splitEquallyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Split Equally') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('Number of splits') }}</label>
                    <input type="number" class="form-control" id="num-splits" value="2" min="2" max="20">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="confirmSplitEqually()">{{ __('Split') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const orderId = {{ $order->id }};
    const orderTotal = {{ $order->total }};
    let splits = [];
    let splitCounter = 0;

    function addSplit() {
        splitCounter++;
        const splitId = 'split-' + splitCounter;

        splits.push({
            id: splitId,
            label: 'Guest ' + splitCounter,
            items: [],
            total: 0
        });

        renderSplits();
    }

    function removeSplit(splitId) {
        const split = splits.find(s => s.id === splitId);
        if (split) {
            // Return items to available
            split.items.forEach(item => {
                const card = document.querySelector(`[data-item-id="${item.productSaleId}"]`);
                if (card) {
                    const availableQty = parseInt(card.dataset.availableQty) + item.quantity;
                    card.dataset.availableQty = availableQty;
                    if (availableQty > 0) {
                        card.classList.remove('assigned');
                    }
                }
            });
        }

        splits = splits.filter(s => s.id !== splitId);
        renderSplits();
    }

    function renderSplits() {
        const container = document.getElementById('splits-container');

        if (splits.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-hand-point-up fa-3x mb-3"></i>
                    <h5>{{ __('Click "Add Split" to create bill splits') }}</h5>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';

        splits.forEach((split, index) => {
            html += `
                <div class="col-md-6">
                    <div class="split-card">
                        <div class="split-header d-flex justify-content-between align-items-center">
                            <input type="text" class="form-control form-control-sm bg-transparent border-0 text-white fw-bold"
                                   value="${split.label}" onchange="updateSplitLabel('${split.id}', this.value)"
                                   style="max-width: 150px;">
                            <button class="btn btn-sm btn-outline-light" onclick="removeSplit('${split.id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="split-body" id="${split.id}"
                             ondragover="handleDragOver(event)" ondrop="handleDrop(event, '${split.id}')"
                             ondragleave="handleDragLeave(event)">
                            ${split.items.length === 0 ?
                                '<div class="drop-zone-hint">Drag items here</div>' :
                                split.items.map(item => `
                                    <div class="split-item">
                                        <div>
                                            <strong>${item.quantity}x</strong> ${item.name}
                                        </div>
                                        <div>
                                            <span class="me-2">${item.amount.toFixed(2)}</span>
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeItemFromSplit('${split.id}', '${item.productSaleId}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                `).join('')
                            }
                        </div>
                        <div class="split-footer d-flex justify-content-between align-items-center">
                            <span class="split-total">Total: ${split.total.toFixed(2)}</span>
                            <button class="btn btn-sm btn-success" onclick="payForSplit('${split.id}')"
                                    ${split.total <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-credit-card me-1"></i>Pay
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';

        // Add save button if splits exist
        html += `
            <div class="text-center mt-3">
                <button class="btn btn-lg btn-primary" onclick="saveSplits()">
                    <i class="fas fa-save me-2"></i>{{ __('Save Split Configuration') }}
                </button>
            </div>
        `;

        container.innerHTML = html;

        // Re-initialize drag events
        initDragEvents();
    }

    function updateSplitLabel(splitId, label) {
        const split = splits.find(s => s.id === splitId);
        if (split) {
            split.label = label;
        }
    }

    function initDragEvents() {
        document.querySelectorAll('.item-card').forEach(card => {
            card.addEventListener('dragstart', handleDragStart);
            card.addEventListener('dragend', handleDragEnd);
        });
    }

    function handleDragStart(e) {
        e.target.classList.add('dragging');
        e.dataTransfer.setData('text/plain', JSON.stringify({
            itemId: e.target.dataset.itemId,
            itemName: e.target.dataset.itemName,
            itemPrice: parseFloat(e.target.dataset.itemPrice),
            itemQty: parseInt(e.target.dataset.itemQty),
            availableQty: parseInt(e.target.dataset.availableQty)
        }));
    }

    function handleDragEnd(e) {
        e.target.classList.remove('dragging');
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    function handleDrop(e, splitId) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        if (data.availableQty <= 0) return;

        addItemToSplit(splitId, data);
    }

    function addItemToSplit(splitId, itemData) {
        const split = splits.find(s => s.id === splitId);
        if (!split) return;

        const unitPrice = itemData.itemPrice / itemData.itemQty;
        const qtyToAdd = 1; // Add one at a time

        // Check if item already in split
        const existingItem = split.items.find(i => i.productSaleId == itemData.itemId);
        if (existingItem) {
            existingItem.quantity += qtyToAdd;
            existingItem.amount = existingItem.quantity * unitPrice;
        } else {
            split.items.push({
                productSaleId: itemData.itemId,
                name: itemData.itemName,
                quantity: qtyToAdd,
                amount: unitPrice * qtyToAdd
            });
        }

        // Update split total
        split.total = split.items.reduce((sum, item) => sum + item.amount, 0);

        // Update available qty on source card
        const card = document.querySelector(`[data-item-id="${itemData.itemId}"]`);
        if (card) {
            const newAvailableQty = parseInt(card.dataset.availableQty) - qtyToAdd;
            card.dataset.availableQty = newAvailableQty;
            if (newAvailableQty <= 0) {
                card.classList.add('assigned');
            }
        }

        renderSplits();
    }

    function removeItemFromSplit(splitId, productSaleId) {
        const split = splits.find(s => s.id === splitId);
        if (!split) return;

        const itemIndex = split.items.findIndex(i => i.productSaleId == productSaleId);
        if (itemIndex === -1) return;

        const item = split.items[itemIndex];

        // Return quantity to source
        const card = document.querySelector(`[data-item-id="${productSaleId}"]`);
        if (card) {
            const newAvailableQty = parseInt(card.dataset.availableQty) + item.quantity;
            card.dataset.availableQty = newAvailableQty;
            card.classList.remove('assigned');
        }

        // Remove from split
        split.items.splice(itemIndex, 1);
        split.total = split.items.reduce((sum, item) => sum + item.amount, 0);

        renderSplits();
    }

    function splitEqually() {
        new bootstrap.Modal(document.getElementById('splitEquallyModal')).show();
    }

    function confirmSplitEqually() {
        const numSplits = parseInt(document.getElementById('num-splits').value);
        if (numSplits < 2 || numSplits > 20) {
            alert('Please enter a number between 2 and 20');
            return;
        }

        $.ajax({
            url: "{{ url('admin/pos/split-bill') }}/" + orderId + "/split-equally",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { number_of_splits: numSplits },
            success: function(response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('splitEquallyModal')).hide();
                    location.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Failed to split bill');
            }
        });
    }

    function resetSplits() {
        if (confirm('Are you sure you want to reset all splits?')) {
            $.ajax({
                url: "{{ url('admin/pos/split-bill') }}/" + orderId + "/remove",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    splits = [];
                    splitCounter = 0;
                    document.querySelectorAll('.item-card').forEach(card => {
                        card.dataset.availableQty = card.dataset.itemQty;
                        card.classList.remove('assigned');
                    });
                    renderSplits();
                }
            });
        }
    }

    function saveSplits() {
        const splitData = splits.map(split => ({
            label: split.label,
            items: split.items.map(item => ({
                product_sale_id: item.productSaleId,
                quantity: item.quantity
            }))
        }));

        $.ajax({
            url: "{{ url('admin/pos/split-bill') }}/" + orderId + "/create",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            contentType: 'application/json',
            data: JSON.stringify({ splits: splitData }),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Bill splits saved successfully.'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to save splits'
                });
            }
        });
    }

    function payForSplit(splitId) {
        // Navigate to payment page or open payment modal
        window.location.href = "{{ url('admin/pos/split-bill/pay') }}/" + splitId.replace('split-', '');
    }

    // Initialize
    initDragEvents();
</script>
@endpush
