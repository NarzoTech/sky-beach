<?php
namespace Modules\Expense\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Expense\app\Models\ExpenseType;

class ExpenseTypeController extends Controller
{
    use RedirectHelperTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('expense.type.view');
        $types = ExpenseType::query();

        if (request('keyword')) {
            $keyword = request('keyword');
            $types   = $types->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        }
        if (request('order_type')) {
            $orderBy = request('order_by', 'desc'); // Default to 'desc' if not specified
            $types   = $types->orderBy(request('order_type'), $orderBy);
        } else {
            $types = $types->orderBy('name', 'asc');
        }
        $parPage = request('par_page');
        if ($parPage === 'all') {
            $types = $types->get();
        } elseif ($parPage) {
            $types = $types->paginate((int) $parPage);
            $types->appends(request()->query());
        } else {
            $types = $types->paginate(20);
            $types->appends(request()->query());
        }
        $parentTypes = ExpenseType::whereNull('parent_id')->orderBy('name')->get();

        return view('expense::type', compact('types', 'parentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('expense.type.create');
        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|exists:expense_types,id',
        ]);

        try {
            $type            = new ExpenseType();
            $type->name      = $request->name;
            $type->parent_id = $request->parent_id;
            $type->save();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Created Successfully', 'alert-type' => 'success']);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.type.create', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('expense.type.edit');
        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|exists:expense_types,id|not_in:' . $id,
        ]);

        try {
            $type            = ExpenseType::find($id);
            $type->name      = $request->name;
            $type->parent_id = $request->parent_id;
            $type->save();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Updated Successfully', 'alert-type' => 'success']);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense.type.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('expense.type.delete');
        try {
            $type = ExpenseType::find($id);
            $type->delete();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense.type.index', [], ['messege' => 'Expense Type Deleted Successfully', 'alert-type' => 'success']);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense.type.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    public function getChildren($id)
    {
        $children = ExpenseType::where('parent_id', $id)->get();
        return response()->json($children);
    }
}
