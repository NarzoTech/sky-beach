<?php

namespace Modules\Product\app\Http\Controllers;


use App\Enums\RedirectType;
use App\Http\Controllers\Controller;

use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Http\Requests\BrandRequest;
use Modules\Product\app\Services\BrandService;

class BrandController extends Controller
{
    use RedirectHelperTrait;
    protected $brandService;
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('product.brand.view');
        $brands = $this->brandService->getPaginateBrands();

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        $brands = $brands->paginate($parpage);
        $brands->appends(request()->query());

        return view('product::products.brand.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('product.brand.create');
        try {
            return view('product::products.brand.create');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with(['messege' => 'Something Went Wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        checkAdminHasPermissionAndThrowException('product.brand.create');
        try {
            $brand = $this->brandService->store($request);

            if ($brand->id) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Brand created successfully', 'status' => 200, 'brand' => $brand], 200);
                }
                return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.brand.create', [], [
                    'messege' => 'Brand created successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.brand.create', [], [
                    'messege' => 'Brand creation failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.brand.create', [], [
                'messege' => 'Brand creation failed',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('product.brand.edit');
        $brand = $this->brandService->find($id);
        try {
            return view('product::products.brand.edit', compact('brand'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with(['messege' => 'Something Went Wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('product.brand.edit');
        try {
            $brand = $this->brandService->update($request, $id);

            if ($brand) {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.brand.index', [], [
                    'messege' => 'Brand updated successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.brand.index', [], [
                    'messege' => 'Brand update failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.brand.index', [], [
                'messege' => 'Brand update failed',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('product.brand.delete');
        try {
            $brand = $this->brandService->delete($id);

            if ($brand) {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.brand.index', [], [
                    'messege' => 'Brand deleted successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.brand.index', [], [
                    'messege' => 'Brand delete failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.brand.index', [], [
                'messege' => 'Brand delete failed',
                'alert-type' => 'error',
            ]);
        }
    }

    public function deleteAll(Request $request)
    {
        checkAdminHasPermissionAndThrowException('product.brand.delete');
        try {
            $brand = $this->brandService->deleteAll($request);

            if ($brand) {
                return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Delete failed'], 400);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed'], 400);
        }
    }
}
