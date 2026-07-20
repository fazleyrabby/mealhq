<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModifierGroup;
use Illuminate\Http\Request;

class ModifierGroupController extends Controller
{
    public function index()
    {
        $groups = ModifierGroup::with('items')->orderBy('sort_order')->paginate(20);

        return view('admin.menu.modifiers.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.menu.modifiers.form', ['group' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:select_one,select_multiple,required_one,required_multiple',
            'max_selections' => 'nullable|integer|min:1',
            'min_selections' => 'nullable|integer|min:0',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        ModifierGroup::create($validated);

        return redirect()->route('admin.menu.modifiers.index')->with('success', 'Modifier group created.');
    }

    public function edit(ModifierGroup $modifierGroup)
    {
        return view('admin.menu.modifiers.form', ['group' => $modifierGroup]);
    }

    public function update(Request $request, ModifierGroup $modifierGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:select_one,select_multiple,required_one,required_multiple',
            'max_selections' => 'nullable|integer|min:1',
            'min_selections' => 'nullable|integer|min:0',
            'sort_order' => 'integer|min:0',
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
            'price_adjustment' => 'required|numeric',
            'cost_price' => 'nullable|numeric|min:0',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $modifierGroup->items()->create($validated);

        return redirect()->route('admin.menu.modifiers.edit', $modifierGroup)->with('success', 'Modifier item added.');
    }
}
