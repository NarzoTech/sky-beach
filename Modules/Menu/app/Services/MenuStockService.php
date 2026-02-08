<?php

namespace Modules\Menu\app\Services;

use App\Helpers\UnitConverter;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Menu\app\Models\Recipe;
use Modules\Ingredient\app\Models\Ingredient;

/**
 * MenuStockService - Handles stock operations for menu item sales
 *
 * This service manages ingredient stock deductions when menu items are sold,
 * with full support for unit conversions.
 *
 * Key Features:
 * - Unit-aware stock deductions (recipe unit -> ingredient's purchase unit)
 * - Stock availability checking with unit conversion
 * - Weighted average cost tracking
 * - Stock reversal for cancellations
 * - Low stock alerts
 */
class MenuStockService
{
    /**
     * Deduct stock when a menu item is sold
     *
     * This method handles unit conversion from recipe quantities to stock quantities.
     * Recipe quantities are typically in consumption units, while stock is tracked
     * in purchase units.
     *
     * @param int $menuItemId The menu item ID
     * @param int $quantity Quantity of menu items sold
     * @param int $warehouseId Warehouse/branch ID
     * @param string|null $reference Optional reference (order ID, invoice number, etc.)
     * @return array Array of stock deductions made
     */
    public function deductStockForSale(int $menuItemId, int $quantity, int $warehouseId, ?string $reference = null): array
    {
        $menuItem = MenuItem::with(['recipes.ingredient', 'recipes.unit'])->findOrFail($menuItemId);
        $deductions = [];

        DB::beginTransaction();
        try {
            foreach ($menuItem->recipes as $recipe) {
                if (!$recipe->ingredient_id) {
                    continue;
                }

                $ingredient = $recipe->ingredient;
                if (!$ingredient) {
                    continue;
                }

                // Calculate total quantity needed in recipe's unit
                $recipeQuantity = $recipe->quantity_required * $quantity;

                // Get the recipe unit (defaults to ingredient's consumption unit)
                $recipeUnitId = $recipe->unit_id ?? $ingredient->consumption_unit_id ?? $ingredient->unit_id;

                // Convert to ingredient's purchase unit (stock is stored in purchase units)
                $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
                $deductQuantity = $this->convertQuantity($recipeQuantity, $recipeUnitId, $purchaseUnitId, $ingredient);

                // Get base unit quantity for accurate tracking
                $baseUnitId = UnitConverter::getBaseUnitId($purchaseUnitId ?? $ingredient->unit_id);
                $baseQuantity = UnitConverter::safeConvert($deductQuantity, $purchaseUnitId, $baseUnitId);

                // Get current stock before deduction
                $currentStock = $this->getCurrentStock($ingredient->id, $warehouseId);

                // Create stock entry
                $stock = Stock::create([
                    'ingredient_id' => $ingredient->id,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $recipeUnitId,
                    'in_quantity' => 0,
                    'out_quantity' => $deductQuantity,
                    'base_in_quantity' => 0,
                    'base_out_quantity' => $baseQuantity,
                    'type' => 'Menu Sale',
                    'date' => now(),
                    'invoice' => $reference,
                    'purchase_price' => $ingredient->average_cost ?? $ingredient->cost ?? 0,
                    'average_cost' => $ingredient->average_cost,
                    'created_by' => auth('admin')->id(),
                ]);

                // Update ingredient stock using the model's unit-aware method
                $ingredient->deductStock($recipeQuantity, $recipeUnitId);

                $deductions[] = [
                    'ingredient_id' => $ingredient->id,
                    'ingredient_name' => $ingredient->name,
                    'recipe_quantity' => $recipeQuantity,
                    'recipe_unit' => $recipe->unit?->ShortName ?? $ingredient->consumptionUnit?->ShortName ?? 'unit',
                    'deducted_quantity' => $deductQuantity,
                    'deducted_unit' => $ingredient->purchaseUnit?->ShortName ?? 'unit',
                    'base_quantity' => $baseQuantity,
                    'remaining_stock' => $ingredient->fresh()->stock,
                    'stock_id' => $stock->id,
                    'cost' => $recipe->ingredient_cost * $quantity,
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
     * @return array ['available' => bool, 'shortages' => [], 'requirements' => []]
     */
    public function checkStockAvailability(int $menuItemId, int $quantity, int $warehouseId): array
    {
        $menuItem = MenuItem::with(['recipes.ingredient', 'recipes.unit'])->findOrFail($menuItemId);
        $shortages = [];
        $requirements = [];

        foreach ($menuItem->recipes as $recipe) {
            if (!$recipe->ingredient_id || !$recipe->ingredient) {
                continue;
            }

            $ingredient = $recipe->ingredient;

            // Calculate required quantity in recipe's unit
            $recipeQuantity = $recipe->quantity_required * $quantity;
            $recipeUnitId = $recipe->unit_id ?? $ingredient->consumption_unit_id ?? $ingredient->unit_id;

            // Convert to purchase units for stock comparison
            $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
            $requiredInPurchaseUnits = $this->convertQuantity($recipeQuantity, $recipeUnitId, $purchaseUnitId, $ingredient);

            // Get current stock (in purchase units)
            $currentStock = $this->getCurrentStock($ingredient->id, $warehouseId);

            $requirements[] = [
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'required_quantity' => $recipeQuantity,
                'required_unit' => $recipe->unit?->ShortName ?? $ingredient->consumptionUnit?->ShortName ?? 'unit',
                'required_in_stock_units' => $requiredInPurchaseUnits,
                'stock_unit' => $ingredient->purchaseUnit?->ShortName ?? 'unit',
                'available_stock' => $currentStock,
            ];

            if ($currentStock < $requiredInPurchaseUnits) {
                $shortage = $requiredInPurchaseUnits - $currentStock;
                $shortages[] = [
                    'ingredient_id' => $ingredient->id,
                    'ingredient_name' => $ingredient->name,
                    'required' => $requiredInPurchaseUnits,
                    'available' => $currentStock,
                    'shortage' => $shortage,
                    'unit' => $ingredient->purchaseUnit?->ShortName ?? 'unit',
                    // Also show in recipe units for clarity
                    'shortage_in_recipe_units' => $this->convertQuantity($shortage, $purchaseUnitId, $recipeUnitId, $ingredient),
                    'recipe_unit' => $recipe->unit?->ShortName ?? $ingredient->consumptionUnit?->ShortName ?? 'unit',
                ];
            }
        }

        return [
            'available' => empty($shortages),
            'shortages' => $shortages,
            'requirements' => $requirements,
        ];
    }

    /**
     * Get current stock for an ingredient in a warehouse
     *
     * @param int $ingredientId
     * @param int $warehouseId
     * @return float Stock in purchase units
     */
    public function getCurrentStock(int $ingredientId, int $warehouseId): float
    {
        $ingredient = Ingredient::find($ingredientId);
        if (!$ingredient) {
            return 0;
        }

        // Return raw stock value (stored in purchase units)
        return (float) str_replace(',', '', $ingredient->getAttributes()['stock'] ?? 0);
    }

    /**
     * Update ingredient stock status based on current stock level
     *
     * @param Ingredient $ingredient
     * @return void
     */
    protected function updateStockStatus(Ingredient $ingredient): void
    {
        $stock = (float) str_replace(',', '', $ingredient->getAttributes()['stock'] ?? 0);

        if ($stock <= 0) {
            $ingredient->update(['stock_status' => 'out_of_stock']);
        } elseif ($ingredient->stock_alert && $stock <= $ingredient->stock_alert) {
            $ingredient->update(['stock_status' => 'low_stock']);
        } else {
            $ingredient->update(['stock_status' => 'in_stock']);
        }
    }

    /**
     * Calculate ingredient cost for a menu item (with unit conversion)
     *
     * @param int $menuItemId
     * @return array ['total_cost' => float, 'breakdown' => []]
     */
    public function calculateIngredientCost(int $menuItemId): array
    {
        $menuItem = MenuItem::with(['recipes.ingredient', 'recipes.unit'])->findOrFail($menuItemId);
        $totalCost = 0;
        $breakdown = [];

        foreach ($menuItem->recipes as $recipe) {
            if (!$recipe->ingredient) {
                continue;
            }

            $ingredient = $recipe->ingredient;

            // Get cost per consumption unit
            $costPerUnit = $ingredient->consumption_unit_cost ?? $ingredient->cost ?? 0;

            // Convert recipe quantity to consumption units if needed
            $recipeUnitId = $recipe->unit_id ?? $ingredient->consumption_unit_id ?? $ingredient->unit_id;
            $consumptionUnitId = $ingredient->consumption_unit_id ?? $ingredient->unit_id;

            $quantityInConsumptionUnits = $this->convertQuantity(
                $recipe->quantity_required,
                $recipeUnitId,
                $consumptionUnitId,
                $ingredient
            );

            $ingredientCost = $quantityInConsumptionUnits * $costPerUnit;
            $totalCost += $ingredientCost;

            $breakdown[] = [
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'quantity' => $recipe->quantity_required,
                'unit' => $recipe->unit?->ShortName ?? $ingredient->consumptionUnit?->ShortName ?? 'unit',
                'cost_per_unit' => $costPerUnit,
                'cost_unit' => $ingredient->consumptionUnit?->ShortName ?? 'unit',
                'total_cost' => $ingredientCost,
            ];
        }

        return [
            'total_cost' => $totalCost,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Get low stock alerts for menu item ingredients
     *
     * @return array
     */
    public function getLowStockAlerts(): array
    {
        $alerts = [];

        // Get all ingredients used in recipes
        $usedIngredientIds = Recipe::pluck('ingredient_id')->unique()->filter();

        $lowStockIngredients = Ingredient::whereIn('id', $usedIngredientIds)
            ->whereNotNull('stock_alert')
            ->get()
            ->filter(function ($ingredient) {
                $stock = (float) str_replace(',', '', $ingredient->getAttributes()['stock'] ?? 0);
                return $stock <= $ingredient->stock_alert;
            });

        foreach ($lowStockIngredients as $ingredient) {
            $stock = (float) str_replace(',', '', $ingredient->getAttributes()['stock'] ?? 0);

            // Find which menu items use this ingredient
            $menuItems = MenuItem::whereHas('recipes', function ($query) use ($ingredient) {
                $query->where('ingredient_id', $ingredient->id);
            })->pluck('name');

            $alerts[] = [
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'current_stock' => $stock,
                'stock_unit' => $ingredient->purchaseUnit?->ShortName ?? $ingredient->unit?->ShortName ?? 'unit',
                'alert_level' => $ingredient->stock_alert,
                'stock_in_consumption_units' => $ingredient->stock_in_consumption_units,
                'consumption_unit' => $ingredient->consumptionUnit?->ShortName ?? 'unit',
                'affected_menu_items' => $menuItems->toArray(),
            ];
        }

        return $alerts;
    }

    /**
     * Reverse stock deduction (for order cancellation)
     *
     * @param int $menuItemId
     * @param int $quantity
     * @param int $warehouseId
     * @param string|null $reference
     * @return array
     */
    public function reverseStockDeduction(int $menuItemId, int $quantity, int $warehouseId, ?string $reference = null): array
    {
        $menuItem = MenuItem::with(['recipes.ingredient', 'recipes.unit'])->findOrFail($menuItemId);
        $reversals = [];

        DB::beginTransaction();
        try {
            foreach ($menuItem->recipes as $recipe) {
                if (!$recipe->ingredient_id || !$recipe->ingredient) {
                    continue;
                }

                $ingredient = $recipe->ingredient;

                // Calculate quantity to add back (in recipe's unit)
                $recipeQuantity = $recipe->quantity_required * $quantity;
                $recipeUnitId = $recipe->unit_id ?? $ingredient->consumption_unit_id ?? $ingredient->unit_id;

                // Convert to purchase units
                $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
                $addQuantity = $this->convertQuantity($recipeQuantity, $recipeUnitId, $purchaseUnitId, $ingredient);

                // Get base unit quantity
                $baseUnitId = UnitConverter::getBaseUnitId($purchaseUnitId ?? $ingredient->unit_id);
                $baseQuantity = UnitConverter::safeConvert($addQuantity, $purchaseUnitId, $baseUnitId);

                // Create stock entry (positive for addition)
                $stock = Stock::create([
                    'ingredient_id' => $ingredient->id,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $recipeUnitId,
                    'in_quantity' => $addQuantity,
                    'out_quantity' => 0,
                    'base_in_quantity' => $baseQuantity,
                    'base_out_quantity' => 0,
                    'type' => 'Menu Sale Reversal',
                    'date' => now(),
                    'invoice' => $reference,
                    'purchase_price' => $ingredient->average_cost ?? $ingredient->cost ?? 0,
                    'average_cost' => $ingredient->average_cost,
                    'created_by' => auth('admin')->id(),
                ]);

                // Update ingredient stock
                $ingredient->addStock($recipeQuantity, $recipeUnitId);

                $reversals[] = [
                    'ingredient_id' => $ingredient->id,
                    'ingredient_name' => $ingredient->name,
                    'quantity_added' => $addQuantity,
                    'unit' => $ingredient->purchaseUnit?->ShortName ?? 'unit',
                    'new_stock' => $ingredient->fresh()->stock,
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

    /**
     * Deduct stock for addon ingredients when an addon is sold
     *
     * @param int $addonId The menu addon ID
     * @param int $quantity Quantity of addons sold (addon_qty * menu_item_qty)
     * @param int $warehouseId Warehouse/branch ID
     * @param string|null $reference Optional reference (invoice number, etc.)
     * @return array Array of stock deductions made
     */
    public function deductAddonStockForSale(int $addonId, int $quantity, int $warehouseId, ?string $reference = null): array
    {
        $addon = MenuAddon::with(['recipes.ingredient'])->find($addonId);

        if (!$addon || $addon->recipes->isEmpty()) {
            return [];
        }

        $deductions = [];

        DB::beginTransaction();
        try {
            foreach ($addon->recipes as $recipe) {
                if (!$recipe->ingredient_id || !$recipe->ingredient) {
                    continue;
                }

                $ingredient = $recipe->ingredient;
                $recipeQuantity = $recipe->quantity_required * $quantity;
                $recipeUnitId = $recipe->unit_id ?? $ingredient->consumption_unit_id ?? $ingredient->unit_id;

                $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
                $deductQuantity = $this->convertQuantity($recipeQuantity, $recipeUnitId, $purchaseUnitId, $ingredient);

                $baseUnitId = UnitConverter::getBaseUnitId($purchaseUnitId ?? $ingredient->unit_id);
                $baseQuantity = UnitConverter::safeConvert($deductQuantity, $purchaseUnitId, $baseUnitId);

                Stock::create([
                    'ingredient_id' => $ingredient->id,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $recipeUnitId,
                    'in_quantity' => 0,
                    'out_quantity' => $deductQuantity,
                    'base_in_quantity' => 0,
                    'base_out_quantity' => $baseQuantity,
                    'type' => 'Addon Sale',
                    'date' => now(),
                    'invoice' => $reference,
                    'purchase_price' => $ingredient->average_cost ?? $ingredient->cost ?? 0,
                    'average_cost' => $ingredient->average_cost,
                    'created_by' => auth('admin')->id(),
                ]);

                $ingredient->deductStock($recipeQuantity, $recipeUnitId);

                $deductions[] = [
                    'addon_id' => $addon->id,
                    'addon_name' => $addon->name,
                    'ingredient_id' => $ingredient->id,
                    'ingredient_name' => $ingredient->name,
                    'deducted_quantity' => $deductQuantity,
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
     * Reverse addon stock deduction (for order cancellation)
     *
     * @param int $addonId The menu addon ID
     * @param int $quantity Quantity of addons to reverse
     * @param int $warehouseId Warehouse/branch ID
     * @param string|null $reference Optional reference
     * @return array
     */
    public function reverseAddonStockDeduction(int $addonId, int $quantity, int $warehouseId, ?string $reference = null): array
    {
        $addon = MenuAddon::with(['recipes.ingredient'])->find($addonId);

        if (!$addon || $addon->recipes->isEmpty()) {
            return [];
        }

        $reversals = [];

        DB::beginTransaction();
        try {
            foreach ($addon->recipes as $recipe) {
                if (!$recipe->ingredient_id || !$recipe->ingredient) {
                    continue;
                }

                $ingredient = $recipe->ingredient;
                $recipeQuantity = $recipe->quantity_required * $quantity;
                $recipeUnitId = $recipe->unit_id ?? $ingredient->consumption_unit_id ?? $ingredient->unit_id;

                $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
                $addQuantity = $this->convertQuantity($recipeQuantity, $recipeUnitId, $purchaseUnitId, $ingredient);

                $baseUnitId = UnitConverter::getBaseUnitId($purchaseUnitId ?? $ingredient->unit_id);
                $baseQuantity = UnitConverter::safeConvert($addQuantity, $purchaseUnitId, $baseUnitId);

                Stock::create([
                    'ingredient_id' => $ingredient->id,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $recipeUnitId,
                    'in_quantity' => $addQuantity,
                    'out_quantity' => 0,
                    'base_in_quantity' => $baseQuantity,
                    'base_out_quantity' => 0,
                    'type' => 'Addon Sale Reversal',
                    'date' => now(),
                    'invoice' => $reference,
                    'purchase_price' => $ingredient->average_cost ?? $ingredient->cost ?? 0,
                    'average_cost' => $ingredient->average_cost,
                    'created_by' => auth('admin')->id(),
                ]);

                $ingredient->addStock($recipeQuantity, $recipeUnitId);

                $reversals[] = [
                    'addon_id' => $addon->id,
                    'addon_name' => $addon->name,
                    'ingredient_id' => $ingredient->id,
                    'ingredient_name' => $ingredient->name,
                    'quantity_added' => $addQuantity,
                ];
            }

            DB::commit();
            return $reversals;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Convert quantity between units using ingredient's configuration
     *
     * @param float $quantity
     * @param int|null $fromUnitId
     * @param int|null $toUnitId
     * @param Ingredient $ingredient
     * @return float
     */
    protected function convertQuantity(float $quantity, ?int $fromUnitId, ?int $toUnitId, Ingredient $ingredient): float
    {
        // If units are the same or not set, no conversion needed
        if (!$fromUnitId || !$toUnitId || $fromUnitId == $toUnitId) {
            return $quantity;
        }

        // Use the ingredient's conversion methods if available
        $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
        $consumptionUnitId = $ingredient->consumption_unit_id ?? $ingredient->unit_id;

        // Special handling for purchase <-> consumption conversion using stored rate
        if ($fromUnitId == $consumptionUnitId && $toUnitId == $purchaseUnitId) {
            return $ingredient->consumptionToPurchaseUnits($quantity);
        }

        if ($fromUnitId == $purchaseUnitId && $toUnitId == $consumptionUnitId) {
            return $ingredient->purchaseToConsumptionUnits($quantity);
        }

        // Fall back to UnitConverter for other conversions
        return UnitConverter::safeConvert($quantity, $fromUnitId, $toUnitId);
    }

    /**
     * Get stock movement history for an ingredient
     *
     * @param int $ingredientId
     * @param int|null $warehouseId
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return array
     */
    public function getStockMovementHistory(int $ingredientId, ?int $warehouseId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        $query = Stock::where('ingredient_id', $ingredientId)
            ->with(['unit', 'createdBy'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($fromDate) {
            $query->where('date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('date', '<=', $toDate);
        }

        $movements = $query->get();

        $ingredient = Ingredient::find($ingredientId);
        $runningStock = 0;

        // Calculate running balance
        $movementsWithBalance = $movements->reverse()->map(function ($movement) use (&$runningStock, $ingredient) {
            $inQty = $movement->in_quantity ?? 0;
            $outQty = $movement->out_quantity ?? 0;
            $runningStock += $inQty - $outQty;

            return [
                'id' => $movement->id,
                'date' => $movement->date,
                'type' => $movement->type,
                'in_quantity' => $inQty,
                'out_quantity' => $outQty,
                'unit' => $movement->unit?->ShortName ?? $ingredient?->purchaseUnit?->ShortName ?? 'unit',
                'base_in_quantity' => $movement->base_in_quantity,
                'base_out_quantity' => $movement->base_out_quantity,
                'running_balance' => $runningStock,
                'invoice' => $movement->invoice,
                'purchase_price' => $movement->purchase_price,
                'created_by' => $movement->createdBy?->name ?? 'System',
                'created_at' => $movement->created_at,
            ];
        })->reverse()->values();

        return [
            'ingredient_id' => $ingredientId,
            'ingredient_name' => $ingredient?->name ?? 'Unknown',
            'current_stock' => $ingredient ? (float) str_replace(',', '', $ingredient->getAttributes()['stock'] ?? 0) : 0,
            'stock_unit' => $ingredient?->purchaseUnit?->ShortName ?? 'unit',
            'movements' => $movementsWithBalance->toArray(),
        ];
    }
}
