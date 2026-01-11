<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use App\Enums\RedirectType;
use Illuminate\Http\Request;
use Modules\Employee\app\Models\Employee;

class WaiterController extends Controller
{
    use RedirectHelperTrait;

    /**
     * Display a listing of waiters.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('pos.view');

        $waiters = Employee::where('status', 1)
            ->when(request('keyword'), function ($query) {
                $keyword = request('keyword');
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('mobile', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('designation', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('name')
            ->paginate(20);

        $waiters->appends(request()->query());

        return view('pos::waiters.index', compact('waiters'));
    }

    /**
     * Show the form for creating a new waiter.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('pos.view');
        return view('pos::waiters.create');
    }

    /**
     * Store a newly created waiter.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('pos.view');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'mobile' => 'required|string|max:20',
            'designation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'nid' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['designation'] = $validated['designation'] ?? 'Waiter';
        $validated['status'] = 1;
        $validated['join_date'] = $request->join_date ? now()->parse($request->join_date) : now();
        $validated['yearly_leaves'] = $request->yearly_leaves ?? 12;

        if ($request->hasFile('image')) {
            $validated['image'] = file_upload($request->file('image'));
        }

        Employee::create($validated);

        saveLog('Waiter added successfully');
        return $this->redirectWithMessage(
            RedirectType::CREATE->value,
            'admin.pos.waiters.index',
            [],
            ['message' => __('Waiter added successfully'), 'alert-type' => 'success']
        );
    }

    /**
     * Show the form for editing a waiter.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('pos.view');
        $waiter = Employee::findOrFail($id);
        return view('pos::waiters.edit', compact('waiter'));
    }

    /**
     * Update the specified waiter.
     */
    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('pos.view');

        $waiter = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $id,
            'mobile' => 'required|string|max:20',
            'designation' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'nid' => 'nullable|string|max:50',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['designation'] = $validated['designation'] ?? 'Waiter';
        $validated['join_date'] = $request->join_date ? now()->parse($request->join_date) : $waiter->join_date;

        if ($request->hasFile('image')) {
            $validated['image'] = file_upload($request->file('image'), oldFile: $waiter->image);
        }

        $waiter->update($validated);

        saveLog('Waiter updated successfully');
        return $this->redirectWithMessage(
            RedirectType::UPDATE->value,
            'admin.pos.waiters.index',
            [],
            ['message' => __('Waiter updated successfully'), 'alert-type' => 'success']
        );
    }

    /**
     * Remove the specified waiter.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('pos.view');

        $waiter = Employee::findOrFail($id);

        // Check if waiter has any orders
        $orderCount = \Modules\Sales\app\Models\Sale::where('waiter_id', $id)->count();
        if ($orderCount > 0) {
            return $this->redirectWithMessage(
                RedirectType::DELETE->value,
                'admin.pos.waiters.index',
                [],
                ['message' => __('Cannot delete waiter with existing orders. Deactivate instead.'), 'alert-type' => 'error']
            );
        }

        if ($waiter->image) {
            @unlink(public_path($waiter->image));
        }

        $waiter->delete();

        saveLog('Waiter deleted successfully');
        return $this->redirectWithMessage(
            RedirectType::DELETE->value,
            'admin.pos.waiters.index',
            [],
            ['message' => __('Waiter deleted successfully'), 'alert-type' => 'success']
        );
    }

    /**
     * Toggle waiter status.
     */
    public function status($id)
    {
        checkAdminHasPermissionAndThrowException('pos.view');

        $waiter = Employee::findOrFail($id);
        $waiter->status = !$waiter->status;
        $waiter->save();

        return $this->redirectWithMessage(
            RedirectType::UPDATE->value,
            'admin.pos.waiters.index',
            [],
            ['message' => __('Waiter status updated successfully'), 'alert-type' => 'success']
        );
    }
}
