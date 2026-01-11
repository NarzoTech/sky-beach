<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Membership\app\Models\LoyaltyProgram;

class LoyaltyProgramController extends Controller
{
    /**
     * Display a listing of loyalty programs
     */
    public function index(Request $request): View
    {
        $warehouseId = $request->query('warehouse_id');

        $programs = LoyaltyProgram::query()
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->with('warehouse', 'createdBy')
            ->paginate(15);

        return view('membership::programs.index', [
            'programs' => $programs,
        ]);
    }

    /**
     * Show the form for creating a new program
     */
    public function create(): View
    {
        return view('membership::programs.create');
    }

    /**
     * Store a newly created program
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'earning_type' => 'required|in:per_transaction,per_amount',
            'earning_rate' => 'required|numeric|min:0.01',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'redemption_type' => 'required|in:discount,free_item,cashback',
            'points_per_unit' => 'required|numeric|min:0.01',
        ]);

        $validated['created_by'] = auth()->id();

        LoyaltyProgram::create($validated);

        return redirect()->route('membership.programs.index')
            ->with('success', 'Loyalty program created successfully');
    }

    /**
     * Display the specified program
     */
    public function show(LoyaltyProgram $program): View
    {
        $program->load('warehouse', 'rules', 'segments', 'createdBy');

        return view('membership::programs.show', [
            'program' => $program,
        ]);
    }

    /**
     * Show the form for editing a program
     */
    public function edit(LoyaltyProgram $program): View
    {
        return view('membership::programs.edit', [
            'program' => $program,
        ]);
    }

    /**
     * Update the specified program
     */
    public function update(Request $request, LoyaltyProgram $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'earning_type' => 'required|in:per_transaction,per_amount',
            'earning_rate' => 'required|numeric|min:0.01',
            'min_transaction_amount' => 'nullable|numeric|min:0',
            'redemption_type' => 'required|in:discount,free_item,cashback',
            'points_per_unit' => 'required|numeric|min:0.01',
        ]);

        $program->update($validated);

        return redirect()->route('membership.programs.show', $program)
            ->with('success', 'Loyalty program updated successfully');
    }

    /**
     * Delete the specified program
     */
    public function destroy(LoyaltyProgram $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('membership.programs.index')
            ->with('success', 'Loyalty program deleted successfully');
    }
}
