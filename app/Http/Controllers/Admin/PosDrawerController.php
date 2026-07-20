<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosDrawer;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class PosDrawerController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            PosDrawer::with('user'),
            [],
            ['is_open' => ['1', '0']],
            'created_at',
            'desc'
        );

        return view('admin.operations.drawers.index', $result + ['drawers' => $result['items']]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        PosDrawer::create([
            'user_id' => auth()->id(),
            'opening_balance' => $validated['opening_balance'],
            'notes' => $validated['notes'] ?? null,
            'opened_at' => now(),
            'is_open' => true,
        ]);

        return redirect()->route('admin.operations.drawers.index')->with('success', 'Drawer opened.');
    }

    public function close(PosDrawer $posDrawer)
    {
        if (! $posDrawer->is_open) {
            return back()->with('error', 'Drawer is already closed.');
        }

        $posDrawer->update([
            'is_open' => false,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Drawer closed.');
    }
}
