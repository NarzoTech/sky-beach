@if($runningOrders->count() > 0)
<div class="row g-3">
    @foreach($runningOrders as $order)
    @php
        $orderTypeConfig = match($order->order_type) {
            'dine_in' => ['icon' => 'bx-chair', 'class' => 'dine-in', 'label' => __('Dine-in')],
            'take_away' => ['icon' => 'bx-shopping-bag', 'class' => 'take-away', 'label' => __('Take Away')],
            default => ['icon' => 'bx-receipt', 'class' => 'dine-in', 'label' => __('Order')]
        };

        // Calculate grand_total if it's 0 or null
        $orderGrandTotal = $order->grand_total;
        if (empty($orderGrandTotal) || $orderGrandTotal == 0) {
            $subtotal = $order->details->sum('sub_total');
            $discount = $order->order_discount ?? 0;
            $tax = $order->total_tax ?? 0;
            $orderGrandTotal = $subtotal - $discount + $tax;

            // Fallback to total_price if still 0
            if ($orderGrandTotal == 0 && $order->total_price > 0) {
                $orderGrandTotal = $order->total_price - $discount + $tax;
            }
        }

        // Prep time calculation
        $isOverdue = false;
        $remainingMinutes = 0;
        $overdueMinutes = 0;
        if($order->estimated_prep_minutes) {
            $prepEndTime = $order->created_at->addMinutes($order->estimated_prep_minutes);
            $now = now();
            $remainingMinutes = $now->lt($prepEndTime) ? $now->diffInMinutes($prepEndTime, false) : 0;
            $isOverdue = $now->gt($prepEndTime);
            $overdueMinutes = $isOverdue ? $now->diffInMinutes($prepEndTime) : 0;
        }
    @endphp
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 running-order-col" data-search="{{ strtolower(($order->invoice ?? '') . ' ' . ($order->table->name ?? '') . ' ' . ($order->customer->name ?? 'guest') . ' ' . ($order->customer->phone ?? '') . ' ' . ($order->waiter->name ?? '')) }}">
        <div class="card h-100 running-order-card shadow-sm" onclick="viewOrderDetails({{ $order->id }})">
            <div class="card-body">
                {{-- Header Row --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="order-badge {{ $orderTypeConfig['class'] }}">
                        <i class="bx {{ $orderTypeConfig['icon'] }}"></i>
                        @if($order->order_type == 'dine_in' && $order->table)
                            {{ $order->table->name }}
                        @else
                            {{ $orderTypeConfig['label'] }}
                        @endif
                    </span>
                    <span class="order-invoice">#{{ $order->invoice }}</span>
                </div>

                {{-- Info Row --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="order-time">
                        <i class="bx bx-time-five"></i>{{ $order->created_at->diffForHumans() }}
                    </span>
                    <div class="d-flex gap-2">
                        <span class="order-meta-badge" title="{{ __('Guests') }}">
                            <i class="bx bx-group"></i>{{ $order->guest_count ?? 1 }}
                        </span>
                        <span class="order-meta-badge" title="{{ __('Items') }}">
                            <i class="bx bx-food-menu"></i>{{ $order->details->sum('quantity') }}
                        </span>
                    </div>
                </div>

                {{-- Status Badge --}}
                @if($order->estimated_prep_minutes)
                <div class="mb-3">
                    @if($isOverdue)
                        <span class="order-status ready w-100 d-block text-center">
                            <i class="bx bx-check-circle"></i> {{ __('Ready') }}
                            <small class="opacity-75 ms-1">(+{{ $overdueMinutes }} min)</small>
                        </span>
                    @else
                        <span class="order-status preparing w-100 d-block text-center">
                            <i class="bx bx-loader-alt bx-spin"></i> {{ $remainingMinutes }} {{ __('min remaining') }}
                        </span>
                    @endif
                </div>
                @endif

                {{-- Items Preview --}}
                <div class="order-items-preview">
                    @foreach($order->details->take(2) as $detail)
                    <div class="item-row">
                        <span class="item-name">
                            {{ $detail->quantity }}x {{ $detail->menuItem->name ?? ($detail->service->name ?? 'Item') }}
                        </span>
                        <span class="item-price">{{ currency($detail->sub_total) }}</span>
                    </div>
                    @endforeach
                    @if($order->details->count() > 2)
                    <small class="text-muted">+{{ $order->details->count() - 2 }} {{ __('more') }}...</small>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="order-customer">
                            <i class="bx bx-user"></i>{{ $order->customer->name ?? __('Guest') }}
                        </div>
                        @if($order->waiter)
                        <div class="order-customer">
                            <i class="bx bx-user-check"></i>{{ $order->waiter->name }}
                        </div>
                        @endif
                    </div>
                    <span class="order-total">{{ currency($orderGrandTotal) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($runningOrders->hasPages())
<div class="d-flex justify-content-center mt-4">
    <nav aria-label="Running orders pagination">
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous Page --}}
            <li class="page-item {{ $runningOrders->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="javascript:void(0)" onclick="loadRunningOrdersPage({{ $runningOrders->currentPage() - 1 }})" aria-label="Previous">
                    <i class="bx bx-chevron-left"></i>
                </a>
            </li>

            {{-- Page Numbers --}}
            @for($i = 1; $i <= $runningOrders->lastPage(); $i++)
                <li class="page-item {{ $runningOrders->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="javascript:void(0)" onclick="loadRunningOrdersPage({{ $i }})">{{ $i }}</a>
                </li>
            @endfor

            {{-- Next Page --}}
            <li class="page-item {{ !$runningOrders->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="javascript:void(0)" onclick="loadRunningOrdersPage({{ $runningOrders->currentPage() + 1 }})" aria-label="Next">
                    <i class="bx bx-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>
<div class="text-center mt-2">
    <small class="text-muted">
        {{ __('Showing') }} {{ $runningOrders->firstItem() }}-{{ $runningOrders->lastItem() }} {{ __('of') }} {{ $runningOrders->total() }} {{ __('orders') }}
    </small>
</div>
@endif
@else
<div class="running-orders-empty">
    <div class="empty-icon">
        <i class="bx bx-food-menu"></i>
    </div>
    <h5>{{ __('No Running Orders') }}</h5>
    <p>{{ __('Active dine-in & takeaway orders will appear here') }}</p>
</div>
@endif
