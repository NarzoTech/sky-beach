<?php

namespace Modules\Service\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Service\app\Services\ServiceCategoryService;

class ServiceCategoryController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private ServiceCategoryService $serviceCategoryService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('service.category.view');
        $categories = $this->serviceCategoryService->all();

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $categories = $categories->get();
        } else {
            $categories = $categories->paginate($parpage);
            $categories->appends(request()->query());
        }


        return view('service::index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        checkAdminHasPermissionAndThrowException('service.category.create');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $categories = $this->serviceCategoryService->store($request->only('name'));

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.serviceCategory.index', [], ['messege' => 'Service Category created successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.serviceCategory.index', [], ['messege' => 'Service Category creation failed', 'alert-type' => 'error']);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('service.category.edit');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        try {
            $this->serviceCategoryService->update($id, $request->only('name'));
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.serviceCategory.index', [], ['messege' => 'Service Category updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.serviceCategory.index', [], ['messege' => 'Service Category update failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('service.category.delete');
        $this->serviceCategoryService->delete($id);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.serviceCategory.index', [], ['messege' => 'Service Category deleted successfully', 'alert-type' => 'success']);
    }
}
