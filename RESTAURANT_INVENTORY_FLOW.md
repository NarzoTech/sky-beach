# Restaurant Inventory Management System

## Complete Flow Documentation

This document describes the complete inventory management flow for the restaurant system. Follow this guide for any future modifications.

---

## 1. Core Philosophy

**Restaurants manage stock by INGREDIENTS, not menu items.**

```
+------------------+     +------------------+     +------------------+
|    PURCHASES     | --> |   INGREDIENTS    | <-- |      SALES       |
|   (Stock IN)     |     |    (Stock)       |     |   (Stock OUT)    |
+------------------+     +------------------+     +------------------+
                               ^
                               |
                         +------------------+
                         |    WASTAGE/      |
                         |   ADJUSTMENTS    |
                         |   (Stock OUT)    |
                         +------------------+
```

### Key Rules:
1. **Menu items do NOT have stock** - only ingredients have stock
2. **Purchases INCREASE ingredient stock**
3. **Sales DECREASE ingredient stock via recipes**
4. **Wastage/Adjustments DECREASE ingredient stock**
5. **All stock stored in BASE UNIT (purchase unit)**

---

## 2. Database Structure

### 2.1 Unit Types (`unit_types`)

Units with conversion support.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Unit name (kg, gm, liter, ml, pcs) |
| ShortName | string | Short name |
| base_unit | int | Parent unit ID (null if base) |
| operator | enum | Conversion operator (*, /) |
| operator_value | decimal | Conversion multiplier |

**Conversion Examples:**
- 1 kg = 1000 gm (operator: *, value: 1000)
- 1 liter = 1000 ml (operator: *, value: 1000)

### 2.2 Ingredient Categories (`categories`)

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Category name |
| type | string | 'ingredient' |
| status | boolean | Active/Inactive |

**Examples:** Vegetables, Meat, Spices, Dairy, Oils

### 2.3 Ingredients (`ingredients`)

The heart of inventory management.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Ingredient name |
| sku | string | Unique code |
| category_id | bigint | Category reference |
| purchase_unit_id | bigint | Unit when buying (kg, liter) |
| consumption_unit_id | bigint | Unit when using in recipes (gm, ml) |
| conversion_rate | decimal(15,4) | How many consumption units = 1 purchase unit |
| purchase_price | decimal(15,4) | Price per purchase unit |
| consumption_unit_cost | decimal(15,4) | Cost per consumption unit (auto-calculated) |
| average_cost | decimal(15,4) | Weighted average cost per purchase unit |
| stock | decimal(15,4) | Current stock in PURCHASE UNIT |
| stock_alert | decimal(10,2) | Low stock alert threshold |
| status | boolean | Active/Inactive |

**Critical Formula:**
```
consumption_unit_cost = purchase_price / conversion_rate
```

**Example:**
- Chicken: Purchase in KG, consume in GM
- conversion_rate = 1000 (1 kg = 1000 gm)
- purchase_price = 500 (500/kg)
- consumption_unit_cost = 500/1000 = 0.50 per gm

### 2.4 Menu Categories (`menu_categories`)

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Category name |
| status | boolean | Active/Inactive |

**Examples:** Starters, Main Course, Drinks, Desserts

### 2.5 Menu Items (`menu_items`)

**Menu items have NO stock - only price and recipe.**

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | string | Menu item name |
| category_id | bigint | Menu category |
| base_price | decimal(10,2) | Selling price |
| cost_price | decimal(10,2) | Recipe cost (auto-calculated) |
| status | boolean | Active/Inactive |

### 2.6 Recipes (`recipes`)

Links menu items to ingredients (Bill of Materials).

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| menu_item_id | bigint | Menu item reference |
| ingredient_id | bigint | Ingredient reference |
| quantity_required | decimal(15,4) | Quantity needed in CONSUMPTION UNIT |
| unit_id | bigint | Unit (should match consumption_unit_id) |
| notes | text | Recipe notes |

