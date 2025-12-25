<?php

namespace Modules\Purchase\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Purchase\app\Models\PurchaseReturnType;

class PurchaseReturnTypeController extends Controller
{

    use RedirectHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('purchase.return.type.view');
        $lists = PurchaseReturnType::query();

        if (request()->keyword) {
            $lists = $lists->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            });
        }
        if (request()->order_by) {
            $sort = request()->order_by;
            $lists = $lists->orderBy('name', $sort);
        }
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        $lists = $lists->paginate($parpage);
        $lists->appends(request()->query());

        return view('purchase::return-list', compact('lists'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('purchase.return.type.create');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        $data['created_by'] = auth('admin')->id();
        PurchaseReturnType::create($data);

        return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.purchase.return.type.list', [], ['messege' => 'Purchase Return Type Created Successfully.', 'alert-type' => 'success']);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('purchase.return.type.edit');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = $request->except('_token');
        $data['updated_by'] = auth('admin')->id();
        PurchaseReturnType::find($id)->update($data);

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.purchase.return.type.list', [], ['messege' => 'Purchase Return Type Updated Successfully.', 'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('purchase.return.type.delete');
        PurchaseReturnType::find($id)->delete();
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.purchase.return.type.list', [], ['messege' => 'Purchase Return Type Deleted Successfully.', 'alert-type' => 'success']);
    }
}
