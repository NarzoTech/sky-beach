<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AssetExport;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Services\AccountsService;

class AssetController extends Controller
{
    public function __construct(private AccountsService $account) {}
    /**
     * Display a listing of the resource.
     */
    public function index()

    {
        checkAdminHasPermissionAndThrowException('asset.view');

        $lists = Asset::paginate(20);
        $types = AssetType::all();
        $accounts = $this->account->all()->get();




        if (request('export')) {
            $fileName = 'asset-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new AssetExport($lists), $fileName);
        }


        if (request('export_pdf')) {

            return view('admin.pages.asset.pdf.asset', [
                'lists' => $lists,
            ]);
        }

        return view('admin.pages.asset.list', compact('lists', 'types', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)

    {
        checkAdminHasPermissionAndThrowException('asset.create');
        try {
            if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
                $account = $this->account->all()->where('account_type', 'cash')->first();
            } else {
                $account = $this->account->find($request->account_id);
            }

            // store asset

            $asset = Asset::create([
                'name' => $request->name,
                'date' => now()->parse($request->date),
                'amount' => $request->amount,
                'account_id' => $account->id,
                'note' => $request->note,
                'payment_type' => $request->payment_type,
                'type_id' => $request->type_id,
                'created_by' => auth('admin')->id(),
            ]);

            return back()->with(['messege' => 'Asset created successfully.', 'alert-type' => 'success']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return back()->with(['messege' => 'Something went wrong.', 'alert-type' => 'danger']);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)

    {
        checkAdminHasPermissionAndThrowException('asset.edit');
        // update assets
        try {
            if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
                $account = $this->account->all()->where('account_type', 'cash')->first();
            } else {
                $account = $this->account->find($request->account_id);
            }

            $asset = Asset::find($id);

            $asset->update([
                'name' => $request->name,
                'date' => now()->parse($request->date),
                'amount' => $request->amount,
                'account_id' => $account->id,
                'payment_type' => $request->payment_type,
                'note' => $request->note,
                'type_id' => $request->type_id,
                'updated_by' => auth('admin')->id(),
            ]);

            return back()->with(['messege' => 'Asset updated successfully.', 'alert-type' => 'success']);
        } catch (\Exception $th) {

            Log::error($th->getMessage());

            return back()->with(['messege' => 'Something went wrong.', 'alert-type' => 'danger']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)

    {
        checkAdminHasPermissionAndThrowException('asset.delete');
        $asset = Asset::find($id);
        $asset->delete();
        return back()->with(['messege' => 'Asset deleted successfully.', 'alert-type' => 'success']);
    }
}
