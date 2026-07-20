<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\TableZone;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function indexTables(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            RestaurantTable::with('zone'),
            ['name'],
            [
                'status' => ['available', 'occupied', 'reserved', 'cleaning', 'maintenance'],
                'is_active' => ['1', '0'],
            ],
            'name',
            'asc'
        );

        return view('admin.operations.tables.index', $result + ['tables' => $result['items']]);
    }

    public function createTable()
    {
        $zones = TableZone::orderBy('name')->get();

        return view('admin.operations.tables.form', ['table' => null, 'zones' => $zones]);
    }

    public function storeTable(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'zone_id' => 'nullable|exists:table_zones,id',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        RestaurantTable::create($validated);

        return redirect()->route('admin.operations.tables.index')->with('success', 'Table created.');
    }

    public function editTable(RestaurantTable $table)
    {
        $zones = TableZone::orderBy('name')->get();

        return view('admin.operations.tables.form', compact('table', 'zones'));
    }

    public function updateTable(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'zone_id' => 'nullable|exists:table_zones,id',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $table->update($validated);

        return redirect()->route('admin.operations.tables.index')->with('success', 'Table updated.');
    }

    public function destroyTable(RestaurantTable $table)
    {
        $table->delete();

        return redirect()->route('admin.operations.tables.index')->with('success', 'Table deleted.');
    }

    public function indexZones()
    {
        $zones = TableZone::with('tables')->orderBy('name')->get();

        return view('admin.operations.zones.index', compact('zones'));
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:table_zones,name',
            'description' => 'nullable|string|max:255',
        ]);

        TableZone::create($validated);

        return redirect()->route('admin.operations.zones.index')->with('success', 'Zone created.');
    }

    public function destroyZone(TableZone $zone)
    {
        $zone->delete();

        return redirect()->route('admin.operations.zones.index')->with('success', 'Zone deleted.');
    }
}
