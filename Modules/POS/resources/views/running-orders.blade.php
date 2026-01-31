@if($runningOrders->count() > 0)
<div class="row">
    @foreach($runningOrders as $order)
    @php
        $orderTypeConfig = match($order->order_type) {
            'dine_in' => ['icon' => 'fa-chair', 'color' => 'primary', 'label' => __('Dine-in')],
            'take_away' => ['icon' => 'fa-shopping-bag', 'color' => 'success', 'label' => __('Take Away')],
            'delivery' => ['icon' => 'fa-motorcycle', 'color' => 'info', 'label' => __('Delivery')],
            default => ['icon' => 'fa-receipt', 'color' => 'secondary', 'label' => __('Order')]
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
    @endphp
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card h-100 running-order-card border-0 shadow-sm" style="cursor: pointer; border-radius: 8px; overflow: hidden;" onclick="viewOrderDetails({{ $order->id }})">
            <div class="card-body p-3">
                {{-- Header Row --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge" style="background: #47c363; color: #fff; font-size: 12px; padding: 6px 10px; border-radius: 4px;">
                        <i class="fas {{ $orderTypeConfig['icon'] }} me-1"></i>
                        @if($order->order_type == 'dine_in' && $order->table)
                            {{ $order->table->name }}
                        @else
                            {{ $orderTypeConfig['label'] }}
                        @endif
                    </span>
                    <span class="text-muted small fw-bold">#{{ $order->invoice }}</span>
                </div>

                {{-- Info Row --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>{{ $order->created_at->diffForHumans() }}
                    </small>
                    <div class="d-flex gap-1">
                        <span class="badge bg-light text-dark" style="font-size: 11px;" title="{{ __('Guests') }}">
                            <i class="fas fa-users me-1"></i>{{ $order->guest_count ?? 1 }}
                        </span>
                        <span class="badge bg-light text-dark" style="font-size: 11px;" title="{{ __('Items') }}">
                            <i class="fas fa-utensils me-1"></i>{{ $order->details->sum('quantity') }}
                        </span>
                    </div>
                </div>

                @if($order->estimated_prep_minutes)
                @php
                    $prepEndTime = $order->created_at->addMinutes($order->estimated_prep_minutes);
                    $now = now();
                    $remainingMinutes = $now->lt($prepEndTime) ? $now->diffInMinutes($prepEndTime, false) : 0;
                    $isOverdue = $now->gt($prepEndTime);
                    $overdueMinutes = $isOverdue ? $now->diffInMinutes($prepEndTime) : 0;
                @endphp
                <div class="mb-2">
                    @if($isOverdue)
                        <span class="badge bg-success w-100 py-1" style="font-size: 11px;">
                            <i class="fas fa-check-circle me-1"></i>{{ __('Ready') }}
                            <small class="opacity-75">(+{{ $overdueMinutes }} min)</small>
                        </span>
                    @else
                        <span class="badge bg-warning text-dark w-100 py-1" style="font-size: 11px;">
                            <i class="fas fa-fire me-1"></i>{{ $remainingMinutes }} {{ __('min remaining') }}
                        </span>
                    @endif
                </div>
                @endif

                {{-- Items Preview --}}
                <div class="order-items-preview bg-light p-2 rounded" style="max-height: 70px; overflow: hidden; font-size: 12px;">
                    @foreach($order->details->take(2) as $detail)
                    <div class="d-flex justify-content-between">
                        <span class="text-truncate" style="max-width: 140px;">
                            {{ $detail->quantity }}x {{ $detail->menuItem->name ?? ($detail->service->name ?? 'Item') }}
                        </span>
                        <span class="fw-bold">{{ currency($detail->sub_total) }}</span>
                    </div>
                    @endforeach
                    @if($order->details->count() > 2)
                    <small class="text-muted">+{{ $order->details->count() - 2 }} {{ __('more') }}...</small>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-white border-top py-2 px-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">
                            <i class="fas fa-user me-1"></i>{{ $order->customer->name ?? 'Guest' }}
                        </small>
                        @if($order->waiter)
                        <small class="text-muted">
                            <i class="fas fa-user-tie me-1"></i>{{ $order->waiter->name }}
                        </small>
                        @endif
                    </div>
                    <span class="fw-bold" style="color: #47c363; font-size: 16px;">
                        {{ currency($orderGrandTotal) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($runningOrders->hasPages())
<div class="d-flex justify-content-center mt-3">
    <nav aria-label="Running orders pagination">
        <ul class="pagination pagination-sm mb-0">
            {{-- Previous Page --}}
            <li class="page-item {{ $runningOrders->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="javascript:void(0)" onclick="loadRunningOrdersPage({{ $runningOrders->currentPage() - 1 }})" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
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
                    <i class="fas fa-chevron-right"></i>
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
<div class="text-center py-5">
    <i class="fas fa-utensils fa-4x text-muted mb-3"></i>
    <h5 class="text-muted">{{ __('No Running Orders') }}</h5>
    <p class="text-muted">{{ __('Active orders (Dine-in, Take Away, Delivery) will appear here') }}</p>
</div>
@endif
