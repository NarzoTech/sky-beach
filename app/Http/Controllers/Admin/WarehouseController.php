<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Services\WarehouseService;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    use RedirectHelperTrait;
    protected $warehouseService;
    public function __construct(WarehouseService $warehouseService)
    {
        $this->middleware('auth:admin');
        $this->warehouseService = $warehouseService;
    }
    public function index()
    {
        checkAdminHasPermissionAndThrowException('warehouse.view');
        $warehouses = $this->warehouseService->all()->get();
        return view('admin.pages.warehouse.index',compact('warehouses'));
    }

    public function store(Request $request){
        checkAdminHasPermissionAndThrowException('warehouse.create');
        $request->validate([
            'name' => "required",
        ]);

        try {
            $this->warehouseService->create($request->all());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.warehouse.index', [], ['messege'=>'Warehouse created successfully','alert-type'=>'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.warehouse.index', [], ['messege'=>'Something went wrong','alert-type'=>'error']);
        }
    }

    public function update(Request $request, $id){
        checkAdminHasPermissionAndThrowException('warehouse.edit');
        $request->validate([
            'name' => "required",
        ]);

        try {
            $this->warehouseService->update($request->all(), $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.warehouse.index', [], ['messege'=>'Warehouse updated successfully','alert-type'=>'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.warehouse.index', [], ['messege'=>'Something went wrong','alert-type'=>'error']);
        }
    }

    public function destroy($id){
        checkAdminHasPermissionAndThrowException('warehouse.delete');
        try {
            $this->warehouseService->delete($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.warehouse.index', [], ['messege'=>'Warehouse deleted successfully','alert-type'=>'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.warehouse.index', [], ['messege'=>'Something went wrong','alert-type'=>'error']);
        }
    }
}
