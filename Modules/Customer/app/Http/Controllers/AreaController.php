<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Customer\app\Http\Services\AreaService;

class AreaController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private AreaService $areaService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('customer.area.view');
        $areas = $this->areaService->getArea();

        if (request()->keyword) {
            $areas = $areas->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            });
        }
        if (request()->order_by) {
            $areas = $areas->orderBy('name', request()->order_by);
        } else {
            $areas = $areas->orderBy('name', 'asc');
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        $areas = $areas->paginate($parpage);
        $areas->appends(request()->query());

        return view('customer::area.index', compact('areas'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('customer.area.create');
        $request->validate([
            'name' => 'required|string',
        ]);


        try {
            $this->areaService->saveArea($request->only('name'));
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.area.index', [], ['messege' => 'Area created successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.area.index', [], ['messege' => 'Area creation failed', 'alert-type' => 'error']);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('customer.area.edit');
        $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $this->areaService->updateArea($request->only('name'), $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.area.index', [], ['messege' => 'Area updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.area.index', [], ['messege' => 'Area update failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('customer.area.delete');
        try {
            $this->areaService->deleteArea($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.area.index', [], ['messege' => 'Area deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.area.index', [], ['messege' => 'Area deletion failed', 'alert-type' => 'error']);
        }
    }
}
