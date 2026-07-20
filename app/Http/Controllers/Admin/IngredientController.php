<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Unit;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            Ingredient::with('unit'),
            ['name'],
            ['is_active' => ['1', '0']],
            'name',
            'asc'
        );

        return view('admin.inventory.ingredients.index', $result + ['ingredients' => $result['items']]);
    }

    public function create()
    {
        $units = Unit::orderBy('name')->get();

        return view('admin.inventory.ingredients.form', ['ingredient' => null, 'units' => $units]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'unit_id' => 'required|exists:units,id',
            'stock_quantity' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.inventory.ingredients.index')->with('success', 'Ingredient created.');
    }

    public function edit(Ingredient $ingredient)
    {
        $units = Unit::orderBy('name')->get();

        return view('admin.inventory.ingredients.form', compact('ingredient', 'units'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'unit_id' => 'required|exists:units,id',
            'stock_quantity' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $ingredient->update($validated);

        return redirect()->route('admin.inventory.ingredients.index')->with('success', 'Ingredient updated.');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return redirect()->route('admin.inventory.ingredients.index')->with('success', 'Ingredient deleted.');
    }
}
