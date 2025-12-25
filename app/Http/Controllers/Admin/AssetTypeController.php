<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetType;
use Illuminate\Http\Request;

class AssetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('asset.type.view');
        $types = AssetType::paginate(20);

        return view('admin.pages.asset.type', compact('types'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('asset.type.create');
        $request->validate([
            'name' => 'required',
        ]);

        AssetType::create([
            'name' => $request->name,
        ]);

        return back()->with(['messege' => 'Asset Type Created', 'alert-type' => 'success']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        checkAdminHasPermissionAndThrowException('asset.type.edit');
        $request->validate([
            'name' => 'required',
        ]);

        $type = AssetType::find($id);

        $type->update([
            'name' => $request->name
        ]);

        return back()->with(['messege' => 'Asset Type Updated', 'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        checkAdminHasPermissionAndThrowException('asset.type.delete');
        $type = AssetType::find($id);

        if ($type) {
            $type->delete();
            return back()->with(['messege' => 'Asset Type Deleted', 'alert-type' => 'success']);
        } else {
            return back()->with(['messege' => 'Asset Type Not Found', 'alert-type' => 'danger']);
        }
    }
}
