@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Order') }} #{{ $order->invoice }}</title>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">{{ __('Order') }} #{{ $order->invoice }}</h4>
                <small class="text-muted">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.restaurant.website-orders.print', $order->id) }}" class="btn btn-outline-secondary" target="_blank">
                    <i class="bx bx-printer me-1"></i> {{ __('Print') }}
                </a>
                <a href="{{ route('admin.restaurant.website-orders.index') }}" class="btn btn-outline-primary">
                    <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Orders') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Order Status -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Order Status') }}</h5>
                        <span class="badge {{ $order->status_badge_class }} fs-6">{{ $order->status_label }}</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.restaurant.website-orders.status', $order->id) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Update Status') }}</label>
                                <select name="status" class="form-control select2" style="height: 38px;">
                                    @foreach(\Modules\Sales\app\Models\Sale::ORDER_STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ (string)$order->status === (string)$key ? 'selected' : '' }}>
                                            {{ __($label) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Staff Note') }}</label>
                                <input type="text" name="staff_note" class="form-control" placeholder="{{ __('Add a note...') }}" value="{{ $order->staff_note }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-check me-1"></i> {{ __('Update Status') }}
                                </button>
                            </div>
                        </form>

                        <!-- Status Timeline -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3">{{ __('Order Progress') }}</h6>
                            @php
                                $orderStatus = (string)$order->status;
                                $allStatuses = ['pending', 'confirmed', 'preparing', 'ready'];
                                if ($order->order_type === 'delivery') {
                                    $allStatuses = array_merge($allStatuses, ['out_for_delivery', 'delivered']);
                                } else {
                                    $allStatuses[] = 'completed';
                                }
                                $currentIndex = array_search($orderStatus, $allStatuses, true);
                                // Handle case where status is not in array (e.g., cancelled)
                                if ($currentIndex === false) {
                                    $currentIndex = -1;
                                }
                            @endphp
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    // Terminal statuses should show checkmark, not spinner
                                    $terminalStatuses = ['completed', 'delivered'];
                                    $isTerminalStatus = in_array($orderStatus, $terminalStatuses);
                                @endphp
                                @foreach($allStatuses as $index => $status)
                                    @php
                                        $isCompleted = $currentIndex >= 0 && $index < $currentIndex;
                                        $isCurrent = $orderStatus === $status;
                                        $isActive = $isCompleted || $isCurrent;
                                        // For terminal statuses, treat current as completed (show checkmark)
                                        $showCheckmark = $isCompleted || ($isCurrent && $isTerminalStatus);
                                        $showSpinner = $isCurrent && !$isTerminalStatus;
                                    @endphp
                                    <span class="badge {{ $isCurrent ? ($isTerminalStatus ? 'bg-success' : 'bg-primary') : ($isCompleted ? 'bg-success' : 'bg-secondary text-white') }} py-2 px-3" style="{{ !$isActive ? 'opacity: 0.5;' : '' }}">
                                        @if($showCheckmark)<i class="bx bx-check me-1"></i>@endif
                                        @if($showSpinner)<i class="bx bx-loader-alt bx-spin me-1"></i>@endif
                                        {{ __(ucfirst(str_replace('_', ' ', $status))) }}
                                    </span>
                                    @if(!$loop->last)
                                        <i class="bx bx-chevron-right {{ $isCompleted || ($isCurrent && $isTerminalStatus) ? 'text-success' : 'text-muted' }} align-self-center"></i>
                                    @endif
                                @endforeach
                            </div>
                            @if($orderStatus === 'cancelled')
                                <div class="alert alert-danger mt-3 mb-0">
                                    <i class="bx bx-x-circle me-1"></i> {{ __('This order has been cancelled.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50%">{{ __('Item') }}</th>
                                        <th class="text-center">{{ __('Qty') }}</th>
                                        <th class="text-end">{{ __('Price') }}</th>
                                        <th class="text-end">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->combo_id)
                                                        <div class="bg-warning bg-opacity-25 rounded me-3 d-flex align-items-center justify-content-center position-relative" style="width: 50px; height: 50px;">
                                                            <i class="bx bx-package text-warning fs-4"></i>
                                                        </div>
                                                    @elseif($item->menuItem && $item->menuItem->image)
                                                        <img src="{{ asset($item->menuItem->image) }}" alt="" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                            <i class="bx bx-food-menu text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        @if($item->combo_id)
                                                            <strong>{{ $item->combo_name ?? ($item->combo->name ?? 'Combo') }}</strong>
                                                            <span class="badge bg-warning text-dark ms-1">{{ __('COMBO') }}</span>
                                                            @if($item->combo && $item->combo->comboItems)
                                                                <br><small class="text-muted">
                                                                    {{ __('Includes') }}:
                                                                    @foreach($item->combo->comboItems->take(3) as $comboItem)
                                                                        {{ $comboItem->quantity }}x {{ $comboItem->menuItem->name ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                                                    @endforeach
                                                                    @if($item->combo->comboItems->count() > 3)
                                                                        {{ __('+ :count more', ['count' => $item->combo->comboItems->count() - 3]) }}
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        @else
                                                            <strong>{{ $item->menuItem->name ?? 'Item' }}</strong>
                                                            @if($item->addons && count($item->addons) > 0)
                                                                <br><small class="text-muted">+ {{ collect($item->addons)->pluck('name')->implode(', ') }}</small>
                                                            @endif
                                                        @endif
                                                        @if($item->note)
                                                            <br><small class="text-info"><i class="bx bx-note me-1"></i>{{ $item->note }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end">{{ currency($item->price + ($item->addons_price ?? 0)) }}</td>
                                            <td class="text-end"><strong>{{ currency($item->sub_total) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Subtotal') }}:</td>
                                        <td class="text-end">{{ currency($order->total_price) }}</td>
                                    </tr>
                                    @if($order->shipping_cost > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">{{ __('Delivery Fee') }}:</td>
                                            <td class="text-end">{{ currency($order->shipping_cost) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->total_tax > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">{{ __('Tax') }}:</td>
                                            <td class="text-end">{{ currency($order->total_tax) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->discount_amount > 0)
                                        <tr class="text-success">
                                            <td colspan="3" class="text-end">{{ __('Discount') }}:</td>
                                            <td class="text-end">-{{ currency($order->discount_amount) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end"><strong class="fs-5">{{ __('Grand Total') }}:</strong></td>
                                        <td class="text-end"><strong class="fs-5 text-primary">{{ currency($order->grand_total) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Customer Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Customer Information') }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $notes = json_decode($order->notes ?? '{}', true);
                        @endphp
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md bg-label-primary me-3">
                                <span class="avatar-initial rounded">{{ strtoupper(substr($notes['customer_name'] ?? 'G', 0, 1)) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $notes['customer_name'] ?? ($order->customer->name ?? 'Guest') }}</h6>
                                @if($order->customer)
                                    <small class="text-success">{{ __('Registered Customer') }}</small>
                                @else
                                    <small class="text-muted">{{ __('Guest Customer') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2">
                            <i class="bx bx-envelope text-muted me-2"></i>
                            {{ $notes['customer_email'] ?? ($order->customer->email ?? 'N/A') }}
                        </div>
                        <div class="mb-2">
                            <i class="bx bx-phone text-muted me-2"></i>
                            {{ $notes['customer_phone'] ?? $order->delivery_phone ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">{{ __('Order Type') }}</span>
                                <span>
                                    @if($order->order_type === 'delivery')
                                        <span class="badge bg-info"><i class="bx bx-car me-1"></i>{{ __('Delivery') }}</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bx bx-shopping-bag me-1"></i>{{ __('Take Away') }}</span>
                                    @endif
                                </span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">{{ __('Payment Method') }}</span>
                                <span>
                                    @if(is_array($order->payment_method) && in_array('cash', $order->payment_method))
                                        <i class="bx bx-money me-1"></i>{{ __('Cash') }}
                                    @else
                                        <i class="bx bx-credit-card me-1"></i>{{ __('Card') }}
                                    @endif
                                </span>
                            </li>
                            <li class="d-flex justify-content-between align-items-center py-4 border-bottom" style="min-height: 70px;">
                                <span class="text-muted">{{ __('Payment Status') }}</span>
                                <select class="form-select payment-status-select no-nice-select" data-order-id="{{ $order->id }}" style="width: 130px; height: 45px; font-size: 14px;">
                                    <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                    <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                </select>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted">{{ __('Order Date') }}</span>
                                <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Delivery Info (if delivery) -->
                @if($order->order_type === 'delivery')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-map me-2"></i>{{ __('Delivery Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>{{ __('Address') }}:</strong><br>
                                {{ $order->delivery_address ?? 'N/A' }}
                            </p>
                            @if($order->delivery_notes)
                                <p class="mb-0">
                                    <strong>{{ __('Delivery Instructions') }}:</strong><br>
                                    <span class="text-muted">{{ $order->delivery_notes }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if($order->sale_note || $order->staff_note || $order->special_instructions)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-note me-2"></i>{{ __('Notes') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($order->sale_note)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">{{ __('Sale Note') }}</small>
                                    <p class="mb-0">{{ $order->sale_note }}</p>
                                </div>
                            @endif
                            @if($order->special_instructions)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">{{ __('Special Instructions') }}</small>
                                    <p class="mb-0">{{ $order->special_instructions }}</p>
                                </div>
                            @endif
                            @if($order->staff_note)
                                <div>
                                    <small class="text-muted d-block mb-1">{{ __('Staff Note') }}</small>
                                    <p class="mb-0 text-info">{{ $order->staff_note }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    /* Override nice-select for payment status dropdown */
    .payment-status-select.no-nice-select {
        display: block !important;
        width: 130px !important;
        height: 45px !important;
        font-size: 14px !important;
        padding: 8px 12px !important;
    }
    .payment-status-select.no-nice-select + .nice-select {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Destroy nice-select for payment status dropdown if it exists
    if ($.fn.niceSelect) {
        $('.payment-status-select.no-nice-select').niceSelect('destroy');
    }

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
