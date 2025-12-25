<?php

namespace Modules\Attendance\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Attendance\app\Http\Requests\HolidaysRequest;
use Modules\Attendance\app\Models\HolidaySetup;

class HolidaysController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('attendance.setting.view');
        $holidays = HolidaySetup::query();

        if (request('keyword')) {
            $holidays = $holidays->where('name', 'like', '%' . request('keyword') . '%')
                ->orWhere('description', 'like', '%' . request('keyword') . '%');
        }
        if (request()->from_date) {
            $date = now()->parse(request()->from_date);
            $holidays = $holidays->where('start_date', '>=', $date);
        }
        if (request()->to_date) {
            $date = now()->parse(request()->to_date);
            $holidays = $holidays->where('end_date', '<=', $date);
        }

        if (request()->order_type || request()->order_by) {
            $orderBy = request()->order_by ? request()->order_by : 'desc';
            $orderType = request()->order_type ? request()->order_type : 'id';
            $holidays = $holidays->orderBy($orderType, $orderBy);
        } else {
            $holidays = $holidays->orderBy('id', 'desc');
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
            $holidays = $holidays->paginate($parpage);
        } else {
            $holidays = $holidays->paginate(20);
        }

        $holidays->appends(request()->query());

        return view('attendance::holidays.index', compact('holidays'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidaysRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('attendance.setting.create');
        try {
            $data = $request->validated();
            $data['start_date'] = now()->parse($data['start_date']);
            $data['end_date'] = now()->parse($data['end_date']);
            HolidaySetup::create($data);

            $notification = [
                'message' => 'Holiday created successfully',
                'alert-type' => 'success',
            ];
            saveLog('Holiday created successfully', 'info');
            return back()->with($notification);
        } catch (\Exception $e) {
            saveLog($e->getMessage(), 'error');
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ];
            return back()->with($notification);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HolidaysRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('attendance.setting.edit');
        try {
            $data = $request->validated();
            $data['start_date'] = now()->parse($data['start_date']);
            $data['end_date'] = now()->parse($data['end_date']);
            HolidaySetup::where('id', $id)->update($data);

            $notification = [
                'message' => 'Holiday updated successfully',
                'alert-type' => 'success',
            ];
            saveLog('Holiday updated successfully', 'info');
            return back()->with($notification);
        } catch (\Exception $e) {
            saveLog($e->getMessage(), 'error');
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ];
            return back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('attendance.setting.delete');
        try {
            HolidaySetup::find($id)->delete();
            $notification = [
                'message' => 'Holiday deleted successfully',
                'alert-type' => 'success',
            ];
            saveLog('Holiday deleted successfully', 'info');
            return back()->with($notification);
        } catch (\Exception $e) {
            saveLog($e->getMessage(), 'error');
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ];
            return back()->with($notification);
        }
    }
}
