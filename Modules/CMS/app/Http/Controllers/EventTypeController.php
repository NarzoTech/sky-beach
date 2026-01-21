<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\EventType;

class EventTypeController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.view');
        $eventTypes = EventType::orderBy('sort_order')->get();
        return view('cms::admin.event-types.index', compact('eventTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.create');
        return view('cms::admin.event-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.create');

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'icon', 'description', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                $data['image'] = 'storage/' . $request->file('image')->store('cms/event-types', 'public');
            }

            EventType::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.event-types.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.event-types.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.edit');
        $eventType = EventType::findOrFail($id);
        return view('cms::admin.event-types.edit', compact('eventType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.edit');

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $eventType = EventType::findOrFail($id);
            $data = $request->only(['name', 'icon', 'description', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                if ($eventType->image && Storage::disk('public')->exists(str_replace('storage/', '', $eventType->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $eventType->image));
                }
                $data['image'] = 'storage/' . $request->file('image')->store('cms/event-types', 'public');
            }

            $eventType->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.event-types.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.event-types.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.delete');

        try {
            $eventType = EventType::findOrFail($id);

            if ($eventType->image && Storage::disk('public')->exists(str_replace('storage/', '', $eventType->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $eventType->image));
            }

            $eventType->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.event-types.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.event-types.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.event-types.edit');

        try {
            $eventType = EventType::findOrFail($id);
            $eventType->is_active = !$eventType->is_active;
            $eventType->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $eventType->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
