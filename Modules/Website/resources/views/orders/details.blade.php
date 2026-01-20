@extends('website::layouts.master')

@section('title', __('Order Details') . ' #' . $order->invoice . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Order Details') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.orders.index') }}">{{ __('My Orders') }}</a></li>
                                <li><a href="#">#{{ $order->invoice }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========ORDER DETAILS START===========-->
        <section class="order_details pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Order Header -->
                        <div class="order_detail_card wow fadeInUp mb-4">
                            <div class="card_header">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h4>{{ __('Order') }} #{{ $order->invoice }}</h4>
                                        <p class="mb-0 text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                        <span class="status_badge badge {{ $order->status_badge_class }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Status Timeline -->
                        <div class="order_detail_card wow fadeInUp mb-4">
                            <h5 class="card_title"><i class="fas fa-truck me-2"></i>{{ __('Order Status') }}</h5>
                            <div class="status_timeline">
                                @php
                                    $statuses = ['pending', 'confirmed', 'preparing', 'ready'];
                                    if ($order->order_type === 'delivery') {
                                        $statuses = array_merge($statuses, ['out_for_delivery', 'delivered']);
                                    } else {
                                        $statuses[] = 'completed';
                                    }
                                    $currentIndex = array_search($order->status, $statuses);
                                    if ($order->status === 'cancelled') $currentIndex = -1;
                                @endphp

                                <div class="timeline_track">
                                    @foreach($statuses as $index => $status)
                                        @php
                                            $isActive = $currentIndex !== false && $index <= $currentIndex;
                                            $isCurrent = $order->status === $status;
                                            $statusLabels = [
                                                'pending' => __('Order Placed'),
                                                'confirmed' => __('Confirmed'),
                                                'preparing' => __('Preparing'),
                                                'ready' => __('Ready'),
                                                'out_for_delivery' => __('On The Way'),
                                                'delivered' => __('Delivered'),
                                                'completed' => __('Completed'),
                                            ];
                                            $statusIcons = [
                                                'pending' => 'fas fa-check',
                                                'confirmed' => 'fas fa-clipboard-check',
                                                'preparing' => 'fas fa-fire',
                                                'ready' => 'fas fa-box',
                                                'out_for_delivery' => 'fas fa-motorcycle',
                                                'delivered' => 'fas fa-home',
                                                'completed' => 'fas fa-smile',
                                            ];
                                        @endphp
                                        <div class="timeline_item {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}">
                                            <div class="timeline_icon">
                                                <i class="{{ $statusIcons[$status] ?? 'fas fa-circle' }}"></i>
                                            </div>
                                            <div class="timeline_text">{{ $statusLabels[$status] ?? ucfirst($status) }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($order->status === 'cancelled')
                                    <div class="cancelled_notice mt-4">
                                        <i class="fas fa-times-circle me-2"></i>
                                        {{ __('This order has been cancelled.') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="order_detail_card wow fadeInUp mb-4">
                            <h5 class="card_title"><i class="fas fa-utensils me-2"></i>{{ __('Order Items') }}</h5>
                            <div class="order_items_list">
                                @foreach($order->details as $item)
                                    <div class="order_item_row">
                                        <div class="item_image">
                                            @if($item->menuItem && $item->menuItem->image)
                                                <img src="{{ asset($item->menuItem->image) }}" alt="{{ $item->menuItem->name }}">
                                            @else
                                                <div class="image_placeholder">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="item_details">
                                            <h6>{{ $item->menuItem->name ?? 'Item' }}</h6>
                                            @if($item->addons && count($item->addons) > 0)
                                                <small class="text-muted">
                                                    + {{ collect($item->addons)->pluck('name')->implode(', ') }}
                                                </small>
                                            @endif
                                            @if($item->note)
                                                <small class="d-block text-muted mt-1">
                                                    <i class="fas fa-sticky-note"></i> {{ $item->note }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="item_qty">x{{ $item->quantity }}</div>
                                        <div class="item_price">${{ number_format($item->sub_total, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Delivery Info (if delivery order) -->
                        @if($order->order_type === 'delivery' && $order->delivery_address)
                            <div class="order_detail_card wow fadeInUp mb-4">
                                <h5 class="card_title"><i class="fas fa-map-marker-alt me-2"></i>{{ __('Delivery Information') }}</h5>
                                <div class="delivery_info">
                                    <p class="mb-2"><strong>{{ __('Address') }}:</strong> {{ $order->delivery_address }}</p>
                                    @if($order->delivery_phone)
                                        <p class="mb-2"><strong>{{ __('Phone') }}:</strong> {{ $order->delivery_phone }}</p>
                                    @endif
                                    @if($order->delivery_notes)
                                        <p class="mb-0"><strong>{{ __('Instructions') }}:</strong> {{ $order->delivery_notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <!-- Order Summary -->
                        <div class="order_summary_card wow fadeInUp mb-4">
                            <h5 class="card_title">{{ __('Order Summary') }}</h5>
                            <div class="summary_content">
                                <div class="summary_row">
                                    <span>{{ __('Order Type') }}</span>
                                    <span>
                                        @if($order->order_type === 'delivery')
                                            <i class="fas fa-motorcycle me-1"></i>{{ __('Delivery') }}
                                        @else
                                            <i class="fas fa-shopping-bag me-1"></i>{{ __('Take Away') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="summary_row">
                                    <span>{{ __('Payment Method') }}</span>
                                    <span>
                                        @if(is_array($order->payment_method) && in_array('cash', $order->payment_method))
                                            <i class="fas fa-money-bill-wave me-1"></i>{{ __('Cash') }}
                                        @else
                                            <i class="fas fa-credit-card me-1"></i>{{ __('Card') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="summary_row">
                                    <span>{{ __('Payment Status') }}</span>
                                    <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                                <hr>
                                <div class="summary_row">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span>${{ number_format($order->total_price, 2) }}</span>
                                </div>
                                @if($order->shipping_cost > 0)
                                    <div class="summary_row">
                                        <span>{{ __('Delivery Fee') }}</span>
                                        <span>${{ number_format($order->shipping_cost, 2) }}</span>
                                    </div>
                                @endif
                                @if($order->total_tax > 0)
                                    <div class="summary_row">
                                        <span>{{ __('Tax') }}</span>
                                        <span>${{ number_format($order->total_tax, 2) }}</span>
                                    </div>
                                @endif
                                @if($order->discount_amount > 0)
                                    <div class="summary_row text-success">
                                        <span>{{ __('Discount') }}</span>
                                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                @endif
                                <hr>
                                <div class="summary_row total">
                                    <span>{{ __('Total') }}</span>
                                    <span>${{ number_format($order->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="order_actions_card wow fadeInUp">
                            <a href="{{ route('website.orders.track', $order->id) }}" class="common_btn w-100 mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ __('Track Order') }}
                            </a>

                            @if($order->canBeCancelled())
                                <button type="button" class="common_btn btn_danger w-100 mb-3" id="cancelOrderBtn">
                                    <i class="fas fa-times me-2"></i>{{ __('Cancel Order') }}
                                </button>
                            @endif

                            <button type="button" class="common_btn btn_secondary w-100 mb-3" id="reorderBtn">
                                <i class="fas fa-redo me-2"></i>{{ __('Order Again') }}
                            </button>

                            <a href="{{ route('website.orders.index') }}" class="common_btn btn_outline w-100">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Orders') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========ORDER DETAILS END===========-->

        <!-- Cancel Order Modal -->
        <div class="modal fade" id="cancelOrderModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Cancel Order') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to cancel this order?') }}</p>
                        <p class="text-muted">{{ __('This action cannot be undone.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('No, Keep Order') }}</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelBtn">{{ __('Yes, Cancel Order') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .order_detail_card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
    }

    .card_header {
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
    }

    .card_header h4 {
        margin-bottom: 5px;
        font-weight: 700;
    }

    .status_badge {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .card_title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .status_timeline {
        padding: 20px 0;
    }

    .timeline_track {
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    .timeline_track::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 25px;
        right: 25px;
        height: 3px;
        background: #e0e0e0;
        z-index: 0;
    }

    .timeline_item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .timeline_icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        transition: all 0.3s;
    }

    .timeline_icon i {
        font-size: 18px;
        color: #999;
    }

    .timeline_item.active .timeline_icon {
        background: #71dd37;
    }

    .timeline_item.active .timeline_icon i {
        color: #fff;
    }

    .timeline_item.current .timeline_icon {
        box-shadow: 0 0 0 5px rgba(113, 221, 55, 0.3);
    }

    .timeline_text {
        font-size: 12px;
        font-weight: 600;
        color: #888;
        text-align: center;
    }

    .timeline_item.active .timeline_text {
        color: #333;
    }

    .cancelled_notice {
        background: #fff3cd;
        color: #856404;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
    }

    .order_items_list {
        border: 1px solid #eee;
        border-radius: 10px;
        overflow: hidden;
    }

    .order_item_row {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
        gap: 15px;
    }

    .order_item_row:last-child {
        border-bottom: none;
    }

    .item_image img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }

    .image_placeholder {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }

    .item_details {
        flex: 1;
    }

    .item_details h6 {
        margin-bottom: 3px;
        font-weight: 600;
    }

    .item_qty {
        color: #888;
        font-weight: 500;
    }

    .item_price {
        font-weight: 700;
        color: #ff6b35;
    }

    .delivery_info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
    }

    .order_summary_card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
    }

    .summary_row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }

    .summary_row.total {
        font-size: 20px;
        font-weight: 700;
    }

    .summary_row.total span:last-child {
        color: #ff6b35;
    }

    .order_actions_card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
    }

    .common_btn.btn_danger {
        background: #dc3545;
        border-color: #dc3545;
    }

    .common_btn.btn_secondary {
        background: #6c757d;
        border-color: #6c757d;
    }

    .common_btn.btn_outline {
        background: transparent;
        border: 2px solid #ff6b35;
        color: #ff6b35;
    }

    .common_btn.btn_outline:hover {
        background: #ff6b35;
        color: #fff;
    }

    @media (max-width: 992px) {
        .timeline_track {
            flex-wrap: wrap;
            gap: 20px;
        }

        .timeline_track::before {
            display: none;
        }

        .timeline_item {
            flex: 0 0 auto;
            width: 30%;
        }
    }

    @media (max-width: 576px) {
        .order_item_row {
            flex-wrap: wrap;
        }

        .item_details {
            width: calc(100% - 75px);
        }

        .item_qty, .item_price {
            width: 50%;
            text-align: center;
            padding-top: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Cancel order
    const cancelBtn = document.getElementById('cancelOrderBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('cancelOrderModal')).show();
        });
    }

    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        fetch(`/order/{{ $order->id }}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("website.orders.index") }}';
            } else {
                alert(data.message || 'Failed to cancel order');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    });

    // Reorder
    document.getElementById('reorderBtn').addEventListener('click', function() {
        const button = this;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adding...';

        fetch(`/order/{{ $order->id }}/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("website.cart.index") }}';
            } else {
                alert(data.message || 'Failed to add items to cart');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-redo me-2"></i> Order Again';
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-redo me-2"></i> Order Again';
        });
    });
</script>
@endpush
