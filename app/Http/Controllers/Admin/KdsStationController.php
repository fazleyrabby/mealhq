<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KdsStation;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class KdsStationController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            KdsStation::query(),
            ['name', 'description'],
            ['is_active' => ['1', '0']],
            'name',
            'asc'
        );

        return view('admin.operations.kds.index', $result + ['stations' => $result['items']]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:kds_stations,name',
            'description' => 'nullable|string|max:255',
        ]);

        KdsStation::create($validated);

        return redirect()->route('admin.operations.kds.index')->with('success', 'KDS station created.');
    }

    public function destroy(KdsStation $kdsStation)
    {
        $kdsStation->delete();

        return redirect()->route('admin.operations.kds.index')->with('success', 'KDS station deleted.');
    }
}
