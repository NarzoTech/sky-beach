<?php

namespace Modules\Menu\app\Services;

use App\Models\Warehouse;
use Modules\Menu\app\Models\BranchMenuAvailability;
use Modules\Menu\app\Models\BranchMenuPrice;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuVariant;

class BranchMenuService
{
    /**
     * Get all branches (warehouses)
     */
    public function getAllBranches()
    {
        return Warehouse::where('status', 1)->orderBy('name')->get();
    }

    /**
     * Get all menu items for pricing
     */
    public function getMenuItemsForPricing()
    {
        return MenuItem::with(['variants', 'category'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get branch-specific prices for a branch
     */
    public function getBranchPrices(int $branchId)
    {
        return BranchMenuPrice::where('warehouse_id', $branchId)
            ->get()
            ->keyBy(function ($item) {
                return $item->menu_item_id . '_' . ($item->variant_id ?? '0');
            });
    }

    /**
     * Save branch-specific prices
     */
    public function saveBranchPrices(int $branchId, array $prices): int
    {
        $saved = 0;

        foreach ($prices as $key => $price) {
            if (empty($price) || !is_numeric($price)) {
                // Delete existing price override if price is empty
                BranchMenuPrice::where('warehouse_id', $branchId)
                    ->where('menu_item_id', $this->extractMenuItemId($key))
                    ->where('variant_id', $this->extractVariantId($key))
                    ->delete();
                continue;
            }

            BranchMenuPrice::updateOrCreate(
                [
                    'warehouse_id' => $branchId,
                    'menu_item_id' => $this->extractMenuItemId($key),
                    'variant_id' => $this->extractVariantId($key),
                ],
                [
                    'price' => $price,
                ]
            );
            $saved++;
        }

        return $saved;
    }

    /**
     * Get branch availability for all items
     */
    public function getBranchAvailability(int $branchId)
    {
        return BranchMenuAvailability::where('warehouse_id', $branchId)
            ->pluck('is_available', 'menu_item_id')
            ->toArray();
    }

    /**
     * Save branch availability
     */
    public function saveBranchAvailability(int $branchId, array $availability): int
    {
        $saved = 0;

        // Get all menu items
        $menuItems = MenuItem::pluck('id');

        foreach ($menuItems as $menuItemId) {
            $isAvailable = isset($availability[$menuItemId]) && $availability[$menuItemId];

            BranchMenuAvailability::updateOrCreate(
                [
                    'warehouse_id' => $branchId,
                    'menu_item_id' => $menuItemId,
                ],
                [
                    'is_available' => $isAvailable,
                ]
            );
            $saved++;
        }

        return $saved;
    }

    /**
     * Get the effective price for a menu item at a branch
     */
    public function getEffectivePrice(int $menuItemId, int $branchId, ?int $variantId = null): float
    {
        // Check for branch-specific price
        $branchPrice = BranchMenuPrice::where('warehouse_id', $branchId)
            ->where('menu_item_id', $menuItemId)
            ->where('variant_id', $variantId)
            ->first();

        if ($branchPrice) {
            return $branchPrice->price;
        }

        // Fall back to default price
        $menuItem = MenuItem::find($menuItemId);
        $basePrice = $menuItem ? $menuItem->base_price : 0;

        if ($variantId) {
            $variant = MenuVariant::find($variantId);
            if ($variant) {
                $basePrice += $variant->price_adjustment;
            }
        }

        return $basePrice;
    }

    /**
     * Check if menu item is available at branch
     */
    public function isAvailableAtBranch(int $menuItemId, int $branchId): bool
    {
        $availability = BranchMenuAvailability::where('warehouse_id', $branchId)
            ->where('menu_item_id', $menuItemId)
            ->first();

        // If no specific availability is set, default to available
        if (!$availability) {
            return true;
        }

        return $availability->is_available;
    }

    /**
     * Get menu items for a specific branch with pricing and availability
     */
    public function getMenuForBranch(int $branchId)
    {
        $items = MenuItem::with(['category', 'variants', 'addons'])
            ->where('status', 1)
            ->where('is_available', 1)
            ->get();

        $branchPrices = $this->getBranchPrices($branchId);
        $branchAvailability = $this->getBranchAvailability($branchId);

        return $items->filter(function ($item) use ($branchAvailability) {
            // Check branch availability (default to available if not set)
            if (isset($branchAvailability[$item->id]) && !$branchAvailability[$item->id]) {
                return false;
            }
            return true;
        })->map(function ($item) use ($branchPrices) {
            // Apply branch-specific pricing
            $priceKey = $item->id . '_0';
            if (isset($branchPrices[$priceKey])) {
                $item->branch_price = $branchPrices[$priceKey]->price;
            } else {
                $item->branch_price = $item->base_price;
            }

            // Apply branch-specific variant pricing
            $item->variants->each(function ($variant) use ($item, $branchPrices) {
                $variantKey = $item->id . '_' . $variant->id;
                if (isset($branchPrices[$variantKey])) {
                    $variant->branch_price = $branchPrices[$variantKey]->price;
                } else {
                    $variant->branch_price = $item->base_price + $variant->price_adjustment;
                }
            });

            return $item;
        });
    }

    /**
     * Extract menu item ID from key
     */
    protected function extractMenuItemId(string $key): int
    {
        $parts = explode('_', $key);
        return (int) $parts[0];
    }

    /**
     * Extract variant ID from key
     */
    protected function extractVariantId(string $key): ?int
    {
        $parts = explode('_', $key);
        $variantId = isset($parts[1]) ? (int) $parts[1] : null;
        return $variantId === 0 ? null : $variantId;
    }
}
