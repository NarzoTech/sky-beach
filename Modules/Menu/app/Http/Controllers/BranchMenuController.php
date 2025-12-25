<?php

namespace Modules\Menu\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Modules\Menu\app\Services\BranchMenuService;

class BranchMenuController extends Controller
{
    use RedirectHelperTrait;

    protected BranchMenuService $branchMenuService;

    public function __construct(BranchMenuService $branchMenuService)
    {
        $this->branchMenuService = $branchMenuService;
    }

    /**
     * Display branch pricing page.
     */
    public function pricing(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.branch.pricing');

        $branches = $this->branchMenuService->getAllBranches();
        $menuItems = $this->branchMenuService->getMenuItemsForPricing();
        $selectedBranch = $request->get('branch_id', $branches->first()?->id);
        $branchPrices = $selectedBranch ? $this->branchMenuService->getBranchPrices($selectedBranch) : collect();

        return view('menu::admin.branch.pricing', compact('branches', 'menuItems', 'selectedBranch', 'branchPrices'));
    }

    /**
     * Save branch pricing.
     */
    public function savePricing(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.branch.pricing');

        $request->validate([
            'branch_id' => 'required|exists:warehouses,id',
            'prices' => 'nullable|array',
        ]);

        try {
            $saved = $this->branchMenuService->saveBranchPrices(
                $request->branch_id,
                $request->prices ?? []
            );

            return response()->json([
                'success' => true,
                'message' => __(':count price(s) saved successfully', ['count' => $saved]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display branch availability page.
     */
    public function availability(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.branch.availability');

        $branches = $this->branchMenuService->getAllBranches();
        $menuItems = $this->branchMenuService->getMenuItemsForPricing();
        $selectedBranch = $request->get('branch_id', $branches->first()?->id);
        $branchAvailability = $selectedBranch ? $this->branchMenuService->getBranchAvailability($selectedBranch) : [];

        return view('menu::admin.branch.availability', compact('branches', 'menuItems', 'selectedBranch', 'branchAvailability'));
    }

    /**
     * Save branch availability.
     */
    public function saveAvailability(Request $request)
    {
        // checkAdminHasPermissionAndThrowException('menu.branch.availability');

        $request->validate([
            'branch_id' => 'required|exists:warehouses,id',
            'availability' => 'nullable|array',
        ]);

        try {
            $saved = $this->branchMenuService->saveBranchAvailability(
                $request->branch_id,
                $request->availability ?? []
            );

            return response()->json([
                'success' => true,
                'message' => __('Availability updated successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get items for a specific branch (AJAX).
     */
    public function getItemsForBranch(int $branchId)
    {
        // checkAdminHasPermissionAndThrowException('menu.branch.view');

        try {
            $items = $this->branchMenuService->getMenuForBranch($branchId);

            return response()->json([
                'success' => true,
                'items' => $items,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
