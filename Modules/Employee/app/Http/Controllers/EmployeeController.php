<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\app\Http\Requests\EmployeeRequest;
use Modules\Employee\app\Services\EmployeeService;

class EmployeeController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private EmployeeService $employee) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('employee.view');
        $employees = $this->employee->all();

        if (request('keyword')) {
            $keyword = request('keyword');
            $employees = $employees->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('designation', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                ;
            });
        }

        $sort = request()->order_by ? request()->order_by : 'asc';

        if (request('order_type')) {
            $employees = $employees->orderBy(request('order_type'), $sort);
        } else {
            $employees = $employees->orderBy('name', $sort);
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $employees = $employees->get();
        } else {
            $employees = $employees->paginate($parpage);
            $employees->appends(request()->query());
        }



        if (request('export')) {
            $fileName = 'employee-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new EmployeeExport($employees), $fileName);
        }

        if (request('export_pdf')) {

            return view('employee::pdf.employee', [
                'employees' => $employees,
            ]);
        }
        return view('employee::index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('employee.create');
        return view('employee::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('employee.create');
        try {
            $data = $request->validated();
            $data['join_date'] = now()->parse($request->join_date);
            $data['yearly_leaves'] = $request->yearly_leaves ?? 0;
            if ($request->hasFile('image')) {
                $data['image'] = file_upload($request->file('image'));
            }
            $this->employee->store($data);
            saveLog('Employee added successfully');
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['message' => 'Employee added successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {

            saveLog($e->getMessage(), 'error');
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['message' => $e->getMessage(), 'alert-type' => 'danger']);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('employee.edit');
        $employee = $this->employee->find($id);
        return view('employee::edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('employee.edit');
        try {
            $data = $request->validated();
            $data['join_date'] = now()->parse($request->join_date);
            $data['yearly_leaves'] = $request->yearly_leaves ?? 0;
            if ($request->hasFile('image')) {
                $data['image'] = file_upload($request->file('image'), oldFile: $this->employee->find($id)->image);
            }
            $this->employee->update($id, $data);
            saveLog('Employee updated successfully');
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['message' => 'Employee updated successfully', 'alert-type' => 'success']);
        } catch (\Throwable $th) {

            saveLog($th->getMessage(), 'error');
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['message' => $th->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('employee.delete');
        $this->employee->destroy($id);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.employee.index', [], ['message' => 'Employee deleted successfully', 'alert-type' => 'success']);
    }

    public function status($id)
    {
        checkAdminHasPermissionAndThrowException('employee.status');
        $this->employee->changeStatus($id);
        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['message' => 'Employee status updated successfully', 'alert-type' => 'success']);
    }
}
