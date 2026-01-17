<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Kitchen Display') }}</title>

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
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .order-card {
            background: #16213e;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .order-card.has-ready {
            border: 3px solid #28a745;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        }
        .order-header {
            background: #0f3460;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-number {
            font-size: 1.3rem;
            font-weight: bold;
        }
        .table-badge {
            background: #e94560;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .order-meta {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            background: rgba(255,255,255,0.05);
            font-size: 0.85rem;
        }
        .order-items {
            padding: 15px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .order-item:hover {
            background: rgba(255,255,255,0.1);
        }
        .order-item.pending {
            border-left: 4px solid #ffc107;
        }
        .order-item.preparing {
            border-left: 4px solid #17a2b8;
            background: rgba(23, 162, 184, 0.1);
        }
        .order-item.ready {
            border-left: 4px solid #28a745;
            background: rgba(40, 167, 69, 0.15);
        }
        .order-item.served {
            border-left: 4px solid #6c757d;
            opacity: 0.6;
        }
        .item-qty {
            font-size: 1.5rem;
            font-weight: bold;
            min-width: 50px;
            text-align: center;
        }
        .item-details {
            flex: 1;
            padding: 0 15px;
        }
        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
        }
        .item-addons {
            font-size: 0.85rem;
            color: #aaa;
            margin-top: 5px;
        }
        .item-note {
            font-size: 0.85rem;
            color: #ffc107;
            margin-top: 5px;
            font-style: italic;
        }
        .item-status {
            min-width: 80px;
            text-align: center;
        }
        .status-btn {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        .status-btn.pending { background: #ffc107; color: #000; }
        .status-btn.preparing { background: #17a2b8; color: #fff; }
        .status-btn.ready { background: #28a745; color: #fff; }
        .status-btn.served { background: #6c757d; color: #fff; }
        .status-btn:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
        .order-footer {
            padding: 15px;
            background: rgba(255,255,255,0.05);
            display: flex;
            gap: 10px;
        }
        .bump-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .bump-btn.bump-all {
            background: #28a745;
            color: white;
        }
        .bump-btn.bump-all:hover {
            background: #218838;
        }
        .order-timer {
            font-size: 0.9rem;
            padding: 3px 10px;
            border-radius: 15px;
        }
        .timer-ok { background: #28a745; }
        .timer-warning { background: #ffc107; color: #000; }
        .timer-danger { background: #dc3545; animation: blink 1s infinite; }
        @keyframes blink {
            50% { opacity: 0.7; }
        }
        .no-orders {
            text-align: center;
            padding: 100px 20px;
        }
        .no-orders i {
            font-size: 5rem;
            color: #333;
            margin-bottom: 20px;
        }
        .refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            display: none;
        }
        .audio-alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-utensils me-2"></i>{{ __('Kitchen Display System') }}</h1>
            <div>
                <span class="badge bg-success me-2" id="orders-count">{{ $orders->count() }} Orders</span>
                <span class="text-muted" id="current-time"></span>
                <a href="{{ route('admin.kitchen.history') }}" class="btn btn-outline-light btn-sm ms-3">
                    <i class="fas fa-history"></i> History
                </a>
            </div>
        </div>
    </div>

    <div class="orders-grid" id="orders-container">
        @forelse($orders as $order)
        @include('pos::kitchen.partials.order-card', ['order' => $order])
        @empty
        <div class="no-orders col-12">
            <i class="fas fa-check-circle"></i>
            <h3>{{ __('All caught up!') }}</h3>
            <p class="text-muted">{{ __('No pending orders at the moment.') }}</p>
        </div>
        @endforelse
    </div>

    <div class="refresh-indicator" id="refresh-indicator">
        <i class="fas fa-sync-alt fa-spin me-2"></i>Refreshing...
    </div>

    <audio id="new-order-sound" class="audio-alert">
        <source src="{{ asset('sounds/new-order.mp3') }}" type="audio/mpeg">
    </audio>
    <audio id="item-ready-sound" class="audio-alert">
        <source src="{{ asset('sounds/item-ready.mp3') }}" type="audio/mpeg">
    </audio>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let previousOrderCount = {{ $orders->count() }};

        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Calculate order duration
        function getOrderDuration(createdAt) {
            const created = new Date(createdAt);
            const now = new Date();
            const diff = Math.floor((now - created) / 1000 / 60); // minutes
            return diff;
        }

        // Update timers
        function updateTimers() {
            document.querySelectorAll('.order-timer').forEach(timer => {
                const createdAt = timer.dataset.createdAt;
                const minutes = getOrderDuration(createdAt);
                timer.textContent = minutes + ' min';

                timer.classList.remove('timer-ok', 'timer-warning', 'timer-danger');
                if (minutes < 10) {
                    timer.classList.add('timer-ok');
                } else if (minutes < 20) {
                    timer.classList.add('timer-warning');
                } else {
                    timer.classList.add('timer-danger');
                }
            });
        }
        setInterval(updateTimers, 30000);

        // Refresh orders
        function refreshOrders() {
            document.getElementById('refresh-indicator').style.display = 'block';

            $.ajax({
                url: "{{ route('admin.kitchen.orders') }}",
                method: 'GET',
                success: function(orders) {
                    // Check for new orders
                    if (orders.length > previousOrderCount) {
                        playSound('new-order-sound');
                    }
                    previousOrderCount = orders.length;

                    document.getElementById('orders-count').textContent = orders.length + ' Orders';

                    if (orders.length === 0) {
                        document.getElementById('orders-container').innerHTML = `
                            <div class="no-orders col-12">
                                <i class="fas fa-check-circle"></i>
                                <h3>{{ __('All caught up!') }}</h3>
                                <p class="text-muted">{{ __('No pending orders at the moment.') }}</p>
                            </div>
                        `;
                    } else {
                        // Re-render would require server-side partial rendering
                        // For now, full page reload every 30 seconds
                    }

                    document.getElementById('refresh-indicator').style.display = 'none';
                },
                error: function() {
                    document.getElementById('refresh-indicator').style.display = 'none';
                }
            });
        }
        setInterval(refreshOrders, 15000);

        // Update item status
        function updateItemStatus(itemId, status) {
            $.ajax({
                url: "{{ url('admin/kitchen/item') }}/" + itemId + "/status",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { status: status },
                success: function(response) {
                    if (response.success) {
                        const btn = document.querySelector(`[data-item-id="${itemId}"] .status-btn`);
                        if (btn) {
                            btn.className = 'status-btn ' + status;
                            btn.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                        }

                        const item = document.querySelector(`[data-item-id="${itemId}"]`);
                        if (item) {
                            item.className = 'order-item ' + status;
                        }

                        if (status === 'ready') {
                            playSound('item-ready-sound');
                        }

                        if (status === 'served') {
                            setTimeout(() => location.reload(), 500);
                        }
                    }
                }
            });
        }

        // Cycle through statuses on click
        function cycleStatus(itemId, currentStatus) {
            const statusOrder = ['pending', 'preparing', 'ready', 'served'];
            const currentIndex = statusOrder.indexOf(currentStatus);
            const nextStatus = statusOrder[(currentIndex + 1) % statusOrder.length];
            updateItemStatus(itemId, nextStatus);
        }

        // Bump entire order
        function bumpOrder(orderId) {
            $.ajax({
                url: "{{ url('admin/kitchen/order') }}/" + orderId + "/bump",
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        playSound('item-ready-sound');
                        setTimeout(() => location.reload(), 500);
                    }
                }
            });
        }

        // Play sound
        function playSound(soundId) {
            const sound = document.getElementById(soundId);
            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(() => {});
            }
        }

        // Auto-reload page every 2 minutes
        setTimeout(() => location.reload(), 120000);
    </script>
</body>
</html>
