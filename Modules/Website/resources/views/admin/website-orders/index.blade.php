@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Website Orders') }}</title>
@endsection

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
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 bg-label-primary rounded">
                                <i class="bx bx-package fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $stats['total'] }}</h6>
                                <small class="text-muted">{{ __('Total Orders') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 bg-label-warning rounded">
                                <i class="bx bx-time-five fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $stats['pending'] }}</h6>
                                <small class="text-muted">{{ __('Pending') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 bg-label-info rounded">
                                <i class="bx bx-loader-alt fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $stats['preparing'] }}</h6>
                                <small class="text-muted">{{ __('Preparing') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 bg-label-success rounded">
                                <i class="bx bx-check-circle fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $stats['completed'] }}</h6>
                                <small class="text-muted">{{ __('Completed') }}</small>
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
                                        <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
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
    // Quick status update
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const status = this.value;
            const originalValue = this.getAttribute('data-original') || this.value;

            fetch(`{{ url('admin/restaurant/website-orders') }}/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success toast or notification
                    this.setAttribute('data-original', status);
                    // Optionally show a toast notification
                } else {
                    alert(data.message || 'Failed to update status');
                    this.value = originalValue;
                }
            })
            .catch(error => {
                alert('An error occurred');
                this.value = originalValue;
            });
        });
    });
</script>
@endpush
