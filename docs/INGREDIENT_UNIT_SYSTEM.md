# Ingredient Unit Conversion & Stock Tracking System

## Overview

This document describes the unit conversion and stock tracking system for ingredients used in menu items. The system supports:

- **Hierarchical unit families** (e.g., Liter -> Milliliter)
- **Separate purchase and consumption units** per ingredient
- **Automatic unit conversion** when tracking stock
- **Weighted average cost** calculation
- **Recipe-based consumption** tracking

---

## Table of Contents

1. [Unit Structure](#1-unit-structure)
2. [Ingredient Configuration](#2-ingredient-configuration)
3. [Conversion Logic](#3-conversion-logic)
4. [Stock Tracking](#4-stock-tracking)
5. [Recipe & Menu Integration](#5-recipe--menu-integration)
6. [Cost Calculation](#6-cost-calculation)
7. [API Reference](#7-api-reference)
8. [Examples](#8-examples)
9. [Database Schema](#9-database-schema)
10. [Best Practices](#10-best-practices)

---

## 1. Unit Structure

### Unit Types Table (`unit_types`)

Units are organized in a hierarchical structure:

```
Base Unit (base_unit = NULL)
├── Child Unit 1 (base_unit = Base.id)
├── Child Unit 2 (base_unit = Base.id)
└── ...
```

### Fields

| Field | Description | Example |
|-------|-------------|---------|
| `name` | Full unit name | "Kilogram" |
| `ShortName` | Abbreviation | "Kg" |
| `base_unit` | Parent unit ID (NULL for base units) | NULL or 1 |
| `operator` | Conversion operator (`*` or `/`) | `/` |
| `operator_value` | Conversion factor | 1000 |
| `status` | Active (1) or Inactive (0) | 1 |

### Operator Logic

The operator defines how to convert FROM base unit TO child unit:

- **`*` (Multiply)**: `child_value = base_value * operator_value`
- **`/` (Divide)**: `child_value = base_value / operator_value`

### Example: Volume Units

```
Liter (base_unit = NULL)
└── Milliliter
    ├── base_unit = Liter.id
    ├── operator = '/'
    └── operator_value = 1000

Conversion: 1 Liter = 1 / (1/1000) = 1000 ml
```

### Example: Weight Units

```
Kilogram (base_unit = NULL)
└── Gram
    ├── base_unit = Kilogram.id
    ├── operator = '/'
    └── operator_value = 1000

Conversion: 1 Kg = 1000 grams
```

---

## 2. Ingredient Configuration

### Key Fields

| Field | Description | Example |
|-------|-------------|---------|
| `unit_id` | Legacy/default unit | Kilogram |
| `purchase_unit_id` | Unit for purchasing | Kilogram |
| `consumption_unit_id` | Unit for recipes | Gram |
| `conversion_rate` | Consumption units per purchase unit | 1000 |
| `purchase_price` | Price per purchase unit | 50.00 |
| `average_cost` | Weighted average cost per purchase unit | 48.50 |
| `consumption_unit_cost` | Cost per consumption unit (auto-calculated) | 0.0485 |
| `stock` | Current stock in **purchase units** | 10 |

### Auto-Calculation on Save

When an ingredient is saved, the system automatically:

1. Calculates `conversion_rate` from unit relationships if not set
2. Calculates `consumption_unit_cost = average_cost / conversion_rate`

### Example: Fresh Milk

```php
Ingredient: Fresh Milk
├── purchase_unit_id = Liter (id: 1)
├── consumption_unit_id = Milliliter (id: 2)
├── conversion_rate = 1000 (1 Liter = 1000 ml)
├── purchase_price = 60.00 (per Liter)
├── average_cost = 58.00 (weighted average)
├── consumption_unit_cost = 0.058 (per ml)
└── stock = 10 (Liters)
```

---

## 3. Conversion Logic

### UnitConverter Class

Location: `app/Helpers/UnitConverter.php`

### Key Methods

#### `convert($quantity, $fromUnitId, $toUnitId)`
Converts quantity between any two compatible units.

```php
// Convert 5 Liters to Milliliters
$ml = UnitConverter::convert(5, $literId, $mlId);
// Result: 5000
```

#### `safeConvert($quantity, $fromUnitId, $toUnitId)`
Same as `convert()` but returns original quantity if conversion fails.

```php
// Safe conversion with fallback
$result = UnitConverter::safeConvert(100, $fromId, $toId);
```

#### `convertToBase($quantity, $unit)`
Converts to the base unit of the family.

```php
// Convert 5000 ml to Liters (base)
$liters = UnitConverter::convertToBase(5000, $mlUnit);
// Result: 5
```

#### `areUnitsCompatible($unitId1, $unitId2)`
Checks if two units can be converted between each other.

```php
if (UnitConverter::areUnitsCompatible($kgId, $gramId)) {
    // Units are in the same family
}
```

#### `getConversionRate($fromUnitId, $toUnitId)`
Gets the multiplier to convert from one unit to another.

```php
$rate = UnitConverter::getConversionRate($literId, $mlId);
// Result: 1000 (multiply liters by 1000 to get ml)
```

### Conversion Flow

```
Source Unit → Base Unit → Target Unit

Example: 500 grams → ? pounds

1. grams → kg (base): 500 / 1000 = 0.5 kg
2. kg → pounds: 0.5 * 2.20462 = 1.10231 pounds
```

---

## 4. Stock Tracking

### Stock Storage

**Important**: Stock is always stored in **purchase units**.

```php
Ingredient: Tomato
├── purchase_unit = Kilogram
├── stock = 25 (means 25 Kg)
└── stock_in_consumption_units = 25000 (grams)
```

### Stock Table (`stocks`)

Each stock movement records:

| Field | Description |
|-------|-------------|
| `ingredient_id` | The ingredient |
| `unit_id` | Unit of the recorded quantity |
| `in_quantity` | Quantity added |
| `out_quantity` | Quantity removed |
| `base_in_quantity` | In quantity in base units |
| `base_out_quantity` | Out quantity in base units |
| `type` | Movement type (Purchase, Menu Sale, etc.) |
| `average_cost` | Cost at time of movement |

### Ingredient Model Methods

#### `deductStock($quantity, $unitId)`
Deducts stock with automatic unit conversion.

```php
// Deduct 500 grams (consumption unit) from stock
$ingredient->deductStock(500, $gramUnitId);
// Internally converts to 0.5 Kg and updates stock
```

#### `addStock($quantity, $unitId)`
Adds stock with automatic unit conversion.

```php
// Add 2 Liters to stock
$ingredient->addStock(2, $literUnitId);
```

#### `hasEnoughStock($quantity, $unitId)`
Checks if enough stock is available.

```php
// Check if 500ml is available
if ($ingredient->hasEnoughStock(500, $mlUnitId)) {
    // Proceed with sale
}
```

---

## 5. Recipe & Menu Integration

### Recipe Table (`recipes`)

| Field | Description |
|-------|-------------|
| `menu_item_id` | The menu item |
| `ingredient_id` | The ingredient |
| `quantity_required` | Amount needed per menu item |
| `unit_id` | Unit of the quantity (typically consumption unit) |

### Recipe Example

```
Menu Item: Vanilla Milkshake
├── Recipe 1: Fresh Milk
│   ├── quantity_required = 200
│   └── unit_id = Milliliter
├── Recipe 2: Sugar
│   ├── quantity_required = 30
│   └── unit_id = Gram
└── Recipe 3: Vanilla Ice Cream
    ├── quantity_required = 100
    └── unit_id = Gram
```

### Recipe Model Methods

#### `getQuantityInConsumptionUnits()`
Gets quantity in ingredient's consumption unit (for cost calculation).

#### `getQuantityInPurchaseUnits()`
Gets quantity in ingredient's purchase unit (for stock deduction).

#### `hasEnoughStock($menuItemQuantity)`
Checks if stock is available for N menu items.

```php
// Check if we can make 10 milkshakes
if ($recipe->hasEnoughStock(10)) {
    // Stock is sufficient
}
```

### MenuStockService

Location: `Modules/Menu/app/Services/MenuStockService.php`

#### `deductStockForSale($menuItemId, $quantity, $warehouseId)`

Deducts ingredients for a menu item sale:

```php
$service = new MenuStockService();
$deductions = $service->deductStockForSale(
    menuItemId: 1,
    quantity: 5,      // 5 milkshakes sold
    warehouseId: 1
);

// Returns:
[
    [
        'ingredient_name' => 'Fresh Milk',
        'recipe_quantity' => 1000,        // 200ml × 5
        'recipe_unit' => 'ml',
        'deducted_quantity' => 1,         // Converted to Liters
        'deducted_unit' => 'L',
        'remaining_stock' => 9,
    ],
    // ... more ingredients
]
```

#### `checkStockAvailability($menuItemId, $quantity, $warehouseId)`

Checks if all ingredients are available:

```php
$availability = $service->checkStockAvailability(1, 10, 1);

if (!$availability['available']) {
    foreach ($availability['shortages'] as $shortage) {
        echo "{$shortage['ingredient_name']}: need {$shortage['required']}, have {$shortage['available']}";
    }
}
```

---

## 6. Cost Calculation

### Cost Flow

```
Purchase Price → Average Cost → Consumption Unit Cost → Recipe Cost → Menu Item Cost

Example:
1. Purchase: 10 Kg tomatoes @ ₹50/Kg
2. Average Cost: ₹48.50/Kg (weighted with previous purchases)
3. Consumption Unit Cost: ₹48.50 / 1000 = ₹0.0485/gram
4. Recipe Cost (200g): 200 × ₹0.0485 = ₹9.70
5. Menu Item Cost: Sum of all recipe costs
```

### Weighted Average Cost

Updated on each purchase:

```
New Average = (Current Stock × Current Avg + New Qty × New Price) / (Current Stock + New Qty)

Example:
- Current: 10 Kg @ ₹48/Kg = ₹480
- Purchase: 5 Kg @ ₹52/Kg = ₹260
- New Average: (480 + 260) / 15 = ₹49.33/Kg
```

### Ingredient Model Methods

#### `calculateConsumptionCost($quantity)`
Calculates cost for a quantity in consumption units.

```php
// Cost of 500 grams
$cost = $ingredient->calculateConsumptionCost(500);
// Uses consumption_unit_cost
```

#### `calculateCost($quantity, $unitId)`
Calculates cost for any unit.

```php
// Cost of 2 Kg
$cost = $ingredient->calculateCost(2, $kgUnitId);
```

#### `updateAverageCost($purchaseQty, $purchasePrice)`
Updates weighted average after a purchase.

```php
$ingredient->updateAverageCost(10, 55.00);
$ingredient->save();
```

---

## 7. API Reference

### UnitConverter

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `convert` | quantity, fromUnitId, toUnitId | float | Convert between units |
| `safeConvert` | quantity, fromUnitId, toUnitId | float | Convert with fallback |
| `convertToBase` | quantity, unit | float | Convert to base unit |
| `convertFromBase` | baseQuantity, targetUnit | float | Convert from base unit |
| `areUnitsCompatible` | unitId1, unitId2 | bool | Check unit compatibility |
| `getConversionRate` | fromUnitId, toUnitId | float | Get conversion multiplier |
| `getUnitFamily` | unitId | Collection | Get all related units |
| `formatWithUnit` | quantity, unitId, useShortName | string | Format "5.00 Kg" |
| `validateIngredientUnits` | ingredient | array | Validate ingredient config |

### Ingredient Model

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `convertToConsumptionUnits` | quantity, fromUnitId | float | Convert to consumption unit |
| `convertToPurchaseUnits` | quantity, fromUnitId | float | Convert to purchase unit |
| `purchaseToConsumptionUnits` | purchaseQty | float | Use stored conversion rate |
| `consumptionToPurchaseUnits` | consumptionQty | float | Use stored conversion rate |
| `calculateConsumptionCost` | quantity | float | Cost in consumption units |
| `calculateCost` | quantity, unitId | float | Cost for any unit |
| `deductStock` | quantity, unitId | bool | Deduct with conversion |
| `addStock` | quantity, unitId | bool | Add with conversion |
| `hasEnoughStock` | quantity, unitId | bool | Check availability |
| `updateAverageCost` | qty, price | void | Update weighted average |
| `validateUnitConfiguration` | - | array | Validate unit setup |

### Recipe Model

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `getQuantityInConsumptionUnits` | - | float | Quantity for costing |
| `getQuantityInPurchaseUnits` | - | float | Quantity for stock |
| `hasEnoughStock` | menuItemQty | bool | Check stock availability |
| `getStockShortage` | menuItemQty | array\|null | Get shortage details |
| `deductStock` | menuItemQty | bool | Deduct ingredient stock |
| `calculateTotalCost` | menuItemQty | float | Cost for N items |
| `validate` | - | array | Validate configuration |

### MenuStockService

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `deductStockForSale` | menuItemId, qty, warehouseId, ref | array | Deduct for sale |
| `checkStockAvailability` | menuItemId, qty, warehouseId | array | Check all ingredients |
| `reverseStockDeduction` | menuItemId, qty, warehouseId, ref | array | Reverse for cancellation |
| `calculateIngredientCost` | menuItemId | array | Get cost breakdown |
| `getLowStockAlerts` | - | array | Get low stock warnings |
| `getStockMovementHistory` | ingredientId, warehouseId, from, to | array | Get movement log |

---

## 8. Examples

### Example 1: Setting Up a New Ingredient

```php
use Modules\Ingredient\app\Models\Ingredient;

// Create ingredient with proper unit configuration
$ingredient = Ingredient::create([
    'name' => 'Fresh Milk',
    'purchase_unit_id' => 1,        // Liter
    'consumption_unit_id' => 2,     // Milliliter
    'conversion_rate' => 1000,      // 1 L = 1000 ml
    'purchase_price' => 60.00,      // ₹60 per Liter
    'stock' => 0,
    'stock_alert' => 5,             // Alert when < 5 Liters
]);

// consumption_unit_cost is auto-calculated: 60/1000 = ₹0.06/ml
```

### Example 2: Recording a Purchase

```php
// Purchase 20 Liters of milk at ₹58/L
$purchaseQty = 20;
$purchasePrice = 58.00;

// Update weighted average
$ingredient->updateAverageCost($purchaseQty, $purchasePrice);

// Add to stock
$ingredient->addStock($purchaseQty, $ingredient->purchase_unit_id);
$ingredient->save();

// Create stock record
Stock::create([
    'ingredient_id' => $ingredient->id,
    'in_quantity' => $purchaseQty,
    'unit_id' => $ingredient->purchase_unit_id,
    'type' => 'Purchase',
    'purchase_price' => $purchasePrice,
    'average_cost' => $ingredient->average_cost,
    'date' => now(),
]);
```

### Example 3: Creating a Recipe

```php
use Modules\Menu\app\Models\Recipe;

// Recipe: 200ml milk for a milkshake
$recipe = Recipe::create([
    'menu_item_id' => $milkshakeId,
    'ingredient_id' => $milkId,
    'quantity_required' => 200,
    'unit_id' => $mlUnitId,        // Milliliter
]);

// Get cost for this recipe
$cost = $recipe->ingredient_cost;  // 200 × ₹0.06 = ₹12.00
```

### Example 4: Selling a Menu Item

```php
use Modules\Menu\app\Services\MenuStockService;

$service = new MenuStockService();

// Check availability first
$availability = $service->checkStockAvailability(
    menuItemId: $milkshakeId,
    quantity: 5,
    warehouseId: 1
);

if ($availability['available']) {
    // Deduct stock
    $deductions = $service->deductStockForSale(
        menuItemId: $milkshakeId,
        quantity: 5,
        warehouseId: 1,
        reference: 'ORDER-001'
    );

    // Log what was deducted
    foreach ($deductions as $d) {
        logger()->info("{$d['ingredient_name']}: -{$d['deducted_quantity']} {$d['deducted_unit']}");
    }
} else {
    // Handle shortage
    foreach ($availability['shortages'] as $shortage) {
        logger()->warning("Insufficient {$shortage['ingredient_name']}");
    }
}
```

### Example 5: Cancelling an Order

```php
// Reverse the stock deduction
$reversals = $service->reverseStockDeduction(
    menuItemId: $milkshakeId,
    quantity: 5,
    warehouseId: 1,
    reference: 'ORDER-001-CANCELLED'
);
```

---

## 9. Database Schema

### unit_types
```sql
CREATE TABLE unit_types (
    id BIGINT PRIMARY KEY,
    name VARCHAR(250),
    ShortName VARCHAR(192),
    base_unit INT NULL,              -- FK to self for parent
    operator CHAR(1) DEFAULT '*',    -- '*' or '/'
    operator_value FLOAT DEFAULT 1,
    status TINYINT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### ingredients
```sql
CREATE TABLE ingredients (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    unit_id BIGINT,                  -- Legacy/default unit
    purchase_unit_id BIGINT,         -- Unit for purchasing
    consumption_unit_id BIGINT,      -- Unit for recipes
    conversion_rate DECIMAL(15,4),   -- Consumption per purchase
    purchase_price DECIMAL(15,4),    -- Price per purchase unit
    average_cost DECIMAL(15,4),      -- Weighted average cost
    consumption_unit_cost DECIMAL(15,4), -- Cost per consumption unit
    stock INT DEFAULT 0,             -- In PURCHASE units
    stock_alert FLOAT,               -- Low stock threshold
    stock_status ENUM('in_stock','out_of_stock','low_stock'),
    -- ... other fields
);
```

### stocks
```sql
CREATE TABLE stocks (
    id BIGINT PRIMARY KEY,
    ingredient_id BIGINT,
    warehouse_id BIGINT,
    unit_id BIGINT,                  -- Unit of recorded quantity
    in_quantity INT DEFAULT 0,
    out_quantity INT DEFAULT 0,
    base_in_quantity DECIMAL(20,4),  -- In base units
    base_out_quantity DECIMAL(20,4), -- In base units
    type VARCHAR(50),                -- Purchase, Menu Sale, etc.
    purchase_price DECIMAL(10,2),
    average_cost DECIMAL(15,4),
    date DATE,
    -- ... other fields
);
```

### recipes
```sql
CREATE TABLE recipes (
    id BIGINT PRIMARY KEY,
    menu_item_id BIGINT,
    ingredient_id BIGINT,
    quantity_required DECIMAL(12,4),
    unit_id BIGINT,                  -- Typically consumption unit
    notes TEXT,
    UNIQUE(menu_item_id, ingredient_id)
);
```

---

## 10. Best Practices

### Unit Setup

1. **Always create base units first**, then child units
2. **Use `/` operator for smaller units** (ml, gram)
3. **Use `*` operator for larger units** (dozen = piece × 12)
4. **Ensure operator_value is never 0**

### Ingredient Configuration

1. **Always set both purchase_unit_id and consumption_unit_id**
2. **Set conversion_rate explicitly** or let the system calculate it
3. **Set stock_alert** for low stock notifications
4. **Review consumption_unit_cost** after price changes

### Recipe Setup

1. **Use consumption units** for recipe quantities (grams, ml)
2. **Validate unit compatibility** before saving
3. **Check ingredient.validateUnitConfiguration()** for issues

### Stock Operations

1. **Always use model methods** (deductStock, addStock) instead of direct updates
2. **Create Stock records** for audit trail
3. **Check availability** before deducting
4. **Handle cancellations** with reverseStockDeduction

### Cost Management

1. **Update average_cost** on each purchase
2. **Monitor consumption_unit_cost** for menu pricing
3. **Recalculate menu item costs** after ingredient price changes

---

## Troubleshooting

### Common Issues

**1. Conversion returns wrong values**
- Check operator and operator_value in unit_types
- Verify units are in the same family (same base_unit)

**2. Stock not updating correctly**
- Ensure using model methods, not direct DB updates
- Check unit_id matches ingredient's purchase_unit_id

**3. Recipe cost seems wrong**
- Verify consumption_unit_cost is calculated
- Check recipe unit matches ingredient's consumption_unit

**4. Units not compatible error**
- Ensure both units have the same base_unit
- Check for circular references in unit hierarchy

### Debug Commands

```php
// Check unit configuration
$validation = $ingredient->validateUnitConfiguration();
dd($validation);

// Check conversion rate
$rate = UnitConverter::getConversionRate($fromId, $toId);
dd($rate);

// Check unit family
$family = UnitConverter::getUnitFamily($unitId);
dd($family->pluck('name', 'id'));
```

---

## Version History

- **v1.0** (2026-01-11): Initial implementation
  - Hierarchical unit system
  - Dual unit support (purchase/consumption)
  - Unit-aware stock tracking
  - Recipe integration with cost calculation
  - MenuStockService for automated deductions
