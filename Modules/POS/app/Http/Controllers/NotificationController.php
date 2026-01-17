<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\POS\app\Models\OrderNotification;

class NotificationController extends Controller
{
    /**
     * Get notifications for current waiter
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        if (!$employee) {
            return response()->json([]);
        }

        $notifications = OrderNotification::forWaiter($employee->id)
            ->with('sale.table')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        if (!$employee) {
            return response()->json(['count' => 0]);
        }

        $count = OrderNotification::forWaiter($employee->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get unread notifications
     */
    public function getUnread()
    {
        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        if (!$employee) {
            return response()->json([]);
        }

        $notifications = OrderNotification::forWaiter($employee->id)
            ->unread()
            ->with('sale.table')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markRead($id)
    {
        $notification = OrderNotification::findOrFail($id);

        // Verify ownership
        $admin = Auth::guard('admin')->user();
        if ($notification->waiter_id !== $admin->employee?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Waiter profile not found.',
            ], 400);
        }

        OrderNotification::forWaiter($employee->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete old notifications
     */
    public function clearOld()
    {
        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Waiter profile not found.',
            ], 400);
        }

        // Delete notifications older than 24 hours
        OrderNotification::forWaiter($employee->id)
            ->where('created_at', '<', now()->subDay())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Old notifications cleared.',
        ]);
    }
}
