<?php

namespace Modules\Menu\app\Services;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\Recipe;
use Modules\Product\app\Models\Product;

class MenuStockService
{
    /**
     * Deduct stock when a menu item is sold
     *
     * @param int $menuItemId The menu item ID
     * @param int $quantity Quantity of menu items sold
     * @param int $warehouseId Warehouse/branch ID
     * @param string|null $reference Optional reference (order ID, invoice number, etc.)
     * @return array Array of stock deductions made
     */
    public function deductStockForSale(int $menuItemId, int $quantity, int $warehouseId, ?string $reference = null): array
    {
        $menuItem = MenuItem::with('recipes.product')->findOrFail($menuItemId);
        $deductions = [];

        DB::beginTransaction();
        try {
            foreach ($menuItem->recipes as $recipe) {
                if (!$recipe->product_id) {
                    continue;
                }

                $product = $recipe->product;
                if (!$product) {
                    continue;
                }

                // Calculate total quantity to deduct
                $deductQuantity = $recipe->quantity_required * $quantity;

                // Get current stock
                $currentStock = $this->getCurrentStock($product->id, $warehouseId);

                // Create stock entry (negative for deduction)
                $stock = Stock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId,
                    'quantity' => -$deductQuantity,
                    'stock_type' => 'out',
                    'reference_type' => 'menu_sale',
                    'reference_id' => $menuItemId,
                    'note' => "Menu item sale: {$menuItem->name}" . ($reference ? " (Ref: {$reference})" : ''),
                    'date' => now(),
                ]);

                // Update product stock
                $product->decrement('stock', $deductQuantity);

                // Check and update stock status
                $this->updateStockStatus($product);

                $deductions[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_deducted' => $deductQuantity,
                    'remaining_stock' => $product->stock,
                    'stock_id' => $stock->id,
                ];
            }

            DB::commit();
            return $deductions;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if stock is available for a menu item
     *
     * @param int $menuItemId
     * @param int $quantity Quantity of menu items
     * @param int $warehouseId
     * @return array ['available' => bool, 'shortages' => []]
     */
    public function checkStockAvailability(int $menuItemId, int $quantity, int $warehouseId): array
    {
        $menuItem = MenuItem::with('recipes.product')->findOrFail($menuItemId);
        $shortages = [];

        foreach ($menuItem->recipes as $recipe) {
            if (!$recipe->product_id || !$recipe->product) {
                continue;
            }

            $requiredQuantity = $recipe->quantity_required * $quantity;
            $currentStock = $this->getCurrentStock($recipe->product_id, $warehouseId);

            if ($currentStock < $requiredQuantity) {
                $shortages[] = [
                    'product_id' => $recipe->product_id,
                    'product_name' => $recipe->product->name,
                    'required' => $requiredQuantity,
                    'available' => $currentStock,
                    'shortage' => $requiredQuantity - $currentStock,
                ];
            }
        }

        return [
            'available' => empty($shortages),
            'shortages' => $shortages,
        ];
    }

    /**
     * Get current stock for a product in a warehouse
     */
    public function getCurrentStock(int $productId, int $warehouseId): float
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        // If warehouse-specific stock tracking exists, use it
        // Otherwise, use product's global stock
        return $product->stock ?? 0;
    }

    /**
     * Update product stock status based on current stock level
     */
    protected function updateStockStatus(Product $product): void
    {
        if ($product->stock <= 0) {
            $product->update(['stock_status' => 'out_of_stock']);
        } elseif ($product->alert_quantity && $product->stock <= $product->alert_quantity) {
            $product->update(['stock_status' => 'low_stock']);
        } else {
            $product->update(['stock_status' => 'in_stock']);
        }
    }

    /**
     * Get ingredient cost for a menu item
     */
    public function calculateIngredientCost(int $menuItemId): float
    {
        $menuItem = MenuItem::with('recipes.product')->findOrFail($menuItemId);
        $totalCost = 0;

        foreach ($menuItem->recipes as $recipe) {
            if ($recipe->product) {
                $totalCost += ($recipe->product->cost ?? 0) * $recipe->quantity_required;
            }
        }

        return $totalCost;
    }

    /**
     * Get low stock alerts for menu item ingredients
     */
    public function getLowStockAlerts(): array
    {
        $alerts = [];

        // Get all products used as ingredients in recipes
        $usedProductIds = Recipe::pluck('product_id')->unique()->filter();

        $lowStockProducts = Product::whereIn('id', $usedProductIds)
            ->whereNotNull('alert_quantity')
            ->whereColumn('stock', '<=', 'alert_quantity')
            ->get();

        foreach ($lowStockProducts as $product) {
            // Find which menu items use this product
            $menuItems = MenuItem::whereHas('recipes', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })->pluck('name');

            $alerts[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock,
                'alert_level' => $product->alert_quantity,
                'affected_menu_items' => $menuItems->toArray(),
            ];
        }

        return $alerts;
    }

    /**
     * Reverse stock deduction (for order cancellation)
     */
    public function reverseStockDeduction(int $menuItemId, int $quantity, int $warehouseId, ?string $reference = null): array
    {
        $menuItem = MenuItem::with('recipes.product')->findOrFail($menuItemId);
        $reversals = [];

        DB::beginTransaction();
        try {
            foreach ($menuItem->recipes as $recipe) {
                if (!$recipe->product_id || !$recipe->product) {
                    continue;
                }

                $product = $recipe->product;
                $addQuantity = $recipe->quantity_required * $quantity;

                // Create stock entry (positive for addition)
                $stock = Stock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $addQuantity,
                    'stock_type' => 'in',
                    'reference_type' => 'menu_sale_reversal',
                    'reference_id' => $menuItemId,
                    'note' => "Menu sale reversal: {$menuItem->name}" . ($reference ? " (Ref: {$reference})" : ''),
                    'date' => now(),
                ]);

                // Update product stock
                $product->increment('stock', $addQuantity);

                // Update stock status
                $this->updateStockStatus($product);

                $reversals[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_added' => $addQuantity,
                    'new_stock' => $product->stock,
                    'stock_id' => $stock->id,
                ];
            }

            DB::commit();
            return $reversals;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
