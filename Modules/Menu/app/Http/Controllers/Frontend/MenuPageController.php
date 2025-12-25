<?php

namespace Modules\Menu\app\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\MenuCategory;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Services\BranchMenuService;
use Modules\Menu\app\Services\ComboService;
use Modules\Menu\app\Services\MenuCategoryService;
use Modules\Menu\app\Services\MenuItemService;

class MenuPageController extends Controller
{
    protected MenuCategoryService $categoryService;
    protected MenuItemService $itemService;
    protected ComboService $comboService;
    protected BranchMenuService $branchMenuService;

    public function __construct(
        MenuCategoryService $categoryService,
        MenuItemService $itemService,
        ComboService $comboService,
        BranchMenuService $branchMenuService
    ) {
        $this->categoryService = $categoryService;
        $this->itemService = $itemService;
        $this->comboService = $comboService;
        $this->branchMenuService = $branchMenuService;
    }

    /**
     * Display the main menu page.
     */
    public function index(Request $request)
    {
        $categories = MenuCategory::with(['children'])
            ->where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->get();

        $featuredItems = MenuItem::with(['category', 'variants'])
            ->where('status', 1)
            ->where('is_available', 1)
            ->where('is_featured', 1)
            ->take(8)
            ->get();

        $activeCombos = $this->comboService->getActiveCombos()->take(4);

        return view('menu::frontend.menu', compact('categories', 'featuredItems', 'activeCombos'));
    }

    /**
     * Display items for a specific category.
     */
    public function category(string $slug)
    {
        $category = MenuCategory::with(['children', 'parent'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // Get items from this category and its children
        $categoryIds = collect([$category->id]);
        if ($category->children->count() > 0) {
            $categoryIds = $categoryIds->merge($category->children->pluck('id'));
        }

        $items = MenuItem::with(['category', 'variants', 'addons'])
            ->whereIn('category_id', $categoryIds)
            ->where('status', 1)
            ->where('is_available', 1)
            ->orderBy('display_order')
            ->paginate(12);

        $allCategories = MenuCategory::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->get();

        return view('menu::frontend.category', compact('category', 'items', 'allCategories'));
    }

    /**
     * Display a single menu item.
     */
    public function item(string $slug)
    {
        $item = MenuItem::with(['category', 'variants', 'addons', 'recipes.product'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // Get related items from same category
        $relatedItems = MenuItem::with(['category', 'variants'])
            ->where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->where('status', 1)
            ->where('is_available', 1)
            ->take(4)
            ->get();

        return view('menu::frontend.item-detail', compact('item', 'relatedItems'));
    }

    /**
     * Display all combos.
     */
    public function combos()
    {
        $combos = $this->comboService->getActiveCombos();

        return view('menu::frontend.combos', compact('combos'));
    }

    /**
     * Display a single combo detail.
     */
    public function comboDetail(string $slug)
    {
        $combo = $this->comboService->getComboBySlug($slug);

        if (!$combo->status || !$combo->is_active) {
            abort(404);
        }

        // Check date validity
        if ($combo->start_date && $combo->start_date > now()) {
            abort(404);
        }
        if ($combo->end_date && $combo->end_date < now()) {
            abort(404);
        }

        return view('menu::frontend.combo-detail', compact('combo'));
    }

    /**
     * Display menu for a specific branch.
     */
    public function branchMenu(int $id)
    {
        $items = $this->branchMenuService->getMenuForBranch($id);

        $categories = MenuCategory::with(['children'])
            ->where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->get();

        $branch = \App\Models\Warehouse::findOrFail($id);

        return view('menu::frontend.branch-menu', compact('items', 'categories', 'branch'));
    }

    /**
     * Search menu items.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $items = MenuItem::with(['category', 'variants'])
            ->where('status', 1)
            ->where('is_available', 1)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('short_description', 'like', '%' . $query . '%')
                    ->orWhere('long_description', 'like', '%' . $query . '%');
            })
            ->paginate(12);

        $allCategories = MenuCategory::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->get();

        return view('menu::frontend.search', compact('items', 'query', 'allCategories'));
    }
}
