<?php

namespace Modules\Menu\app\Models;

use App\Helpers\UnitConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\UnitType;

/**
 * Recipe Model - Links ingredients to menu items
 *
 * This model represents the ingredients required for a menu item,
 * including quantity and unit specifications.
 *
 * Key Features:
 * - Unit validation (ensures recipe unit is compatible with ingredient)
 * - Automatic cost calculation based on consumption_unit_cost
 * - Stock checking with unit conversion
 */
class Recipe extends Model
{

    protected $fillable = [
        'menu_item_id',
        'ingredient_id',
        'quantity_required',
        'unit_id',
        'notes',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
    ];

    protected $appends = ['ingredient_cost', 'is_unit_valid'];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validate unit compatibility on save
        static::saving(function ($recipe) {
            if ($recipe->unit_id && $recipe->ingredient_id) {
                $ingredient = Ingredient::find($recipe->ingredient_id);
                if ($ingredient && !$recipe->isUnitCompatibleWith($ingredient)) {
                    // Log warning but don't prevent save
                    logger()->warning("Recipe unit may not be compatible with ingredient", [
                        'recipe_unit_id' => $recipe->unit_id,
                        'ingredient_id' => $recipe->ingredient_id,
                        'ingredient_unit_id' => $ingredient->consumption_unit_id ?? $ingredient->unit_id,
                    ]);
                }
            }

            // Auto-set unit to ingredient's consumption unit if not set
            if (!$recipe->unit_id && $recipe->ingredient_id) {
                $ingredient = Ingredient::find($recipe->ingredient_id);
                if ($ingredient) {
                    $recipe->unit_id = $ingredient->consumption_unit_id ?? $ingredient->unit_id;
                }
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    // Alias for backward compatibility
    public function product(): BelongsTo
    {
        return $this->ingredient();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }

    // ==================== ATTRIBUTES ====================

    /**
     * Calculate the ingredient cost for this recipe
     *
     * @return float
     */
    public function getIngredientCostAttribute(): float
    {
        if (!$this->ingredient) {
            return 0;
        }

        // Get the quantity in consumption units
        $quantityInConsumptionUnits = $this->getQuantityInConsumptionUnits();

        // Use consumption_unit_cost for accurate costing
        $costPerUnit = $this->ingredient->consumption_unit_cost ?? $this->ingredient->cost ?? 0;

        return $quantityInConsumptionUnits * $costPerUnit;
    }

    /**
     * Check if the recipe's unit is valid/compatible with the ingredient
     *
     * @return bool
     */
    public function getIsUnitValidAttribute(): bool
    {
        if (!$this->ingredient) {
            return true;
        }

        if (!$this->unit_id) {
            return true; // Will use ingredient's default unit
        }

        return $this->isUnitCompatibleWith($this->ingredient);
    }

    /**
     * Get the effective unit ID (recipe unit or ingredient's consumption unit)
     *
     * @return int|null
     */
    public function getEffectiveUnitIdAttribute(): ?int
    {
        if ($this->unit_id) {
            return $this->unit_id;
        }

        if ($this->ingredient) {
            return $this->ingredient->consumption_unit_id ?? $this->ingredient->unit_id;
        }

        return null;
    }

    /**
     * Get formatted quantity with unit
     *
     * @return string
     */
    public function getFormattedQuantityAttribute(): string
    {
        $unitId = $this->effective_unit_id;

        if (!$unitId) {
            return number_format($this->quantity_required, 2);
        }

        return UnitConverter::formatWithUnit($this->quantity_required, $unitId);
    }

    // ==================== METHODS ====================

    /**
     * Check if a unit is compatible with the ingredient
     *
     * @param Ingredient $ingredient
     * @return bool
     */
    public function isUnitCompatibleWith(Ingredient $ingredient): bool
    {
        if (!$this->unit_id) {
            return true;
        }

        $ingredientUnitId = $ingredient->consumption_unit_id ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;

        if (!$ingredientUnitId) {
            return true;
        }

        return UnitConverter::areUnitsCompatible($this->unit_id, $ingredientUnitId);
    }

    /**
     * Get quantity converted to ingredient's consumption units
     *
     * @return float
     */
    public function getQuantityInConsumptionUnits(): float
    {
        if (!$this->ingredient) {
            return $this->quantity_required;
        }

        $recipeUnitId = $this->unit_id ?? $this->ingredient->consumption_unit_id ?? $this->ingredient->unit_id;
        $consumptionUnitId = $this->ingredient->consumption_unit_id ?? $this->ingredient->unit_id;

        if (!$recipeUnitId || !$consumptionUnitId || $recipeUnitId == $consumptionUnitId) {
            return $this->quantity_required;
        }

        return UnitConverter::safeConvert($this->quantity_required, $recipeUnitId, $consumptionUnitId);
    }

    /**
     * Get quantity converted to ingredient's purchase units (for stock comparison)
     *
     * @return float
     */
    public function getQuantityInPurchaseUnits(): float
    {
        if (!$this->ingredient) {
            return $this->quantity_required;
        }

        $recipeUnitId = $this->unit_id ?? $this->ingredient->consumption_unit_id ?? $this->ingredient->unit_id;
        $purchaseUnitId = $this->ingredient->purchase_unit_id ?? $this->ingredient->unit_id;

        if (!$recipeUnitId || !$purchaseUnitId || $recipeUnitId == $purchaseUnitId) {
            return $this->quantity_required;
        }

        // Use ingredient's conversion rate if converting between purchase and consumption units
        $consumptionUnitId = $this->ingredient->consumption_unit_id ?? $this->ingredient->unit_id;

        if ($recipeUnitId == $consumptionUnitId) {
            return $this->ingredient->consumptionToPurchaseUnits($this->quantity_required);
        }

        return UnitConverter::safeConvert($this->quantity_required, $recipeUnitId, $purchaseUnitId);
    }

    /**
     * Check if there's enough stock for this recipe (for given quantity of menu items)
     *
     * @param int $menuItemQuantity Number of menu items to prepare
     * @return bool
     */
    public function hasEnoughStock(int $menuItemQuantity = 1): bool
    {
        if (!$this->ingredient) {
            return true;
        }

        // Get required quantity in purchase units
        $requiredQty = $this->getQuantityInPurchaseUnits() * $menuItemQuantity;

        return $this->ingredient->hasEnoughStock($requiredQty);
    }

    /**
     * Get stock shortage information
     *
     * @param int $menuItemQuantity Number of menu items to prepare
     * @return array|null Null if no shortage, array with details if shortage exists
     */
    public function getStockShortage(int $menuItemQuantity = 1): ?array
    {
        if (!$this->ingredient) {
            return null;
        }

        $requiredQty = $this->getQuantityInPurchaseUnits() * $menuItemQuantity;
        $currentStock = (float) str_replace(',', '', $this->ingredient->getAttributes()['stock'] ?? 0);

        if ($currentStock >= $requiredQty) {
            return null;
        }

        $shortage = $requiredQty - $currentStock;

        return [
            'ingredient_id' => $this->ingredient->id,
            'ingredient_name' => $this->ingredient->name,
            'required' => $requiredQty,
            'available' => $currentStock,
            'shortage' => $shortage,
            'unit' => $this->ingredient->purchaseUnit?->ShortName ?? 'unit',
        ];
    }

    /**
     * Deduct stock for this recipe (for given quantity of menu items)
     *
     * @param int $menuItemQuantity Number of menu items sold
     * @return bool
     */
    public function deductStock(int $menuItemQuantity = 1): bool
    {
        if (!$this->ingredient) {
            return false;
        }

        $recipeUnitId = $this->unit_id ?? $this->ingredient->consumption_unit_id ?? $this->ingredient->unit_id;
        $totalQuantity = $this->quantity_required * $menuItemQuantity;

        return $this->ingredient->deductStock($totalQuantity, $recipeUnitId);
    }

    /**
     * Calculate total cost for a given quantity of menu items
     *
     * @param int $menuItemQuantity
     * @return float
     */
    public function calculateTotalCost(int $menuItemQuantity = 1): float
    {
        return $this->ingredient_cost * $menuItemQuantity;
    }

    /**
     * Validate the recipe configuration
     *
     * @return array ['valid' => bool, 'errors' => [], 'warnings' => []]
     */
    public function validate(): array
    {
        $errors = [];
        $warnings = [];

        // Check ingredient exists
        if (!$this->ingredient_id) {
            $errors[] = "No ingredient selected";
        } elseif (!$this->ingredient) {
            $errors[] = "Selected ingredient does not exist";
        }

        // Check quantity
        if ($this->quantity_required <= 0) {
            $errors[] = "Quantity must be greater than 0";
        }

        // Check unit compatibility
        if ($this->ingredient && $this->unit_id) {
            if (!$this->isUnitCompatibleWith($this->ingredient)) {
                $warnings[] = sprintf(
                    "Recipe unit (%s) may not be compatible with ingredient's unit (%s)",
                    $this->unit?->name ?? 'Unknown',
                    $this->ingredient->consumptionUnit?->name ?? $this->ingredient->unit?->name ?? 'Unknown'
                );
            }
        }

        // Check ingredient unit configuration
        if ($this->ingredient) {
            $unitValidation = $this->ingredient->validateUnitConfiguration();
            if (!$unitValidation['valid']) {
                foreach ($unitValidation['errors'] as $error) {
                    $warnings[] = "Ingredient: " . $error;
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
