@extends('website::layouts.master')

@section('title', __('My Orders') . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('My Orders') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('My Orders') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========MY ORDERS START===========-->
        <section class="my_orders pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Filter Section -->
                        <div class="orders_filter mb-4 wow fadeInUp">
                            <form method="GET" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Filter by Status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('All Orders') }}</option>
                                        @foreach(\Modules\Sales\app\Models\Sale::ORDER_STATUSES as $key => $label)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ __($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="common_btn w-100">{{ __('Filter') }}</button>
                                </div>
                            </form>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @forelse($orders as $order)
                            <div class="order_card wow fadeInUp mb-4">
                                <div class="order_header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="order_info">
                                                <h5>{{ __('Order') }} #{{ $order->invoice }}</h5>
                                                <span class="order_date">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <span class="order_status badge {{ $order->status_badge_class }}">
                                                {{ $order->status_label }}
                                            </span>
                                            <span class="order_type ms-2">
                                                @if($order->order_type === 'delivery')
                                                    <i class="fas fa-motorcycle"></i> {{ __('Delivery') }}
                                                @else
                                                    <i class="fas fa-shopping-bag"></i> {{ __('Take Away') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="order_body">
                                    <div class="order_items">
                                        @foreach($order->details->take(3) as $item)
                                            <div class="order_item">
                                                @if($item->menuItem && $item->menuItem->image)
                                                    <img src="{{ asset($item->menuItem->image) }}" alt="{{ $item->menuItem->name }}">
                                                @else
                                                    <div class="item_placeholder">
                                                        <i class="fas fa-utensils"></i>
                                                    </div>
                                                @endif
                                                <div class="item_info">
                                                    <h6>{{ $item->menuItem->name ?? 'Item' }}</h6>
                                                    <span>x{{ $item->quantity }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->details->count() > 3)
                                            <div class="more_items">
                                                +{{ $order->details->count() - 3 }} {{ __('more item(s)') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="order_total">
                                        <span class="label">{{ __('Total') }}:</span>
                                        <span class="amount">${{ number_format($order->grand_total, 2) }}</span>
                                    </div>
                                </div>

                                <div class="order_footer">
                                    <a href="{{ route('website.orders.show', $order->id) }}" class="common_btn btn_sm">
                                        <i class="fas fa-eye me-1"></i> {{ __('View Details') }}
                                    </a>
                                    <a href="{{ route('website.orders.track', $order->id) }}" class="common_btn btn_sm btn_outline">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ __('Track Order') }}
                                    </a>
                                    @if($order->canBeCancelled())
                                        <button type="button" class="common_btn btn_sm btn_danger cancel-order-btn"
                                                data-order-id="{{ $order->id }}"
                                                data-order-invoice="{{ $order->invoice }}">
                                            <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                                        </button>
                                    @endif
                                    <button type="button" class="common_btn btn_sm btn_secondary reorder-btn"
                                            data-order-id="{{ $order->id }}">
                                        <i class="fas fa-redo me-1"></i> {{ __('Reorder') }}
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty_orders text-center py-5 wow fadeInUp">
                                <div class="empty_icon mb-4">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <h3>{{ __('No Orders Yet') }}</h3>
                                <p class="text-muted mb-4">{{ __("You haven't placed any orders yet. Start exploring our menu!") }}</p>
                                <a href="{{ route('website.menu') }}" class="common_btn">
                                    <i class="fas fa-utensils me-2"></i>{{ __('Browse Menu') }}
                                </a>
                            </div>
                        @endforelse

                        @if($orders->hasPages())
                            <div class="pagination_wrap mt-4 wow fadeInUp">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <!--==========MY ORDERS END===========-->

        <!-- Cancel Order Modal -->
        <div class="modal fade" id="cancelOrderModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Cancel Order') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to cancel order') }} <strong id="cancelOrderInvoice"></strong>?</p>
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
    .orders_filter {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }

    .order_card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .order_card:hover {
        transform: translateY(-3px);
    }

    .order_header {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    .order_info h5 {
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    .order_date {
        color: #888;
        font-size: 14px;
    }

    .order_status {
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }

    .order_type {
        color: #666;
        font-size: 14px;
    }

    .order_body {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order_items {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .order_item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .order_item img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }

    .item_placeholder {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }

    .item_info h6 {
        margin-bottom: 2px;
        font-size: 14px;
        font-weight: 500;
    }

    .item_info span {
        color: #888;
        font-size: 13px;
    }

    .more_items {
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 13px;
        color: #666;
    }

    .order_total {
        text-align: right;
    }

    .order_total .label {
        color: #888;
        font-size: 14px;
    }

    .order_total .amount {
        display: block;
        font-size: 24px;
        font-weight: 700;
        color: #ff6b35;
    }

    .order_footer {
        padding: 15px 20px;
        background: #fafafa;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .common_btn.btn_sm {
        padding: 8px 15px;
        font-size: 13px;
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

    .common_btn.btn_danger {
        background: #dc3545;
        border-color: #dc3545;
    }

    .common_btn.btn_danger:hover {
        background: #c82333;
    }

    .common_btn.btn_secondary {
        background: #6c757d;
        border-color: #6c757d;
    }

    .common_btn.btn_secondary:hover {
        background: #5a6268;
    }

    .empty_orders {
        background: #fff;
        padding: 60px 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .empty_icon {
        width: 100px;
        height: 100px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .empty_icon i {
        font-size: 40px;
        color: #ddd;
    }

    @media (max-width: 768px) {
        .order_body {
            flex-direction: column;
            align-items: flex-start;
        }

        .order_total {
            text-align: left;
            width: 100%;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .order_footer {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let cancelOrderId = null;

    // Cancel order modal
    document.querySelectorAll('.cancel-order-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            cancelOrderId = this.dataset.orderId;
            document.getElementById('cancelOrderInvoice').textContent = '#' + this.dataset.orderInvoice;
            new bootstrap.Modal(document.getElementById('cancelOrderModal')).show();
        });
    });

    // Confirm cancel
    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        if (!cancelOrderId) return;

        fetch(`/order/${cancelOrderId}/cancel`, {
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
                location.reload();
            } else {
                alert(data.message || 'Failed to cancel order');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    });

    // Reorder
    document.querySelectorAll('.reorder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';

            fetch(`/order/${orderId}/reorder`, {
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
                    button.innerHTML = '<i class="fas fa-redo me-1"></i> Reorder';
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-redo me-1"></i> Reorder';
            });
        });
    });
</script>
@endpush
