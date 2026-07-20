<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosDrawer;
use Illuminate\Http\Request;

class PosDrawerController extends Controller
{
    public function index()
    {
        $drawers = PosDrawer::with('openedBy')->latest()->paginate(20);

        return view('admin.operations.drawers.index', compact('drawers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        $drawer = PosDrawer::create(['name' => $validated['name']]);
        $drawer->open($validated['opening_balance'], auth()->id());

        return redirect()->route('admin.operations.drawers.index')->with('success', 'Drawer opened.');
    }

    public function close(Request $request, PosDrawer $posDrawer)
    {
        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
        ]);

        $posDrawer->close($validated['closing_balance'], auth()->id());

        return redirect()->route('admin.operations.drawers.index')->with('success', 'Drawer closed.');
    }
}
