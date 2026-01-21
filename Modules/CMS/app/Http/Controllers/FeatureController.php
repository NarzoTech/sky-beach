<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\Feature;

class FeatureController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.features.view');

        $query = Feature::orderBy('page')->orderBy('section')->orderBy('sort_order');

        if ($request->has('page_filter') && $request->page_filter) {
            $query->where('page', $request->page_filter);
        }

        $features = $query->paginate(20);
        $pages = Feature::select('page')->distinct()->pluck('page');

        return view('cms::admin.features.index', compact('features', 'pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.features.create');
        $pages = ['catering', 'about', 'home', 'service'];
        return view('cms::admin.features.create', compact('pages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.features.create');

        $request->validate([
            'page' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['page', 'section', 'title', 'description', 'icon', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                $data['image'] = 'storage/' . $request->file('image')->store('cms/features', 'public');
            }

            Feature::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.features.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.features.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.features.edit');
        $feature = Feature::findOrFail($id);
        $pages = ['catering', 'about', 'home', 'service'];
        return view('cms::admin.features.edit', compact('feature', 'pages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.features.edit');

        $request->validate([
            'page' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $feature = Feature::findOrFail($id);
            $data = $request->only(['page', 'section', 'title', 'description', 'icon', 'is_active', 'sort_order']);

            if ($request->hasFile('image')) {
                if ($feature->image && Storage::disk('public')->exists(str_replace('storage/', '', $feature->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $feature->image));
                }
                $data['image'] = 'storage/' . $request->file('image')->store('cms/features', 'public');
            }

            $feature->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.features.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.features.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.features.delete');

        try {
            $feature = Feature::findOrFail($id);

            if ($feature->image && Storage::disk('public')->exists(str_replace('storage/', '', $feature->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $feature->image));
            }

            $feature->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.features.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.features.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.features.edit');

        try {
            $feature = Feature::findOrFail($id);
            $feature->is_active = !$feature->is_active;
            $feature->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $feature->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
