<?php

namespace Modules\StockAdjustment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\StockAdjustment\app\Models\StockAdjustment;
use Modules\StockAdjustment\app\Services\StockAdjustmentService;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private StockAdjustmentService $service
    ) {}

    public function index()
    {
        $adjustments = $this->service->all()->paginate(20);
        $types = $this->service->getAdjustmentTypes();
        $ingredients = $this->service->getIngredients();

        return view('stockadjustment::index', compact('adjustments', 'types', 'ingredients'));
    }

    public function create()
    {
        $types = $this->service->getAdjustmentTypes();
        $ingredients = $this->service->getIngredients();
        $warehouses = Warehouse::where('status', 1)->get();

        return view('stockadjustment::create', compact('types', 'ingredients', 'warehouses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'adjustment_type' => 'required|in:' . implode(',', array_keys(StockAdjustment::TYPES)),
            'quantity' => 'required|numeric|min:0.0001',
            'adjustment_date' => 'required|date_format:d-m-Y',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            $this->service->store($request);
            return redirect()->route('admin.stock-adjustment.index')
                ->with('success', 'Stock adjustment created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $adjustment = $this->service->find($id);

        if (!$adjustment) {
            return redirect()->route('admin.stock-adjustment.index')
                ->with('error', 'Adjustment not found.');
        }

        return view('stockadjustment::show', compact('adjustment'));
    }

    public function edit($id)
    {
        $adjustment = $this->service->find($id);

        if (!$adjustment) {
            return redirect()->route('admin.stock-adjustment.index')
                ->with('error', 'Adjustment not found.');
        }

        $types = $this->service->getAdjustmentTypes();
        $ingredients = $this->service->getIngredients();
        $warehouses = Warehouse::where('status', 1)->get();

        return view('stockadjustment::edit', compact('adjustment', 'types', 'ingredients', 'warehouses'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'adjustment_type' => 'required|in:' . implode(',', array_keys(StockAdjustment::TYPES)),
            'quantity' => 'required|numeric|min:0.0001',
            'adjustment_date' => 'required|date_format:d-m-Y',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            $this->service->update($request, $id);
            return redirect()->route('admin.stock-adjustment.index')
                ->with('success', 'Stock adjustment updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $this->service->destroy($id);
            return redirect()->route('admin.stock-adjustment.index')
                ->with('success', 'Stock adjustment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting adjustment: ' . $e->getMessage());
        }
    }

    public function wastageSummary()
    {
        $fromDate = request('from_date') ? \Carbon\Carbon::parse(request('from_date')) : now()->startOfMonth();
        $toDate = request('to_date') ? \Carbon\Carbon::parse(request('to_date')) : now();

        $summary = $this->service->getWastageSummary($fromDate, $toDate);
        $types = $this->service->getAdjustmentTypes();

        return view('stockadjustment::wastage-summary', compact('summary', 'types', 'fromDate', 'toDate'));
    }
}
