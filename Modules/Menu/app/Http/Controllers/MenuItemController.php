<?php

namespace Modules\Menu\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Menu\app\Http\Requests\MenuItemRequest;
use Modules\Menu\app\Services\MenuItemService;
use Modules\Menu\app\Services\MenuCategoryService;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\UnitType;

class MenuItemController extends Controller
{
    use RedirectHelperTrait;

    protected $menuItemService;
    protected $categoryService;

    public function __construct(MenuItemService $menuItemService, MenuCategoryService $categoryService)
    {
        $this->menuItemService = $menuItemService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('menu.item.view');
        $items = $this->menuItemService->getAllItems();
        $categories = $this->categoryService->getActiveCategories();
        return view('menu::admin.items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('menu.item.create');
        $categories = $this->categoryService->getActiveCategories();
        $allergenOptions = $this->getAllergenOptions();
        $products = Ingredient::where('status', 1)->get();
        $units = UnitType::where('status', 1)->get();
        return view('menu::admin.items.create', compact('categories', 'allergenOptions', 'products', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuItemRequest $request)
    {
        checkAdminHasPermissionAndThrowException('menu.item.create');
        DB::beginTransaction();
        try {
            $item = $this->menuItemService->storeItem($request);
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Menu Item created successfully',
                    'item' => $item,
                    'status' => 200
                ], 200);
            }

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.menu-item.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-item.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.view');
        $item = $this->menuItemService->getItem($id);
        return view('menu::admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        $item = $this->menuItemService->getItem($id);
        $categories = $this->categoryService->getActiveCategories();
        $allergenOptions = $this->getAllergenOptions();
        $products = Ingredient::where('status', 1)->get();
        $units = UnitType::where('status', 1)->get();
        return view('menu::admin.items.edit', compact('item', 'categories', 'allergenOptions', 'products', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuItemRequest $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        DB::beginTransaction();

        try {
            $this->menuItemService->updateItem($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.menu-item.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-item.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.delete');
        try {
            $this->menuItemService->deleteItem($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.menu-item.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-item.index');
        }
    }

    /**
     * Delete multiple items.
     */
    public function deleteAll(Request $request)
    {
        checkAdminHasPermissionAndThrowException('menu.item.delete');
        try {
            $this->menuItemService->deleteAll($request);
            return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Toggle item status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $item = $this->menuItemService->toggleStatus($id);
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $item->status
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Toggle item availability.
     */
    public function toggleAvailability(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $item = $this->menuItemService->toggleAvailability($id);
            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully',
                'is_available' => $item->is_available
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Toggle item featured status.
     */
    public function toggleFeatured(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $item = $this->menuItemService->toggleFeatured($id);
            return response()->json([
                'success' => true,
                'message' => 'Featured status updated successfully',
                'is_featured' => $item->is_featured
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Manage variants for a menu item.
     */
    public function manageVariants(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        $item = $this->menuItemService->getItem($id);
        return view('menu::admin.items.variants', compact('item'));
    }

    /**
     * Store a variant for a menu item.
     */
    public function storeVariant(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $variant = $this->menuItemService->addVariant($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Variant added successfully',
                'variant' => $variant
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Update a variant.
     */
    public function updateVariant(Request $request, string $menuItemId, string $variantId)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $variant = $this->menuItemService->updateVariant($variantId, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'Variant updated successfully',
                'variant' => $variant
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Delete a variant.
     */
    public function deleteVariant(string $menuItemId, string $variantId)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $this->menuItemService->deleteVariant($variantId);
            return response()->json([
                'success' => true,
                'message' => 'Variant deleted successfully'
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Manage addons for a menu item.
     */
    public function manageAddons(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        $item = $this->menuItemService->getItem($id);
        $attachedIds = $item->addons->pluck('id')->toArray();
        $availableAddons = MenuAddon::active()->whereNotIn('id', $attachedIds)->get();
        return view('menu::admin.items.addons', compact('item', 'availableAddons'));
    }

    /**
     * Attach addon to a menu item.
     */
    public function attachAddon(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $item = $this->menuItemService->getItem($id);

            // Check if already attached
            if ($item->addons()->where('menu_addons.id', $request->addon_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This add-on is already attached to this item'
                ], 422);
            }

            $item->addons()->attach($request->addon_id, [
                'max_quantity' => $request->max_quantity ?? 5,
                'is_required' => $request->is_required ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Add-on attached successfully'
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Update addon pivot data for a menu item.
     */
    public function updateAddon(Request $request, string $menuItemId, string $addonId)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $item = $this->menuItemService->getItem($menuItemId);
            $item->addons()->updateExistingPivot($addonId, [
                'max_quantity' => $request->max_quantity,
                'is_required' => $request->is_required,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Add-on updated successfully'
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Detach addon from a menu item.
     */
    public function detachAddon(string $menuItemId, string $addonId)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        try {
            $item = $this->menuItemService->getItem($menuItemId);
            $item->addons()->detach($addonId);

            return response()->json([
                'success' => true,
                'message' => 'Add-on removed successfully'
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Manage recipe for a menu item.
     */
    public function manageRecipe(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        $item = $this->menuItemService->getItem($id);
        $products = Ingredient::where('status', 1)->get();
        $units = UnitType::where('status', 1)->get();
        return view('menu::admin.items.recipe', compact('item', 'products', 'units'));
    }

    /**
     * Save recipe for a menu item.
     */
    public function saveRecipe(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.item.edit');
        DB::beginTransaction();
        try {
            $item = $this->menuItemService->saveRecipe($id, $request->recipes ?? []);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Recipe saved successfully',
                'cost_price' => $item->cost_price
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Get allergen options.
     */
    private function getAllergenOptions()
    {
        return [
            'gluten' => 'Gluten',
            'dairy' => 'Dairy',
            'eggs' => 'Eggs',
            'nuts' => 'Tree Nuts',
            'peanuts' => 'Peanuts',
            'soy' => 'Soy',
            'fish' => 'Fish',
            'shellfish' => 'Shellfish',
            'sesame' => 'Sesame',
            'mustard' => 'Mustard',
            'celery' => 'Celery',
            'lupin' => 'Lupin',
            'molluscs' => 'Molluscs',
            'sulphites' => 'Sulphites',
        ];
    }
}
