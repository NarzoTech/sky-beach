@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Waiter Dashboard') }}</title>
@endsection

@push('css')
<style>
    .stat-card {
        border-radius: 15px;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        padding: 15px;
    }
    .table-item {
        aspect-ratio: 1;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .table-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .table-item.available {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    .table-item.occupied {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        color: white;
    }
    .table-item.reserved {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: #333;
    }
    .table-item.maintenance {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
    }
    .table-name {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .table-capacity {
        font-size: 0.8rem;
        opacity: 0.9;
    }
    .order-card {
        border-left: 4px solid #007bff;
        transition: all 0.2s;
    }
    .order-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .order-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .btn-new-order {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        z-index: 1000;
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
                        <i class="fas fa-utensils me-2"></i>{{ __('Waiter Dashboard') }}
                    </h3>
                    <p class="text-muted mb-0">Welcome, {{ $employee->name ?? Auth::guard('admin')->user()->name }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.select-table') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>{{ __('New Order') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ __('My Active Orders') }}</h6>
                                <h2 class="mb-0">{{ $stats['active_orders'] }}</h2>
                            </div>
                            <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ __('Available Tables') }}</h6>
                                <h2 class="mb-0">{{ $stats['available_tables'] }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ __('Occupied Tables') }}</h6>
                                <h2 class="mb-0">{{ $stats['occupied_tables'] }}</h2>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Tables Section -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-th-large me-2"></i>{{ __('Tables') }}</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshTables()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-grid" id="tables-grid">
                            @foreach($tables as $table)
                            <div class="table-item {{ $table->status }}"
                                 onclick="handleTableClick({{ $table->id }}, '{{ $table->status }}')"
                                 data-table-id="{{ $table->id }}">
                                <div class="table-name">{{ $table->name }}</div>
                                <div class="table-capacity">
                                    <i class="fas fa-user"></i> {{ $table->occupied_seats }}/{{ $table->capacity }}
                                </div>
                                @if($table->status === 'occupied')
                                <small class="mt-1">
                                    <i class="fas fa-clock"></i>
                                </small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-around text-center small">
                            <span><span class="badge bg-success">&nbsp;</span> Available</span>
                            <span><span class="badge bg-danger">&nbsp;</span> Occupied</span>
                            <span><span class="badge bg-warning">&nbsp;</span> Reserved</span>
                            <span><span class="badge bg-secondary">&nbsp;</span> Maintenance</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Active Orders -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>{{ __('My Active Orders') }}</h5>
                        <a href="{{ route('admin.waiter.my-orders') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('View All') }}
                        </a>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @forelse($activeOrders as $order)
                        <div class="order-card card mb-3">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            @if($order->table)
                                            <span class="badge bg-info ms-1">{{ $order->table->name }}</span>
                                            @endif
                                        </h6>
                                        <div class="order-time">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $order->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">{{ number_format($order->total, 2) }}</div>
                                        <small class="text-muted">{{ $order->details->count() }} items</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.waiter.order-details', $order->id) }}"
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.waiter.add-to-order', $order->id) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus"></i> Add Items
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>{{ __('No active orders') }}</p>
                            <a href="{{ route('admin.waiter.select-table') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>{{ __('Create New Order') }}
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating New Order Button (Mobile) -->
<a href="{{ route('admin.waiter.select-table') }}" class="btn btn-primary btn-new-order d-lg-none">
    <i class="fas fa-plus"></i>
</a>
@endsection

@push('js')
<script>
    function handleTableClick(tableId, status) {
        if (status === 'available') {
            window.location.href = "{{ url('admin/waiter/create-order') }}/" + tableId;
        } else if (status === 'occupied') {
            // Show table details or orders
            showTableDetails(tableId);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Table Unavailable',
                text: 'This table is currently ' + status + '.'
            });
        }
    }

    function showTableDetails(tableId) {
        $.get("{{ url('admin/waiter/table-status') }}/" + tableId, function(data) {
            let ordersHtml = '';
            if (data.active_orders && data.active_orders.length > 0) {
                data.active_orders.forEach(function(order) {
                    ordersHtml += `<div class="mb-2">
                        <strong>Order #${order.id}</strong>
                        ${order.waiter ? '- ' + order.waiter.name : ''}
                    </div>`;
                });
            }

            Swal.fire({
                title: data.name,
                html: `
                    <div class="text-start">
                        <p><strong>Status:</strong> ${data.status}</p>
                        <p><strong>Capacity:</strong> ${data.occupied_seats}/${data.capacity}</p>
                        <hr>
                        <p><strong>Active Orders:</strong></p>
                        ${ordersHtml || '<p class="text-muted">No active orders</p>'}
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add to Order',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed && data.active_orders.length > 0) {
                    window.location.href = "{{ url('admin/waiter/add-to-order') }}/" + data.active_orders[0].id;
                }
            });
        });
    }

    function refreshTables() {
        location.reload();
    }

    // Auto-refresh every 30 seconds
    setInterval(function() {
        // Could implement AJAX refresh here
    }, 30000);
</script>
@endpush
