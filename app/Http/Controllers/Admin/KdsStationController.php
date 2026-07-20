<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KdsStation;
use Illuminate\Http\Request;

class KdsStationController extends Controller
{
    public function index()
    {
        $stations = KdsStation::withCount('orders')->orderBy('sort_order')->paginate(20);

        return view('admin.operations.kds.index', compact('stations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'display_name' => 'nullable|string|max:100',
            'type' => 'required|in:kitchen,bar,grill,prep,expo',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
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
