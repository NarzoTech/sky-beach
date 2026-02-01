@extends('admin.layouts.master')
@section('title', __('Website Orders'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Website Orders') }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.restaurant.website-orders.export', request()->query()) }}" class="btn btn-outline-primary">
                    <i class="bx bx-export me-1"></i> {{ __('Export CSV') }}
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-2">
                                <p class="text-muted mb-1">{{ __('Total Orders') }}</p>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-cart-alt bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-2">
                                <p class="text-muted mb-1">{{ __('Pending') }}</p>
                                <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time-five bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-2">
                                <p class="text-muted mb-1">{{ __('Preparing') }}</p>
                                <h3 class="mb-0 text-info">{{ $stats['preparing'] }}</h3>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-loader-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="me-2">
                                <p class="text-muted mb-1">{{ __('Completed') }}</p>
                                <h3 class="mb-0 text-success">{{ $stats['completed'] }}</h3>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('All Status') }}</option>
                            @foreach(\Modules\Sales\app\Models\Sale::ORDER_STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                    {{ __($label) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Order Type') }}</label>
                        <select name="order_type" class="form-select">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="delivery" {{ request('order_type') == 'delivery' ? 'selected' : '' }}>{{ __('Delivery') }}</option>
                            <option value="take_away" {{ request('order_type') == 'take_away' ? 'selected' : '' }}>{{ __('Take Away') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('From Date') }}</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('To Date') }}</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('Search') }}</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Invoice, Name, Email...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i> {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.restaurant.website-orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Order #') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Payment') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                @php
                                    $notes = json_decode($order->notes ?? '{}', true);
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.restaurant.website-orders.show', $order->id) }}" class="fw-bold text-primary">
                                            {{ $order->invoice }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $notes['customer_name'] ?? ($order->customer->name ?? 'Guest') }}</strong>
                                            @if(isset($notes['customer_email']))
                                                <br><small class="text-muted">{{ $notes['customer_email'] }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->order_type === 'delivery')
                                            <span class="badge bg-info"><i class="bx bx-car me-1"></i>{{ __('Delivery') }}</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bx bx-shopping-bag me-1"></i>{{ __('Take Away') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->details->sum('quantity') }} {{ __('items') }}</td>
                                    <td><strong>{{ currency($order->grand_total) }}</strong></td>
                                    <td>
                                        <select class="form-control form-control-sm status-select select2" data-order-id="{{ $order->id }}" style="width: 130px;">
                                            @foreach(\Modules\Sales\app\Models\Sale::ORDER_STATUSES as $key => $label)
                                                <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                                    {{ __($label) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm payment-status-select" data-order-id="{{ $order->id }}" style="width: 100px;">
                                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.restaurant.website-orders.show', $order->id) }}">
                                                        <i class="bx bx-show me-2"></i> {{ __('View Details') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.restaurant.website-orders.print', $order->id) }}" target="_blank">
                                                        <i class="bx bx-printer me-2"></i> {{ __('Print Order') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bx bx-package fs-1 text-muted mb-2 d-block"></i>
                                        {{ __('No orders found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for status dropdowns
    $('.status-select').select2({
        minimumResultsForSearch: Infinity,
        width: '130px'
    });

    // Quick status update using Select2 change event
    $('.status-select').on('select2:select', function(e) {
        const select = $(this);
        const orderId = select.data('order-id');
        const status = select.val();
        const originalValue = select.data('original') || status;

        $.ajax({
            url: `{{ url('admin/restaurant/website-orders') }}/${orderId}/status`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            contentType: 'application/json',
            data: JSON.stringify({ status: status }),
            success: function(data) {
                if (data.success) {
                    select.data('original', status);
                    toastr.success(data.message || '{{ __("Status updated successfully") }}');
                } else {
                    toastr.error(data.message || '{{ __("Failed to update status") }}');
                    select.val(originalValue).trigger('change.select2');
                }
            },
            error: function(xhr) {
                toastr.error('{{ __("An error occurred") }}');
                select.val(originalValue).trigger('change.select2');
            }
        });
    });

    // Payment status update
    $('.payment-status-select').on('change', function() {
        const select = $(this);
        const orderId = select.data('order-id');
        const paymentStatus = select.val();
        const originalValue = select.data('original') || paymentStatus;

        $.ajax({
            url: `{{ url('admin/restaurant/website-orders') }}/${orderId}/payment-status`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            contentType: 'application/json',
            data: JSON.stringify({ payment_status: paymentStatus }),
            success: function(data) {
                if (data.success) {
                    select.data('original', paymentStatus);
                    toastr.success(data.message || '{{ __("Payment status updated successfully") }}');
                } else {
                    toastr.error(data.message || '{{ __("Failed to update payment status") }}');
                    select.val(originalValue);
                }
            },
            error: function(xhr) {
                toastr.error('{{ __("An error occurred") }}');
                select.val(originalValue);
            }
        });
    });
});
</script>
@endpush
