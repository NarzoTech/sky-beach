<?php

namespace Modules\Menu\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Modules\Menu\app\Http\Requests\ComboRequest;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Services\ComboService;

class ComboController extends Controller
{
    use RedirectHelperTrait;

    protected ComboService $comboService;

    public function __construct(ComboService $comboService)
    {
        $this->comboService = $comboService;
    }

    /**
     * Display a listing of the combos.
     */
    public function index(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.view');

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'is_active' => $request->get('is_active'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 15),
        ];

        $combos = $this->comboService->getAll($filters);

        return view('menu::admin.combos.index', compact('combos', 'filters'));
    }

    /**
     * Show the form for creating a new combo.
     */
    public function create()
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.create');

        $menuItems = MenuItem::with('variants')->where('status', 1)->get();

        return view('menu::admin.combos.create', compact('menuItems'));
    }

    /**
     * Store a newly created combo.
     */
    public function store(ComboRequest $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.create');

        try {
            $this->comboService->create($request->validated());

            return $this->redirectWithMessage(
                'admin.combo.index',
                'success',
                __('Combo created successfully')
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified combo.
     */
    public function show(Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.view');

        $combo->load(['items.menuItem', 'items.variant']);

        return view('menu::admin.combos.show', compact('combo'));
    }

    /**
     * Show the form for editing the specified combo.
     */
    public function edit(Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.edit');

        $combo->load(['items.menuItem', 'items.variant']);
        $menuItems = MenuItem::with('variants')->where('status', 1)->get();

        return view('menu::admin.combos.edit', compact('combo', 'menuItems'));
    }

    /**
     * Update the specified combo.
     */
    public function update(ComboRequest $request, Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.edit');

        try {
            $this->comboService->update($combo, $request->validated());

            return $this->redirectWithMessage(
                'admin.combo.index',
                'success',
                __('Combo updated successfully')
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified combo.
     */
    public function destroy(Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.delete');

        try {
            $this->comboService->delete($combo);

            return response()->json([
                'success' => true,
                'message' => __('Combo deleted successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete combos.
     */
    public function deleteAll(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.delete');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:combos,id',
        ]);

        $deleted = $this->comboService->bulkDelete($request->ids);

        return response()->json([
            'success' => true,
            'message' => __(':count combos deleted successfully', ['count' => $deleted]),
        ]);
    }

    /**
     * Toggle combo status.
     */
    public function toggleStatus(Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.edit');

        $combo = $this->comboService->toggleStatus($combo);

        return response()->json([
            'success' => true,
            'message' => __('Combo status updated successfully'),
            'status' => $combo->status,
        ]);
    }

    /**
     * Toggle combo active status.
     */
    public function toggleActive(Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.edit');

        $combo = $this->comboService->toggleActive($combo);

        return response()->json([
            'success' => true,
            'message' => __('Combo active status updated successfully'),
            'is_active' => $combo->is_active,
        ]);
    }

    /**
     * Manage combo items.
     */
    public function manageItems(Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.edit');

        $combo->load(['items.menuItem', 'items.variant']);
        $menuItems = MenuItem::with('variants')->where('status', 1)->get();

        return view('menu::admin.combos.items', compact('combo', 'menuItems'));
    }

    /**
     * Save combo items.
     */
    public function saveItems(Request $request, Combo $combo)
    {
        // checkAdminHasPermissionAndThrowException('menu.combo.edit');

        try {
            $this->comboService->syncItems($combo, $request->items ?? []);
            $combo = $this->comboService->recalculatePrices($combo);

            return response()->json([
                'success' => true,
                'message' => __('Combo items saved successfully'),
                'original_price' => $combo->original_price,
                'combo_price' => $combo->combo_price,
                'savings' => $combo->savings,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