**Example: Chicken Burger Recipe**
| Ingredient | Quantity | Unit |
|------------|----------|------|
| Chicken Breast | 150 | gm |
| Burger Bun | 1 | pcs |
| Oil | 10 | ml |
| Lettuce | 20 | gm |

---

## 3. Purchase Flow (Stock IN)

### 3.1 Tables

**purchases**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| supplier_id | bigint | Supplier reference |
| purchase_date | date | Date of purchase |
| total_amount | decimal | Total purchase amount |
| status | string | pending/approved |

**purchase_details**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| purchase_id | bigint | Purchase reference |
| ingredient_id | bigint | Ingredient reference |
| quantity | decimal | Quantity in purchase unit |
| unit_id | bigint | Purchase unit |
| purchase_price | decimal | Price per unit |
| sub_total | decimal | quantity × price |

### 3.2 Stock Update Logic

When purchase is APPROVED:

```php
foreach ($purchaseItems as $item) {
    $ingredient = Ingredient::find($item->ingredient_id);

    // 1. Get current values
    $oldStock = $ingredient->stock;
    $oldAvgCost = $ingredient->average_cost ?? $ingredient->purchase_price;

    // 2. Calculate new stock
    $newStock = $oldStock + $item->quantity;

    // 3. Calculate WEIGHTED AVERAGE COST
    $oldValue = $oldStock * $oldAvgCost;
    $newValue = $item->quantity * $item->purchase_price;
    $newAvgCost = ($oldValue + $newValue) / $newStock;

    // 4. Update ingredient
    $ingredient->stock = $newStock;
    $ingredient->average_cost = $newAvgCost;
    $ingredient->purchase_price = $item->purchase_price; // Latest price
    $ingredient->consumption_unit_cost = $newAvgCost / $ingredient->conversion_rate;
    $ingredient->save();

    // 5. Log stock movement
    Stock::create([
        'product_id' => $ingredient->id,
        'type' => 'Purchase',
        'in_quantity' => $item->quantity,
        'purchase_price' => $item->purchase_price,
        'date' => $purchase->purchase_date
    ]);
}
```

### 3.3 Weighted Average Cost Formula

```
New Avg Cost = (Old Stock × Old Avg Cost) + (New Qty × New Price)
               ─────────────────────────────────────────────────────
                              Old Stock + New Qty
```

**Example:**
- Current: 10 kg @ 450/kg = 4,500
- Purchase: 5 kg @ 500/kg = 2,500
- New Avg: (4,500 + 2,500) / 15 = 466.67/kg

---

## 4. Sales Flow (Stock OUT via Recipe)

### 4.1 Tables

**sales**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| invoice | string | Invoice number |
| customer_id | bigint | Customer reference |
| total_price | decimal | Subtotal |
| grand_total | decimal | Final total |
| order_date | date | Sale date |

**ingredient_sales** (ProductSale)
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| sale_id | bigint | Sale reference |
| menu_item_id | bigint | Menu item sold |
| quantity | int | Quantity sold |
| price | decimal | Selling price |
| sub_total | decimal | quantity × price |

### 4.2 Stock Deduction Logic

When sale is completed:

```php
foreach ($cartItems as $item) {
    if ($item['type'] == 'menu_item') {
        $menuItem = MenuItem::with('recipes.ingredient')->find($item['id']);
        $qtySold = $item['qty'];

        // Deduct each ingredient based on recipe
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;

            // Calculate consumption quantity
            $consumptionQty = $recipe->quantity_required * $qtySold;

            // Convert to purchase unit for stock deduction
            $deductInPurchaseUnit = $consumptionQty / $ingredient->conversion_rate;

            // Update stock
            $ingredient->stock -= $deductInPurchaseUnit;
            $ingredient->save();

            // Log stock movement
            Stock::create([
                'product_id' => $ingredient->id,
                'sale_id' => $sale->id,
                'type' => 'Sale',
                'out_quantity' => $consumptionQty, // In consumption unit
                'base_out_quantity' => $deductInPurchaseUnit, // In purchase unit
                'note' => 'Menu Item: ' . $menuItem->name
            ]);
        }
    }
}
```

