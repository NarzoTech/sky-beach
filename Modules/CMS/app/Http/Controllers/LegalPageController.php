<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CMS\app\Models\LegalPage;

class LegalPageController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.view');
        $pages = LegalPage::orderBy('sort_order')->get();
        return view('cms::admin.legal-pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.create');
        return view('cms::admin.legal-pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.create');

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:legal_pages,slug',
            'content' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['title', 'content', 'meta_title', 'meta_description', 'is_active', 'sort_order']);
            $data['slug'] = $request->slug ?: Str::slug($request->title);

            LegalPage::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.legal-pages.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.legal-pages.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.edit');
        $page = LegalPage::findOrFail($id);
        return view('cms::admin.legal-pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.edit');

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:legal_pages,slug,' . $id,
            'content' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $page = LegalPage::findOrFail($id);
            $data = $request->only(['title', 'content', 'meta_title', 'meta_description', 'is_active', 'sort_order']);

            if ($request->slug) {
                $data['slug'] = $request->slug;
            }

            $page->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.legal-pages.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.legal-pages.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.delete');

        try {
            LegalPage::findOrFail($id)->delete();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.legal-pages.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.legal-pages.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.legal-pages.edit');

        try {
            $page = LegalPage::findOrFail($id);
            $page->is_active = !$page->is_active;
            $page->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $page->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
