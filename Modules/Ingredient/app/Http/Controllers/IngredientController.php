<?php
namespace Modules\Ingredient\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\IngredientsExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Milon\Barcode\DNS1D;
use Modules\Ingredient\app\Http\Requests\IngredientRequest;
use Modules\Ingredient\app\Services\AttributeService;
use Modules\Ingredient\app\Services\BrandService;
use Modules\Ingredient\app\Services\IngredientCategoryService;
use Modules\Ingredient\app\Services\IngredientService;
use Modules\Ingredient\app\Services\UnitTypeService;

class IngredientController extends Controller
{
    use RedirectHelperTrait;
    private IngredientService $ingredientService;
    private IngredientCategoryService $categoryService;
    private AttributeService $attributeService;
    private BrandService $brandService;
    private UnitTypeService $unitService;
    public function __construct(IngredientService $ingredientService, IngredientCategoryService $categoryService, AttributeService $attributeService, BrandService $brandService, UnitTypeService $unitService)
    {
        $this->ingredientService   = $ingredientService;
        $this->categoryService  = $categoryService;
        $this->attributeService = $attributeService;
        $this->brandService     = $brandService;
        $this->unitService      = $unitService;
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('ingredient.view');
        try {
            $ingredients = $this->ingredientService->getIngredients();

            if (request('export')) {
                $fileName = 'ingredients-' . date('Y-m-d') . '-' . time() . '.xlsx';
                return Excel::download(new IngredientsExport($ingredients), $fileName, );
            }

            if (request('par-page')) {
                if (request('par-page') == 'all') {
                    $ingredients = $ingredients->get();
                } else {
                    $ingredients = $ingredients->paginate(request('par-page'));
                    $ingredients->appends(request()->query());
                }
            } else {
                $ingredients = $ingredients->paginate(20);
                $ingredients->appends(request()->query());
            }

            if (request('export_pdf')) {
                return view('ingredient::ingredients.ingredient-pdf', ['ingredients' => $ingredients]);
            }

            $brands     = $this->brandService->getActiveBrands();
            $categories = $this->categoryService->getAllIngredientCategoriesForSelect();
            return view('ingredient::ingredients.index', compact('ingredients', 'brands', 'categories'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('ingredient.create');
        $categories  = $this->categoryService->getAllIngredientCategoriesForSelect();
        $brands      = $this->brandService->getActiveBrands();
        $units       = $this->unitService->getParentUnits();
        $parentUnits = $this->unitService->getParentUnits();
        return view('ingredient::ingredients.create', compact('categories', 'brands', 'units', 'parentUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IngredientRequest $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.create');
        DB::beginTransaction();
        try {
            $ingredient = $this->ingredientService->storeIngredient($request);
            DB::commit();
            Log::info('Ingredient Created Successfully. ID: ' . $ingredient->id);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.ingredient.create', [], ['messege' => 'Ingredient Created Successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.view');
        try {
            $ingredient = $this->ingredientService->getIngredient($id);

            return view('ingredient::ingredients.show', compact('ingredient'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    public function singleIngredient($id)
    {
        $ingredient = $this->ingredientService->getIngredient($id);
        return view('ingredient::ingredients.single-ingredient-modal', compact('ingredient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.edit');
        try {
            $ingredient    = $this->ingredientService->getIngredient($id);
            $categories = $this->categoryService->getAllIngredientCategoriesForSelect();
            $brands     = $this->brandService->getActiveBrands();
            $units      = $this->unitService->getParentUnits();
            return view('ingredient::ingredients.edit', compact('categories', 'brands', 'ingredient', 'units'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IngredientRequest $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.edit');
        try {
            DB::beginTransaction();
            $ingredient = $this->ingredientService->getIngredient($id);
            if (! $ingredient) {
                return back()->with([
                    'messege'    => 'Ingredient not found',
                    'alert-type' => 'error',
                ]);
            }
            $ingredient = $this->ingredientService->updateIngredient($request, $ingredient);
            DB::commit();
            if ($ingredient->id) {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.ingredient.index', [], [
                    'messege'    => 'Ingredient updated successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.ingredient.index', [], [
                    'messege'    => 'Ingredient update failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.delete');
        try {
            $ingredient = $this->ingredientService->getIngredient($id);
            if (! $ingredient) {
                return back()->with([
                    'messege'    => 'Ingredient not found',
                    'alert-type' => 'error',
                ]);
            }
            $ingredient = $this->ingredientService->deleteIngredient($ingredient);
            if ($ingredient) {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.ingredient.index', [], [
                    'messege'    => 'Ingredient deleted successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.ingredient.index', [], [
                    'messege'    => 'Ingredient deletion failed. Ingredient has orders',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function ingredient_variant(string $id)
    {
        try {
            $ingredient = $this->ingredientService->getIngredient($id);
            if (! $ingredient) {
                return back()->with([
                    'messege'    => 'Ingredient not found',
                    'alert-type' => 'error',
                ]);
            }
            $variants = $this->ingredientService->getIngredientVariants($ingredient);
            return view('ingredient::ingredients.ingredient_variant', compact('ingredient', 'variants'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function ingredient_variant_create(string $id)
    {
        try {
            $ingredient = $this->ingredientService->getIngredient($id);
            if (! $ingredient) {
                return back()->with([
                    'messege'    => 'Ingredient not found',
                    'alert-type' => 'error',
                ]);
            }
            $attributes = $this->attributeService->getAllAttributesForSelect();
            return view('ingredient::ingredients.ingredient_variant_create', compact('ingredient', 'attributes'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function ingredient_variant_store(Request $request, string $id)
    {

        try {
            DB::beginTransaction();
            $ingredient = $this->ingredientService->getIngredient($id);
            if (! $ingredient) {
                return back()->with([
                    'messege'    => 'Ingredient not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->ingredientService->storeIngredientVariant($request, $ingredient);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.ingredient-variant', [$ingredient->id], [
                'messege'    => 'Ingredient Variant created successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function ingredient_variant_edit(string $variant_id)
    {
        try {
            $variant = $this->ingredientService->getIngredientVariant($variant_id);
            if (! $variant) {
                return back()->with([
                    'messege'    => 'Ingredient Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $attributes = $this->attributeService->getAllAttributesForSelect();
            $ingredient    = $variant->ingredient;
            return view('ingredient::ingredients.ingredient_variant_edit', compact('variant', 'attributes', 'ingredient'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function ingredient_variant_update(Request $request, string $variant_id)
    {
        try {
            DB::beginTransaction();
            $variant = $this->ingredientService->getIngredientVariant($variant_id);
            if (! $variant) {
                return back()->with([
                    'messege'    => 'Ingredient Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->ingredientService->updateIngredientVariant($request, $variant);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.ingredient-variant', [$variant->ingredient->id], [
                'messege'    => 'Ingredient Variant updated successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function ingredient_variant_delete(string $variant_id)
    {
        try {
            DB::beginTransaction();
            $variant = $this->ingredientService->getIngredientVariant($variant_id);
            if (! $variant) {
                return back()->with([
                    'messege'    => 'Ingredient Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->ingredientService->deleteIngredientVariant($variant);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.ingredient-variant', [$variant->ingredient->id], [
                'messege'    => 'Ingredient Variant deleted successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function wishlist(Request $request, $id)
    {
        $ingredient = $this->ingredientService->getIngredient($id);

        if (! $ingredient) {
            return back()->with([
                'messege'    => 'Ingredient not found',
                'alert-type' => 'error',
            ]);
        }

        if ($request->type) {
            if ($request->type == 'add') {
                $ingredient->is_favorite = 1;
                $ingredient->save();
                return response()->json(['message' => 'Ingredient Added To Wishlist', 'alert-type' => 'success'], 200);
            } else {
                $ingredient->is_favorite = 0;
                $ingredient->save();

                return response()->json(['message' => 'Ingredient Removed From Wishlist', 'alert-type' => 'success'], 200);
            }
        } else {
            return response()->json(['message' => 'Ingredient Not Found', 'alert-type' => 'error'], 404);
        }
    }

    // bulk ingredient import
    public function bulkImport()
    {
        checkAdminHasPermissionAndThrowException('ingredient.bulk.import');
        return view('ingredient::ingredients.import');
    }

    // store bulk ingredient
    public function bulkImportStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.bulk.import');

        DB::beginTransaction();
        try {
            $this->ingredientService->bulkImport($request);
            DB::commit();
            return back()->with([
                'messege'    => 'Ingredients imported successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    // search  ingredient
    public function search()
    {
        $ingredient = $this->ingredientService->getIngredients()->first();
        if (! $ingredient) {
            return response()->json([
                'status'  => false,
                'message' => 'Ingredient not found',
            ]);
        } else {
            return response()->json([
                'status' => true,
                'data'   => $ingredient,
            ]);
        }
    }
    public function searchIngredients(Request $request)
    {
        $keyword = $request->keyword;

        if (empty($keyword)) {
            return response()->json([
                'status'  => false,
                'message' => 'Keyword is required',
            ]);
        }

        $ingredients = $this->ingredientService->getIngredients()
            ->with('unit', 'purchaseUnit', 'consumptionUnit')
            ->where('status', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%');
            })
            ->limit(20)
            ->get();

        if (!$ingredients->count()) {
            return response()->json([
                'status'  => false,
                'message' => 'Ingredient not found',
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => $ingredients,
        ]);
    }
    public function barcode()
    {
        checkAdminHasPermissionAndThrowException('ingredient.barcode.print');
        return view('ingredient::ingredients.barcode-table');
    }

    public function barcodePrint(Request $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.barcode.print');
        $setting  = cache()->get('setting');
        $ingredients = $this->ingredientService->getIngredients()->whereIn('id', $request->ingredient_id)->get();
        $d        = new DNS1D();
        $codes    = [];

        foreach ($request->barcode_id as $key => $value) {
            for ($i = 1; $i <= (int) $request->qty[$key]; $i++) {
                $code = [
                    'code'   => $value,
                    'qrcode' => $d->getBarcodeSVG($value, 'C39+', .53),
                ];
                $codes[] = $code;
            }
        }

        $action = $request->action;
        return view('ingredient::ingredients.barcode-print', compact('ingredients', 'codes', 'setting', 'action'));
    }

    public function status($id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.status');
        $ingredient = $this->ingredientService->getIngredient($id);
        $status  = $ingredient->status == 1 ? 0 : 1;

        $ingredient->status = $status;
        $ingredient->save();

        $notification = $status == 1 ? 'Ingredient Enabled' : 'Ingredient Disabled';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function bulkDelete(Request $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.delete');
        $ids = $request->ids;
        $this->ingredientService->bulkDelete($ids);
        return response()->json(['success' => true, 'message' => 'Ingredient Deleted Successfully']);
    }

    public function getUnitFamily(Request $request)
    {
        $ingredientId = $request->ingredient_id;
        $ingredient = $this->ingredientService->getIngredient($ingredientId);

        if (!$ingredient || !$ingredient->unit_id) {
            return response()->json(['units' => []]);
        }

        $units = $this->unitService->getUnitFamily($ingredient->unit_id);

        return response()->json(['units' => $units]);
    }
}
