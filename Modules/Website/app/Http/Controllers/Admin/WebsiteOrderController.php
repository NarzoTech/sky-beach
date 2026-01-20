<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sales\app\Models\Sale;

class WebsiteOrderController extends Controller
{
    /**
     * Display list of website orders
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'details.menuItem'])
            ->websiteOrders()
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by order type (delivery/take_away)
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        // Search by invoice or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get statistics
        $stats = [
            'total' => Sale::websiteOrders()->count(),
            'pending' => Sale::websiteOrders()->where('status', 'pending')->count(),
            'confirmed' => Sale::websiteOrders()->where('status', 'confirmed')->count(),
            'preparing' => Sale::websiteOrders()->where('status', 'preparing')->count(),
            'completed' => Sale::websiteOrders()->whereIn('status', ['delivered', 'completed'])->count(),
        ];

        return view('website::admin.website-orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details
     */
    public function show($id)
    {
        $order = Sale::with(['customer', 'details.menuItem', 'createdBy'])
            ->websiteOrders()
            ->findOrFail($id);

        return view('website::admin.website-orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,completed,cancelled',
            'staff_note' => 'nullable|string|max:500',
        ]);

        $order = Sale::websiteOrders()->findOrFail($id);

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->filled('staff_note')) {
            $updateData['staff_note'] = $request->staff_note;
        }

        // Update payment status if order is delivered/completed
        if (in_array($request->status, ['delivered', 'completed']) && $order->payment_status !== 'paid') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Order status updated successfully.'),
                'status' => $order->status,
                'status_label' => $order->status_label,
                'status_badge_class' => $order->status_badge_class,
            ]);
        }

        return redirect()->back()->with('success', __('Order status updated successfully.'));
    }

    /**
     * Print order (printer-friendly view)
     */
    public function printOrder($id)
    {
        $order = Sale::with(['customer', 'details.menuItem'])
            ->websiteOrders()
            ->findOrFail($id);

        return view('website::admin.website-orders.print', compact('order'));
    }

    /**
     * Bulk update order statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:sales,id',
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,completed,cancelled',
        ]);

        $count = Sale::websiteOrders()
            ->whereIn('id', $request->order_ids)
            ->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __(':count order(s) updated successfully.', ['count' => $count]),
            ]);
        }

        return redirect()->back()->with('success', __(':count order(s) updated successfully.', ['count' => $count]));
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Sale::with(['customer', 'details.menuItem'])
            ->websiteOrders()
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        $orders = $query->get();

        $filename = 'website_orders_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Order #',
                'Date',
                'Customer',
                'Email',
                'Phone',
                'Order Type',
                'Status',
                'Items',
                'Subtotal',
                'Delivery Fee',
                'Tax',
                'Grand Total',
                'Payment Method',
                'Payment Status',
                'Delivery Address',
            ]);

            foreach ($orders as $order) {
                $notes = json_decode($order->notes ?? '{}', true);
                $items = $order->details->map(fn($d) => ($d->menuItem->name ?? 'Item') . ' x' . $d->quantity)->implode(', ');

                fputcsv($file, [
                    $order->invoice,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $notes['customer_name'] ?? ($order->customer->name ?? 'Guest'),
                    $notes['customer_email'] ?? ($order->customer->email ?? ''),
                    $notes['customer_phone'] ?? $order->delivery_phone,
                    ucfirst(str_replace('_', ' ', $order->order_type)),
                    ucfirst($order->status),
                    $items,
                    $order->total_price,
                    $order->shipping_cost,
                    $order->total_tax,
                    $order->grand_total,
                    is_array($order->payment_method) ? implode(', ', $order->payment_method) : $order->payment_method,
                    ucfirst($order->payment_status),
                    $order->delivery_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