### 4.3 Example: Selling 2 Chicken Burgers

**Recipe per burger:**
- Chicken: 150 gm
- Bun: 1 pcs
- Oil: 10 ml

**Deduction for 2 burgers:**
| Ingredient | Recipe | × Qty | Total | In Purchase Unit |
|------------|--------|-------|-------|------------------|
| Chicken | 150 gm | × 2 | 300 gm | 0.3 kg |
| Bun | 1 pcs | × 2 | 2 pcs | 2 pcs |
| Oil | 10 ml | × 2 | 20 ml | 0.02 liter |

---

## 5. Wastage & Adjustment (Stock Correction)

### 5.1 Tables

**stock_adjustments**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| ingredient_id | bigint | Ingredient reference |
| type | enum | wastage, damage, correction, theft |
| quantity | decimal | Quantity affected |
| unit_id | bigint | Unit of quantity |
| reason | text | Reason for adjustment |
| cost_impact | decimal | Financial impact |
| adjusted_by | bigint | User who made adjustment |
| adjustment_date | date | Date of adjustment |

### 5.2 Adjustment Logic

```php
public function createAdjustment($data)
{
    $ingredient = Ingredient::find($data['ingredient_id']);

    // Convert to purchase unit if needed
    $qtyInPurchaseUnit = $data['quantity'];
    if ($data['unit_id'] != $ingredient->purchase_unit_id) {
        $qtyInPurchaseUnit = UnitConverter::convert(
            $data['quantity'],
            $data['unit_id'],
            $ingredient->purchase_unit_id
        );
    }

    // Calculate cost impact
    $costImpact = $qtyInPurchaseUnit * $ingredient->average_cost;

    // Deduct stock
    $ingredient->stock -= $qtyInPurchaseUnit;
    $ingredient->save();

    // Create adjustment record
    StockAdjustment::create([
        'ingredient_id' => $ingredient->id,
        'type' => $data['type'],
        'quantity' => $data['quantity'],
        'unit_id' => $data['unit_id'],
        'reason' => $data['reason'],
        'cost_impact' => $costImpact
    ]);

    // Log stock movement
    Stock::create([
        'product_id' => $ingredient->id,
        'type' => 'Adjustment',
        'out_quantity' => $qtyInPurchaseUnit,
        'note' => $data['type'] . ': ' . $data['reason']
    ]);
}
```

---

## 6. Cost of Goods Sold (COGS)

### 6.1 Per Menu Item COGS

```php
public function calculateMenuItemCOGS(MenuItem $menuItem)
{
    $totalCost = 0;

    foreach ($menuItem->recipes as $recipe) {
        $ingredient = $recipe->ingredient;

        // Cost = quantity in consumption unit × cost per consumption unit
        $ingredientCost = $recipe->quantity_required * $ingredient->consumption_unit_cost;
        $totalCost += $ingredientCost;
    }

    return $totalCost;
}
```

### 6.2 Per Sale COGS

```php
public function calculateSaleCOGS(Sale $sale)
{
    $totalCOGS = 0;

    foreach ($sale->menuItems as $saleItem) {
        $menuItem = MenuItem::find($saleItem->menu_item_id);
        $itemCOGS = $this->calculateMenuItemCOGS($menuItem);
        $totalCOGS += $itemCOGS * $saleItem->quantity;
    }

    return $totalCOGS;
}
```

### 6.3 Example COGS Calculation

**Chicken Burger Recipe Cost:**
| Ingredient | Qty | Unit Cost | Total |
|------------|-----|-----------|-------|
| Chicken | 150 gm | 0.50/gm | 75.00 |
| Bun | 1 pcs | 15.00/pcs | 15.00 |
| Oil | 10 ml | 0.08/ml | 0.80 |
| Lettuce | 20 gm | 0.10/gm | 2.00 |
| **Total COGS** | | | **92.80** |

