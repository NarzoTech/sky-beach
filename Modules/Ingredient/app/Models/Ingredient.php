<?php

namespace Modules\Ingredient\app\Models;

use App\Helpers\UnitConverter;
use App\Http\Resources\IngredientResource;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;
use Modules\Order\app\Models\OrderDetails;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturnDetails;
use Modules\Sales\app\Models\IngredientSale;
use Modules\Sales\app\Models\SalesReturnDetails;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';

    protected $fillable = [
        'name',
        'short_description',
        'brand_id',
        'category_id',
        'unit_id',
        'purchase_unit_id',
        'consumption_unit_id',
        'conversion_rate',
        'purchase_price',
        'average_cost',
        'consumption_unit_cost',
        'image',
        'cost',
        'stock_alert',
        'is_imei',
        'not_selling',
        'stock',
        'stock_status',
        'sku',
        'status',
        "tax_type",
        "tax",
        'is_favorite',
    ];

    public function getSingleImageAttribute()
    {

        $imageUrl =  $this->getImagesUrlAttribute();
        if ($imageUrl && file_exists(public_path($imageUrl))) {
            return asset($imageUrl);
        }
        return asset('backend/img/image_icon.png');
    }

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
        'conversion_rate' => 'decimal:4',
        'purchase_price' => 'decimal:4',
        'average_cost' => 'decimal:4',
        'consumption_unit_cost' => 'decimal:4',
    ];

    protected $with = ['unit', 'purchaseUnit', 'consumptionUnit'];

    protected $appends = [
        'image_url',
        'stock_status',
        'has_variant',
        'total_stock',
        'current_price',
        'single_image',
    ];

    /**
     * Boot method to calculate consumption_unit_cost on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-calculate conversion rate if units are set but rate is not
            if (!$model->conversion_rate || $model->conversion_rate <= 0) {
                $model->conversion_rate = $model->calculateConversionRate();
            }

            // Calculate consumption_unit_cost
            if ($model->conversion_rate && $model->conversion_rate > 0) {
                // Use average_cost for consumption calculation (weighted average)
                // Fall back to purchase_price if average_cost is not set
                $costBasis = $model->average_cost ?? $model->purchase_price ?? $model->cost;
                if ($costBasis) {
                    $model->consumption_unit_cost = $costBasis / $model->conversion_rate;
                }
            }
        });
    }

    /**
     * Calculate conversion rate from purchase unit to consumption unit
     *
     * @return float
     */
    public function calculateConversionRate(): float
    {
        $purchaseUnitId = $this->purchase_unit_id ?? $this->unit_id;
        $consumptionUnitId = $this->consumption_unit_id ?? $this->unit_id;

        if (!$purchaseUnitId || !$consumptionUnitId) {
            return 1;
        }

        if ($purchaseUnitId == $consumptionUnitId) {
            return 1;
        }

        try {
            return UnitConverter::getConversionRate($purchaseUnitId, $consumptionUnitId);
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Get the effective purchase unit ID
     *
     * @return int|null
     */
    public function getPurchaseUnitIdAttribute($value): ?int
    {
        return $value ?? $this->unit_id;
    }

    /**
     * Get the effective consumption unit ID
     *
     * @return int|null
     */
    public function getConsumptionUnitIdAttribute($value): ?int
    {
        return $value ?? $this->unit_id;
    }

    /**
     * Get the base unit ID for this ingredient
     *
     * @return int|null
     */
    public function getBaseUnitIdAttribute(): ?int
    {
        $unitId = $this->purchase_unit_id ?? $this->unit_id;
        if (!$unitId) {
            return null;
        }
        return UnitConverter::getBaseUnitId($unitId);
    }

    /**
     * Get stock in consumption units
     *
     * @return float
     */
    public function getStockInConsumptionUnitsAttribute(): float
    {
        $stock = (float) str_replace(',', '', $this->attributes['stock'] ?? 0);
        return $stock * ($this->conversion_rate ?? 1);
    }

    /**
     * Get stock in purchase units
     *
     * @return float
     */
    public function getStockInPurchaseUnitsAttribute(): float
    {
        return (float) str_replace(',', '', $this->attributes['stock'] ?? 0);
    }

    /**
     * Convert quantity from any unit to this ingredient's consumption unit
     *
     * @param float $quantity
     * @param int|null $fromUnitId Source unit ID (null = use ingredient's consumption unit)
     * @return float
     */
    public function convertToConsumptionUnits(float $quantity, ?int $fromUnitId = null): float
    {
        $consumptionUnitId = $this->consumption_unit_id ?? $this->unit_id;

        if (!$fromUnitId || $fromUnitId == $consumptionUnitId) {
            return $quantity;
        }

        return UnitConverter::safeConvert($quantity, $fromUnitId, $consumptionUnitId);
    }

    /**
     * Convert quantity from any unit to this ingredient's purchase unit
     *
     * @param float $quantity
     * @param int|null $fromUnitId Source unit ID (null = use ingredient's purchase unit)
     * @return float
     */
    public function convertToPurchaseUnits(float $quantity, ?int $fromUnitId = null): float
    {
        $purchaseUnitId = $this->purchase_unit_id ?? $this->unit_id;

        if (!$fromUnitId || $fromUnitId == $purchaseUnitId) {
            return $quantity;
        }

        return UnitConverter::safeConvert($quantity, $fromUnitId, $purchaseUnitId);
    }

    /**
     * Convert purchase units to consumption units using the stored conversion rate
     *
     * @param float $purchaseQty The quantity in purchase units
     * @return float The quantity in consumption units
     */
    public function purchaseToConsumptionUnits(float $purchaseQty): float
    {
        return $purchaseQty * ($this->conversion_rate ?? 1);
    }

    /**
     * Convert consumption units to purchase units using the stored conversion rate
     *
     * @param float $consumptionQty The quantity in consumption units
     * @return float The quantity in purchase units
     */
    public function consumptionToPurchaseUnits(float $consumptionQty): float
    {
        if ($this->conversion_rate && $this->conversion_rate > 0) {
            return $consumptionQty / $this->conversion_rate;
        }
        return $consumptionQty;
    }

    /**
     * Calculate the consumption cost for a given quantity
     *
     * @param float $quantity The quantity in consumption units
     * @return float The total cost
     */
    public function calculateConsumptionCost(float $quantity): float
    {
        return $quantity * ($this->consumption_unit_cost ?? 0);
    }

    /**
     * Calculate cost for a given quantity in any unit
     *
     * @param float $quantity
     * @param int|null $unitId The unit of the quantity (null = consumption unit)
     * @return float
     */
    public function calculateCost(float $quantity, ?int $unitId = null): float
    {
        // Convert to consumption units first
        $consumptionQty = $this->convertToConsumptionUnits($quantity, $unitId);
        return $this->calculateConsumptionCost($consumptionQty);
    }

    /**
     * Check if a unit is compatible with this ingredient's units
     *
     * @param int $unitId
     * @return bool
     */
    public function isUnitCompatible(int $unitId): bool
    {
        $ingredientUnitId = $this->purchase_unit_id ?? $this->consumption_unit_id ?? $this->unit_id;

        if (!$ingredientUnitId) {
            return true; // No unit set, allow any
        }

        return UnitConverter::areUnitsCompatible($unitId, $ingredientUnitId);
    }

    /**
     * Validate unit configuration for this ingredient
     *
     * @return array ['valid' => bool, 'errors' => []]
     */
    public function validateUnitConfiguration(): array
    {
        return UnitConverter::validateIngredientUnits($this);
    }

    /**
     * Deduct stock in any unit
     *
     * @param float $quantity
     * @param int|null $unitId The unit of the quantity (null = purchase unit, which is how stock is stored)
     * @return bool
     */
    public function deductStock(float $quantity, ?int $unitId = null): bool
    {
        // Convert to purchase units (stock is stored in purchase units)
        $deductQty = $this->convertToPurchaseUnits($quantity, $unitId);

        $currentStock = (float) str_replace(',', '', $this->attributes['stock'] ?? 0);
        $newStock = $currentStock - $deductQty;

        $this->attributes['stock'] = max(0, $newStock);

        // Update stock status
        if ($this->attributes['stock'] <= 0) {
            $this->attributes['stock_status'] = 'out_of_stock';
        } elseif ($this->stock_alert && $this->attributes['stock'] <= $this->stock_alert) {
            $this->attributes['stock_status'] = 'low_stock';
        } else {
            $this->attributes['stock_status'] = 'in_stock';
        }

        return $this->save();
    }

    /**
     * Add stock in any unit
     *
     * @param float $quantity
     * @param int|null $unitId The unit of the quantity (null = purchase unit)
     * @return bool
     */
    public function addStock(float $quantity, ?int $unitId = null): bool
    {
        // Convert to purchase units (stock is stored in purchase units)
        $addQty = $this->convertToPurchaseUnits($quantity, $unitId);

        $currentStock = (float) str_replace(',', '', $this->attributes['stock'] ?? 0);
        $this->attributes['stock'] = $currentStock + $addQty;

        // Update stock status
        if ($this->attributes['stock'] <= 0) {
            $this->attributes['stock_status'] = 'out_of_stock';
        } elseif ($this->stock_alert && $this->attributes['stock'] <= $this->stock_alert) {
            $this->attributes['stock_status'] = 'low_stock';
        } else {
            $this->attributes['stock_status'] = 'in_stock';
        }

        return $this->save();
    }

    /**
     * Check if enough stock is available
     *
     * @param float $quantity
     * @param int|null $unitId The unit of the quantity
     * @return bool
     */
    public function hasEnoughStock(float $quantity, ?int $unitId = null): bool
    {
        // Convert to purchase units for comparison
        $requiredQty = $this->convertToPurchaseUnits($quantity, $unitId);
        $currentStock = (float) str_replace(',', '', $this->attributes['stock'] ?? 0);

        return $currentStock >= $requiredQty;
    }

    /**
     * Get formatted stock with unit
     *
     * @param bool $useShortName
     * @return string
     */
    public function getFormattedStock(bool $useShortName = true): string
    {
        $stock = (float) str_replace(',', '', $this->attributes['stock'] ?? 0);
        $unitId = $this->purchase_unit_id ?? $this->unit_id;

        if (!$unitId) {
            return number_format($stock, 2);
        }

        return UnitConverter::formatWithUnit($stock, $unitId, $useShortName);
    }

    /**
     * Update weighted average cost after a purchase
     *
     * @param float $purchaseQty Quantity purchased (in purchase units)
     * @param float $purchasePrice Price per purchase unit
     * @return void
     */
    public function updateAverageCost(float $purchaseQty, float $purchasePrice): void
    {
        $currentStock = (float) str_replace(',', '', $this->attributes['stock'] ?? 0);
        $currentAvgCost = $this->average_cost ?? $this->purchase_price ?? 0;

        // Weighted average formula: ((currentStock * currentAvgCost) + (newQty * newPrice)) / (currentStock + newQty)
        $totalValue = ($currentStock * $currentAvgCost) + ($purchaseQty * $purchasePrice);
        $totalQty = $currentStock + $purchaseQty;

        if ($totalQty > 0) {
            $this->average_cost = $totalValue / $totalQty;

            // Recalculate consumption unit cost
            if ($this->conversion_rate && $this->conversion_rate > 0) {
                $this->consumption_unit_cost = $this->average_cost / $this->conversion_rate;
            }
        }
    }

    // ==================== EXISTING METHODS BELOW ====================

    public function getCurrentPriceAttribute()
    {
        // check last purchase
        $purchase =  $this->latestPurchaseDetail;

        // get the selling price

        if ($purchase) {
            return remove_comma($purchase->sale_price);
        }

        return 0;
    }

    public function getTotalPurchaseAttribute()
    {
        $purchase = $this->purchaseDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $purchase = $purchase->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $purchase->sum('sub_total');
        $qty = $purchase->sum('quantity');

        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }


    /**
     * Get the calculated average purchase price from purchase history
     * Note: Renamed from getPurchasePriceAttribute to avoid overwriting the database column
     */
    public function getCalculatedPurchasePriceAttribute()
    {
        $query = $this->purchaseDetails();

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $query = $query->whereHas('purchase', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('purchase_date', [$fromDate, $toDate]);
            });
        }

        $purchase = $query->selectRaw('SUM(purchase_price) as total_price, COUNT(*) as total_quantity')->first();

        $totalPrice = $purchase->total_price ?? 0;
        $totalQuantity = $purchase->total_quantity ?? 0;

        return $totalQuantity > 0 ? $totalPrice / $totalQuantity : 0;
    }




    public function getSalesAttribute()
    {
        $sales = $this->salesDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $sales->sum('sub_total');
        $qty = $sales->sum('quantity');
        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }




    public function getSalesReturnAttribute()
    {
        $sales = $this->salesReturnDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $sales->sum('sub_total');
        $qty = $sales->sum('quantity');

        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }



    public function getPurchaseReturnAttribute()
    {
        $purchase = $this->purchaseReturnDetails;

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $purchase = $purchase->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $price = $purchase->sum('total');
        $qty = $purchase->sum('quantity');

        return [
            'qty' => $qty ?? 0,
            'price' => $price ?? 0
        ];
    }


    public function getStockCountAttribute()
    {
        // If no dates provided, return total stock count (all time)
        if (!request('from_date') && !request('to_date')) {
            $totalInQty = $this->stockDetails->sum('in_quantity') ?? 0;
            $totalOutQty = $this->stockDetails->sum('out_quantity') ?? 0;
            return $totalInQty - $totalOutQty;
        }

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        // Get all stock data within the date range in a single query
        $stock = $this->stockDetails
            ->whereBetween('date', [$fromDate, $toDate]);
        $toDayInQty = $stock->sum('in_quantity') ?? 0;
        $toDayOutQty = $stock->sum('out_quantity') ?? 0;


        // Get previous stock quantities in a single query
        $previousStock = $this->stockDetails
            ->where('date', '<', $fromDate);


        // Calculate today stock
        $toDayStock = $toDayInQty - $toDayOutQty;

        $prevInQty = $previousStock->sum('in_quantity') ?? 0;
        $prevOutQty = $previousStock->sum('out_quantity') ?? 0;
        // Calculate total stock
        $previousStockTotal = $prevInQty - $prevOutQty;

        return $toDayStock + $previousStockTotal;
    }

    public function getAvgPurchasePriceAttribute()
    {
        $purchase = $this->purchaseDetails->sortByDesc('id');
        $totalPrice = $purchase->sum('purchase_price');
        $totalQuantity = $purchase->count();

        $price = $totalQuantity > 0 ? $totalPrice / $totalQuantity : 0;
        return (int) $price;
    }


    public function getCostAttribute()
    {
        $lastPurchase = $this->getLastPurchasePriceAttribute();

        return $lastPurchase > 0 ? $lastPurchase : $this->attributes['cost'];
    }



    public function getLastPurchasePriceAttribute()
    {
        $purchase = $this->latestPurchaseDetail;

        return $purchase ? $purchase->purchase_price : 0;
    }

    public function getSellingPriceAttribute()
    {
        $latestPurchase = $this->latestPurchaseDetail;
        return $latestPurchase ? $latestPurchase->sale_price : 0;
    }

    public function latestPurchaseDetail(): HasOne
    {
        return $this->hasOne(PurchaseDetails::class, 'ingredient_id', 'id')->latestOfMany();
    }

    public function stockDetails(): HasMany
    {
        return $this->hasMany(Stock::class, 'ingredient_id', 'id');
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetails::class, 'ingredient_id', 'id');
    }



    public function salesDetails(): HasMany
    {
        return $this->hasMany(IngredientSale::class, 'ingredient_id', 'id');
    }

    public function salesReturnDetails(): HasMany
    {
        return $this->hasMany(SalesReturnDetails::class, 'ingredient_id', 'id');
    }

    public function purchaseReturnDetails(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetails::class, 'ingredient_id', 'id');
    }
    public function getHasVariantAttribute(): bool
    {
        return $this->variants_count > 0;
    }

    public function getActualPriceAttribute()
    {
        return $this->getSellingPriceAttribute();
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault();
    }
    public function brand()
    {
        return $this->belongsTo(IngredientBrand::class, 'brand_id', 'id')->withDefault();
    }

    public function unit()
    {
        return $this->belongsTo(UnitType::class, 'unit_id', 'id')->withDefault();
    }

    public function purchaseUnit()
    {
        return $this->belongsTo(UnitType::class, 'purchase_unit_id', 'id')->withDefault();
    }

    public function consumptionUnit()
    {
        return $this->belongsTo(UnitType::class, 'consumption_unit_id', 'id')->withDefault();
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value);
    }

    public function getImagesUrlAttribute()
    {
        $image = $this->image;
        if (gettype($image) == 'array') {
            return false;
        }

        return $image;
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = json_encode($value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }


    public function getImageUrlAttribute()
    {
        return $this->image;
    }


    public function setAttributesAttribute($value)
    {
        $this->attributes['attributes'] = json_encode($value);
    }

    public function getPriceAttribute()
    {
        return $this->getSellingPriceAttribute();
    }

    public function getStockAttribute($value)
    {
        return number_format($value, 0);
    }

    public function orders()
    {
        return $this->hasMany(OrderDetails::class, 'ingredient_id', 'id');
    }

    public function getRelatedIngredientAttribute()
    {
        return $this->relatedIngredients->map(function ($relatedIngredient) {
            return $relatedIngredient->relatedIngredient;
        });
    }

    public function getStockStatusAttribute($value)
    {
        return $value == 'in_stock' ? 'In Stock' : 'Out of Stock';
    }

    // variations section

    public function variants()
    {
        return $this->hasMany(Variant::class, 'ingredient_id', 'id');
    }

    public function getAttributeAndValuesAttribute()
    {
        $attr = $this->variants->flatMap(function ($variant) {
            return $variant->options->map(function ($option) {
                return [
                    'attribute_id' => $option->attribute_id,
                    'attribute_value_id' => $option->attribute_value_id,
                    'attribute' => $option->attribute->name,
                    'attribute_value' => $option->attributeValue->name,
                ];
            });
        });

        $uniqueAttributes = $attr->unique('attribute')->values();

        $uniqueAttrWithValue = $uniqueAttributes->map(function ($uniqueAttr) use ($attr) {
            $values = $attr->filter(function ($item) use ($uniqueAttr) {
                return $item['attribute'] === $uniqueAttr['attribute'];
            })->map(function ($item) {
                return [
                    'id' => $item['attribute_value_id'],
                    'value' => $item['attribute_value']
                ];
            })->unique('id')->values()->toArray();

            return [
                'attribute_id' => $uniqueAttr['attribute_id'],
                'attribute' => $uniqueAttr['attribute'],
                'attribute_values' => $values,
            ];
        });

        return $uniqueAttrWithValue;
    }

    // get all variants price and sku with attribute value ids
    public function getVariantsPriceAndSkuAttribute()
    {
        $this->load('variants.variantOptions.attributeValue');

        $variantsPriceAndSku = [];

        foreach ($this->variants as $variant) {
            $variantsPriceAndSku[$variant->id] = [
                'price' => $variant->price,
                'currency_price' => currency($variant->price),
                'sku' => $variant->sku,
                'attribute_value_ids' => $variant->options->pluck('attribute_value_id')->toArray(),
            ];
        }

        return $variantsPriceAndSku;
    }

    public function getVariantsWithAttributes()
    {
        $this->load('variants.variantOptions.attributeValue.attribute');

        $variantsWithAttributes = [];

        foreach ($this->variants as $variant) {

            foreach ($variant->variantOptions as $variantOption) {
                $attributeValue = $variantOption->attributeValue;
                $attribute = $attributeValue->attribute;

                $variantsWithAttributes[$variant->id][] = [
                    'attribute' => $attribute->name,
                    'value' => $attributeValue->name,
                    'value_id' => $attributeValue->id,
                ];
            }
        }
        return $variantsWithAttributes;
    }

    public function getTotalStockAttribute()
    {
        return $this->attributes['stock'] ?? 0;
    }
}
