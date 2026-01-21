<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\CMS\app\Models\Counter;

class CounterController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('cms.counters.view');
        $counters = Counter::orderBy('sort_order')->get();
        return view('cms::admin.counters.index', compact('counters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.counters.create');
        return view('cms::admin.counters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.counters.create');

        $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            Counter::create($request->only(['label', 'value', 'icon', 'suffix', 'is_active', 'sort_order']));

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.counters.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.counters.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.counters.edit');
        $counter = Counter::findOrFail($id);
        return view('cms::admin.counters.edit', compact('counter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.counters.edit');

        $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $counter = Counter::findOrFail($id);
            $counter->update($request->only(['label', 'value', 'icon', 'suffix', 'is_active', 'sort_order']));

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.counters.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.counters.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.counters.delete');

        try {
            Counter::findOrFail($id)->delete();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.counters.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.counters.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.counters.edit');

        try {
            $counter = Counter::findOrFail($id);
            $counter->is_active = !$counter->is_active;
            $counter->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $counter->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
