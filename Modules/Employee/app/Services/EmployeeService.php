<?php

namespace Modules\Employee\app\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Accounts\app\Models\Account;
use Modules\Attendance\app\Models\HolidaySetup;
use Modules\Attendance\app\Models\WeekendSetup;
use Modules\Attendance\app\Models\Attendance;
use Modules\Employee\app\Models\Employee;
use Modules\Employee\app\Models\EmployeeSalary;

class EmployeeService
{
    public function __construct(private Employee $employee) {}

    public function all()
    {
        return $this->employee->with(['employeeSalary', 'attendance', 'currentSalary']);
    }

    public function find($id)
    {
        return $this->all()->find($id);
    }
    public function store(array $data)
    {
        $this->employee->create($data);
    }

    public function update($id, array $data)
    {
        $this->employee->find($id)->update($data);
    }

    public function destroy($id)
    {
        $this->employee->find($id)->delete();
    }

    public function changeStatus($id)
    {
        $employee = $this->employee->find($id);
        $employee->update(['status' => $employee->status == 1 ? 0 : 1]);
    }

    public function addSalary($request, $employee)
    {

        $data = $request->except('_token');
        $data['employee_id'] = $employee->id;
        $data['date'] = now()->parse($request->date);
        $data['month'] = now()->parse($request->month)->format('F');
        $data['year'] = now()->parse($request->date)->format('Y');
        $data['type'] = isset($request->type) && $request->type == 2 ? 'advance' : 'salary';
        $data['salary'] = $request->salary;
        $data['payable_salary'] = $request->payable_salary;
        $data['payment_type'] = $request->payment_type;
        $data['amount'] = $request->amount;
        $data['note'] = $request->note;

        if ($request->payment_type == 'cash') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::where('account_type', $request->payment_type)
                ->where('id', $request->account_id)->first();
        }
        $data['account_id'] = $account->id;
        $employee->employeeSalary()->create($data);
    }


    public function updateSalary($request, $payment)
    {

        $data = $request->except('_token');
        $data['date'] = now()->parse($request->date);
        $data['month'] = $request->month ? now()->parse($request->month)->format('F') : now()->parse($request->date)->format('F');
        $data['year'] = $request->year ?? now()->parse($request->date)->format('Y');
        $data['type'] = isset($request->type) && $request->type == 2 ? 'advance' : 'salary';
        $data['payment_type'] = $request->payment_type;
        $data['amount'] = $request->amount;
        $data['note'] = $request->note;
        $data['account_id'] = $payment->account_id;

        if ($request->payment_type == 'cash') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::where('account_type', $request->payment_type)
                ->where('id', $request->account_id)->first();
        }
        $data['account_id'] = $account->id;

        $payment->update($data);

        return back()->with(['message' => 'Salary updated successfully', 'alert-type' => 'success']);
    }


    public function calculateSalary(Request $request, $id)
    {

        // check if weekend days is in cache (expires after 1 hour)
        if (!Cache::has('weekends')) {
            $weekends = WeekendSetup::where('is_weekend', 1)->pluck('name')->toArray();
            Cache::put('weekends', $weekends, 3600);
        }

        // get the  weekend days
        $weekends = Cache::get('weekends');

        $employee = $this->employee->find($id);
        $month = $request->month ?? now()->format('F');
        $monthNumber = now()->parse($month)->month;
        $year = $request->year ?? now()->format('Y');


        $payments = EmployeeSalary::with(['employee', 'account'])->where('employee_id', $id)->where('month', $month)->where('year', $year)
            ->get();


        // total attendance of employee in that month (query directly to avoid relationship's built-in month filter)
        $totalAttendance = Attendance::where('employee_id', $id)
            ->whereMonth('date', $monthNumber)
            ->whereYear('date', $year)
            ->where(function ($query) {
                $query->where('status', 'present')->orWhere('status', 'weekend');
            })
            ->count();



        $weekendDays = collect($weekends)->map(function ($day) {
            return now()->parse($day)->dayOfWeek;
        })->toArray();



        $totalWeekends = 0;

        $startOfMonth = now()->month($monthNumber)->year($year)->startOfMonth()->copy();
        $endOfMonth = now()->month($monthNumber)->year($year)->endOfMonth()->copy();
        $currentDate = now()->copy();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;
        $searchMonth = $startOfMonth->month;
        $searchYear = $startOfMonth->year;


        if ($searchYear < $currentYear || ($searchYear == $currentYear && $searchMonth <= $currentMonth)) {
            $totalDaysOfTheMonth = $endOfMonth->copy();
            if ($searchYear == $currentYear && $searchMonth == $currentMonth) {
                $currentDay = $currentDate->day;
                $endDay = $endOfMonth->day;
                $totalDaysOfTheMonth = $endOfMonth->copy()->subDays($endDay - $currentDay);
            }
            $loopDate = $startOfMonth->copy();
            while ($loopDate <= $totalDaysOfTheMonth) {
                if (in_array($loopDate->dayOfWeek, $weekendDays)) {
                    $totalWeekends++;
                }
                $loopDate->addDay();
            }
        } else {
            $totalWeekends = 0;
        }

        // Get holidays that overlap with the current month (start in this month OR end in this month OR span across this month)
        $monthStart = now()->month($monthNumber)->year($year)->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->month($monthNumber)->year($year)->endOfMonth()->format('Y-m-d');

        $holidays = HolidaySetup::where(function ($query) use ($monthStart, $monthEnd) {
            $query->where(function ($q) use ($monthStart, $monthEnd) {
                // Holiday starts within this month
                $q->whereBetween('start_date', [$monthStart, $monthEnd]);
            })->orWhere(function ($q) use ($monthStart, $monthEnd) {
                // Holiday ends within this month
                $q->whereBetween('end_date', [$monthStart, $monthEnd]);
            })->orWhere(function ($q) use ($monthStart, $monthEnd) {
                // Holiday spans the entire month
                $q->where('start_date', '<', $monthStart)
                  ->where('end_date', '>', $monthEnd);
            });
        })->get();


        // count total holidays (only count days within the current month)
        $totalHolidays = 0;
        $monthStartDate = now()->month($monthNumber)->year($year)->startOfMonth();
        $monthEndDate = now()->month($monthNumber)->year($year)->endOfMonth();

        foreach ($holidays as $holiday) {
            $holidayStart = now()->parse($holiday->start_date);
            $holidayEnd = now()->parse($holiday->end_date);

            // Clamp holiday dates to current month boundaries
            $effectiveStart = $holidayStart->lt($monthStartDate) ? $monthStartDate->copy() : $holidayStart->copy();
            $effectiveEnd = $holidayEnd->gt($monthEndDate) ? $monthEndDate->copy() : $holidayEnd->copy();

            $loopDate = $effectiveStart->copy();
            while ($loopDate <= $effectiveEnd) {
                // Only count if not a weekend day
                if (!in_array($loopDate->dayOfWeek, $weekendDays)) {
                    $totalHolidays++;
                }
                $loopDate->addDay();
            }
        }


        // current month total days
        $totalDays = now()->month($monthNumber)->year($year)->daysInMonth;

        $totalWorkingDays = $totalDays - ($totalWeekends + $totalHolidays);


        $totalDayOff = $totalWeekends + $totalHolidays;

        $payableSalary = $employee->salary;
        if ($totalWorkingDays != $totalAttendance) {
            $payableSalary = ($payableSalary / $totalDays) * ($totalWeekends + $totalHolidays + $totalAttendance);
        }
        $payableSalary = (int) $payableSalary;

        return [$payments, $employee, $month, $payableSalary, $totalAttendance, $totalDayOff];
    }
}
