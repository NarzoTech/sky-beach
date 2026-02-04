@extends('admin.layouts.master')

@section('title', __('Waiter Dashboard'))

@push('css')
<style>
    .stat-card {
        border-radius: 8px;
    }
    .stat-card .card-body h6 {
        color: #697a8d !important;
    }
    .stat-card .card-body h3 {
        color: #566a7f !important;
    }
    .stat-card .avatar {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-card .avatar.bg-label-primary {
        background-color: rgba(105, 108, 255, 0.16) !important;
    }
    .stat-card .avatar.bg-label-primary i {
        color: #696cff !important;
        font-size: 1.5rem;
    }
    .stat-card .avatar.bg-label-success {
        background-color: rgba(113, 221, 55, 0.16) !important;
    }
    .stat-card .avatar.bg-label-success i {
        color: #71dd37 !important;
        font-size: 1.5rem;
    }
    .stat-card .avatar.bg-label-warning {
        background-color: rgba(255, 171, 0, 0.16) !important;
    }
    .stat-card .avatar.bg-label-warning i {
        color: #ffab00 !important;
        font-size: 1.5rem;
    }
    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        padding: 15px;
    }
    .table-item {
        aspect-ratio: 1;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .table-item:hover {
        opacity: 0.85;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .table-item.available {
        background-color: #71dd37;
        color: #fff;
    }
    .table-item.occupied {
        background-color: #ff3e1d;
        color: #fff;
    }
    .table-item.partial {
        background-color: #ffab00;
        color: #fff;
    }
    .table-item.reserved {
        background-color: #8592a3;
        color: #fff;
    }
    .table-item.maintenance {
        background-color: #8592a3;
        color: #fff;
        opacity: 0.6;
    }
    .table-name {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .table-capacity {
        font-size: 0.8rem;
        opacity: 0.9;
    }
    .order-card {
        border-left: 4px solid #696cff;
    }
    .order-time {
        font-size: 0.85rem;
        color: #8592a3;
    }
    /* Tablet only (768px to 1199px) */
    @media (min-width: 768px) and (max-width: 1199px) {
        .active-orders-card {
            margin-top: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1">{{ __('Waiter Dashboard') }}</h4>
                    <p class="text-muted mb-0">{{ __('Welcome') }}, {{ $employee->name ?? Auth::guard('admin')->user()->name }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('My Active Orders') }}</h6>
                                <h3 class="mb-0">{{ $stats['active_orders'] }}</h3>
                            </div>
                            <div class="avatar avatar-sm bg-label-primary">
                                <span class="avatar-initial rounded"><i class="bx bx-receipt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Available Tables') }}</h6>
                                <h3 class="mb-0">{{ $stats['available_tables'] }}</h3>
                            </div>
                            <div class="avatar avatar-sm bg-label-success">
                                <span class="avatar-initial rounded"><i class="bx bx-check-circle"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">{{ __('Occupied Tables') }}</h6>
                                <h3 class="mb-0">{{ $stats['occupied_tables'] }}</h3>
                            </div>
                            <div class="avatar avatar-sm bg-label-warning">
                                <span class="avatar-initial rounded"><i class="bx bx-group"></i></span>
                            </div>
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
                        <h5 class="mb-0">{{ __('Tables') }}</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshTables()">
                            <i class="bx bx-refresh"></i>
                        </button>
                    </div>
                    <div class="px-3 pt-3">
                        <div class="d-flex flex-wrap gap-3 small">
                            <span><span class="badge bg-success">&nbsp;&nbsp;</span> {{ __('Available') }}</span>
                            <span><span class="badge bg-warning">&nbsp;&nbsp;</span> {{ __('Partial') }}</span>
                            <span><span class="badge bg-danger">&nbsp;&nbsp;</span> {{ __('Full') }}</span>
                            <span><span class="badge bg-secondary">&nbsp;&nbsp;</span> {{ __('Other') }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-grid" id="tables-grid">
                            @foreach($tables as $table)
                            @php
                                $availableSeats = $table->capacity - ($table->occupied_seats ?? 0);
                                $tableClass = $table->status;
                                if ($table->status === 'occupied' && $availableSeats > 0) {
                                    $tableClass = 'partial';
                                }
                            @endphp
                            <div class="table-item {{ $tableClass }}"
                                 onclick="handleTableClick({{ $table->id }}, '{{ $table->status }}', {{ $availableSeats }})"
                                 data-table-id="{{ $table->id }}">
                                <div class="table-name">{{ $table->name }}</div>
                                <div class="table-capacity">
                                    <i class="bx bx-user"></i> {{ $table->occupied_seats ?? 0 }}/{{ $table->capacity }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Active Orders -->
            <div class="col-lg-5">
                <div class="card active-orders-card border">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('My Active Orders') }}</h5>
                        <a href="{{ route('admin.waiter.my-orders') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('View All') }}
                        </a>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @forelse($activeOrders as $order)
                        <div class="order-card card mb-3">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                            @if($order->table)
                                            <span class="badge bg-label-info ms-1">{{ $order->table->name }}</span>
                                            @endif
                                        </h6>
                                        <div class="order-time">
                                            <i class="bx bx-time-five me-1"></i>
                                            {{ $order->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">${{ number_format($order->total, 2) }}</div>
                                        <small class="text-muted">{{ $order->details->count() }} {{ __('items') }}</small>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.waiter.order-details', $order->id) }}"
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bx bx-show"></i> {{ __('View') }}
                                    </a>
                                    <a href="{{ route('admin.waiter.add-to-order', $order->id) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bx bx-plus"></i> {{ __('Add') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bx bx-inbox bx-lg mb-2"></i>
                            <p class="mb-0">{{ __('No active orders') }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    function handleTableClick(tableId, status, availableSeats) {
        if (status === 'available' || (status === 'occupied' && availableSeats > 0)) {
            window.location.href = "{{ url('admin/waiter/create-order') }}/" + tableId;
        } else if (status === 'occupied') {
            showTableDetails(tableId);
        } else {
            Swal.fire({
                icon: 'info',
                title: '{{ __("Table Unavailable") }}',
                text: '{{ __("This table is currently") }} ' + status + '.'
            });
        }
    }

    function showTableDetails(tableId) {
        $.get("{{ url('admin/waiter/table-status') }}/" + tableId, function(data) {
            let ordersHtml = '';
            if (data.active_orders && data.active_orders.length > 0) {
                data.active_orders.forEach(function(order) {
                    ordersHtml += '<div class="mb-2"><strong>#' + order.id + '</strong>' +
                        (order.waiter ? ' - ' + order.waiter.name : '') + '</div>';
                });
            }

            Swal.fire({
                title: data.name,
                html: '<div class="text-start">' +
                    '<p><strong>{{ __("Status") }}:</strong> ' + data.status + '</p>' +
                    '<p><strong>{{ __("Capacity") }}:</strong> ' + data.occupied_seats + '/' + data.capacity + '</p>' +
                    '<hr><p><strong>{{ __("Active Orders") }}:</strong></p>' +
                    (ordersHtml || '<p class="text-muted">{{ __("No active orders") }}</p>') +
                    '</div>',
                showCancelButton: true,
                confirmButtonText: '{{ __("Add to Order") }}',
                confirmButtonColor: '#696cff',
                cancelButtonText: '{{ __("Close") }}'
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
</script>
@endpush
