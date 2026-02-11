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
        checkAdminHasPermissionAndThrowException('membership.view');

        $programs = LoyaltyProgram::query()
            ->with('createdBy')
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
        checkAdminHasPermissionAndThrowException('membership.create');
        return view('membership::programs.create');
    }

    /**
     * Store a newly created program
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.create');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'spend_amount' => 'required|numeric|min:1',
            'points_earned' => 'required|integer|min:1',
            'tier_points' => 'required|array|min:1',
            'tier_points.*' => 'required|integer|min:1',
            'tier_discounts' => 'required|array|min:1',
            'tier_discounts.*' => 'required|numeric|min:1',
        ]);

        // Build coupon tiers array
        $couponTiers = [];
        foreach ($validated['tier_points'] as $i => $points) {
            $couponTiers[] = [
                'points_required' => (int) $points,
                'discount_amount' => (float) $validated['tier_discounts'][$i],
            ];
        }

        // Sort tiers by points required ascending
        usort($couponTiers, fn($a, $b) => $a['points_required'] <=> $b['points_required']);

        // Calculate earning_rate for the service layer
        $earningRate = $validated['points_earned'] / $validated['spend_amount'];

        LoyaltyProgram::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
            'earning_type' => 'per_amount',
            'earning_rate' => $earningRate,
            'redemption_type' => 'discount',
            'points_per_unit' => $validated['spend_amount'],
            'earning_rules' => [
                'spend_amount' => (float) $validated['spend_amount'],
                'points_earned' => (int) $validated['points_earned'],
            ],
            'redemption_rules' => [
                'coupon_tiers' => $couponTiers,
            ],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('membership.programs.index')
            ->with('success', __('Loyalty program created successfully'));
    }

    /**
     * Display the specified program
     */
    public function show(LoyaltyProgram $program): View
    {
        checkAdminHasPermissionAndThrowException('membership.view');
        $program->load('createdBy');

        return view('membership::programs.show', [
            'program' => $program,
        ]);
    }

    /**
     * Show the form for editing a program
     */
    public function edit(LoyaltyProgram $program): View
    {
        checkAdminHasPermissionAndThrowException('membership.edit');
        return view('membership::programs.edit', [
            'program' => $program,
        ]);
    }

    /**
     * Update the specified program
     */
    public function update(Request $request, LoyaltyProgram $program): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.edit');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'spend_amount' => 'required|numeric|min:1',
            'points_earned' => 'required|integer|min:1',
            'tier_points' => 'required|array|min:1',
            'tier_points.*' => 'required|integer|min:1',
            'tier_discounts' => 'required|array|min:1',
            'tier_discounts.*' => 'required|numeric|min:1',
        ]);

        // Build coupon tiers array
        $couponTiers = [];
        foreach ($validated['tier_points'] as $i => $points) {
            $couponTiers[] = [
                'points_required' => (int) $points,
                'discount_amount' => (float) $validated['tier_discounts'][$i],
            ];
        }

        // Sort tiers by points required ascending
        usort($couponTiers, fn($a, $b) => $a['points_required'] <=> $b['points_required']);

        // Calculate earning_rate for the service layer
        $earningRate = $validated['points_earned'] / $validated['spend_amount'];

        $program->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
            'earning_type' => 'per_amount',
            'earning_rate' => $earningRate,
            'redemption_type' => 'discount',
            'points_per_unit' => $validated['spend_amount'],
            'earning_rules' => [
                'spend_amount' => (float) $validated['spend_amount'],
                'points_earned' => (int) $validated['points_earned'],
            ],
            'redemption_rules' => [
                'coupon_tiers' => $couponTiers,
            ],
        ]);

        return redirect()->route('membership.programs.show', $program)
            ->with('success', __('Loyalty program updated successfully'));
    }

    /**
     * Delete the specified program
     */
    public function destroy(LoyaltyProgram $program): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.delete');
        $program->delete();

        return redirect()->route('membership.programs.index')
            ->with('success', __('Loyalty program deleted successfully'));
    }
}
