# Product Unit System Analysis

## Business Requirement
Restaurant needs to:
- **Purchase products** in bulk units: liters (L), kilograms (kg)
- **Use products** in menu/food items in smaller units: milliliters (ml), grams (g)

## Example Scenarios
1. **Oil**: Buy in liters → Use in recipes as ml (1L = 1000ml)
2. **Flour**: Buy in kg → Use in recipes as grams (1kg = 1000g)

## Analysis Completed
Date: 2025-12-25

---

## Current Implementation Status

### ✅ What EXISTS:
1. **Unit Type System** (`unit_types` table)
   - Has `base_unit` field (nullable integer - references parent unit)
   - Has `operator` field (char: `*` or `/`)
   - Has `operator_value` field (float - conversion factor)
   - Supports parent-child unit relationships

2. **Product-Unit Association**
   - Products have `unit_id` field (links to unit_types table)
   - Products are assigned ONE unit only
   - Unit displayed in POS cart and invoices

3. **Unit Display**
   - Purchase module shows unit ShortName
   - POS module shows unit name
   - Sales invoices show unit name

### ❌ What's MISSING:
1. **No Unit Conversion Logic**
   - Stock is stored in product's base unit only
   - No conversion happens during purchase/sale
   - operator_value is stored but NEVER USED in calculations

2. **No Multiple Units Per Product**
   - Product can only have ONE unit assigned
   - Cannot buy in liters and sell in ml
   - Cannot track different units for same product

3. **No Stock Unit Conversion**
   - Stock model has no unit tracking
   - All quantities stored in single unit
   - No conversion when purchasing/selling in different units

---

## Database Schema Analysis

### unit_types Table
```php
id                 - Primary key
name               - Full name (e.g., "Liter", "Milliliter")
ShortName          - Short name (e.g., "L", "ml")
base_unit          - Parent unit ID (nullable)
operator           - '*' or '/' (for conversion)
operator_value     - Conversion factor (e.g., 1000 for ml to L)
status             - Active/Inactive
```

### products Table
```php
unit_id            - Single unit assignment
stock              - Quantity (in assigned unit)
cost               - Purchase cost per unit
```

### stocks Table
```php
in_quantity        - Purchase quantity
out_quantity       - Sale quantity
// NO unit_id field - assumes product's unit
```

### purchase_details Table
```php
quantity           - Purchase quantity
purchase_price     - Price per unit
// NO unit_id field - assumes product's unit
```

---

## Critical Issues Found

### Issue #1: NO Unit Conversion in Purchase
**Location**: `Modules/Purchase/app/Services/PurchaseService.php` (Lines 101-116)

```php
// Current code - NO conversion:
$product->stock += $request->quantity[$index];  // Direct addition
```

**Problem**: If product unit is "L" but purchasing in "ml", stock becomes incorrect.

### Issue #2: NO Unit Conversion in Sales
**Location**: `Modules/POS/app/Http/Controllers/POSController.php` (Line 312)

```php
// Cart shows unit name but no conversion
$data['unit'] = $product->unit->name;
```

**Problem**: Cannot sell in different unit than purchase unit.

### Issue #3: NO Unit Selection in Purchase Form
**Location**: `Modules/Purchase/resources/views/create.blade.php`

**Problem**: No dropdown to select purchase unit - always uses product's assigned unit.

### Issue #4: Stock Model Missing Unit Field
**Location**: `app/Models/Stock.php`

**Problem**: Stock entries don't track which unit was used for the transaction.

---

## Required Changes for Multi-Unit Support

### Phase 1: Database Changes
1. Add `unit_id` to `purchase_details` table
2. Add `unit_id` to `stocks` table  
3. Add `unit_id` to `product_sales` table (if exists)
4. Consider adding `base_stock_quantity` to products (for normalized storage)

### Phase 2: Unit Conversion Helper
Create conversion utility:
```php
class UnitConverter {
    public function convertToBase($quantity, $fromUnitId) {
        // Convert any unit to base unit
    }
    
    public function convertFromBase($quantity, $toUnitId) {
        // Convert base unit to target unit
    }
    
    public function convert($quantity, $fromUnitId, $toUnitId) {
        // Direct conversion between units
    }
}
```

### Phase 3: Purchase Module Updates
1. Add unit dropdown in purchase form
2. Store selected unit in purchase_details
3. Convert to base unit before updating stock
4. Display both purchase unit and quantity

### Phase 4: Sales/POS Module Updates
1. Add unit selection in POS
2. Convert from base unit for display
3. Convert to base unit when reducing stock
4. Show unit in cart and invoice

### Phase 5: Stock Management Updates
1. Store all stock in base unit (normalized)
2. Track transaction unit in stock records
3. Update reports to show correct units

---

## Recommended Implementation Approach

### Option A: Single Base Unit (RECOMMENDED)
- Store ALL stock in product's assigned base unit
- Convert on-the-fly during purchase/sale
- Simpler to maintain
- Less data redundancy

