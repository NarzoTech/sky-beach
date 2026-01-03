<?php

namespace Modules\TableManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\TableManagement\app\Models\RestaurantTable;
use Modules\TableManagement\app\Models\TableReservation;
use Modules\TableManagement\app\Services\ReservationService;
use Modules\TableManagement\app\Services\TableService;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $service,
        private TableService $tableService
    ) {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $reservations = $this->service->all()->paginate(20);
        $stats = $this->service->getStats();
        $tables = RestaurantTable::active()->get();
        $statuses = TableReservation::STATUSES;

        return view('tablemanagement::reservations.index', compact('reservations', 'stats', 'tables', 'statuses'));
    }

    public function create()
    {
        $tables = RestaurantTable::active()->orderBy('name')->get();
        $customers = \App\Models\User::orderBy('name')->get();

        return view('tablemanagement::reservations.create', compact('tables', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'party_size' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:30|max:480',
        ]);

        try {
            $this->service->store($request);
            return redirect()->route('admin.reservations.index')
                ->with('success', 'Reservation created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $reservation = $this->service->find($id);

        if (!$reservation) {
            return redirect()->route('admin.reservations.index')->with('error', 'Reservation not found.');
        }

        return view('tablemanagement::reservations.show', compact('reservation'));
    }

    public function edit($id)
    {
        $reservation = $this->service->find($id);

        if (!$reservation) {
            return redirect()->route('admin.reservations.index')->with('error', 'Reservation not found.');
        }

        $tables = RestaurantTable::active()->orderBy('name')->get();
        $customers = \App\Models\User::orderBy('name')->get();

        return view('tablemanagement::reservations.edit', compact('reservation', 'tables', 'customers'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'party_size' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:30|max:480',
        ]);

        try {
            $this->service->update($request, $id);
            return redirect()->route('admin.reservations.index')
                ->with('success', 'Reservation updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $this->service->destroy($id);
            return redirect()->route('admin.reservations.index')
                ->with('success', 'Reservation deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirm($id): RedirectResponse
    {
        try {
            $this->service->confirm($id);
            return back()->with('success', 'Reservation confirmed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function seat($id): RedirectResponse
    {
        try {
            $this->service->seat($id);
            return back()->with('success', 'Guests have been seated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete($id): RedirectResponse
    {
        try {
            $this->service->complete($id);
            return back()->with('success', 'Reservation completed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id): RedirectResponse
    {
        try {
            $this->service->cancel($id);
            return back()->with('success', 'Reservation cancelled.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markNoShow($id): RedirectResponse
    {
        try {
            $this->service->markNoShow($id);
            return back()->with('success', 'Reservation marked as no-show.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function today()
    {
        $reservations = $this->service->getTodayReservations();
        $stats = $this->service->getStats();

        return view('tablemanagement::reservations.today', compact('reservations', 'stats'));
    }

    public function calendar()
    {
        $startDate = request('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end', now()->endOfMonth()->format('Y-m-d'));

        $reservations = $this->service->getReservationsByDateRange($startDate, $endDate);
        $tables = RestaurantTable::active()->get();

        return view('tablemanagement::reservations.calendar', compact('reservations', 'tables', 'startDate', 'endDate'));
    }

    public function getAvailableTimeslots(Request $request): JsonResponse
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'date' => 'required|date',
            'duration' => 'nullable|integer|min:30',
        ]);

        $timeslots = $this->service->getAvailableTimeslots(
            $request->table_id,
            $request->date,
            $request->duration ?? 120
        );

        return response()->json(['success' => true, 'timeslots' => $timeslots]);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'nullable|integer|min:30',
            'exclude_id' => 'nullable|integer',
        ]);

        $isAvailable = TableReservation::checkTableAvailability(
            $request->table_id,
            $request->date,
            $request->time,
            $request->duration ?? 120,
            $request->exclude_id
        );

        return response()->json([
            'success' => true,
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Table is available.' : 'Table is not available at this time.',
        ]);
    }
}
