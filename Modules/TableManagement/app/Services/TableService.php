<?php

namespace Modules\TableManagement\app\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\TableManagement\app\Models\RestaurantTable;

class TableService
{
    public function __construct(private RestaurantTable $table) {}

    public function all()
    {
        $query = $this->table->with('currentSale');

        if (request('keyword')) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . request('keyword') . '%')
                    ->orWhere('table_number', 'like', '%' . request('keyword') . '%');
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('floor')) {
            $query->where('floor', request('floor'));
        }

        if (request('section')) {
            $query->where('section', request('section'));
        }

        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getActiveTablesGroupedByFloor()
    {
        return $this->table->active()
            ->with(['currentSale', 'todayReservations'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('floor');
    }

    public function getAvailableTables()
    {
        return $this->table->available()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request): RestaurantTable
    {
        return DB::transaction(function () use ($request) {
            return $this->table->create([
                'name' => $request->name,
                'table_number' => $request->table_number ?? RestaurantTable::generateTableNumber(),
                'capacity' => $request->capacity ?? 4,
                'floor' => $request->floor,
                'section' => $request->section,
                'shape' => $request->shape ?? 'square',
                'position_x' => $request->position_x ?? 0,
                'position_y' => $request->position_y ?? 0,
                'status' => RestaurantTable::STATUS_AVAILABLE,
                'notes' => $request->notes,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
                'sort_order' => $request->sort_order ?? 0,
            ]);
        });
    }

    public function update(Request $request, $id): RestaurantTable
    {
        return DB::transaction(function () use ($request, $id) {
            $table = $this->table->findOrFail($id);

            $table->update([
                'name' => $request->name,
                'table_number' => $request->table_number,
                'capacity' => $request->capacity,
                'floor' => $request->floor,
                'section' => $request->section,
                'shape' => $request->shape,
                'position_x' => $request->position_x ?? $table->position_x,
                'position_y' => $request->position_y ?? $table->position_y,
                'notes' => $request->notes,
                'is_active' => $request->has('is_active') ? $request->is_active : $table->is_active,
                'sort_order' => $request->sort_order ?? $table->sort_order,
            ]);

            return $table;
        });
    }

    public function destroy($id): bool
    {
        $table = $this->table->findOrFail($id);

        // Check if table is currently occupied
        if ($table->isOccupied()) {
            throw new \Exception('Cannot delete an occupied table. Please complete or cancel the current order first.');
        }

        return $table->delete();
    }

    public function find($id): ?RestaurantTable
    {
        return $this->table->with(['currentSale', 'reservations'])->find($id);
    }

    public function updateStatus($id, $status): RestaurantTable
    {
        $table = $this->table->findOrFail($id);
        $table->status = $status;

        if ($status === RestaurantTable::STATUS_AVAILABLE) {
            $table->current_sale_id = null;
        }

        $table->save();
        return $table;
    }

    public function updatePositions(array $positions): void
    {
        DB::transaction(function () use ($positions) {
            foreach ($positions as $position) {
                $this->table->where('id', $position['id'])->update([
                    'position_x' => $position['x'],
                    'position_y' => $position['y'],
                ]);
            }
        });
    }

    public function getFloors(): array
    {
        return $this->table->distinct()
            ->whereNotNull('floor')
            ->pluck('floor')
            ->toArray();
    }

    public function getSections(): array
    {
        return $this->table->distinct()
            ->whereNotNull('section')
            ->pluck('section')
            ->toArray();
    }

    public function getTableStats(): array
    {
        $tables = $this->table->active()->get();

        return [
            'total' => $tables->count(),
            'available' => $tables->where('status', RestaurantTable::STATUS_AVAILABLE)->count(),
            'occupied' => $tables->where('status', RestaurantTable::STATUS_OCCUPIED)->count(),
            'reserved' => $tables->where('status', RestaurantTable::STATUS_RESERVED)->count(),
            'maintenance' => $tables->where('status', RestaurantTable::STATUS_MAINTENANCE)->count(),
        ];
    }
}
