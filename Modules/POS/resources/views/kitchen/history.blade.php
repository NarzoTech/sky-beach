<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Kitchen History') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #1a1a2e;
            color: #eee;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .header {
            background: #16213e;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .order-row {
            background: #16213e;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 15px;
        }
        .order-row:hover {
            background: #1f2a47;
        }
        .badge-table {
            background: #e94560;
            padding: 5px 15px;
            border-radius: 20px;
        }
        .item-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .item-badge {
            background: rgba(255,255,255,0.1);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        .completed {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0"><i class="fas fa-history me-2"></i>{{ __('Completed Orders - Today') }}</h1>
            <div>
                <a href="{{ route('admin.kitchen.index') }}" class="btn btn-primary">
                    <i class="fas fa-tv me-1"></i>{{ __('Back to Display') }}
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        @forelse($orders as $order)
        <div class="order-row">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="mb-1">
                        <span class="completed"><i class="fas fa-check-circle me-2"></i></span>
                        Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                        @if($order->table)
                        <span class="badge-table ms-2">{{ $order->table->name }}</span>
                        @endif
                    </h5>
                    <small class="text-muted">
                        <i class="fas fa-user me-1"></i>{{ $order->waiter->name ?? 'N/A' }} |
                        <i class="fas fa-clock me-1"></i>{{ $order->created_at->format('H:i') }} -
                        {{ $order->updated_at->format('H:i') }}
                        ({{ $order->created_at->diffInMinutes($order->updated_at) }} min)
                    </small>
                </div>
                <div class="text-end">
                    <span class="badge bg-success">Completed</span>
                </div>
            </div>

            <div class="item-list">
                @foreach($order->details as $item)
                <span class="item-badge {{ $item->is_voided ? 'text-decoration-line-through text-muted' : '' }}">
                    {{ $item->quantity }}x {{ $item->menuItem->name ?? $item->service->name ?? 'Item' }}
                </span>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">{{ __('No completed orders today') }}</h4>
        </div>
        @endforelse
    </div>
</body>
</html>
