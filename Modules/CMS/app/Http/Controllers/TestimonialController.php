<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\Testimonial;

class TestimonialController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.view');

        $query = Testimonial::orderBy('sort_order');

        if ($request->has('keyword') && $request->keyword) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $testimonials = $query->paginate(20);

        return view('cms::admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.create');
        return view('cms::admin.testimonials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.create');

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'position', 'company', 'content', 'rating', 'is_active', 'is_featured', 'sort_order']);

            if ($request->hasFile('image')) {
                $data['image'] = 'storage/' . $request->file('image')->store('cms/testimonials', 'public');
            }

            Testimonial::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.testimonials.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.testimonials.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.edit');
        $testimonial = Testimonial::findOrFail($id);
        return view('cms::admin.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.edit');

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        DB::beginTransaction();
        try {
            $testimonial = Testimonial::findOrFail($id);
            $data = $request->only(['name', 'position', 'company', 'content', 'rating', 'is_active', 'is_featured', 'sort_order']);

            if ($request->hasFile('image')) {
                // Delete old image
                if ($testimonial->image && Storage::disk('public')->exists(str_replace('storage/', '', $testimonial->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $testimonial->image));
                }
                $data['image'] = 'storage/' . $request->file('image')->store('cms/testimonials', 'public');
            } elseif ($request->boolean('remove_image')) {
                // Remove existing image
                if ($testimonial->image && Storage::disk('public')->exists(str_replace('storage/', '', $testimonial->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $testimonial->image));
                }
                $data['image'] = null;
            }

            $testimonial->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.testimonials.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.testimonials.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.delete');

        try {
            $testimonial = Testimonial::findOrFail($id);

            if ($testimonial->image && Storage::disk('public')->exists(str_replace('storage/', '', $testimonial->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $testimonial->image));
            }

            $testimonial->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.testimonials.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.testimonials.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.edit');

        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->is_active = !$testimonial->is_active;
            $testimonial->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $testimonial->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Delete multiple testimonials.
     */
    public function deleteAll(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.testimonials.delete');

        try {
            $ids = $request->ids;
            $testimonials = Testimonial::whereIn('id', $ids)->get();

            foreach ($testimonials as $testimonial) {
                if ($testimonial->image && Storage::disk('public')->exists(str_replace('storage/', '', $testimonial->image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $testimonial->image));
                }
            }

            Testimonial::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'Deleted successfully'], 200);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
