<?php

namespace Modules\Product\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;

use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Http\Requests\ProductCategoryRequest;
use Modules\Product\app\Services\ProductCategoryService;

class ProductCategoryController extends Controller
{
    use RedirectHelperTrait;
    protected $category;
    public function __construct(ProductCategoryService $category)
    {
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('product.category.view');
        $categories = $this->category->getAllProductCategories();
        return view('product::products.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('product.category.create');
        $categories = $this->category->getAllProductCategoriesForSelect();
        return view('product::products.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request)
    {
        checkAdminHasPermissionAndThrowException('product.category.create');
        DB::beginTransaction();
        try {
            $category = $this->category->storeProductCategory($request);
            DB::commit();
            if ($request->ajax()) {
                return response()->json(['message' => 'Service Category created successfully', 'categories' => $category, 'status' => 200], 200);
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
        checkAdminHasPermissionAndThrowException('product.category.edit');
        $cat = $this->category->getProductCategory($id);
        $categories = $this->category->getAllProductCategoriesForSelect();
        return view('product::products.category.edit', compact('categories', 'cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('product.category.edit');
        DB::beginTransaction();

        try {
            $this->category->updateProductCategory($request, $id);
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
        checkAdminHasPermissionAndThrowException('product.category.delete');
        try {
            $category = $this->category->deleteProductCategory($id);
            if (!$category) {
                return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index', [], [
                    'messege' => 'Category has products',
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
        checkAdminHasPermissionAndThrowException('product.category.delete');
        try {
            $this->category->deleteAll($request);
            return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
