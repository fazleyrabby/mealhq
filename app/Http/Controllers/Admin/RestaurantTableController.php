<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\TableZone;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function indexTables()
    {
        $tables = RestaurantTable::with('zone')->orderBy('sort_order')->paginate(20);

        return view('admin.operations.tables.index', compact('tables'));
    }

    public function createTable()
    {
        $zones = TableZone::active()->get();

        return view('admin.operations.tables.form', ['table' => null, 'zones' => $zones]);
    }

    public function storeTable(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'nullable|exists:table_zones,id',
            'table_number' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,cleaning,maintenance',
            'sort_order' => 'integer|min:0',
        ]);

        RestaurantTable::create($validated);

        return redirect()->route('admin.operations.tables.index')->with('success', 'Table created.');
    }

    public function editTable(RestaurantTable $table)
    {
        $zones = TableZone::active()->get();

        return view('admin.operations.tables.form', compact('table', 'zones'));
    }

    public function updateTable(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'zone_id' => 'nullable|exists:table_zones,id',
            'table_number' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,cleaning,maintenance',
            'sort_order' => 'integer|min:0',
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
        $zones = TableZone::withCount('tables')->orderBy('sort_order')->paginate(20);

        return view('admin.operations.zones.index', compact('zones'));
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
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
