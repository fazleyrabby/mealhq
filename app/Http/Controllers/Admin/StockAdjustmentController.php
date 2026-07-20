<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with('ingredient', 'adjustedBy')->latest()->paginate(20);

        return view('admin.inventory.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $ingredients = Ingredient::active()->with('unit')->get();

        return view('admin.inventory.adjustments.form', ['adjustment' => null, 'ingredients' => $ingredients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'type' => 'required|in:addition,removal,correction,waste,return',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        StockAdjustment::adjustStock(
            $validated['ingredient_id'],
            $validated['type'],
            $validated['quantity'],
            $validated['reason'] ?? null,
            auth()->id()
        );

        return redirect()->route('admin.inventory.adjustments.index')->with('success', 'Stock adjustment recorded.');
    }
}
