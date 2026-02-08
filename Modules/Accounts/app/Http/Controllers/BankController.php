<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Services\BankService;

class BankController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private BankService $bankService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('bank.view');
        $banks = $this->bankService->all()->paginate(20);
        $banks->appends(request()->query());

        return view('accounts::bank.index', compact('banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('bank.create');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $this->bankService->create($request->only('name'));
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.bank.index', [], ['messege' => __('Bank created successfully'), 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.bank.index', [], ['messege' => __('Something went wrong'), 'alert-type' => 'error']);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('bank.edit');
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $bank = $this->bankService->find($id);
            $this->bankService->update($bank, $request->only('name'));
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.bank.index', [], ['messege' => __('Bank updated successfully'), 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.bank.index', [], ['messege' => __('Something went wrong'), 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('bank.delete');
        try {
            $bank = $this->bankService->find($id);
            $this->bankService->delete($bank);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.bank.index', [], ['messege' => __('Bank deleted successfully'), 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.bank.index', [], ['messege' => __('Something went wrong'), 'alert-type' => 'error']);
        }
    }
}
