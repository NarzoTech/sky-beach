<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\POS\app\Services\PrintService;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuAddon;
use App\Models\Stock;

class VoidItemController extends Controller
{
    protected $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    /**
     * Void a single item from order
     */
    public function voidItem(Request $request, $itemId)
    {
        checkAdminHasPermissionAndThrowException('void.item');
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'notify_kitchen' => 'boolean',
        ]);

        $item = ProductSale::with(['sale.table', 'menuItem', 'service'])->findOrFail($itemId);
        $order = $item->sale;

        if ($order->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Can only void items from processing orders.',
            ], 400);
        }

        if ($item->is_voided) {
            return response()->json([
                'success' => false,
                'message' => 'Item is already voided.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Mark item as voided
            $item->update([
                'is_voided' => true,
                'void_reason' => $validated['reason'],
                'kitchen_status' => 'cancelled',
                'status_updated_at' => now(),
            ]);

            // Restore ingredient stock for voided item
            $this->restoreItemStock($item, $order);

            // Update order totals
            $order->subtotal -= $item->sub_total;
            $order->total -= $item->sub_total;
            if ($order->tax_amount > 0) {
                $taxReduction = $item->sub_total * (($order->tax_rate ?? 0) / 100);
                $order->total -= $taxReduction;
            }
            $order->save();

            // Print void ticket to kitchen if requested
            if ($validated['notify_kitchen'] ?? true) {
                $this->printService->printVoidItem($order, $item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item voided successfully.',
                'item' => $item->fresh(),
                'order_total' => $order->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to void item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Void multiple items
     */
    public function voidMultiple(Request $request)
    {
        checkAdminHasPermissionAndThrowException('void.item');
        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:product_sales,id',
            'reason' => 'required|string|max:500',
            'notify_kitchen' => 'boolean',
        ]);

        $items = ProductSale::whereIn('id', $validated['item_ids'])
            ->with(['sale', 'menuItem', 'service'])
            ->get();

        // Verify all items are from same order
        $orderIds = $items->pluck('sale_id')->unique();
        if ($orderIds->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'All items must be from the same order.',
            ], 400);
        }

        $order = $items->first()->sale;

        if ($order->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Can only void items from processing orders.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $totalVoided = 0;

            foreach ($items as $item) {
                if ($item->is_voided) continue;

                $item->update([
                    'is_voided' => true,
                    'void_reason' => $validated['reason'],
                    'kitchen_status' => 'cancelled',
                    'status_updated_at' => now(),
                ]);

                // Restore ingredient stock for voided item
                $this->restoreItemStock($item, $order);

                $totalVoided += $item->sub_total;
            }

            // Update order totals
            $order->subtotal -= $totalVoided;
            $order->total -= $totalVoided;
            if ($order->tax_amount > 0) {
                $taxReduction = $totalVoided * (($order->tax_rate ?? 0) / 100);
                $order->total -= $taxReduction;
            }
            $order->save();

            // Print void ticket to kitchen
            if ($validated['notify_kitchen'] ?? true) {
                $this->printService->printVoidItems($order, $items);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($validated['item_ids']) . ' items voided successfully.',
                'order_total' => $order->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to void items: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get void history for an order
     */
    public function getVoidHistory($orderId)
    {
        checkAdminHasPermissionAndThrowException('void.view_history');
        $voidedItems = ProductSale::where('sale_id', $orderId)
            ->where('is_voided', true)
            ->with(['menuItem', 'service'])
            ->get();

        return response()->json($voidedItems);
    }

    /**
     * Restore voided item (undo void)
     */
    public function restoreItem(Request $request, $itemId)
    {
        checkAdminHasPermissionAndThrowException('void.restore');
        $item = ProductSale::with('sale')->findOrFail($itemId);
        $order = $item->sale;

        if (!$item->is_voided) {
            return response()->json([
                'success' => false,
                'message' => 'Item is not voided.',
            ], 400);
        }

        if ($order->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Can only restore items on processing orders.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Restore item
            $item->update([
                'is_voided' => false,
                'void_reason' => null,
                'kitchen_status' => 'pending',
                'status_updated_at' => now(),
            ]);

            // Re-deduct ingredient stock for restored item
            $this->deductItemStock($item, $order);

            // Update order totals
            $order->subtotal += $item->sub_total;
            $order->total += $item->sub_total;
            if ($order->tax_rate > 0) {
                $taxAddition = $item->sub_total * ($order->tax_rate / 100);
                $order->total += $taxAddition;
            }
            $order->save();

            // Reprint to kitchen
            $this->printService->printOrderUpdate($order, [[
                'name' => $item->menuItem->name ?? $item->service->name ?? 'Item',
                'qty' => $item->quantity,
                'addons' => $item->addons ? json_decode($item->addons, true) : [],
                'note' => 'RESTORED: ' . ($item->note ?? ''),
            ]]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item restored successfully.',
                'item' => $item->fresh(),
                'order_total' => $order->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore ingredient stock for a voided item (return stock)
     */
    private function restoreItemStock(ProductSale $item, Sale $order)
    {
        if (!$item->menu_item_id) return;

        $menuItem = MenuItem::with('recipes.ingredient')->find($item->menu_item_id);
        if (!$menuItem) return;

        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            $restoreQty = $recipe->quantity_required * $item->quantity;
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $restoreBase = $restoreQty / $conversionRate;

            $ingredient->addStock($restoreQty, $ingredient->consumption_unit_id);

            Stock::create([
                'sale_id' => $order->id,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => now(),
                'type' => 'Void Reversal',
                'invoice' => $order->invoice ?? null,
                'in_quantity' => $restoreQty,
                'base_in_quantity' => $restoreBase,
                'out_quantity' => 0,
                'base_out_quantity' => 0,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'average_cost' => $ingredient->average_cost ?? 0,
                'created_by' => auth('admin')->id(),
            ]);
        }

        // Restore addon stock
        $addons = $item->addons;
        if (is_string($addons)) {
            $addons = json_decode($addons, true);
        }
        if (!empty($addons) && is_array($addons)) {
            foreach ($addons as $addon) {
                $addonId = $addon['id'] ?? null;
                $addonQty = $addon['qty'] ?? 1;
                if (!$addonId) continue;

                $addonModel = MenuAddon::with('recipes.ingredient')->find($addonId);
                if (!$addonModel || $addonModel->recipes->isEmpty()) continue;

                $totalAddonQty = $addonQty * $item->quantity;
                foreach ($addonModel->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if (!$ingredient) continue;

                    $restoreQty = $recipe->quantity_required * $totalAddonQty;
                    $conversionRate = $ingredient->conversion_rate ?? 1;
                    $restoreBase = $restoreQty / $conversionRate;

                    $ingredient->addStock($restoreQty, $ingredient->consumption_unit_id);

                    Stock::create([
                        'sale_id' => $order->id,
                        'ingredient_id' => $ingredient->id,
                        'unit_id' => $ingredient->consumption_unit_id,
                        'date' => now(),
                        'type' => 'Addon Void Reversal',
                        'invoice' => $order->invoice ?? null,
                        'in_quantity' => $restoreQty,
                        'base_in_quantity' => $restoreBase,
                        'out_quantity' => 0,
                        'base_out_quantity' => 0,
                        'sku' => $ingredient->sku,
                        'purchase_price' => $ingredient->purchase_price ?? 0,
                        'average_cost' => $ingredient->average_cost ?? 0,
                        'created_by' => auth('admin')->id(),
                    ]);
                }
            }
        }
    }

    /**
     * Deduct ingredient stock for a restored item (re-deduct stock)
     */
    private function deductItemStock(ProductSale $item, Sale $order)
    {
        if (!$item->menu_item_id) return;

        $menuItem = MenuItem::with('recipes.ingredient')->find($item->menu_item_id);
        if (!$menuItem) return;

        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            $deductQty = $recipe->quantity_required * $item->quantity;
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $deductBase = $deductQty / $conversionRate;

            $ingredient->deductStock($deductQty, $ingredient->consumption_unit_id);

            Stock::create([
                'sale_id' => $order->id,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => now(),
                'type' => 'Sale',
                'invoice' => $order->invoice ?? null,
                'out_quantity' => $deductQty,
                'base_out_quantity' => $deductBase,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'sale_price' => 0,
                'rate' => $ingredient->consumption_unit_cost ?? 0,
                'profit' => 0,
                'created_by' => auth('admin')->id(),
            ]);
        }

        // Deduct addon stock
        $addons = $item->addons;
        if (is_string($addons)) {
            $addons = json_decode($addons, true);
        }
        if (!empty($addons) && is_array($addons)) {
            foreach ($addons as $addon) {
                $addonId = $addon['id'] ?? null;
                $addonQty = $addon['qty'] ?? 1;
                if (!$addonId) continue;

                $addonModel = MenuAddon::with('recipes.ingredient')->find($addonId);
                if (!$addonModel || $addonModel->recipes->isEmpty()) continue;

                $totalAddonQty = $addonQty * $item->quantity;
                foreach ($addonModel->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if (!$ingredient) continue;

                    $deductQty = $recipe->quantity_required * $totalAddonQty;
                    $conversionRate = $ingredient->conversion_rate ?? 1;
                    $deductBase = $deductQty / $conversionRate;

                    $ingredient->deductStock($deductQty, $ingredient->consumption_unit_id);

                    Stock::create([
                        'sale_id' => $order->id,
                        'ingredient_id' => $ingredient->id,
                        'unit_id' => $ingredient->consumption_unit_id,
                        'date' => now(),
                        'type' => 'Addon Sale',
                        'invoice' => $order->invoice ?? null,
                        'out_quantity' => $deductQty,
                        'base_out_quantity' => $deductBase,
                        'sku' => $ingredient->sku,
                        'purchase_price' => $ingredient->purchase_price ?? 0,
                        'sale_price' => 0,
                        'rate' => $ingredient->consumption_unit_cost ?? 0,
                        'profit' => 0,
                        'created_by' => auth('admin')->id(),
                    ]);
                }
            }
        }
    }
}
