@php
    $hasReadyItems = $order->details->where('kitchen_status', 'ready')->count() > 0;
    $orderAge = $order->created_at->diffInMinutes(now());
@endphp

<div class="order-card {{ $hasReadyItems ? 'has-ready' : '' }}" data-order-id="{{ $order->id }}">
    <div class="order-header">
        <div>
            <span class="order-number">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            @if($order->table)
            <span class="table-badge ms-2">{{ $order->table->name }}</span>
            @endif
        </div>
        <span class="order-timer {{ $orderAge < 10 ? 'timer-ok' : ($orderAge < 20 ? 'timer-warning' : 'timer-danger') }}"
              data-created-at="{{ $order->created_at->toIso8601String() }}">
            {{ $orderAge }} min
        </span>
    </div>

    <div class="order-meta">
        <span><i class="fas fa-user me-1"></i>{{ $order->waiter->name ?? 'N/A' }}</span>
        <span><i class="fas fa-users me-1"></i>{{ $order->guest_count ?? 1 }} guests</span>
        <span><i class="fas fa-clock me-1"></i>{{ $order->created_at->format('H:i') }}</span>
    </div>

    <div class="order-items">
        @foreach($order->details->where('is_voided', false) as $item)
        <div class="order-item {{ $item->kitchen_status }}" data-item-id="{{ $item->id }}"
             onclick="cycleStatus({{ $item->id }}, '{{ $item->kitchen_status }}')">
            <div class="item-qty">{{ $item->quantity }}x</div>
            <div class="item-details">
                <div class="item-name">{{ $item->menuItem->name ?? $item->service->name ?? 'Item' }}</div>
                @if($item->addons)
                    @php $addons = is_string($item->addons) ? json_decode($item->addons, true) : $item->addons; @endphp
                    @if(is_array($addons) && count($addons) > 0)
                    <div class="item-addons">
                        @foreach($addons as $addon)
                        <span>+ {{ $addon['name'] }}</span>@if(!$loop->last), @endif
                        @endforeach
                    </div>
                    @endif
                @endif
                @if($item->note)
                <div class="item-note"><i class="fas fa-sticky-note me-1"></i>{{ $item->note }}</div>
                @endif
            </div>
            <div class="item-status">
                <button class="status-btn {{ $item->kitchen_status }}"
                        onclick="event.stopPropagation(); cycleStatus({{ $item->id }}, '{{ $item->kitchen_status }}')">
                    {{ ucfirst($item->kitchen_status) }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($order->special_instructions)
    <div class="px-3 pb-2">
        <div class="alert alert-warning py-2 mb-0">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Note:</strong> {{ $order->special_instructions }}
        </div>
    </div>
    @endif

    <div class="order-footer">
        <button class="bump-btn bump-all" onclick="bumpOrder({{ $order->id }})">
            <i class="fas fa-check-double me-1"></i>BUMP ALL
        </button>
    </div>
</div>
