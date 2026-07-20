<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Unit;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::with('unit')->withTrashed()->latest()->paginate(20);

        return view('admin.inventory.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        $units = Unit::all();

        return view('admin.inventory.ingredients.form', ['ingredient' => null, 'units' => $units]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:50|unique:ingredients,sku',
            'unit_id' => 'required|exists:units,id',
            'cost_per_unit' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'min_stock_level' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.inventory.ingredients.index')->with('success', 'Ingredient created.');
    }

    public function edit(Ingredient $ingredient)
    {
        $units = Unit::all();

        return view('admin.inventory.ingredients.form', compact('ingredient', 'units'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:50|unique:ingredients,sku,'.$ingredient->id,
            'unit_id' => 'required|exists:units,id',
            'cost_per_unit' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'min_stock_level' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string',
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
