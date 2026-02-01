@extends('admin.layouts.master')

@section('title', __('Order Details') . ' #' . $order->id)

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1">{{ __('Order') }} #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h4>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.my-orders') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
                    </a>
                    @if($order->status == 0)
                    <a href="{{ route('admin.waiter.add-to-order', $order->id) }}" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i>{{ __('Add Items') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Info -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td><strong>{{ __('Order #') }}</strong></td>
                                <td>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Table') }}</strong></td>
                                <td>
                                    @if($order->table)
                                    <span class="badge bg-label-info">{{ $order->table->name }}</span>
                                    @if($order->status == 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-1" onclick="showChangeTableModal()">
                                        <i class="bx bx-transfer"></i>
                                    </button>
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Guests') }}</strong></td>
                                <td>{{ $order->guest_count ?? 1 }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Status') }}</strong></td>
                                <td>
                                    @if($order->status == 0)
                                    <span class="badge bg-warning">{{ __('Processing') }}</span>
                                    @elseif($order->status == 1)
                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                    @else
                                    <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Payment') }}</strong></td>
                                <td>
                                    @if($order->payment_status == 1)
                                    <span class="badge bg-success">{{ __('Paid') }}</span>
                                    @else
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Created') }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            @if($order->customer)
                            <tr>
                                <td><strong>{{ __('Customer') }}</strong></td>
                                <td>{{ $order->customer->name }}</td>
                            </tr>
                            @endif
                        </table>

                        @if($order->special_instructions)
                        <hr>
                        <div>
                            <strong>{{ __('Special Instructions') }}:</strong>
                            <p class="text-muted mb-0">{{ $order->special_instructions }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($order->status == 0)
                <div class="card mt-3">
                    <div class="card-body">
                        <button class="btn btn-danger w-100" onclick="cancelOrder()">
                            <i class="bx bx-x me-1"></i>{{ __('Cancel Order') }}
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Item') }}</th>
                                        <th class="text-center">{{ __('Qty') }}</th>
                                        <th class="text-end">{{ __('Price') }}</th>
                                        <th class="text-end">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $detail)
                                    <tr>
                                        <td>
                                            <strong>{{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}</strong>
                                            @if($detail->addons)
                                                @php $addons = is_string($detail->addons) ? json_decode($detail->addons, true) : $detail->addons; @endphp
                                                @if(is_array($addons) && count($addons) > 0)
                                                <div class="small text-muted">
                                                    @foreach($addons as $addon)
                                                    <span class="badge bg-light text-dark me-1">+ {{ $addon['name'] }}</span>
                                                    @endforeach
                                                </div>
                                                @endif
                                            @endif
                                            @if($detail->note)
                                            <div class="small text-info">
                                                <i class="fas fa-sticky-note"></i> {{ $detail->note }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $detail->quantity }}</td>
                                        <td class="text-end">{{ number_format($detail->price, 2) }}</td>
                                        <td class="text-end">{{ number_format($detail->sub_total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>{{ __('Subtotal') }}</strong></td>
                                        <td class="text-end">{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Tax') }} @if(($order->tax_rate ?? 0) > 0)({{ $order->tax_rate }}%)@endif</td>
                                        <td class="text-end">{{ number_format($order->total_tax ?? $order->tax_amount ?? 0, 2) }}</td>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Discount') }}</td>
                                        <td class="text-end">-{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->total, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if($order->payments && $order->payments->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Payments') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Method') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                <tr>
                                    <td>{{ ucfirst($payment->payment_type ?? 'Cash') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->created_at->format('d M, H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Change Table Modal -->
<div class="modal fade" id="changeTableModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>{{ __('Change Table') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Current Table') }}</label>
                    <div class="badge bg-info fs-6" id="currentTableBadge">{{ $order->table?->name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('Select New Table') }}</label>
                    <div id="availableTablesContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span class="ms-2">{{ __('Loading tables...') }}</span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Reason (Optional)') }}</label>
                    <textarea class="form-control" id="changeTableReason" rows="2" placeholder="{{ __('Enter reason for table change...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirmChangeTableBtn" disabled onclick="confirmChangeTable()">
                    <i class="fas fa-check me-1"></i>{{ __('Change Table') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .table-option {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .table-option:hover {
        border-color: #696cff;
        background-color: #f5f5f9;
    }
    .table-option.selected {
        border-color: #696cff;
        background-color: #e8e8ff;
    }
    .table-option .table-name {
        font-weight: 600;
        font-size: 1rem;
    }
    .table-option .table-capacity {
        font-size: 0.85rem;
        color: #8592a3;
    }
    .table-option.available {
        border-color: #71dd37;
    }
    .table-option.occupied {
        border-color: #ffab00;
    }
    .swal2-container {
        z-index: 2000 !important;
    }
</style>
@endpush

@push('js')
<script>
    let selectedNewTableId = null;

    function showChangeTableModal() {
        selectedNewTableId = null;
        $('#confirmChangeTableBtn').prop('disabled', true);
        $('#changeTableReason').val('');

        // Load available tables
        $('#availableTablesContainer').html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <span class="ms-2">{{ __('Loading tables...') }}</span>
            </div>
        `);

        $('#changeTableModal').modal('show');

        $.get("{{ route('admin.waiter.change-table.data', $order->id) }}", function(data) {
            let html = '<div class="row g-2">';

            if (data.available_tables && data.available_tables.length > 0) {
                data.available_tables.forEach(function(table) {
                    let statusClass = table.status === 'available' ? 'available' : 'occupied';
                    let availableSeats = table.capacity - (table.occupied_seats || 0);

                    html += `
                        <div class="col-4">
                            <div class="table-option ${statusClass}" data-table-id="${table.id}" onclick="selectNewTable(${table.id}, this)">
                                <div class="table-name">${table.name}</div>
                                <div class="table-capacity">
                                    <i class="fas fa-user"></i> ${availableSeats}/${table.capacity}
                                </div>
                                <small class="text-${table.status === 'available' ? 'success' : 'warning'}">
                                    ${table.status === 'available' ? '{{ __("Available") }}' : '{{ __("Partial") }}'}
                                </small>
                            </div>
                        </div>
                    `;
                });
            } else {
                html += `
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                        <p class="mb-0">{{ __('No available tables found') }}</p>
                    </div>
                `;
            }

            html += '</div>';
            $('#availableTablesContainer').html(html);
        }).fail(function(xhr) {
            $('#availableTablesContainer').html(`
                <div class="alert alert-danger">
                    {{ __('Failed to load available tables') }}
                </div>
            `);
        });
    }

    function selectNewTable(tableId, element) {
        $('.table-option').removeClass('selected');
        $(element).addClass('selected');
        selectedNewTableId = tableId;
        $('#confirmChangeTableBtn').prop('disabled', false);
    }

    function confirmChangeTable() {
        if (!selectedNewTableId) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("Select Table") }}',
                text: '{{ __("Please select a table to transfer to.") }}'
            });
            return;
        }

        const reason = $('#changeTableReason').val();

        Swal.fire({
            title: '{{ __("Change Table?") }}',
            text: '{{ __("Are you sure you want to move this order to the selected table?") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            cancelButtonColor: '#8592a3',
            confirmButtonText: '{{ __("Yes, Change It") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#confirmChangeTableBtn').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>{{ __('Changing...') }}
                `);

                $.ajax({
                    url: "{{ route('admin.waiter.change-table', $order->id) }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        new_table_id: selectedNewTableId,
                        reason: reason
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#changeTableModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Table Changed") }}',
                                text: response.message,
                                confirmButtonColor: '#696cff'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#confirmChangeTableBtn').prop('disabled', false).html(`
                            <i class="bx bx-check me-1"></i>{{ __('Change Table') }}
                        `);
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: xhr.responseJSON?.message || '{{ __("Failed to change table.") }}',
                            confirmButtonColor: '#696cff'
                        });
                    }
                });
            }
        });
    }

    function cancelOrder() {
        Swal.fire({
            title: "{{ __('Cancel Order?') }}",
            text: "{{ __('Are you sure you want to cancel this order? This action cannot be undone.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            cancelButtonColor: '#8592a3',
            confirmButtonText: "{{ __('Yes, Cancel It') }}",
            cancelButtonText: "{{ __('No, Keep It') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.waiter.cancel-order', $order->id) }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Cancelled") }}',
                                text: response.message,
                                confirmButtonColor: '#696cff'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("Error") }}',
                            text: xhr.responseJSON?.message || '{{ __("Failed to cancel order.") }}',
                            confirmButtonColor: '#696cff'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
