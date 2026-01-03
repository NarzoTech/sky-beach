<?php

namespace Modules\StockAdjustment\app\Services;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\StockAdjustment\app\Models\StockAdjustment;

class StockAdjustmentService
{
    public function __construct(
        private StockAdjustment $stockAdjustment,
        private Ingredient $ingredient
    ) {}

    public function all()
    {
        $query = $this->stockAdjustment->with(['ingredient', 'warehouse', 'unit', 'createdBy'])
            ->orderBy('adjustment_date', 'desc');

        if (request('keyword')) {
            $query->where(function ($q) {
                $q->where('adjustment_number', 'like', '%' . request('keyword') . '%')
                    ->orWhereHas('ingredient', function ($iq) {
                        $iq->where('name', 'like', '%' . request('keyword') . '%');
                    });
            });
        }

        if (request('adjustment_type')) {
            $query->where('adjustment_type', request('adjustment_type'));
        }

        if (request('ingredient_id')) {
            $query->where('ingredient_id', request('ingredient_id'));
        }

        if (request('from_date') && request('to_date')) {
            $query->whereBetween('adjustment_date', [
                Carbon::parse(request('from_date')),
                Carbon::parse(request('to_date'))
            ]);
        }

        return $query;
    }

    public function store(Request $request): StockAdjustment
    {
        return DB::transaction(function () use ($request) {
            $ingredient = $this->ingredient->find($request->ingredient_id);

            // Determine if this is a decrease or increase
            $isDecrease = in_array($request->adjustment_type, StockAdjustment::DECREASE_TYPES);

            // Get quantity (make it negative for decreases)
            $quantity = abs($request->quantity);
            if ($isDecrease) {
                $quantity = -$quantity;
            }

            // Calculate cost using average_cost from ingredient
            $costPerUnit = $ingredient->average_cost ?? $ingredient->purchase_price ?? 0;
            $totalCost = abs($quantity) * $costPerUnit;

            // Create the adjustment record
            $adjustment = $this->stockAdjustment->create([
                'adjustment_number' => StockAdjustment::generateAdjustmentNumber(),
                'ingredient_id' => $request->ingredient_id,
                'warehouse_id' => $request->warehouse_id,
                'adjustment_type' => $request->adjustment_type,
                'quantity' => $quantity,
                'unit_id' => $request->unit_id ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id,
                'cost_per_unit' => $costPerUnit,
                'total_cost' => $totalCost,
                'adjustment_date' => Carbon::createFromFormat('d-m-Y', $request->adjustment_date),
                'reason' => $request->reason,
                'notes' => $request->notes,
                'created_by' => auth('admin')->id(),
                'status' => 'approved', // Auto-approve for now
            ]);

            // Update ingredient stock
            $this->updateIngredientStock($ingredient, $quantity);

            // Create stock movement record
            $this->createStockRecord($adjustment, $ingredient);

            return $adjustment;
        });
    }

    public function update(Request $request, $id): StockAdjustment
    {
        return DB::transaction(function () use ($request, $id) {
            $adjustment = $this->stockAdjustment->find($id);
            $ingredient = $this->ingredient->find($adjustment->ingredient_id);

            // Reverse the previous adjustment
            $this->reverseAdjustment($adjustment, $ingredient);

            // Delete old stock record
            Stock::where('stock_adjustment_id', $adjustment->id)->delete();

            // Recalculate new values
            $isDecrease = in_array($request->adjustment_type, StockAdjustment::DECREASE_TYPES);
            $quantity = abs($request->quantity);
            if ($isDecrease) {
                $quantity = -$quantity;
            }

            $costPerUnit = $ingredient->average_cost ?? $ingredient->purchase_price ?? 0;
            $totalCost = abs($quantity) * $costPerUnit;

            // Update the adjustment record
            $adjustment->update([
                'ingredient_id' => $request->ingredient_id,
                'warehouse_id' => $request->warehouse_id,
                'adjustment_type' => $request->adjustment_type,
                'quantity' => $quantity,
                'unit_id' => $request->unit_id ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id,
                'cost_per_unit' => $costPerUnit,
                'total_cost' => $totalCost,
                'adjustment_date' => Carbon::createFromFormat('d-m-Y', $request->adjustment_date),
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Apply new adjustment to stock
            $newIngredient = $this->ingredient->find($request->ingredient_id);
            $this->updateIngredientStock($newIngredient, $quantity);

            // Create new stock record
            $this->createStockRecord($adjustment->fresh(), $newIngredient);

            return $adjustment;
        });
    }

    public function destroy($id): bool
    {
        return DB::transaction(function () use ($id) {
            $adjustment = $this->stockAdjustment->find($id);
            $ingredient = $this->ingredient->find($adjustment->ingredient_id);

            // Reverse the adjustment
            $this->reverseAdjustment($adjustment, $ingredient);

            // Delete stock record
            Stock::where('stock_adjustment_id', $adjustment->id)->delete();

            return $adjustment->delete();
        });
    }

    public function find($id): ?StockAdjustment
    {
        return $this->stockAdjustment->with(['ingredient', 'warehouse', 'unit', 'createdBy'])->find($id);
    }

    private function updateIngredientStock(Ingredient $ingredient, float $quantity): void
    {
        $currentStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
        $ingredient->stock = $currentStock + $quantity;
        $ingredient->save();
    }

    private function reverseAdjustment(StockAdjustment $adjustment, Ingredient $ingredient): void
    {
        // Reverse the quantity (if it was negative, add it back; if positive, subtract)
        $reverseQuantity = -$adjustment->quantity;
        $this->updateIngredientStock($ingredient, $reverseQuantity);
    }

    private function createStockRecord(StockAdjustment $adjustment, Ingredient $ingredient): void
    {
        $stockData = [
            'stock_adjustment_id' => $adjustment->id,
            'ingredient_id' => $adjustment->ingredient_id,
            'warehouse_id' => $adjustment->warehouse_id,
            'unit_id' => $adjustment->unit_id,
            'date' => $adjustment->adjustment_date,
            'type' => 'Adjustment - ' . ucfirst($adjustment->adjustment_type),
            'invoice' => route('admin.stock-adjustment.show', $adjustment->id),
            'sku' => $ingredient->sku,
            'purchase_price' => $adjustment->cost_per_unit,
            'average_cost' => $adjustment->cost_per_unit,
            'created_by' => auth('admin')->id(),
        ];

        if ($adjustment->quantity >= 0) {
            $stockData['in_quantity'] = $adjustment->quantity;
            $stockData['base_in_quantity'] = $adjustment->quantity;
        } else {
            $stockData['out_quantity'] = abs($adjustment->quantity);
            $stockData['base_out_quantity'] = abs($adjustment->quantity);
        }

        Stock::create($stockData);
    }

    public function getAdjustmentTypes(): array
    {
        return StockAdjustment::TYPES;
    }

    public function getIngredients()
    {
        return $this->ingredient->where('status', 1)->orderBy('name')->get();
    }

    public function getWastageSummary($fromDate = null, $toDate = null)
    {
        $query = $this->stockAdjustment
            ->whereIn('adjustment_type', ['wastage', 'damage', 'theft', 'consumption'])
            ->where('status', 'approved');

        if ($fromDate && $toDate) {
            $query->whereBetween('adjustment_date', [$fromDate, $toDate]);
        }

        return $query->selectRaw('
            adjustment_type,
            COUNT(*) as count,
            SUM(ABS(quantity)) as total_quantity,
            SUM(total_cost) as total_cost
        ')
            ->groupBy('adjustment_type')
            ->get();
    }
}
