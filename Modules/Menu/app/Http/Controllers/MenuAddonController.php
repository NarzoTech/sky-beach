<?php

namespace Modules\Menu\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Modules\Menu\app\Http\Requests\MenuAddonRequest;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Menu\app\Services\MenuAddonService;

class MenuAddonController extends Controller
{
    use RedirectHelperTrait;

    protected MenuAddonService $addonService;

    public function __construct(MenuAddonService $addonService)
    {
        $this->addonService = $addonService;
    }

    /**
     * Display a listing of the addons.
     */
    public function index(Request $request)
    {
        // $this->authorize('menu-addon-list');

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 15),
        ];

        $addons = $this->addonService->getAll($filters);

        return view('menu::admin.addons.index', compact('addons', 'filters'));
    }

    /**
     * Show the form for creating a new addon.
     */
    public function create()
    {
        // $this->authorize('menu-addon-create');

        return view('menu::admin.addons.create');
    }

    /**
     * Store a newly created addon.
     */
    public function store(MenuAddonRequest $request)
    {
        // $this->authorize('menu-addon-create');

        try {
            $this->addonService->create($request->validated());

            return $this->redirectWithMessage(
                'admin.menu-addon.index',
                'success',
                __('Add-on created successfully')
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified addon.
     */
    public function show(MenuAddon $menuAddon)
    {
        // $this->authorize('menu-addon-view');

        return view('menu::admin.addons.show', ['addon' => $menuAddon]);
    }

    /**
     * Show the form for editing the specified addon.
     */
    public function edit(MenuAddon $menuAddon)
    {
        // $this->authorize('menu-addon-edit');

        return view('menu::admin.addons.edit', ['addon' => $menuAddon]);
    }

    /**
     * Update the specified addon.
     */
    public function update(MenuAddonRequest $request, MenuAddon $menuAddon)
    {
        // $this->authorize('menu-addon-edit');

        try {
            $this->addonService->update($menuAddon, $request->validated());

            return $this->redirectWithMessage(
                'admin.menu-addon.index',
                'success',
                __('Add-on updated successfully')
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified addon.
     */
    public function destroy(MenuAddon $menuAddon)
    {
        // $this->authorize('menu-addon-delete');

        try {
            $this->addonService->delete($menuAddon);

            return response()->json([
                'success' => true,
                'message' => __('Add-on deleted successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete addons.
     */
    public function bulkDelete(Request $request)
    {
        // $this->authorize('menu-addon-delete');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:menu_addons,id',
        ]);

        $deleted = $this->addonService->bulkDelete($request->ids);

        return response()->json([
            'success' => true,
            'message' => __(':count add-ons deleted successfully', ['count' => $deleted]),
        ]);
    }

    /**
     * Toggle addon status.
     */
    public function toggleStatus(MenuAddon $menuAddon)
    {
        // $this->authorize('menu-addon-edit');

        $addon = $this->addonService->toggleStatus($menuAddon);

        return response()->json([
            'success' => true,
            'message' => __('Add-on status updated successfully'),
            'status' => $addon->status,
        ]);
    }
}
