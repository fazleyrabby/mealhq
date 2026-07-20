<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModifierGroup;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class ModifierGroupController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            ModifierGroup::with('items'),
            ['name'],
            ['is_active' => ['1', '0']],
            'name',
            'asc'
        );

        return view('admin.menu.modifiers.index', $result + ['groups' => $result['items']]);
    }

    public function create()
    {
        return view('admin.menu.modifiers.form', ['group' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:single,multiple',
            'min_selection' => 'integer|min:0',
            'max_selection' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        ModifierGroup::create($validated);

        return redirect()->route('admin.menu.modifiers.index')->with('success', 'Modifier group created.');
    }

    public function edit(ModifierGroup $modifierGroup)
    {
        $modifierGroup->load('items');

        return view('admin.menu.modifiers.form', ['group' => $modifierGroup]);
    }

    public function update(Request $request, ModifierGroup $modifierGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:single,multiple',
            'min_selection' => 'integer|min:0',
            'max_selection' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        $modifierGroup->update($validated);

        return redirect()->route('admin.menu.modifiers.index')->with('success', 'Modifier group updated.');
    }

    public function destroy(ModifierGroup $modifierGroup)
    {
        $modifierGroup->delete();

        return redirect()->route('admin.menu.modifiers.index')->with('success', 'Modifier group deleted.');
    }

    public function storeItem(Request $request, ModifierGroup $modifierGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $modifierGroup->items()->create($validated);

        return back()->with('success', 'Modifier item added.');
    }
}
