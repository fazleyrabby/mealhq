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
            ['status' => ['open', 'closed', 'pending_review']],
            'created_at',
            'desc'
        );

        return view('admin.operations.drawers.index', $result + ['drawers' => $result['items']]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        PosDrawer::create([
            'name' => $validated['name'],
            'status' => 'open',
            'opening_balance' => $validated['opening_balance'],
            'opened_by' => auth()->id(),
            'opened_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.operations.drawers.index')->with('success', 'Drawer opened.');
    }

    public function close(PosDrawer $posDrawer)
    {
        if ($posDrawer->status !== 'open') {
            return back()->with('error', 'Drawer is not open.');
        }

        $posDrawer->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Drawer closed.');
    }
}
