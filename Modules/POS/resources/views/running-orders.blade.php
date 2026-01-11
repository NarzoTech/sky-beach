@if($runningOrders->count() > 0)
<div class="row">
    @foreach($runningOrders as $order)
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="card h-100 running-order-card" style="cursor: pointer;" onclick="viewOrderDetails({{ $order->id }})">
            <div class="card-header bg-{{ $order->status == 'processing' ? 'warning' : 'primary' }} text-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">
                        <i class="fas fa-chair me-1"></i>
                        {{ $order->table->name ?? 'No Table' }}
                    </span>
                    <span class="badge bg-light text-dark">
                        #{{ $order->invoice }}
                    </span>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $order->created_at->diffForHumans() }}
                    </small>
                    <div class="d-flex gap-2">
                        <span class="badge bg-info" title="{{ __('Guests') }}">
                            <i class="fas fa-users me-1"></i>{{ $order->guest_count ?? 1 }}
                        </span>
                        <span class="badge bg-secondary" title="{{ __('Items') }}">
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
                        <span class="badge bg-success w-100 py-1">
                            <i class="fas fa-check-circle me-1"></i>{{ __('Ready') }}
                            <small class="opacity-75">(+{{ $overdueMinutes }} min)</small>
                        </span>
                    @else
                        <span class="badge bg-warning text-light w-100 py-1">
                            <i class="fas fa-fire me-1"></i>{{ $remainingMinutes }} {{ __('min remaining') }}
                        </span>
                    @endif
                </div>
                @endif

                <div class="order-items-preview" style="max-height: 80px; overflow: hidden;">
                    @foreach($order->details->take(3) as $detail)
                    <div class="d-flex justify-content-between small">
                        <span class="text-truncate" style="max-width: 150px;">
                            {{ $detail->quantity }}x {{ $detail->menuItem->name ?? ($detail->service->name ?? 'Item') }}
                        </span>
                        <span>{{ currency($detail->sub_total) }}</span>
                    </div>
                    @endforeach
                    @if($order->details->count() > 3)
                    <small class="text-muted">+{{ $order->details->count() - 3 }} more items...</small>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-light py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="fas fa-user me-1"></i>
                        {{ $order->customer->name ?? 'Guest' }}
                    </span>
                    <span class="fw-bold text-primary">
                        {{ currency($order->grand_total) }}
                    </span>
                </div>
                @if($order->waiter)
                <div class="text-muted small mt-1">
                    <i class="fas fa-user-tie me-1"></i>{{ __('Waiter') }}: {{ $order->waiter->name }}
                </div>
                @endif
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
    <p class="text-muted">{{ __('Active dine-in orders will appear here') }}</p>
</div>
@endif
