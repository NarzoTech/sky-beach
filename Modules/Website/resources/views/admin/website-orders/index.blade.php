@extends('admin.layouts.master')
@section('title', __('Website Orders'))

@push('css')
<style>
.wo-stats-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e9ecef;
}
.wo-stats-card .stat-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}
.wo-stats-card .stat-label {
    font-size: 13px;
    color: #697a8d;
}
.wo-stats-card.total .stat-value { color: #696cff; }
.wo-stats-card.pending .stat-value { color: #ffab00; }
.wo-stats-card.preparing .stat-value { color: #03c3ec; }
.wo-stats-card.completed .stat-value { color: #71dd37; }

.wo-status-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.wo-status-tab {
    padding: 10px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    border: 2px solid #e9ecef;
    background: #fff;
    color: #697a8d;
    transition: all 0.2s;
}
.wo-status-tab:hover {
    border-color: #696cff;
    color: #696cff;
}
.wo-status-tab.active {
    background: #696cff;
    border-color: #696cff;
    color: #fff;
}
.wo-status-tab.active.pending-tab {
    background: #ffab00;
    border-color: #ffab00;
}
.wo-status-tab.active.preparing-tab {
    background: #03c3ec;
    border-color: #03c3ec;
}
.wo-status-tab.active.completed-tab {
    background: #71dd37;
    border-color: #71dd37;
}
.wo-status-tab .count {
    background: rgba(255,255,255,0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 8px;
}

.wo-filter-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.wo-table-wrapper {
    min-height: 350px;
}
.wo-table {
    width: 100%;
}
.wo-table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    color: #566a7f;
    padding: 12px 15px;
    border-bottom: 2px solid #e9ecef;
}
.wo-table td {
    padding: 12px 15px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}
.wo-table tbody tr:hover {
    background: #f8f9fa;
}

.wo-invoice-cell {
    font-weight: 600;
    color: #696cff;
}
.wo-customer-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.wo-customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e7e7ff;
    color: #696cff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    flex-shrink: 0;
}
.wo-customer-info .name {
    font-weight: 500;
    color: #566a7f;
}
.wo-customer-info .meta {
    font-size: 12px;
    color: #a1acb8;
}

.wo-amount-cell {
    text-align: right;
}
.wo-amount-cell .amount {
    font-weight: 600;
    color: #566a7f;
}
.wo-amount-cell .label {
    font-size: 11px;
    color: #a1acb8;
}

.wo-status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}
.wo-status-badge.pending { background: #fff2d6; color: #ffab00; }
.wo-status-badge.confirmed { background: #d7f5fc; color: #03c3ec; }
.wo-status-badge.preparing { background: #e7e7ff; color: #696cff; }
.wo-status-badge.ready { background: #e8fadf; color: #71dd37; }
.wo-status-badge.completed { background: #e8fadf; color: #71dd37; }
.wo-status-badge.cancelled { background: #ffe0db; color: #ff3e1d; }

.wo-action-btn {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 18px;
    text-decoration: none;
}
.wo-action-btn.view { background: #e7e7ff; color: #696cff; }
.wo-action-btn.print { background: #fff2d6; color: #ffab00; }
.wo-action-btn:hover { opacity: 0.85; transform: scale(1.05); }

.wo-payment-progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 6px;
}
.wo-payment-progress .bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
}
.wo-payment-progress .bar.full { background: #71dd37; }
.wo-payment-progress .bar.partial { background: #ffab00; }
.wo-payment-progress .bar.none { background: #ff3e1d; width: 0 !important; }

.wo-payment-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}
.wo-payment-badge.paid { background: #e8fadf; color: #71dd37; }
.wo-payment-badge.partial { background: #fff2d6; color: #ffab00; }
.wo-payment-badge.due { background: #ffe0db; color: #ff3e1d; }

.wo-action-btn.payment { background: #71dd37; color: #fff; }

/* Status select styling */
.wo-status-select-wrapper {
    position: relative;
}
.wo-status-select-wrapper select {
    appearance: none;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 4px 28px 4px 10px;
    font-size: 12px;
    font-weight: 500;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%23697a8d' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") no-repeat right 8px center;
    cursor: pointer;
    transition: all 0.2s;
}
.wo-status-select-wrapper select:focus {
    border-color: #696cff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.1);
}
</style>
@endpush

@section('content')
    {{-- Summary Stats --}}
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="wo-stats-card total">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">{{ __('Total Orders') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="wo-stats-card pending">
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">{{ __('Pending') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-sm-0">
            <div class="wo-stats-card preparing">
                <div class="stat-value">{{ $stats['preparing'] }}</div>
                <div class="stat-label">{{ __('Preparing') }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="wo-stats-card completed">
                <div class="stat-value">{{ $stats['completed'] }}</div>
                <div class="stat-label">{{ __('Completed') }}</div>
            </div>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="wo-status-tabs">
        <a href="{{ route('admin.restaurant.website-orders.index', array_merge(request()->except('status', 'page'), [])) }}"
           class="wo-status-tab {{ !request('status') ? 'active' : '' }}">
            {{ __('All Orders') }}
            <span class="count">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ route('admin.restaurant.website-orders.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
           class="wo-status-tab pending-tab {{ request('status') === 'pending' ? 'active' : '' }}">
            {{ __('Pending') }}
            <span class="count">{{ $stats['pending'] }}</span>
        </a>
        <a href="{{ route('admin.restaurant.website-orders.index', array_merge(request()->except('page'), ['status' => 'preparing'])) }}"
           class="wo-status-tab preparing-tab {{ request('status') === 'preparing' ? 'active' : '' }}">
            {{ __('Preparing') }}
            <span class="count">{{ $stats['preparing'] }}</span>
        </a>
        <a href="{{ route('admin.restaurant.website-orders.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}"
           class="wo-status-tab completed-tab {{ request('status') === 'completed' ? 'active' : '' }}">
            {{ __('Completed') }}
            <span class="count">{{ $stats['completed'] }}</span>
        </a>
    </div>

    {{-- Filters --}}
    <div class="wo-filter-card">
        <form method="GET" action="{{ route('admin.restaurant.website-orders.index') }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">{{ __('Search') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Invoice, Name, Email...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('From Date') }}</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('To Date') }}</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('Order Type') }}</label>
                    <select name="order_type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="take_away" {{ request('order_type') == 'take_away' ? 'selected' : '' }}>{{ __('Take Away') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bx bx-search me-1"></i>{{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.restaurant.website-orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ __('Website Orders') }}
                @if(request('status'))
                    <span class="badge {{ request('status') === 'pending' ? 'bg-warning' : (request('status') === 'preparing' ? 'bg-info' : 'bg-success') }} ms-2">
                        {{ __(ucfirst(request('status'))) }}
                    </span>
                @endif
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.restaurant.website-orders.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                    <i class="bx bx-export me-1"></i>{{ __('Export CSV') }}
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive wo-table-wrapper">
                <table class="wo-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Invoice') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center" style="width: 160px;">{{ __('Payment') }}</th>
                            <th class="text-center" style="width: 150px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key => $order)
                            @php
                                $notes = json_decode($order->notes ?? '{}', true);
                                $customerName = $notes['customer_name'] ?? ($order->customer->name ?? 'Guest');
                                $customerEmail = $notes['customer_email'] ?? '';
                                $paidPercent = $order->grand_total > 0 ? min(100, (($order->paid_amount ?? 0) / $order->grand_total) * 100) : 0;
                                $isPaid = (float)($order->paid_amount ?? 0) >= (float)$order->grand_total;
                                $isPartial = (float)($order->paid_amount ?? 0) > 0 && !$isPaid;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $key + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.restaurant.website-orders.show', $order->id) }}" class="wo-invoice-cell text-decoration-none">
                                        {{ $order->invoice }}
                                    </a>
                                    <br><small class="text-muted">{{ __('Take Away') }}</small>
                                </td>
                                <td>
                                    <div class="wo-customer-cell">
                                        <div class="wo-customer-avatar">
                                            {{ strtoupper(substr($customerName, 0, 1)) }}
                                        </div>
                                        <div class="wo-customer-info">
                                            <div class="name">{{ $customerName }}</div>
                                            @if($customerEmail)
                                                <div class="meta">{{ $customerEmail }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">{{ $order->details->sum('quantity') }} {{ __('items') }}</span>
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="wo-amount-cell">
                                    <div class="amount">{{ currency($order->grand_total) }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="wo-status-select-wrapper">
                                        <select class="status-select" data-order-id="{{ $order->id }}" data-original="{{ $order->status }}">
                                            @foreach(\Modules\Sales\app\Models\Sale::ORDER_STATUSES as $key => $label)
                                                <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                                    {{ __($label) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">{{ currency($order->paid_amount ?? 0) }}</small>
                                        <span class="wo-payment-badge {{ $isPaid ? 'paid' : ($isPartial ? 'partial' : 'due') }}">
                                            {{ $isPaid ? __('Paid') : ($isPartial ? __('Partial') : __('Due')) }}
                                        </span>
                                    </div>
                                    <div class="wo-payment-progress">
                                        <div class="bar {{ $isPaid ? 'full' : ($isPartial ? 'partial' : 'none') }}"
                                             style="width: {{ $paidPercent }}%"></div>
                                    </div>
                                    @if(!$isPaid)
                                        <small class="text-danger">{{ __('Due') }}: {{ currency($order->due_amount ?? $order->grand_total) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if(!$isPaid)
                                            <button type="button" class="wo-action-btn payment receive-payment"
                                                    data-id="{{ $order->id }}"
                                                    data-invoice="{{ $order->invoice }}"
                                                    data-total="{{ $order->grand_total }}"
                                                    data-paid="{{ $order->paid_amount ?? 0 }}"
                                                    data-due="{{ $order->due_amount ?? $order->grand_total }}"
                                                    title="{{ __('Receive Payment') }}">
                                                <i class="bx bx-dollar"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.restaurant.website-orders.show', $order->id) }}" class="wo-action-btn view" title="{{ __('View Details') }}">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        <a href="{{ route('admin.restaurant.website-orders.print', $order->id) }}" target="_blank" class="wo-action-btn print" title="{{ __('Print Order') }}">
                                            <i class="bx bx-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bx bx-package" style="font-size: 48px;"></i>
                                        <p class="mt-2 mb-0">{{ __('No orders found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($orders, 'hasPages') && $orders->hasPages())
                <div class="p-3 border-top">
                    {{ $orders->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
    {{-- Include POS Running Order Payment Modal --}}
    @include('pos::modals.running-order-payment', ['accounts' => $accounts, 'posSettings' => $posSettings ?? null])
@endsection

@push('scripts')
<script>
    'use strict'

    var currencyIcon = '{{ currency_icon() }}';

$(document).ready(function() {
    // Quick status update
    $('.status-select').on('change', function() {
        const select = $(this);
        const orderId = select.data('order-id');
        const status = select.val();
        const originalValue = select.data('original');

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
                    select.val(originalValue);
                }
            },
            error: function(xhr) {
                toastr.error('{{ __("An error occurred") }}');
                select.val(originalValue);
            }
        });
    });

    // Receive Payment - use POS running order payment modal
    $(document).on('click', '.receive-payment', function() {
        var id = $(this).data('id');
        if (typeof openRunningOrderPayment === 'function') {
            openRunningOrderPayment(id);
        } else {
            toastr.error('{{ __("Payment modal not available") }}');
        }
    });
});
</script>
@endpush
