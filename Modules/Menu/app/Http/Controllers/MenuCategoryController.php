<?php

namespace Modules\Menu\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Menu\app\Http\Requests\MenuCategoryRequest;
use Modules\Menu\app\Services\MenuCategoryService;

class MenuCategoryController extends Controller
{
    use RedirectHelperTrait;

    protected $categoryService;

    public function __construct(MenuCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('menu.category.view');
        $categories = $this->categoryService->getAllCategories();
        return view('menu::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('menu.category.create');
        $parentCategories = $this->categoryService->getCategoriesForSelect();
        return view('menu::admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuCategoryRequest $request)
    {
        checkAdminHasPermissionAndThrowException('menu.category.create');
        DB::beginTransaction();
        try {
            $category = $this->categoryService->storeCategory($request);
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Menu Category created successfully',
                    'category' => $category,
                    'status' => 200
                ], 200);
            }

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.menu-category.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-category.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.category.edit');
        $category = $this->categoryService->getCategory($id);
        $parentCategories = $this->categoryService->getCategoriesForSelect();
        return view('menu::admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuCategoryRequest $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.category.edit');
        DB::beginTransaction();

        try {
            $this->categoryService->updateCategory($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.menu-category.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-category.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.category.delete');
        try {
            $result = $this->categoryService->deleteCategory($id);
            if (!$result) {
                return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-category.index', [], [
                    'messege' => 'Category has menu items or subcategories',
                    'alert-type' => RedirectType::ERROR->value,
                ]);
            }
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.menu-category.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.menu-category.index');
        }
    }

    /**
     * Delete multiple categories.
     */
    public function deleteAll(Request $request)
    {
        checkAdminHasPermissionAndThrowException('menu.category.delete');
        try {
            $this->categoryService->deleteAll($request);
            return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('menu.category.edit');
        try {
            $category = $this->categoryService->updateStatus($id);
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $category->status
            ], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
