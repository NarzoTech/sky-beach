<?php

namespace Modules\Ingredient\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\LogActivity;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Ingredient\app\Services\UnitTypeService;

class UnitTypeController extends Controller
{
    use RedirectHelperTrait;
    protected $unitTypeService;

    public function __construct(UnitTypeService $unitTypeService)
    {
        $this->unitTypeService = $unitTypeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('ingredient.unit.view');
        $units = $this->unitTypeService->getAll();
        $parentUnits = $this->unitTypeService->getParentUnits();
        return view('ingredient::unit-types.index', compact('units', "parentUnits"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('ingredient.unit.create');
        $request->validate([
            'name' => 'required|unique:unit_types,name',
            'ShortName' => 'required',
            'status' => 'required',
        ]);
        try {
            $unit = $this->unitTypeService->save($request);


            if ($request->ajax()) {
                return response()->json(['message' => 'Unit created successfully', 'unit' => $unit, 'status' => 200], 200);
            }
            return $this->redirectWithMessage(RedirectType::CREATE->value, "admin.unit.index");
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.unit.edit');
        $unit = $this->unitTypeService->findById($id);
        return $unit;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.unit.edit');
        $request->validate([
            'name' => 'required|unique:unit_types,name,' . $id,
            'ShortName' => 'required',
            'status' => 'required',
        ]);
        try {
            $this->unitTypeService->update($request, $id);

            return $this->redirectWithMessage(RedirectType::UPDATE->value, "admin.unit.index");
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('ingredient.unit.delete');
        try {
            $result = $this->unitTypeService->delete($id);
            if ($result == "not_possible") {
                return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index", notification: ['messege' => 'Unit Has Ingredients. Unit cannot be deleted', 'alert-type' => 'error']);
            }

            return $this->redirectWithMessage(RedirectType::DELETE->value, "admin.unit.index");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, "admin.unit.index");
        }
    }

    public function unitByParent($id)
    {
        $unit = $this->unitTypeService->findById($id);

        return response()->json($unit, 200);
    }
}
