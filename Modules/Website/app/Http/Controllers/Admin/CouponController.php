<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\Coupon;

class CouponController extends Controller
{
    /**
     * Display coupons list
     */
    public function index(Request $request)
    {
        $query = Coupon::withCount('usages');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            switch ($request->status) {
                case 'active':
                    $query->active()->valid()->available();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('valid_until', '<', now()->toDateString());
                    break;
                case 'scheduled':
                    $query->where('valid_from', '>', now()->toDateString());
                    break;
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->latest()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::active()->valid()->available()->count(),
            'total_usage' => Coupon::sum('used_count'),
        ];

        return view('website::admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Show create coupon form
     */
    public function create()
    {
        return view('website::admin.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        // Validate percentage max value
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withInput()->withErrors(['value' => __('Percentage discount cannot exceed 100%.')]);
        }

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', __('Coupon created successfully.'));
    }

    /**
     * Show coupon details
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['usages' => function ($query) {
            $query->with('sale')->latest()->take(20);
        }]);

        return view('website::admin.coupons.show', compact('coupon'));
    }

    /**
     * Show edit coupon form
     */
    public function edit(Coupon $coupon)
    {
        return view('website::admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        // Validate percentage max value
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withInput()->withErrors(['value' => __('Percentage discount cannot exceed 100%.')]);
        }

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', __('Coupon updated successfully.'));
    }

    /**
     * Delete coupon
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', __('Coupon deleted successfully.'));
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $coupon->is_active,
            'message' => $coupon->is_active ? __('Coupon activated.') : __('Coupon deactivated.'),
        ]);
    }

    /**
     * Generate random coupon code
     */
    public function generateCode()
    {
        $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

        // Ensure uniqueness
        while (Coupon::where('code', $code)->exists()) {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        }

        return response()->json(['code' => $code]);
    }
}
