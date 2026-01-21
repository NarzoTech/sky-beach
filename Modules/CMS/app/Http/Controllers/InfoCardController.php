<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\InfoCard;

class InfoCardController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.view');

        $query = InfoCard::orderBy('page')->orderBy('sort_order');

        if ($request->has('page_filter') && $request->page_filter) {
            $query->where('page', $request->page_filter);
        }

        $cards = $query->paginate(20);
        $pages = InfoCard::select('page')->distinct()->pluck('page');

        return view('cms::admin.info-cards.index', compact('cards', 'pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.create');
        $pages = ['reservation', 'contact', 'about', 'home'];
        return view('cms::admin.info-cards.create', compact('pages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.create');

        $request->validate([
            'page' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['page', 'title', 'content', 'icon', 'link', 'is_active', 'sort_order']);

            if ($request->hasFile('icon_image')) {
                $data['icon_image'] = 'storage/' . $request->file('icon_image')->store('cms/info-cards', 'public');
            }

            InfoCard::create($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.cms.info-cards.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.info-cards.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.edit');
        $card = InfoCard::findOrFail($id);
        $pages = ['reservation', 'contact', 'about', 'home'];
        return view('cms::admin.info-cards.edit', compact('card', 'pages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.edit');

        $request->validate([
            'page' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon_image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $card = InfoCard::findOrFail($id);
            $data = $request->only(['page', 'title', 'content', 'icon', 'link', 'is_active', 'sort_order']);

            if ($request->hasFile('icon_image')) {
                if ($card->icon_image && Storage::disk('public')->exists(str_replace('storage/', '', $card->icon_image))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $card->icon_image));
                }
                $data['icon_image'] = 'storage/' . $request->file('icon_image')->store('cms/info-cards', 'public');
            }

            $card->update($data);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.cms.info-cards.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.info-cards.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.delete');

        try {
            $card = InfoCard::findOrFail($id);

            if ($card->icon_image && Storage::disk('public')->exists(str_replace('storage/', '', $card->icon_image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $card->icon_image));
            }

            $card->delete();

            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.cms.info-cards.index');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.cms.info-cards.index');
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(string $id)
    {
        checkAdminHasPermissionAndThrowException('cms.info-cards.edit');

        try {
            $card = InfoCard::findOrFail($id);
            $card->is_active = !$card->is_active;
            $card->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $card->is_active
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
