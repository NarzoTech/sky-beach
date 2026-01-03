<?php

namespace Modules\Ingredient\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;

use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Ingredient\app\Http\Requests\IngredientCategoryRequest;
use Modules\Ingredient\app\Services\IngredientCategoryService;

class IngredientCategoryController extends Controller
{
    use RedirectHelperTrait;
    protected $category;
    public function __construct(IngredientCategoryService $category)
    {
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.view');
        $categories = $this->category->getAllIngredientCategories();
        return view('ingredient::ingredients.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.create');
        $categories = $this->category->getAllIngredientCategoriesForSelect();
        return view('ingredient::ingredients.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IngredientCategoryRequest $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.create');
        DB::beginTransaction();
        try {
            $category = $this->category->storeIngredientCategory($request);
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['message' => 'Category created successfully', 'categories' => $category, 'status' => 200], 200);
            }
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.category.index', ['category' => $category->id, 'code' => getSessionLanguage()]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.edit');
        $cat = $this->category->getIngredientCategory($id);
        $categories = $this->category->getAllIngredientCategoriesForSelect();
        return view('ingredient::ingredients.category.edit', compact('categories', 'cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.edit');
        DB::beginTransaction();

        try {
            $this->category->updateIngredientCategory($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.category.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.delete');
        try {
            $category = $this->category->deleteIngredientCategory($id);
            if (!$category) {
                return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index', [], [
                    'messege' => 'Category has ingredients',
                    'alert-type' => RedirectType::ERROR->value,
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.category.index');
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index');
        }
    }

    public function deleteAll(Request $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.category.delete');
        try {
            $this->category->deleteAll($request);
            return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
