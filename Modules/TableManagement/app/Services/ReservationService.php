<?php

namespace Modules\TableManagement\app\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\TableManagement\app\Models\RestaurantTable;
use Modules\TableManagement\app\Models\TableReservation;

class ReservationService
{
    public function __construct(private TableReservation $reservation) {}

    public function all()
    {
        $query = $this->reservation->with(['table', 'customer', 'createdBy']);

        if (request('keyword')) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . request('keyword') . '%')
                    ->orWhere('customer_phone', 'like', '%' . request('keyword') . '%')
                    ->orWhere('reservation_number', 'like', '%' . request('keyword') . '%');
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('table_id')) {
            $query->where('table_id', request('table_id'));
        }

        if (request('date')) {
            $query->whereDate('reservation_date', request('date'));
        } elseif (request('from_date') && request('to_date')) {
            $query->whereBetween('reservation_date', [request('from_date'), request('to_date')]);
        }

        return $query->orderBy('reservation_date', 'desc')->orderBy('reservation_time', 'desc');
    }

    public function getTodayReservations()
    {
        return $this->reservation->with(['table', 'customer'])
            ->today()
            ->orderBy('reservation_time')
            ->get();
    }

    public function getUpcomingReservations($limit = 10)
    {
        return $this->reservation->with(['table', 'customer'])
            ->upcoming()
            ->limit($limit)
            ->get();
    }

    public function store(Request $request): TableReservation
    {
        return DB::transaction(function () use ($request) {
            // Check table availability
            $isAvailable = TableReservation::checkTableAvailability(
                $request->table_id,
                $request->reservation_date,
                $request->reservation_time,
                $request->duration_minutes ?? 120
            );

            if (!$isAvailable) {
                throw new \Exception('This table is not available at the selected time. Please choose a different time or table.');
            }

            $reservation = $this->reservation->create([
                'reservation_number' => TableReservation::generateReservationNumber(),
                'table_id' => $request->table_id,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'reservation_date' => Carbon::parse($request->reservation_date),
                'reservation_time' => $request->reservation_time,
                'duration_minutes' => $request->duration_minutes ?? 120,
                'party_size' => $request->party_size,
                'status' => TableReservation::STATUS_PENDING,
                'special_requests' => $request->special_requests,
                'notes' => $request->notes,
                'created_by' => auth('admin')->id(),
            ]);

            // Auto-confirm if setting is enabled or explicitly requested
            if ($request->auto_confirm) {
                $reservation->confirm();
            }

            return $reservation;
        });
    }

    public function update(Request $request, $id): TableReservation
    {
        return DB::transaction(function () use ($request, $id) {
            $reservation = $this->reservation->findOrFail($id);

            // Check if changing table/time and if new slot is available
            if ($request->table_id != $reservation->table_id ||
                $request->reservation_date != $reservation->reservation_date->format('Y-m-d') ||
                $request->reservation_time != $reservation->reservation_time->format('H:i')) {

                $isAvailable = TableReservation::checkTableAvailability(
                    $request->table_id,
                    $request->reservation_date,
                    $request->reservation_time,
                    $request->duration_minutes ?? 120,
                    $id
                );

                if (!$isAvailable) {
                    throw new \Exception('This table is not available at the selected time.');
                }
            }

            $reservation->update([
                'table_id' => $request->table_id,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'reservation_date' => Carbon::parse($request->reservation_date),
                'reservation_time' => $request->reservation_time,
                'duration_minutes' => $request->duration_minutes ?? 120,
                'party_size' => $request->party_size,
                'special_requests' => $request->special_requests,
                'notes' => $request->notes,
            ]);

            return $reservation;
        });
    }

    public function destroy($id): bool
    {
        $reservation = $this->reservation->findOrFail($id);

        // Release table if it was reserved
        if ($reservation->table && $reservation->table->status === RestaurantTable::STATUS_RESERVED) {
            $reservation->table->release();
        }

        return $reservation->delete();
    }

    public function find($id): ?TableReservation
    {
        return $this->reservation->with(['table', 'customer', 'createdBy', 'confirmedBy'])->find($id);
    }

    public function confirm($id): TableReservation
    {
        $reservation = $this->reservation->findOrFail($id);
        $reservation->confirm();
        return $reservation;
    }

    public function seat($id): TableReservation
    {
        $reservation = $this->reservation->findOrFail($id);
        $reservation->seat();

        // Update table status to occupied
        $reservation->table->status = RestaurantTable::STATUS_OCCUPIED;
        $reservation->table->save();

        return $reservation;
    }

    public function complete($id): TableReservation
    {
        $reservation = $this->reservation->findOrFail($id);
        $reservation->complete();

        // Release table
        $reservation->table->release();

        return $reservation;
    }

    public function cancel($id): TableReservation
    {
        $reservation = $this->reservation->findOrFail($id);
        $reservation->cancel();
        return $reservation;
    }

    public function markNoShow($id): TableReservation
    {
        $reservation = $this->reservation->findOrFail($id);
        $reservation->markNoShow();
        return $reservation;
    }

    public function getAvailableTimeslots($tableId, $date, $duration = 120): array
    {
        $table = RestaurantTable::findOrFail($tableId);
        $existingReservations = $this->reservation
            ->where('table_id', $tableId)
            ->whereDate('reservation_date', $date)
            ->whereNotIn('status', [TableReservation::STATUS_CANCELLED, TableReservation::STATUS_COMPLETED, TableReservation::STATUS_NO_SHOW])
            ->get();

        $availableSlots = [];
        $startHour = 10; // Restaurant opens at 10:00
        $endHour = 22;   // Last reservation at 22:00

        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            foreach ([0, 30] as $minute) {
                $timeSlot = sprintf('%02d:%02d', $hour, $minute);
                $slotStart = Carbon::parse($date . ' ' . $timeSlot);
                $slotEnd = $slotStart->copy()->addMinutes($duration);

                $isAvailable = true;
                foreach ($existingReservations as $res) {
                    $resStart = $res->reservation_date_time;
                    $resEnd = $res->end_time;

                    if ($slotStart < $resEnd && $slotEnd > $resStart) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $availableSlots[] = $timeSlot;
                }
            }
        }

        return $availableSlots;
    }

    public function getReservationsByDateRange($startDate, $endDate)
    {
        return $this->reservation->with(['table'])
            ->whereBetween('reservation_date', [$startDate, $endDate])
            ->whereNotIn('status', [TableReservation::STATUS_CANCELLED])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->get()
            ->groupBy(function ($item) {
                return $item->reservation_date->format('Y-m-d');
            });
    }

    public function getStats(): array
    {
        $today = $this->reservation->today()->get();

        return [
            'today_total' => $today->count(),
            'today_pending' => $today->where('status', TableReservation::STATUS_PENDING)->count(),
            'today_confirmed' => $today->where('status', TableReservation::STATUS_CONFIRMED)->count(),
            'today_seated' => $today->where('status', TableReservation::STATUS_SEATED)->count(),
            'today_completed' => $today->where('status', TableReservation::STATUS_COMPLETED)->count(),
            'upcoming' => $this->reservation->upcoming()->count(),
        ];
    }
}
