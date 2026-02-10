<?php

namespace Modules\TableManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\TableManagement\app\Models\RestaurantTable;
use Modules\TableManagement\app\Services\TableService;

class TableController extends Controller
{
    public function __construct(private TableService $service)
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        checkAdminHasPermissionAndThrowException('table.view');
        $tables = $this->service->all()->paginate(20);
        $stats = $this->service->getTableStats();
        $floors = $this->service->getFloors();
        $sections = $this->service->getSections();

        return view('tablemanagement::tables.index', compact('tables', 'stats', 'floors', 'sections'));
    }

    public function create()
    {
        checkAdminHasPermissionAndThrowException('table.create');
        $floors = $this->service->getFloors();
        $sections = $this->service->getSections();
        $shapes = RestaurantTable::SHAPES;

        return view('tablemanagement::tables.create', compact('floors', 'sections', 'shapes'));
    }

    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('table.create');
        $request->validate([
            'name' => 'required|string|max:255',
            'table_number' => 'nullable|string|max:50|unique:restaurant_tables,table_number',
            'capacity' => 'required|integer|min:1|max:50',
            'floor' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'shape' => 'nullable|in:square,round,rectangle',
        ]);

        try {
            $this->service->store($request);
            return redirect()->route('admin.tables.index')
                ->with('success', 'Table created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating table: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('table.view');
        $table = $this->service->find($id);

        if (!$table) {
            return redirect()->route('admin.tables.index')->with('error', 'Table not found.');
        }

        return view('tablemanagement::tables.show', compact('table'));
    }

    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('table.edit');
        $table = $this->service->find($id);

        if (!$table) {
            return redirect()->route('admin.tables.index')->with('error', 'Table not found.');
        }

        $floors = $this->service->getFloors();
        $sections = $this->service->getSections();
        $shapes = RestaurantTable::SHAPES;

        return view('tablemanagement::tables.edit', compact('table', 'floors', 'sections', 'shapes'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('table.edit');
        $request->validate([
            'name' => 'required|string|max:255',
            'table_number' => 'required|string|max:50|unique:restaurant_tables,table_number,' . $id,
            'capacity' => 'required|integer|min:1|max:50',
            'floor' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'shape' => 'nullable|in:square,round,rectangle',
        ]);

        try {
            $this->service->update($request, $id);
            return redirect()->route('admin.tables.index')
                ->with('success', 'Table updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating table: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('table.delete');
        try {
            $this->service->destroy($id);
            return redirect()->route('admin.tables.index')
                ->with('success', 'Table deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        checkAdminHasPermissionAndThrowException('table.status');
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        try {
            $table = $this->service->updateStatus($id, $request->status);
            return response()->json([
                'success' => true,
                'message' => 'Table status updated.',
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function layout()
    {
        checkAdminHasPermissionAndThrowException('table.view');
        $tablesGrouped = $this->service->getActiveTablesGroupedByFloor();
        $stats = $this->service->getTableStats();
        $floors = $this->service->getFloors();

        return view('tablemanagement::tables.layout', compact('tablesGrouped', 'stats', 'floors'));
    }

    public function updatePositions(Request $request): JsonResponse
    {
        checkAdminHasPermissionAndThrowException('table.edit');
        $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:restaurant_tables,id',
            'positions.*.x' => 'required|integer',
            'positions.*.y' => 'required|integer',
        ]);

        try {
            $this->service->updatePositions($request->positions);
            return response()->json(['success' => true, 'message' => 'Positions updated.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function getAvailable(): JsonResponse
    {
        checkAdminHasPermissionAndThrowException('table.view');
        $tables = $this->service->getAvailableTables();
        return response()->json(['success' => true, 'tables' => $tables]);
    }

    public function releaseTable($id): JsonResponse
    {
        checkAdminHasPermissionAndThrowException('table.status');
        try {
            $table = RestaurantTable::findOrFail($id);
            $table->release();
            return response()->json(['success' => true, 'message' => 'Table released successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
