@extends('website::layouts.master')

@section('title', __('Track Order') . ' #' . $order->invoice . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Track Order') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="{{ route('website.orders.index') }}">{{ __('My Orders') }}</a></li>
                                <li><a href="#">{{ __('Track') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========TRACK ORDER START===========-->
        <section class="track_order pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="tracking_card wow fadeInUp">
                            <!-- Order Info Header -->
                            <div class="tracking_header">
                                <div class="order_icon">
                                    @if($order->order_type === 'delivery')
                                        <i class="fas fa-motorcycle"></i>
                                    @else
                                        <i class="fas fa-shopping-bag"></i>
                                    @endif
                                </div>
                                <div class="order_info">
                                    <h4>{{ __('Order') }} #{{ $order->invoice }}</h4>
                                    <p class="mb-0">
                                        @if($order->order_type === 'delivery')
                                            {{ __('Delivery Order') }}
                                        @else
                                            {{ __('Take Away Order') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="order_status">
                                    <span class="badge {{ $order->status_badge_class }}" id="statusBadge">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Live Status -->
                            <div class="live_status" id="liveStatus">
                                @if($order->status === 'cancelled')
                                    <div class="status_cancelled">
                                        <i class="fas fa-times-circle"></i>
                                        <h5>{{ __('Order Cancelled') }}</h5>
                                        <p>{{ __('This order has been cancelled.') }}</p>
                                    </div>
                                @elseif(in_array($order->status, ['delivered', 'completed']))
                                    <div class="status_completed">
                                        <i class="fas fa-check-circle"></i>
                                        <h5>{{ $order->status === 'delivered' ? __('Order Delivered') : __('Order Completed') }}</h5>
                                        <p>{{ __('Thank you for your order!') }}</p>
                                    </div>
                                @else
                                    <div class="status_active">
                                        <div class="pulse_animation">
                                            <span></span>
                                        </div>
                                        <h5 id="currentStatusText">
                                            @switch($order->status)
                                                @case('pending')
                                                    {{ __('Order Received') }}
                                                    @break
                                                @case('confirmed')
                                                    {{ __('Order Confirmed') }}
                                                    @break
                                                @case('preparing')
                                                    {{ __('Preparing Your Order') }}
                                                    @break
                                                @case('ready')
                                                    {{ __('Order is Ready') }}
                                                    @break
                                                @case('out_for_delivery')
                                                    {{ __('On The Way') }}
                                                    @break
                                            @endswitch
                                        </h5>
                                        <p id="statusDescription">
                                            @switch($order->status)
                                                @case('pending')
                                                    {{ __("We've received your order and will confirm it shortly.") }}
                                                    @break
                                                @case('confirmed')
                                                    {{ __('Your order has been confirmed and will be prepared soon.') }}
                                                    @break
                                                @case('preparing')
                                                    {{ __('Our kitchen is preparing your delicious food.') }}
                                                    @break
                                                @case('ready')
                                                    @if($order->order_type === 'delivery')
                                                        {{ __('Your order is ready and waiting for a delivery rider.') }}
                                                    @else
                                                        {{ __('Your order is ready for pickup!') }}
                                                    @endif
                                                    @break
                                                @case('out_for_delivery')
                                                    {{ __('Your order is on its way to you.') }}
                                                    @break
                                            @endswitch
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Progress Timeline -->
                            <div class="tracking_timeline">
                                @php
                                    $allStatuses = [
                                        'pending' => ['icon' => 'fas fa-check', 'label' => __('Order Placed')],
                                        'confirmed' => ['icon' => 'fas fa-clipboard-check', 'label' => __('Confirmed')],
                                        'preparing' => ['icon' => 'fas fa-fire', 'label' => __('Preparing')],
                                        'ready' => ['icon' => 'fas fa-box', 'label' => __('Ready')],
                                    ];

                                    if ($order->order_type === 'delivery') {
                                        $allStatuses['out_for_delivery'] = ['icon' => 'fas fa-motorcycle', 'label' => __('On The Way')];
                                        $allStatuses['delivered'] = ['icon' => 'fas fa-home', 'label' => __('Delivered')];
                                    } else {
                                        $allStatuses['completed'] = ['icon' => 'fas fa-smile', 'label' => __('Picked Up')];
                                    }

                                    $statusKeys = array_keys($allStatuses);
                                    $currentIndex = array_search($order->status, $statusKeys);
                                    if ($order->status === 'cancelled') $currentIndex = -1;
                                @endphp

                                <div class="timeline_vertical">
                                    @foreach($allStatuses as $key => $status)
                                        @php
                                            $stepIndex = array_search($key, $statusKeys);
                                            $isActive = $currentIndex !== false && $stepIndex <= $currentIndex;
                                            $isCurrent = $order->status === $key;
                                        @endphp
                                        <div class="timeline_step {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}"
                                             data-status="{{ $key }}">
                                            <div class="step_icon">
                                                <i class="{{ $status['icon'] }}"></i>
                                            </div>
                                            <div class="step_content">
                                                <h6>{{ $status['label'] }}</h6>
                                                @if($isCurrent && !in_array($order->status, ['delivered', 'completed', 'cancelled']))
                                                    <span class="step_time">{{ __('In Progress') }}</span>
                                                @elseif($isActive)
                                                    <span class="step_time">{{ __('Completed') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Order Details Summary -->
                            <div class="tracking_details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail_item">
                                            <label>{{ __('Order Date') }}</label>
                                            <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        @if($order->order_type === 'delivery' && $order->delivery_address)
                                            <div class="detail_item">
                                                <label>{{ __('Delivery Address') }}</label>
                                                <span>{{ $order->delivery_address }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail_item">
                                            <label>{{ __('Items') }}</label>
                                            <span>{{ $order->details->sum('quantity') }} {{ __('item(s)') }}</span>
                                        </div>
                                        <div class="detail_item">
                                            <label>{{ __('Total') }}</label>
                                            <span class="total_amount">${{ number_format($order->grand_total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="tracking_actions">
                                <a href="{{ route('website.orders.show', $order->id) }}" class="common_btn">
                                    <i class="fas fa-eye me-2"></i>{{ __('View Full Details') }}
                                </a>
                                <a href="{{ route('website.contact') }}" class="common_btn btn_outline">
                                    <i class="fas fa-headset me-2"></i>{{ __('Need Help?') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========TRACK ORDER END===========-->
@endsection

@push('styles')
<style>
    .tracking_card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .tracking_header {
        display: flex;
        align-items: center;
        padding: 25px 30px;
        background: linear-gradient(135deg, #ff6b35, #f54749);
        color: #fff;
        gap: 20px;
    }

    .order_icon {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .order_icon i {
        font-size: 24px;
    }

    .order_info {
        flex: 1;
    }

    .order_info h4 {
        margin-bottom: 5px;
        font-weight: 700;
    }

    .order_status .badge {
        padding: 8px 20px;
        font-size: 14px;
        border-radius: 20px;
    }

    .live_status {
        padding: 40px 30px;
        text-align: center;
        background: #f8f9fa;
    }

    .status_active .pulse_animation {
        width: 80px;
        height: 80px;
        background: #ff6b35;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        position: relative;
    }

    .status_active .pulse_animation span {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: inherit;
        position: absolute;
        animation: pulse 1.5s ease-out infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    .status_active .pulse_animation::before {
        content: '\f0f4';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 30px;
        color: #fff;
        position: relative;
        z-index: 1;
    }

    .status_completed {
        color: #28a745;
    }

    .status_completed i, .status_cancelled i {
        font-size: 60px;
        margin-bottom: 15px;
    }

    .status_cancelled {
        color: #dc3545;
    }

    .live_status h5 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .live_status p {
        color: #666;
        margin-bottom: 0;
    }

    .tracking_timeline {
        padding: 30px;
    }

    .timeline_vertical {
        position: relative;
        padding-left: 40px;
    }

    .timeline_vertical::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #e0e0e0;
    }

    .timeline_step {
        position: relative;
        padding: 15px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .step_icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        left: -40px;
        z-index: 1;
    }

    .step_icon i {
        font-size: 14px;
        color: #999;
    }

    .timeline_step.active .step_icon {
        background: #71dd37;
    }

    .timeline_step.active .step_icon i {
        color: #fff;
    }

    .timeline_step.current .step_icon {
        background: #ff6b35;
        box-shadow: 0 0 0 5px rgba(255, 107, 53, 0.3);
    }

    .step_content h6 {
        margin-bottom: 3px;
        font-weight: 600;
        color: #888;
    }

    .timeline_step.active .step_content h6 {
        color: #333;
    }

    .step_time {
        font-size: 12px;
        color: #999;
    }

    .timeline_step.current .step_time {
        color: #ff6b35;
        font-weight: 600;
    }

    .tracking_details {
        padding: 25px 30px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }

    .detail_item {
        margin-bottom: 15px;
    }

    .detail_item:last-child {
        margin-bottom: 0;
    }

    .detail_item label {
        display: block;
        font-size: 13px;
        color: #888;
        margin-bottom: 5px;
    }

    .detail_item span {
        font-weight: 600;
        color: #333;
    }

    .total_amount {
        font-size: 20px;
        color: #ff6b35 !important;
    }

    .tracking_actions {
        padding: 25px 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
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

    @media (max-width: 768px) {
        .tracking_header {
            flex-wrap: wrap;
            text-align: center;
            justify-content: center;
        }

        .order_info {
            width: 100%;
            text-align: center;
        }

        .tracking_actions {
            flex-direction: column;
        }

        .tracking_actions .common_btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Poll for status updates every 30 seconds
    @if(!in_array($order->status, ['delivered', 'completed', 'cancelled']))
    setInterval(function() {
        fetch(`/order/{{ $order->id }}/status`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status !== '{{ $order->status }}') {
                // Status changed, reload the page
                location.reload();
            }
        })
        .catch(error => console.log('Status check failed'));
    }, 30000);
    @endif
</script>
@endpush