**Selling Price:** 250.00
**Profit:** 250 - 92.80 = **157.20 (62.88% margin)**

---

## 7. Profit & Loss Calculation

### 7.1 Daily P&L

```php
public function calculateDailyPL($date)
{
    // REVENUE
    $revenue = Sale::whereDate('order_date', $date)->sum('grand_total');

    // COGS
    $cogs = 0;
    $sales = Sale::whereDate('order_date', $date)->with('menuItems')->get();
    foreach ($sales as $sale) {
        $cogs += $this->calculateSaleCOGS($sale);
    }

    // GROSS PROFIT
    $grossProfit = $revenue - $cogs;

    // WASTAGE COST
    $wastageCost = StockAdjustment::whereDate('adjustment_date', $date)
        ->whereIn('type', ['wastage', 'damage'])
        ->sum('cost_impact');

    // NET PROFIT (before other expenses)
    $netProfit = $grossProfit - $wastageCost;

    return [
        'revenue' => $revenue,
        'cogs' => $cogs,
        'gross_profit' => $grossProfit,
        'gross_margin' => ($grossProfit / $revenue) * 100,
        'wastage_cost' => $wastageCost,
        'net_profit' => $netProfit
    ];
}
```

### 7.2 P&L Report Structure

```
+------------------------------------------+
|          PROFIT & LOSS STATEMENT         |
|              Date: 2026-01-03            |
+------------------------------------------+
| REVENUE                                  |
|   Total Sales              +  50,000.00  |
+------------------------------------------+
| COST OF GOODS SOLD                       |
|   Ingredient Costs         -  18,500.00  |
+------------------------------------------+
| GROSS PROFIT                   31,500.00 |
| Gross Margin                      63.00% |
+------------------------------------------+
| OTHER COSTS                              |
|   Wastage                  -     500.00  |
|   Adjustments              -     200.00  |
+------------------------------------------+
| NET PROFIT                     30,800.00 |
+------------------------------------------+
```

---

## 8. Stock Movement Log

Every stock change MUST be logged for audit.

### 8.1 Stock Table (`stocks`)

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| product_id | bigint | Ingredient ID |
| purchase_id | bigint | Purchase reference (if applicable) |
| sale_id | bigint | Sale reference (if applicable) |
| adjustment_id | bigint | Adjustment reference (if applicable) |
| type | string | Purchase, Sale, Adjustment, Return |
| in_quantity | decimal | Quantity added |
| out_quantity | decimal | Quantity removed |
| unit_id | bigint | Unit of quantity |
| base_in_quantity | decimal | In quantity in purchase unit |
| base_out_quantity | decimal | Out quantity in purchase unit |
| purchase_price | decimal | Cost at time of transaction |
| note | text | Additional notes |
| date | date | Transaction date |

### 8.2 Stock Recalculation

If stock ever becomes inconsistent:

```php
public function recalculateStock($ingredientId)
{
    $movements = Stock::where('product_id', $ingredientId)
        ->orderBy('date')
        ->orderBy('id')
        ->get();

    $balance = 0;
    foreach ($movements as $movement) {
        $balance += $movement->base_in_quantity ?? 0;
        $balance -= $movement->base_out_quantity ?? 0;
    }

    Ingredient::find($ingredientId)->update(['stock' => $balance]);

    return $balance;
}
```

---

## 9. Low Stock Alerts

### 9.1 Alert Logic

```php
public function getLowStockIngredients()
{
    return Ingredient::where('status', 1)
        ->whereColumn('stock', '<=', 'stock_alert')
        ->with('purchaseUnit')
        ->get();
}
```

### 9.2 Auto-Disable Menu Items

```php
public function checkMenuItemAvailability(MenuItem $menuItem)
{
    foreach ($menuItem->recipes as $recipe) {
        $ingredient = $recipe->ingredient;
        $requiredInPurchaseUnit = $recipe->quantity_required / $ingredient->conversion_rate;

        if ($ingredient->stock < $requiredInPurchaseUnit) {
            return false; // Not enough stock
        }
    }
    return true;
}
```

