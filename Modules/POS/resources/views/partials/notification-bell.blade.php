{{-- Notification Bell Component - Include in waiter dashboard --}}
<div class="notification-bell position-relative" id="notification-bell">
    <button class="btn btn-link text-dark position-relative p-2" onclick="toggleNotifications()" type="button">
        <i class="fas fa-bell fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-count" style="display: none;">
            0
        </span>
    </button>

    <div class="notification-dropdown card shadow-lg position-absolute end-0" id="notification-dropdown" style="display: none; width: 350px; max-height: 450px; z-index: 1050; top: 100%;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
            <span><i class="fas fa-bell me-2"></i>{{ __('Notifications') }}</span>
            <button class="btn btn-sm btn-light" onclick="markAllAsRead()">{{ __('Mark all read') }}</button>
        </div>
        <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;" id="notification-list">
            <div class="text-center py-4 text-muted">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <p class="mb-0">{{ __('No new notifications') }}</p>
            </div>
        </div>
        <div class="card-footer py-2 text-center">
            <small class="text-muted">{{ __('Auto-refreshes every 10 seconds') }}</small>
        </div>
    </div>
</div>

<style>
    .notification-bell {
        display: inline-block;
    }
    .notification-dropdown {
        border-radius: 10px;
        overflow: hidden;
    }
    .notification-item {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background 0.2s;
    }
    .notification-item:hover {
        background: #f8f9fa;
    }
    .notification-item.unread {
        background: #e7f3ff;
        border-left: 3px solid #007bff;
    }
    .notification-item.unread:hover {
        background: #d0e7ff;
    }
    .notification-item .type-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    .notification-item .type-icon.ready {
        background: #d4edda;
        color: #28a745;
    }
    .notification-item .type-icon.transfer {
        background: #fff3cd;
        color: #ffc107;
    }
    .notification-item .type-icon.cancelled {
        background: #f8d7da;
        color: #dc3545;
    }
    .notification-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .notification-count {
        font-size: 0.65rem;
        animation: pulse-badge 2s infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.1); }
    }
</style>

<script>
(function() {
    let notificationDropdownOpen = false;
    let notificationPollInterval = null;

    window.toggleNotifications = function() {
        const dropdown = document.getElementById('notification-dropdown');
        notificationDropdownOpen = !notificationDropdownOpen;
        dropdown.style.display = notificationDropdownOpen ? 'block' : 'none';

        if (notificationDropdownOpen) {
            fetchNotifications();
        }
    };

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const bell = document.getElementById('notification-bell');
        if (bell && !bell.contains(e.target)) {
            document.getElementById('notification-dropdown').style.display = 'none';
            notificationDropdownOpen = false;
        }
    });

    function fetchNotifications() {
        $.ajax({
            url: "{{ route('admin.notifications.unread') }}",
            method: 'GET',
            success: function(notifications) {
                updateNotificationUI(notifications);
            }
        });
    }

    function fetchUnreadCount() {
        $.ajax({
            url: "{{ route('admin.notifications.count') }}",
            method: 'GET',
            success: function(response) {
                const badge = document.querySelector('.notification-count');
                if (response.count > 0) {
                    badge.textContent = response.count > 99 ? '99+' : response.count;
                    badge.style.display = 'inline';
                    // Play sound for new notifications
                    playNotificationSound();
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }

    function updateNotificationUI(notifications) {
        const list = document.getElementById('notification-list');
        const badge = document.querySelector('.notification-count');

        if (notifications.length === 0) {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="mb-0">{{ __('No new notifications') }}</p>
                </div>
            `;
            badge.style.display = 'none';
            return;
        }

        badge.textContent = notifications.length > 99 ? '99+' : notifications.length;
        badge.style.display = 'inline';

        let html = '';
        notifications.forEach(function(notification) {
            const iconClass = notification.type === 'order_ready' || notification.type === 'item_ready' ? 'ready' :
                              notification.type === 'table_transfer' ? 'transfer' : 'cancelled';
            const icon = notification.type === 'order_ready' || notification.type === 'item_ready' ? 'fa-check-circle' :
                         notification.type === 'table_transfer' ? 'fa-exchange-alt' : 'fa-times-circle';

            const time = new Date(notification.created_at);
            const timeStr = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            html += `
                <div class="notification-item ${notification.is_read ? '' : 'unread'}"
                     onclick="handleNotificationClick(${notification.id}, ${notification.sale_id})">
                    <div class="d-flex align-items-start">
                        <div class="type-icon ${iconClass}">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">${notification.message}</div>
                            <div class="notification-time">
                                <i class="fas fa-clock me-1"></i>${timeStr}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        list.innerHTML = html;
    }

    window.handleNotificationClick = function(notificationId, saleId) {
        // Mark as read
        $.ajax({
            url: "{{ url('admin/notifications') }}/" + notificationId + "/read",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        // Navigate to order
        window.location.href = "{{ url('admin/waiter/order') }}/" + saleId;
    };

    window.markAllAsRead = function() {
        $.ajax({
            url: "{{ route('admin.notifications.read-all') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function() {
                fetchNotifications();
            }
        });
    };

    function playNotificationSound() {
        // Create or reuse audio element
        let audio = document.getElementById('notification-sound');
        if (!audio) {
            audio = document.createElement('audio');
            audio.id = 'notification-sound';
            audio.src = "{{ asset('sounds/notification.mp3') }}";
            document.body.appendChild(audio);
        }
        audio.play().catch(() => {});
    }

    // Start polling
    function startPolling() {
        fetchUnreadCount();
        notificationPollInterval = setInterval(fetchUnreadCount, 10000); // Every 10 seconds
    }

    // Initialize
    $(document).ready(function() {
        startPolling();
    });
})();
</script>
