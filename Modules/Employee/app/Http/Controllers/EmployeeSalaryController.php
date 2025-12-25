<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\EmployeeSalaryExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Attendance\app\Models\HolidaySetup;
use Modules\Attendance\app\Models\WeekendSetup;
use Modules\Employee\app\Http\Requests\EmployeeSalaryRequest;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Employee\app\Services\EmployeeService;
use Modules\Purchase\app\Services\PurchaseService;

class EmployeeSalaryController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private PurchaseService $purchaseService, private EmployeeService $employee)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {

        checkAdminHasPermissionAndThrowException('employee.view.payment');
        [$payments, $employee, $month, $payableSalary, $totalAttendance, $totalDayOff] = $this->employee->calculateSalary($request, $id);

        return view('employee::salary.index', compact('payments', 'employee', 'month', 'payableSalary', 'totalAttendance', 'totalDayOff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        if (!checkAdminHasPermission('employee.pay.salary') && !checkAdminHasPermission('employee.pay.advance')) {
            abort(403);
        }

        $accounts = $this->purchaseService->getAccounts();
        $employee = $this->employee->find($id);
        [$payments, $employee, $month, $payableSalary, $totalAttendance, $totalDayOff] = $this->employee->calculateSalary(request(), $id);
        $paidAmount = $employee->getPaidAmountAttribute();
        return view('employee::salary.create', compact('accounts', 'employee', 'payableSalary', 'paidAmount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeSalaryRequest $request, $id): RedirectResponse
    {
        if (!checkAdminHasPermission('employee.pay.salary') && !checkAdminHasPermission('employee.pay.advance')) {
            abort(403);
        }
        try {
            $employee = $this->employee->find($id);
            $this->employee->addSalary($request, $employee);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['message' => 'Employee salary added successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.employee.index', [], ['message' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('employee.edit.salary');
        $payment = EmployeeSalary::with('account')->find($id);
        $employee = $this->employee->find($payment->employee_id);
        $accounts = $this->purchaseService->getAccounts();
        return view('employee::salary.edit', compact('payment', 'employee', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeSalaryRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('employee.edit.salary');
        try {
            $payment = EmployeeSalary::with('account')->find($id);
            $this->employee->updateSalary($request, $payment);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['message' => 'Employee salary updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.employee.index', [], ['message' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('employee.delete.salary');
        $salary = EmployeeSalary::find($id);
        $salary->delete();
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.employee.index', [], ['message' => 'Employee salary deleted successfully', 'alert-type' => 'success']);
    }

    public function salaryInfo(Request $request, $id)
    {
        $employee = $this->employee->find($id);

        $amount = $employee->getPaidAmountAttribute($request->month, $request->year);
        // $employee->getDueAmountAttribute($request->month, $request->year)
        // (,,,,) skipping destructuring
        [,,, $payableSalary] = $this->employee->calculateSalary($request, $id);
        return ['advanceAmount' => $amount, 'dueAmount' => $payableSalary - $amount, 'payableSalary' => $payableSalary];
    }

    public function salaryList()
    {
        checkAdminHasPermissionAndThrowException('employee.view.payment');

        $payments = EmployeeSalary::with('employee');

        if (request('keyword')) {
            $keyword = request('keyword');
            $payments = $payments->where(function ($query) use ($keyword) {
                $query->where('amount', 'like', "%{$keyword}%")
                    ->orWhereHas('employee', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }
        $sort = request()->order_by ? request()->order_by : 'desc';

        if (request('order_type')) {
            $payments = $payments->orderBy(request('order_type'), $sort);
        } else {
            $payments = $payments->orderBy('id', $sort);
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $payments = $payments->get();
        } else {
            $payments = $payments->paginate($parpage);
            $payments->appends(request()->query());
        }


        if (request('export')) {
            $fileName = 'employee-salary-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new EmployeeSalaryExport($payments), $fileName);
        }


        if (request('export_pdf')) {

            return view('employee::pdf.salary', [
                'payments' => $payments,
            ]);
        }


        return view('employee::salary.salary-list', compact('payments'));
    }
}