---

## 10. Reports

### 10.1 Available Reports

| Report | Description |
|--------|-------------|
| **Ingredient Stock Report** | Current stock levels with valuation |
| **Low Stock Alert** | Ingredients below alert threshold |
| **Daily Sales Report** | Sales summary with COGS |
| **Menu Item Profitability** | Profit margin per item |
| **Ingredient Consumption** | Usage by ingredient over time |
| **Purchase History** | Purchase records by supplier/date |
| **Wastage Report** | Wastage costs and reasons |
| **P&L Statement** | Daily/Weekly/Monthly profit |

---

## 11. Edge Cases & Best Practices

### 11.1 Handled Scenarios

| Scenario | Solution |
|----------|----------|
| Same ingredient in multiple menus | Recipe-based deduction handles this |
| Partial unit usage (0.5 pcs) | Decimal quantities supported |
| Price change doesn't affect past profit | COGS calculated at time of sale |
| Ingredient price fluctuation | Weighted average cost |
| Out of stock ingredient | Block sale or warn user |
| Manual stock correction | Stock adjustment module |

### 11.2 Best Practices

1. **Always use base units for storage** - Stock in purchase unit, recipes in consumption unit
2. **Log every stock movement** - Never modify stock without logging
3. **Use weighted average cost** - More accurate than FIFO/LIFO for restaurants
4. **Calculate COGS at sale time** - Store cost_price in sale details
5. **Regular stock audits** - Compare physical vs system stock monthly
6. **Track wastage religiously** - Major profit leak in restaurants

---

## 12. Module Dependencies

```
┌─────────────────┐
│   Unit Types    │ ◄── Foundation for all units
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Ingredients    │ ◄── Stock management core
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌────────┐  ┌────────┐
│Purchase│  │ Recipe │
└────┬───┘  └───┬────┘
     │          │
     │     ┌────┴────┐
     │     │         │
     ▼     ▼         ▼
┌─────────────┐  ┌────────┐
│Stock Movement│  │MenuItem│
└─────────────┘  └───┬────┘
     ▲               │
     │               ▼
     │          ┌────────┐
     └──────────│  POS   │
                │ Sales  │
                └────────┘
```

---

## 13. API Endpoints Reference

### Ingredients
- `GET /admin/ingredient` - List ingredients
- `POST /admin/ingredient` - Create ingredient
- `PUT /admin/ingredient/{id}` - Update ingredient
- `DELETE /admin/ingredient/{id}` - Delete ingredient

### Purchases
- `GET /admin/purchase` - List purchases
- `POST /admin/purchase` - Create purchase
- `PUT /admin/purchase/{id}` - Update purchase
- `POST /admin/purchase/{id}/approve` - Approve & update stock

### Menu Items
- `GET /admin/menu/items` - List menu items
- `POST /admin/menu/items` - Create menu item
- `PUT /admin/menu/items/{id}` - Update menu item
- `POST /admin/menu/items/{id}/recipe` - Manage recipe

### POS/Sales
- `GET /admin/pos` - POS interface
- `POST /admin/pos/add-to-cart` - Add menu item to cart
- `POST /admin/pos/place-order` - Complete sale & deduct stock

### Stock Adjustments
- `GET /admin/stock-adjustment` - List adjustments
- `POST /admin/stock-adjustment` - Create adjustment

### Reports
- `GET /admin/reports/stock` - Stock report
- `GET /admin/reports/low-stock` - Low stock alert
- `GET /admin/reports/profit-loss` - P&L report
- `GET /admin/reports/menu-profitability` - Menu item profits

---

## 14. Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2026-01-03 | 1.0.0 | Initial documentation |
| | | - POS converted to menu items |
| | | - Recipe-based stock deduction |
| | | - Weighted average cost |
| | | - Stock adjustment module |

---

**Document maintained by:** Development Team
**Last updated:** 2026-01-03
