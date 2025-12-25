<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Customer\app\Http\Requests\UserGroupRequest;
use Modules\Customer\app\Http\Services\UserGroupService;

class CustomerGroupController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private UserGroupService $userGroup)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('customer.group.view');
        $customerGroups = $this->userGroup->getUserGroup()->where('type', 'customer')->paginate(request()->get('par-page') ? request()->get('par-page') : 20);

        $customerGroups->appends(request()->query());

        return view('customer::group.index', compact('customerGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserGroupRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('customer.group.create');
        try {
            $this->userGroup->store($request->validated());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customerGroup.index', [], ['messege' => 'Customer group created successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customerGroup.index', [], ['messege' => 'Customer group creation failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserGroupRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('customer.group.edit');
        try {
            $this->userGroup->update($request->validated(), $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customerGroup.index', [], ['messege' => 'Customer group updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customerGroup.index', [], ['messege' => 'Customer group update failed', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('customer.group.delete');
        try {
            $this->userGroup->destroy($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customerGroup.index', [], ['messege' => 'Customer group deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customerGroup.index', [], ['messege' => 'Customer group deletion failed', 'alert-type' => 'error']);
        }
    }
}
