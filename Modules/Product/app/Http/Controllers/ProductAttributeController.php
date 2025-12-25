<?php

namespace Modules\Product\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Http\Requests\AttributeRequest;
use Modules\Product\app\Services\AttributeService;

class ProductAttributeController extends Controller
{
    use RedirectHelperTrait;
    private $attributeService;
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = $this->attributeService->getAllAttributes();
        return view('product::products.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product::products.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->attributeService->storeAttribute($request);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.attribute.index', [], ['messege' => 'Attribute created successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.attribute.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $attribute = $this->attributeService->getById($id);
        return view('product::products.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $attribute = $this->attributeService->getById($id);
            $this->attributeService->updateAttribute($request, $attribute);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.attribute.index', [], ['messege' => 'Attribute Update successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.attribute.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $attribute = $this->attributeService->deleteAttribute($id);
            DB::commit();
            if ($attribute['status'] == true) {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.attribute.index', [], ['messege' => 'Attribute deleted successfully', 'alert-type' => 'success']);
            } else {
                return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.attribute.index', [], ['messege' => $attribute['message'], 'alert-type' => 'error']);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.attribute.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    public function checkHasValue(Request $request)
    {
        $attribute = $this->attributeService->getById($request->attribute_id);
        if ($attribute->values->count() > 0) {
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    public function deleteValue(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->attributeService->deleteValue($request->all());
            DB::commit();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.attribute.index', [], ['messege' => 'Attribute value deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.attribute.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }
    public function getValue(Request $request)
    {
        try {
            // get attributes values using array
            $values = $this->attributeService->getValues($request);
            if ($values) {
                return response()->json(['status' => true, 'data' => $values]);
            } else {
                return response()->json(['status' => false, 'message' => 'No values found']);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
