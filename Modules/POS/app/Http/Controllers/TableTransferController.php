<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\POS\app\Models\OrderNotification;
use Modules\Sales\app\Models\Sale;
use Modules\TableManagement\app\Models\RestaurantTable;

class TableTransferController extends Controller
{
    /**
     * Show table transfer modal data
     */
    public function getTransferData($orderId)
    {
        $order = Sale::with('table')->findOrFail($orderId);

        $availableTables = RestaurantTable::where('status', 'available')
            ->orWhere(function ($query) use ($order) {
                // Include partially occupied tables
                $query->where('status', 'occupied')
                      ->whereRaw('occupied_seats < capacity');
            })
            ->where('id', '!=', $order->table_id)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'order' => $order,
            'current_table' => $order->table,
            'available_tables' => $availableTables,
        ]);
    }

    /**
     * Transfer order to another table
     */
    public function transfer(Request $request, $orderId)
    {
        $validated = $request->validate([
            'new_table_id' => 'required|exists:restaurant_tables,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $order = Sale::with('table')->findOrFail($orderId);
        $newTable = RestaurantTable::findOrFail($validated['new_table_id']);
        $oldTable = $order->table;

        // Validate new table has capacity
        if ($newTable->status === 'occupied') {
            $availableSeats = $newTable->capacity - $newTable->occupied_seats;
            if ($availableSeats < $order->guest_count) {
                return response()->json([
                    'success' => false,
                    'message' => 'New table does not have enough available seats.',
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Store original table if first transfer
            if (!$order->original_table_id) {
                $order->original_table_id = $order->table_id;
            }

            // Build transfer log
            $transferLog = $order->table_transfer_log ?? [];
            $transferLog[] = [
                'from_table_id' => $oldTable->id,
                'from_table_name' => $oldTable->name,
                'to_table_id' => $newTable->id,
                'to_table_name' => $newTable->name,
                'reason' => $validated['reason'] ?? null,
                'transferred_at' => now()->toIso8601String(),
                'transferred_by' => auth('admin')->id(),
            ];

            // Release old table seats
            if ($oldTable) {
                $oldTable->release($order->guest_count);
            }

            // Occupy new table
            $newTable->occupy($order, $order->guest_count);

            // Update order
            $order->update([
                'table_id' => $newTable->id,
                'table_transfer_log' => $transferLog,
            ]);

            // Create notification
            OrderNotification::notifyTableTransfer(
                $order,
                $oldTable->name ?? 'Unknown',
                $newTable->name
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order transferred from {$oldTable->name} to {$newTable->name}",
                'new_table' => $newTable,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer table: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Merge orders from two tables
     */
    public function merge(Request $request)
    {
        $validated = $request->validate([
            'source_order_id' => 'required|exists:sales,id',
            'target_order_id' => 'required|exists:sales,id',
        ]);

        $sourceOrder = Sale::with('details')->findOrFail($validated['source_order_id']);
        $targetOrder = Sale::with('details')->findOrFail($validated['target_order_id']);

        if ($sourceOrder->id === $targetOrder->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot merge order with itself.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Move all items from source to target
            foreach ($sourceOrder->details as $detail) {
                $detail->update(['sale_id' => $targetOrder->id]);
            }

            // Update target order totals
            $targetOrder->subtotal += $sourceOrder->subtotal;
            $targetOrder->total += $sourceOrder->total;
            $targetOrder->guest_count += $sourceOrder->guest_count;
            $targetOrder->save();

            // Release source table
            if ($sourceOrder->table) {
                $sourceOrder->table->release($sourceOrder->guest_count);
            }

            // Cancel source order
            $sourceOrder->update(['status' => 2]); // Cancelled/merged

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orders merged successfully.',
                'target_order' => $targetOrder->fresh(['details', 'table']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to merge orders: ' . $e->getMessage(),
            ], 500);
        }
    }
}