### Option B: Multiple Stock Entries
- Store stock separately for each unit
- More complex queries
- Risk of sync issues
- NOT RECOMMENDED

---

## Implementation Priority

### HIGH PRIORITY (Critical for Restaurant):
1. ✅ Unit conversion helper class
2. ✅ Purchase unit selection
3. ✅ Stock conversion logic
4. ✅ POS unit display with conversion

### MEDIUM PRIORITY:
5. Recipe/menu unit tracking
6. Unit-based pricing
7. Reports with unit conversion

### LOW PRIORITY:
8. Unit conversion history
9. Multi-currency-like unit handling

---

## Testing Scenarios Required

### Scenario 1: Oil Purchase & Usage
1. Setup: Create "Liter" (base) and "Milliliter" (1000 * ml = L)
2. Create Product: "Cooking Oil" with unit "Liter"
3. Purchase: 5 Liters at $10/L
4. Expected Stock: 5 L (or 5000 ml in base)
5. Sale: 500 ml
6. Expected Stock: 4.5 L (or 4500 ml)

### Scenario 2: Flour Purchase & Usage  
1. Setup: Create "Kilogram" (base) and "Gram" (1000 * g = kg)
2. Create Product: "Wheat Flour" with unit "Kilogram"
3. Purchase: 10 kg at $5/kg
4. Expected Stock: 10 kg (or 10000 g in base)
5. Sale: 250 g
6. Expected Stock: 9.75 kg (or 9750 g)

---

## Code Files That Need Changes

### Core Files:
1. ✅ `Modules/Product/app/Services/UnitTypeService.php` - Add conversion methods
2. ✅ `Modules/Purchase/app/Services/PurchaseService.php` - Add conversion in store/update
3. ✅ `Modules/Purchase/resources/views/create.blade.php` - Add unit selector
4. ✅ `Modules/POS/app/Http/Controllers/POSController.php` - Add unit conversion
5. ✅ `app/Models/Stock.php` - Track unit_id

### Migration Files Needed:
1. ✅ `add_unit_id_to_purchase_details_table.php`
2. ✅ `add_unit_id_to_stocks_table.php`
3. ✅ `add_base_quantity_to_products_table.php` (optional)

---

## Conclusion

**UPDATED STATUS**: ✅ Unit system is NOW FULLY IMPLEMENTED for multi-unit scenarios

### Implementation Complete (2025-12-25)

All required functionality has been implemented:
- ✅ **Unit conversion logic implemented** via UnitConverter helper class
- ✅ **Unit selection in purchase forms** with dropdown for unit family
- ✅ **Stock tracking with unit context** (unit_id, base quantities)
- ✅ **Automatic conversion** during purchase and sales
- ✅ **Sales/POS module updated** with unit support
- ✅ **Database migrations created and run** successfully

**The restaurant can now buy in L/kg and use in ml/g with automatic conversion! 🎉**

---

## Files Created/Modified

### New Files:
1. `app/Helpers/UnitConverter.php` - Unit conversion utility class
2. `database/migrations/2025_12_25_181000_add_unit_id_to_purchase_details_table.php`
3. `database/migrations/2025_12_25_181100_add_unit_id_to_stocks_table.php`
4. `database/migrations/2025_12_25_181200_add_unit_id_to_product_sales_table.php`
5. `tmp_rovodev_unit_test_guide.md` - Complete testing guide

### Modified Files:
1. `Modules/Product/app/Services/UnitTypeService.php` - Added conversion methods
2. `Modules/Product/app/Models/UnitType.php` - Added helper methods
3. `Modules/Product/app/Http/Controllers/ProductController.php` - Added getUnitFamily endpoint
4. `Modules/Product/routes/web.php` - Added unit-family route
5. `Modules/Purchase/app/Services/PurchaseService.php` - Unit conversion in store/update
6. `Modules/Purchase/app/Models/PurchaseDetails.php` - Added unit fields
7. `Modules/Purchase/resources/views/create.blade.php` - Unit dropdown UI
8. `Modules/Sales/app/Services/SaleService.php` - Unit conversion in sales
9. `Modules/Sales/app/Models/ProductSale.php` - Added unit fields
10. `app/Models/Stock.php` - Added unit tracking fields

---

## How to Test

See `tmp_rovodev_unit_test_guide.md` for complete testing instructions.

**Quick Test:**
1. Create units: Liter (base) and Milliliter (child, 1000x)
2. Create product: Cooking Oil (unit: Liter)
3. Purchase: 5 Liters → Stock shows 5 L
4. Purchase: 2000 Milliliters → Stock shows 7 L (automatic conversion!)
5. Sell in POS → Stock reduces correctly

**For restaurant use case, the system now fully supports buying in bulk units and tracking usage in smaller units with automatic conversion.**
