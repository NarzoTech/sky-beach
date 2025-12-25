<?php

namespace Modules\Attendance\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;



use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Modules\Attendance\app\Models\Attendance;
use Modules\Attendance\app\Models\WeekendSetup;
use Modules\Employee\app\Models\Employee;
use Modules\Employee\app\Services\EmployeeService;

class AttendanceController extends Controller
{
    public function __construct(private EmployeeService $employee)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('attendance.view');

        $employees = $this->employee->all()->paginate(20);
        $employees->appends(request()->query());

        return view('attendance::index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('attendance.view');


        $employees = $this->employee->all()->paginate(20);
        $employees->appends(request()->query());
        return view('attendance::create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('attendance.create');

        $request->validate([
            'date' => 'required',
            'employee_id' => 'required',
            'employee_id.*' => 'required|numeric',
            'attendance' => 'required',
            'attendance.*' => 'required|in:absent,present,weekend,clear',
        ], [
            'date.required' => __('Date is required'),
            'employee_id.required' => __('Employee is required'),
            'attendance.required' => __('Attendance is required'),
        ]);
        $date = $request->date;
        $employees = $request->employee_id;
        $attendances = $request->attendance;
        $parsedDate = now()->parse($date);

        // get all attendances for the date
        $attendancesList = Attendance::where('date', $parsedDate)->pluck('employee_id')->toArray();

        // Get employee join dates for validation
        $employeeData = Employee::whereIn('id', $employees)->pluck('join_date', 'id');

        $errors = [];

        foreach ($employees as $key => $employee) {

            // Check if attendance date is before employee's join date
            $joinDate = isset($employeeData[$employee]) ? now()->parse($employeeData[$employee]) : null;
            if ($joinDate && $parsedDate->lt($joinDate)) {
                $emp = Employee::find($employee);
                $errors[] = __(':name cannot be marked attendance before joining date (:date)', [
                    'name' => $emp->name ?? 'Employee',
                    'date' => $joinDate->format('Y-m-d')
                ]);
                continue;
            }

            // check if attendance is clear
            if ($attendances[$key] == 'clear') {
                Attendance::where('date', $parsedDate)->where('employee_id', $employee)->delete();
                continue;
            }


            // check if member has already taken attendance for the date
            if (in_array($employee, $attendancesList)) {
                // update attendance
                $attendance = Attendance::where('date', $parsedDate)->where('employee_id', $employee)->first();
                $attendance->update(['status' => $attendances[$key]]);
                continue;
            }

            Attendance::create([
                'date' => $parsedDate,
                'status' => $attendances[$key],
                'employee_id' => $employee
            ]);
        }

        if (!empty($errors)) {
            return response()->json(['message' => implode(', ', $errors), 'success' => false], 422);
        }

        return response()->json(['message' => __('Attendance Taken'), 'success' => true]);
    }

    public function weekDays()
    {
        checkAdminHasPermissionAndThrowException('attendance.setting.view');
        $days = WeekendSetup::all();
        return view('attendance::weekdays', compact('days'));
    }
    public function weekDaysUpdate(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('attendance.setting.edit');
        $request->validate([
            'name' => 'required',
            'status' => 'required|boolean',
            'is_weekend' => 'required|boolean'
        ]);
        WeekendSetup::updateOrCreate(['id' => $id], $request->except('_token'));
        return back()->with(['message' => 'Weekend days updated successfully', 'alert-type' => 'success']);
    }
}
