@extends('website::layouts.master')

@section('title', __('Order Confirmed') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Order Confirmed') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('Order Confirmed') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========ORDER SUCCESS START===========-->
        <section class="order_success pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="success_content text-center wow fadeInUp">
                            <div class="success_icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2>{{ __('Thank You for Your Order!') }}</h2>
                            <p class="mb-4">{{ __('Your order has been placed successfully. We will process it shortly.') }}</p>

                            <div class="order_info_box">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info_item">
                                            <label>{{ __('Order Number') }}</label>
                                            <strong>#{{ $order->invoice }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info_item">
                                            <label>{{ __('Order Date') }}</label>
                                            <strong>{{ $order->created_at->format('M d, Y h:i A') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info_item">
                                            <label>{{ __('Order Type') }}</label>
                                            <strong>
                                                @if($order->order_type === 'delivery')
                                                    <i class="fas fa-motorcycle me-1"></i>{{ __('Delivery') }}
                                                @else
                                                    <i class="fas fa-shopping-bag me-1"></i>{{ __('Take Away') }}
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info_item">
                                            <label>{{ __('Payment Method') }}</label>
                                            <strong>
                                                @if(is_array($order->payment_method) && in_array('bkash', $order->payment_method))
                                                    <span style="color: #e2136e;"><i class="fas fa-mobile-alt me-1"></i>{{ __('bKash') }}</span>
                                                @elseif(is_array($order->payment_method) && in_array('cash', $order->payment_method))
                                                    <i class="fas fa-money-bill-wave me-1"></i>{{ __('Cash on Delivery') }}
                                                @else
                                                    <i class="fas fa-credit-card me-1"></i>{{ __('Card Payment') }}
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    @if($order->payment_status === 'success' && $order->payment_details)
                                        @php
                                            $paymentDetails = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
                                        @endphp
                                        @if(isset($paymentDetails['transaction_id']))
                                        <div class="col-md-12">
                                            <div class="info_item">
                                                <label>{{ __('Transaction ID') }}</label>
                                                <strong style="color: #e2136e;">{{ $paymentDetails['transaction_id'] }}</strong>
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if($order->delivery_address)
                            <div class="delivery_info mt-4">
                                <h5><i class="fas fa-map-marker-alt me-2"></i>{{ __('Delivery Address') }}</h5>
                                <p>{{ $order->delivery_address }}</p>
                                @if($order->delivery_notes)
                                    <p class="text-muted"><small>{{ __('Instructions') }}: {{ $order->delivery_notes }}</small></p>
                                @endif
                            </div>
                            @endif

                            <div class="order_items_list mt-4">
                                <h5><i class="fas fa-utensils me-2"></i>{{ __('Order Items') }}</h5>
                                <div class="items_table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Item') }}</th>
                                                <th>{{ __('Qty') }}</th>
                                                <th>{{ __('Price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->details as $item)
                                            <tr>
                                                <td class="text-start">
                                                    {{ $item->menuItem->name ?? 'Item' }}
                                                    @if($item->addons)
                                                        <small class="d-block text-muted">
                                                            + {{ collect($item->addons)->pluck('name')->implode(', ') }}
                                                        </small>
                                                    @endif
                                                    @if($item->note)
                                                        <small class="d-block text-muted"><i class="fas fa-sticky-note"></i> {{ $item->note }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ currency($item->sub_total) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-end"><strong>{{ __('Subtotal') }}:</strong></td>
                                                <td><strong>{{ currency($order->total_price) }}</strong></td>
                                            </tr>
                                            @if($order->shipping_cost > 0)
                                            <tr>
                                                <td colspan="2" class="text-end">{{ __('Delivery Fee') }}:</td>
                                                <td>{{ currency($order->shipping_cost) }}</td>
                                            </tr>
                                            @endif
                                            @if($order->total_tax > 0)
                                            <tr>
                                                <td colspan="2" class="text-end">{{ __('Tax') }}:</td>
                                                <td>{{ currency($order->total_tax) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="total_row">
                                                <td colspan="2" class="text-end"><strong>{{ __('Total') }}:</strong></td>
                                                <td><strong>{{ currency($order->grand_total) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="status_timeline mt-4">
                                <h5><i class="fas fa-clock me-2"></i>{{ __('Order Status') }}</h5>
                                <div class="timeline">
                                    <div class="timeline_item {{ $order->status !== 'cancelled' ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-check"></i></div>
                                        <div class="timeline_text">{{ __('Order Placed') }}</div>
                                    </div>
                                    <div class="timeline_item {{ in_array($order->status, ['confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'completed']) ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-clipboard-check"></i></div>
                                        <div class="timeline_text">{{ __('Confirmed') }}</div>
                                    </div>
                                    <div class="timeline_item {{ in_array($order->status, ['preparing', 'ready', 'out_for_delivery', 'delivered', 'completed']) ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-fire"></i></div>
                                        <div class="timeline_text">{{ __('Preparing') }}</div>
                                    </div>
                                    @if($order->order_type === 'delivery')
                                    <div class="timeline_item {{ in_array($order->status, ['out_for_delivery', 'delivered']) ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-motorcycle"></i></div>
                                        <div class="timeline_text">{{ __('On The Way') }}</div>
                                    </div>
                                    <div class="timeline_item {{ $order->status === 'delivered' ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-home"></i></div>
                                        <div class="timeline_text">{{ __('Delivered') }}</div>
                                    </div>
                                    @else
                                    <div class="timeline_item {{ in_array($order->status, ['ready', 'completed']) ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-box"></i></div>
                                        <div class="timeline_text">{{ __('Ready') }}</div>
                                    </div>
                                    <div class="timeline_item {{ $order->status === 'completed' ? 'active' : '' }}">
                                        <div class="timeline_icon"><i class="fas fa-smile"></i></div>
                                        <div class="timeline_text">{{ __('Picked Up') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="action_buttons mt-5">
                                <a href="{{ route('website.menu') }}" class="common_btn me-3">
                                    <i class="fas fa-utensils me-2"></i>{{ __('Continue Shopping') }}
                                </a>
                                @auth
                                <a href="{{ route('website.index') }}" class="common_btn btn_outline">
                                    <i class="fas fa-list me-2"></i>{{ __('My Orders') }}
                                </a>
                                @endauth
                            </div>

                            <div class="contact_info mt-5">
                                <p class="text-muted">
                                    {{ __('Questions about your order?') }}
                                    <a href="{{ route('website.contact') }}">{{ __('Contact Us') }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========ORDER SUCCESS END===========-->
@endsection

@push('styles')
<style>
    .success_content {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.1);
    }

    .success_icon {
        width: 100px;
        height: 100px;
        background: var(--colorGreen, #0F9043);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .success_icon i {
        font-size: 50px;
        color: #fff;
    }

    .success_content h2 {
        color: #333;
        margin-bottom: 10px;
    }

    .order_info_box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        margin-top: 30px;
    }

    .info_item {
        padding: 10px 0;
    }

    .info_item label {
        display: block;
        color: #888;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .info_item strong {
        color: #333;
        font-size: 16px;
    }

    .delivery_info {
        background: rgba(171, 22, 44, 0.05);
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid var(--colorPrimary, #AB162C);
    }

    .delivery_info h5 {
        color: var(--colorPrimary, #AB162C);
        margin-bottom: 10px;
    }

    .order_items_list {
        text-align: left;
    }

    .order_items_list h5 {
        text-align: center;
        margin-bottom: 20px;
    }

    .items_table .table {
        margin-bottom: 0;
    }

    .items_table .table th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .items_table .total_row td {
        background: #f8f9fa;
        font-size: 18px;
    }

    .status_timeline {
        text-align: center;
    }

    .timeline {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .timeline_item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 20px;
        position: relative;
        opacity: 0.4;
    }

    .timeline_item.active {
        opacity: 1;
    }

    .timeline_item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 25px;
        right: -10px;
        width: 20px;
        height: 2px;
        background: #ddd;
    }

    .timeline_item.active:not(:last-child)::after {
        background: var(--colorGreen, #0F9043);
    }

    .timeline_icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .timeline_item.active .timeline_icon {
        background: var(--colorGreen, #0F9043);
        color: #fff;
    }

    .timeline_icon i {
        font-size: 18px;
    }

    .timeline_text {
        font-size: 12px;
        font-weight: 600;
        color: #666;
    }

    .common_btn.btn_outline {
        background: transparent;
        border: 2px solid var(--colorPrimary, #AB162C);
        color: var(--colorPrimary, #AB162C);
    }

    .common_btn.btn_outline:hover {
        background: var(--colorPrimary, #AB162C);
        color: #fff;
    }

    @media (max-width: 768px) {
        .success_content {
            padding: 20px;
        }

        .timeline_item {
            padding: 10px;
        }

        .timeline_item:not(:last-child)::after {
            display: none;
        }
    }
</style>
@endpush
