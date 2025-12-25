<?php
namespace Modules\Product\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Milon\Barcode\DNS1D;
use Modules\Product\app\Http\Requests\ProductRequest;
use Modules\Product\app\Services\AttributeService;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;
use Modules\Product\app\Services\UnitTypeService;

class ProductController extends Controller
{
    use RedirectHelperTrait;
    private ProductService $productService;
    private ProductCategoryService $categoryService;
    private AttributeService $attributeService;
    private BrandService $brandService;
    private UnitTypeService $unitService;
    public function __construct(ProductService $productService, ProductCategoryService $categoryService, AttributeService $attributeService, BrandService $brandService, UnitTypeService $unitService)
    {
        $this->productService   = $productService;
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
        checkAdminHasPermissionAndThrowException('product.view');
        try {
            $products = $this->productService->getProducts();

            if (request('export')) {
                $fileName = 'products-' . date('Y-m-d') . '-' . time() . '.xlsx';
                return Excel::download(new ProductsExport($products), $fileName, );
            }

            if (request('par-page')) {
                if (request('par-page') == 'all') {
                    $products = $products->get();
                } else {
                    $products = $products->paginate(request('par-page'));
                    $products->appends(request()->query());
                }
            } else {
                $products = $products->paginate(20);
                $products->appends(request()->query());
            }

            if (request('export_pdf')) {
                return view('product::products.product-pdf', ['products' => $products]);
            }

            $brands     = $this->brandService->getActiveBrands();
            $categories = $this->categoryService->getAllProductCategoriesForSelect();
            return view('product::products.index', compact('products', 'brands', 'categories'));
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
        checkAdminHasPermissionAndThrowException('product.create');
        $categories  = $this->categoryService->getAllProductCategoriesForSelect();
        $brands      = $this->brandService->getActiveBrands();
        $units       = $this->unitService->getParentUnits();
        $parentUnits = $this->unitService->getParentUnits();
        return view('product::products.create', compact('categories', 'brands', 'units', 'parentUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        checkAdminHasPermissionAndThrowException('product.create');
        DB::beginTransaction();
        try {
            $product = $this->productService->storeProduct($request);
            DB::commit();
            Log::info('Product Created Successfully. ID: ' . $product->id);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.product.create', [], ['messege' => 'Product Created Successfully', 'alert-type' => 'success']);
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
        checkAdminHasPermissionAndThrowException('product.view');
        try {
            $product = $this->productService->getProduct($id);

            return view('product::products.show', compact('product'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    public function singleProduct($id)
    {
        $product = $this->productService->getProduct($id);
        return view('product::products.single-product-modal', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('product.edit');
        try {
            $product    = $this->productService->getProduct($id);
            $categories = $this->categoryService->getAllProductCategoriesForSelect();
            $brands     = $this->brandService->getActiveBrands();
            $units      = $this->unitService->getParentUnits();
            return view('product::products.edit', compact('categories', 'brands', 'product', 'units'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('product.edit');
        try {
            DB::beginTransaction();
            $product = $this->productService->getProduct($id);
            if (! $product) {
                return back()->with([
                    'messege'    => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $product = $this->productService->updateProduct($request, $product);
            DB::commit();
            if ($product->id) {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product.index', [], [
                    'messege'    => 'Product updated successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product.index', [], [
                    'messege'    => 'Product update failed',
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
        checkAdminHasPermissionAndThrowException('product.delete');
        try {
            $product = $this->productService->getProduct($id);
            if (! $product) {
                return back()->with([
                    'messege'    => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $product = $this->productService->deleteProduct($product);
            if ($product) {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.product.index', [], [
                    'messege'    => 'Product deleted successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.product.index', [], [
                    'messege'    => 'Product deletion failed. Product has orders',
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

    public function product_variant(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (! $product) {
                return back()->with([
                    'messege'    => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $variants = $this->productService->getProductVariants($product);
            return view('product::products.product_variant', compact('product', 'variants'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_create(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (! $product) {
                return back()->with([
                    'messege'    => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $attributes = $this->attributeService->getAllAttributesForSelect();
            return view('product::products.product_variant_create', compact('product', 'attributes'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_store(Request $request, string $id)
    {

        try {
            DB::beginTransaction();
            $product = $this->productService->getProduct($id);
            if (! $product) {
                return back()->with([
                    'messege'    => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->storeProductVariant($request, $product);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.product-variant', [$product->id], [
                'messege'    => 'Product Variant created successfully',
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

    public function product_variant_edit(string $variant_id)
    {
        try {
            $variant = $this->productService->getProductVariant($variant_id);
            if (! $variant) {
                return back()->with([
                    'messege'    => 'Product Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $attributes = $this->attributeService->getAllAttributesForSelect();
            $product    = $variant->product;
            return view('product::products.product_variant_edit', compact('variant', 'attributes', 'product'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege'    => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_update(Request $request, string $variant_id)
    {
        try {
            DB::beginTransaction();
            $variant = $this->productService->getProductVariant($variant_id);
            if (! $variant) {
                return back()->with([
                    'messege'    => 'Product Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->updateProductVariant($request, $variant);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product-variant', [$variant->product->id], [
                'messege'    => 'Product Variant updated successfully',
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

    public function product_variant_delete(string $variant_id)
    {
        try {
            DB::beginTransaction();
            $variant = $this->productService->getProductVariant($variant_id);
            if (! $variant) {
                return back()->with([
                    'messege'    => 'Product Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->deleteProductVariant($variant);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.product-variant', [$variant->product->id], [
                'messege'    => 'Product Variant deleted successfully',
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
        $product = $this->productService->getProduct($id);

        if (! $product) {
            return back()->with([
                'messege'    => 'Product not found',
                'alert-type' => 'error',
            ]);
        }

        if ($request->type) {
            if ($request->type == 'add') {
                $product->is_favorite = 1;
                $product->save();
                return response()->json(['message' => 'Product Added To Wishlist', 'alert-type' => 'success'], 200);
            } else {
                $product->is_favorite = 0;
                $product->save();

                return response()->json(['message' => 'Product Removed From Wishlist', 'alert-type' => 'success'], 200);
            }
        } else {
            return response()->json(['message' => 'Product Not Found', 'alert-type' => 'error'], 404);
        }
    }

    // bulk product import
    public function bulkImport()
    {
        checkAdminHasPermissionAndThrowException('product.bulk.import');
        return view('product::products.import');
    }

    // store bulk product
    public function bulkImportStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('product.bulk.import');

        DB::beginTransaction();
        try {
            $this->productService->bulkImport($request);
            DB::commit();
            return back()->with([
                'messege'    => 'Products imported successfully',
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

    // search  product
    public function search()
    {
        $product = $this->productService->getProducts()->first();
        if (! $product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ]);
        } else {
            return response()->json([
                'status' => true,
                'data'   => $product,
            ]);
        }
    }
    public function searchProducts(Request $request)
    {
        $keyword = $request->keyword;

        if (empty($keyword)) {
            return response()->json([
                'status'  => false,
                'message' => 'Keyword is required',
            ]);
        }

        $products = $this->productService->getProducts()
            ->with('unit')
            ->where('status', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%');
            })
            ->limit(20)
            ->get();

        if (!$products->count()) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => $products,
        ]);
    }
    public function barcode()
    {
        checkAdminHasPermissionAndThrowException('product.barcode.print');
        return view('product::products.barcode-table');
    }

    public function barcodePrint(Request $request)
    {
        checkAdminHasPermissionAndThrowException('product.barcode.print');
        $setting  = cache()->get('setting');
        $products = $this->productService->getProducts()->whereIn('id', $request->product_id)->get();
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
        return view('product::products.barcode-print', compact('products', 'codes', 'setting', 'action'));
    }

    public function status($id)
    {
        checkAdminHasPermissionAndThrowException('product.status');
        $product = $this->productService->getProduct($id);
        $status  = $product->status == 1 ? 0 : 1;

        $product->status = $status;
        $product->save();

        $notification = $status == 1 ? 'Product Enabled' : 'Product Disabled';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function bulkDelete(Request $request)
    {
        checkAdminHasPermissionAndThrowException('product.delete');
        $ids = $request->ids;
        $this->productService->bulkDelete($ids);
        return response()->json(['success' => true, 'message' => 'Product Deleted Successfully']);
    }

    public function getUnitFamily(Request $request)
    {
        $productId = $request->product_id;
        $product = $this->productService->getProduct($productId);
        
        if (!$product || !$product->unit_id) {
            return response()->json(['units' => []]);
        }

        $units = $this->unitService->getUnitFamily($product->unit_id);
        
        return response()->json(['units' => $units]);
    }
}
