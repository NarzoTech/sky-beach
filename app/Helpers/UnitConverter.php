<?php

namespace App\Helpers;

use Modules\Ingredient\app\Models\UnitType;
use Illuminate\Support\Facades\Cache;
use Exception;
use InvalidArgumentException;

/**
 * UnitConverter - Handles all unit conversion operations
 *
 * This class provides methods for converting quantities between different units
 * within the same unit family (e.g., Liter -> Milliliter, Kilogram -> Gram).
 *
 * Unit Structure:
 * - Base Units: Units with base_unit = NULL (e.g., Liter, Kilogram)
 * - Child Units: Units that reference a base unit (e.g., Milliliter -> Liter)
 *
 * Conversion Logic:
 * - operator = '*' : Child value = Base value * operator_value
 * - operator = '/' : Child value = Base value / operator_value
 *
 * Example: 1 Liter = 1000 Milliliters
 * - Milliliter: base_unit = Liter.id, operator = '/', operator_value = 1000
 * - To convert 5 Liters to ml: convertFromBase(5, ml) = 5 * 1000 = 5000 ml
 * - To convert 5000 ml to Liters: convertToBase(5000, ml) = 5000 / 1000 = 5 L
 */
class UnitConverter
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Convert quantity from one unit to another
     *
     * @param float $quantity The quantity to convert
     * @param int $fromUnitId The source unit ID
     * @param int $toUnitId The target unit ID
     * @return float The converted quantity
     * @throws InvalidArgumentException If units are invalid
     * @throws Exception If units are from different families
     */
    public static function convert($quantity, $fromUnitId, $toUnitId): float
    {
        // Handle null or zero quantity
        if ($quantity === null || $quantity == 0) {
            return 0;
        }

        // Same unit, no conversion needed
        if ($fromUnitId == $toUnitId) {
            return (float) $quantity;
        }

        // Handle null unit IDs
        if (!$fromUnitId || !$toUnitId) {
            return (float) $quantity;
        }

        $fromUnit = self::getUnit($fromUnitId);
        $toUnit = self::getUnit($toUnitId);

        if (!$fromUnit || !$toUnit) {
            throw new InvalidArgumentException("Invalid unit ID provided: from={$fromUnitId}, to={$toUnitId}");
        }

        // Verify units are compatible (same family)
        if (!self::areUnitsCompatible($fromUnitId, $toUnitId)) {
            throw new Exception(
                "Cannot convert between incompatible units: {$fromUnit->name} and {$toUnit->name}. " .
                "Units must be in the same family."
            );
        }

        // Convert to base unit first, then to target unit
        $baseQuantity = self::convertToBase($quantity, $fromUnit);
        return self::convertFromBase($baseQuantity, $toUnit);
    }

    /**
     * Safely convert quantity, returning original if conversion fails
     *
     * @param float $quantity The quantity to convert
     * @param int|null $fromUnitId The source unit ID
     * @param int|null $toUnitId The target unit ID
     * @return float The converted quantity or original if conversion fails
     */
    public static function safeConvert($quantity, $fromUnitId, $toUnitId): float
    {
        try {
            return self::convert($quantity, $fromUnitId, $toUnitId);
        } catch (Exception $e) {
            // Log the error for debugging
            logger()->warning("Unit conversion failed: " . $e->getMessage(), [
                'quantity' => $quantity,
                'fromUnitId' => $fromUnitId,
                'toUnitId' => $toUnitId,
            ]);
            return (float) $quantity;
        }
    }

    /**
     * Convert quantity to the base unit
     *
     * @param float $quantity The quantity to convert
     * @param UnitType|int $unit The unit to convert from (model or ID)
     * @return float The quantity in base unit
     */
    public static function convertToBase($quantity, $unit): float
    {
        if ($quantity === null || $quantity == 0) {
            return 0;
        }

        if (is_numeric($unit)) {
            $unit = self::getUnit($unit);
        }

        if (!$unit) {
            return (float) $quantity;
        }

        // This is already a base unit
        if (!$unit->base_unit) {
            return (float) $quantity;
        }

        $operator = $unit->operator ?? '*';
        $operatorValue = $unit->operator_value ?? 1;

        // Prevent division by zero
        if ($operatorValue == 0) {
            return (float) $quantity;
        }

        // Apply the conversion to base
        // If operator is '*', the child = base * value, so base = child / value
        // If operator is '/', 1 base = operator_value children, so base = child / value
        if ($operator == '*') {
            return $quantity / $operatorValue;
        } elseif ($operator == '/') {
            return $quantity / $operatorValue;
        }

        return (float) $quantity;
    }

    /**
     * Convert quantity from base unit to target unit
     *
     * @param float $baseQuantity The quantity in base unit
     * @param UnitType|int $targetUnit The target unit (model or ID)
     * @return float The converted quantity
     */
    public static function convertFromBase($baseQuantity, $targetUnit): float
    {
        if ($baseQuantity === null || $baseQuantity == 0) {
            return 0;
        }

        if (is_numeric($targetUnit)) {
            $targetUnit = self::getUnit($targetUnit);
        }

        if (!$targetUnit) {
            return (float) $baseQuantity;
        }

        // Target is base unit, no conversion needed
        if (!$targetUnit->base_unit) {
            return (float) $baseQuantity;
        }

        $operator = $targetUnit->operator ?? '*';
        $operatorValue = $targetUnit->operator_value ?? 1;

        // Prevent division by zero
        if ($operatorValue == 0) {
            return (float) $baseQuantity;
        }

        // Reverse the conversion from base
        // If operator is '*', child = base * value
        // If operator is '/', 1 base = operator_value children, so child = base * value
        if ($operator == '*') {
            return $baseQuantity * $operatorValue;
        } elseif ($operator == '/') {
            return $baseQuantity * $operatorValue;
        }

        return (float) $baseQuantity;
    }

    /**
     * Get unit by ID with caching
     *
     * @param int $unitId The unit ID
     * @return UnitType|null
     */
    public static function getUnit($unitId): ?UnitType
    {
        if (!$unitId) {
            return null;
        }

        $cacheKey = "unit_type_{$unitId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId) {
            return UnitType::with('parent')->find($unitId);
        });
    }

    /**
     * Clear unit cache (call after unit updates)
     *
     * @param int|null $unitId Specific unit ID or null to clear all
     */
    public static function clearCache($unitId = null): void
    {
        if ($unitId) {
            Cache::forget("unit_type_{$unitId}");
            Cache::forget("unit_family_{$unitId}");
        } else {
            // Clear all unit-related cache
            $units = UnitType::pluck('id');
            foreach ($units as $id) {
                Cache::forget("unit_type_{$id}");
                Cache::forget("unit_family_{$id}");
            }
        }
    }

    /**
     * Get the base unit ID for any unit in the family
     *
     * @param int $unitId Any unit ID in the family
     * @return int The base unit ID
     */
    public static function getBaseUnitId($unitId): int
    {
        $unit = self::getUnit($unitId);

        if (!$unit) {
            return $unitId;
        }

        return $unit->base_unit ?? $unit->id;
    }

    /**
     * Get all child units of a base unit
     *
     * @param int $baseUnitId The base unit ID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getChildUnits($baseUnitId)
    {
        return UnitType::where('base_unit', $baseUnitId)
            ->where('status', 1)
            ->get();
    }

    /**
     * Get all units in the same family (base unit + its children)
     *
     * @param int $unitId Any unit ID in the family
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnitFamily($unitId)
    {
        $cacheKey = "unit_family_{$unitId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($unitId) {
            $unit = UnitType::find($unitId);

            if (!$unit) {
                return collect([]);
            }

            // Find the base unit
            $baseUnitId = $unit->base_unit ?? $unit->id;
            $baseUnit = UnitType::find($baseUnitId);

            // Get all units in the family
            $family = collect([$baseUnit]);
            $children = self::getChildUnits($baseUnitId);

            return $family->merge($children)->filter()->values();
        });
    }

    /**
     * Format quantity with unit name
     *
     * @param float $quantity The quantity
     * @param int $unitId The unit ID
     * @param bool $useShortName Use short name instead of full name
     * @param int $decimals Number of decimal places
     * @return string Formatted string like "5.5 L" or "5.5 Liter"
     */
    public static function formatWithUnit($quantity, $unitId, $useShortName = true, $decimals = 2): string
    {
        $unit = self::getUnit($unitId);

        if (!$unit) {
            return number_format((float) $quantity, $decimals);
        }

        $unitName = $useShortName ? $unit->ShortName : $unit->name;
        return number_format((float) $quantity, $decimals) . ' ' . $unitName;
    }

    /**
     * Check if two units are compatible (in same family)
     *
     * @param int $unitId1 First unit ID
     * @param int $unitId2 Second unit ID
     * @return bool True if units are compatible
     */
    public static function areUnitsCompatible($unitId1, $unitId2): bool
    {
        if ($unitId1 == $unitId2) {
            return true;
        }

        if (!$unitId1 || !$unitId2) {
            return false;
        }

        $base1 = self::getBaseUnitId($unitId1);
        $base2 = self::getBaseUnitId($unitId2);

        return $base1 == $base2;
    }

    /**
     * Calculate the conversion rate between two units
     *
     * @param int $fromUnitId Source unit ID
     * @param int $toUnitId Target unit ID
     * @return float The conversion rate (multiply source qty by this to get target qty)
     * @throws Exception If units are incompatible
     */
    public static function getConversionRate($fromUnitId, $toUnitId): float
    {
        if ($fromUnitId == $toUnitId) {
            return 1.0;
        }

        // Convert 1 unit to get the rate
        return self::convert(1, $fromUnitId, $toUnitId);
    }

    /**
     * Convert quantity to ingredient's consumption unit
     *
     * @param float $quantity The quantity to convert
     * @param int $fromUnitId The source unit ID
     * @param \Modules\Ingredient\app\Models\Ingredient $ingredient The ingredient
     * @return float Quantity in consumption units
     */
    public static function convertToConsumptionUnit($quantity, $fromUnitId, $ingredient): float
    {
        $consumptionUnitId = $ingredient->consumption_unit_id ?? $ingredient->unit_id;

        if (!$consumptionUnitId || $fromUnitId == $consumptionUnitId) {
            return (float) $quantity;
        }

        return self::safeConvert($quantity, $fromUnitId, $consumptionUnitId);
    }

    /**
     * Convert quantity to ingredient's purchase unit
     *
     * @param float $quantity The quantity to convert
     * @param int $fromUnitId The source unit ID
     * @param \Modules\Ingredient\app\Models\Ingredient $ingredient The ingredient
     * @return float Quantity in purchase units
     */
    public static function convertToPurchaseUnit($quantity, $fromUnitId, $ingredient): float
    {
        $purchaseUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

        if (!$purchaseUnitId || $fromUnitId == $purchaseUnitId) {
            return (float) $quantity;
        }

        return self::safeConvert($quantity, $fromUnitId, $purchaseUnitId);
    }

    /**
     * Get stock quantity in base units for accurate tracking
     *
     * @param float $quantity The quantity
     * @param int|null $unitId The unit ID (null = already in base unit)
     * @param int $ingredientBaseUnitId The ingredient's base unit ID
     * @return float Quantity in base units
     */
    public static function getStockInBaseUnits($quantity, $unitId, $ingredientBaseUnitId): float
    {
        if (!$unitId || $unitId == $ingredientBaseUnitId) {
            return (float) $quantity;
        }

        return self::safeConvert($quantity, $unitId, $ingredientBaseUnitId);
    }

    /**
     * Validate unit setup for an ingredient
     *
     * @param \Modules\Ingredient\app\Models\Ingredient $ingredient
     * @return array ['valid' => bool, 'errors' => []]
     */
    public static function validateIngredientUnits($ingredient): array
    {
        $errors = [];

        // Check if purchase and consumption units are set
        if (!$ingredient->purchase_unit_id && !$ingredient->unit_id) {
            $errors[] = "No purchase unit configured";
        }

        if (!$ingredient->consumption_unit_id && !$ingredient->unit_id) {
            $errors[] = "No consumption unit configured";
        }

        // Check if units are compatible
        $purchaseUnit = $ingredient->purchase_unit_id ?? $ingredient->unit_id;
        $consumptionUnit = $ingredient->consumption_unit_id ?? $ingredient->unit_id;

        if ($purchaseUnit && $consumptionUnit && !self::areUnitsCompatible($purchaseUnit, $consumptionUnit)) {
            $errors[] = "Purchase unit and consumption unit are not compatible";
        }

        // Validate conversion rate
        if ($ingredient->conversion_rate && $ingredient->conversion_rate <= 0) {
            $errors[] = "Invalid conversion rate: must be greater than 0";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Calculate cost per unit after conversion
     *
     * @param float $totalCost The total cost
     * @param float $quantity The quantity in source unit
     * @param int $sourceUnitId Source unit ID
     * @param int $targetUnitId Target unit ID
     * @return float Cost per target unit
     */
    public static function calculateCostPerUnit($totalCost, $quantity, $sourceUnitId, $targetUnitId): float
    {
        if ($quantity <= 0) {
            return 0;
        }

        // Get quantity in target units
        $targetQuantity = self::safeConvert($quantity, $sourceUnitId, $targetUnitId);

        if ($targetQuantity <= 0) {
            return 0;
        }

        return $totalCost / $targetQuantity;
    }
}
