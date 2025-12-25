<?php

namespace App\Helpers;

use Modules\Product\app\Models\UnitType;
use Exception;

class UnitConverter
{
    /**
     * Convert quantity from one unit to another
     * 
     * @param float $quantity The quantity to convert
     * @param int $fromUnitId The source unit ID
     * @param int $toUnitId The target unit ID
     * @return float The converted quantity
     */
    public static function convert($quantity, $fromUnitId, $toUnitId)
    {
        if ($fromUnitId == $toUnitId) {
            return $quantity;
        }

        $fromUnit = UnitType::find($fromUnitId);
        $toUnit = UnitType::find($toUnitId);

        if (!$fromUnit || !$toUnit) {
            throw new Exception("Invalid unit ID provided");
        }

        // Check if units are in the same family (have same base or are base themselves)
        $fromBase = $fromUnit->base_unit ?? $fromUnit->id;
        $toBase = $toUnit->base_unit ?? $toUnit->id;

        if ($fromBase != $toBase && $fromUnit->id != $toBase && $toUnit->id != $fromBase) {
            throw new Exception("Cannot convert between units from different families");
        }

        // Convert to base unit first, then to target unit
        $baseQuantity = self::convertToBase($quantity, $fromUnit);
        return self::convertFromBase($baseQuantity, $toUnit);
    }

    /**
     * Convert quantity to the base unit
     * 
     * @param float $quantity The quantity to convert
     * @param UnitType $unit The unit to convert from
     * @return float The quantity in base unit
     */
    public static function convertToBase($quantity, $unit)
    {
        if (!$unit->base_unit) {
            // This is already a base unit
            return $quantity;
        }

        $operator = $unit->operator ?? '*';
        $operatorValue = $unit->operator_value ?? 1;

        // Apply the conversion
        if ($operator == '*') {
            return $quantity * $operatorValue;
        } elseif ($operator == '/') {
            return $quantity / $operatorValue;
        }

        return $quantity;
    }

    /**
     * Convert quantity from base unit to target unit
     * 
     * @param float $baseQuantity The quantity in base unit
     * @param UnitType $targetUnit The target unit
     * @return float The converted quantity
     */
    public static function convertFromBase($baseQuantity, $targetUnit)
    {
        if (!$targetUnit->base_unit) {
            // Target is base unit, no conversion needed
            return $baseQuantity;
        }

        $operator = $targetUnit->operator ?? '*';
        $operatorValue = $targetUnit->operator_value ?? 1;

        // Reverse the conversion
        if ($operator == '*') {
            return $baseQuantity / $operatorValue;
        } elseif ($operator == '/') {
            return $baseQuantity * $operatorValue;
        }

        return $baseQuantity;
    }

    /**
     * Convert quantity to product's base unit
     * 
     * @param float $quantity The quantity to convert
     * @param int $fromUnitId The source unit ID
     * @param int $productBaseUnitId The product's base unit ID
     * @return float The converted quantity
     */
    public static function convertToProductBaseUnit($quantity, $fromUnitId, $productBaseUnitId)
    {
        return self::convert($quantity, $fromUnitId, $productBaseUnitId);
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
    }

    /**
     * Format quantity with unit name
     * 
     * @param float $quantity The quantity
     * @param int $unitId The unit ID
     * @param bool $useShortName Use short name instead of full name
     * @return string Formatted string like "5.5 L" or "5.5 Liter"
     */
    public static function formatWithUnit($quantity, $unitId, $useShortName = true)
    {
        $unit = UnitType::find($unitId);
        
        if (!$unit) {
            return number_format($quantity, 2);
        }

        $unitName = $useShortName ? $unit->ShortName : $unit->name;
        return number_format($quantity, 2) . ' ' . $unitName;
    }

    /**
     * Check if two units are compatible (in same family)
     * 
     * @param int $unitId1 First unit ID
     * @param int $unitId2 Second unit ID
     * @return bool True if units are compatible
     */
    public static function areUnitsCompatible($unitId1, $unitId2)
    {
        if ($unitId1 == $unitId2) {
            return true;
        }

        $unit1 = UnitType::find($unitId1);
        $unit2 = UnitType::find($unitId2);

        if (!$unit1 || !$unit2) {
            return false;
        }

        $base1 = $unit1->base_unit ?? $unit1->id;
        $base2 = $unit2->base_unit ?? $unit2->id;

        return $base1 == $base2 || $unit1->id == $base2 || $unit2->id == $base1;
    }
}
