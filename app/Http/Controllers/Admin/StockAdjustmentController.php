<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockAdjustment;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            StockAdjustment::with('ingredient.unit', 'adjustedBy'),
            [],
            ['type' => ['addition', 'removal', 'correction']],
            'created_at',
            'desc'
        );

        return view('admin.inventory.adjustments.index', $result + ['adjustments' => $result['items']]);
    }

    public function create()
    {
        $ingredients = Ingredient::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('admin.inventory.adjustments.form', ['adjustment' => null, 'ingredients' => $ingredients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'type' => 'required|in:addition,removal,correction',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        $ingredient = Ingredient::findOrFail($validated['ingredient_id']);

        // Create the adjustment
        StockAdjustment::create([
            'ingredient_id' => $validated['ingredient_id'],
            'adjusted_by' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
        ]);

        // Update stock
        $ingredient->adjustStock($validated['type'], $validated['quantity']);

        return redirect()->route('admin.inventory.adjustments.index')->with('success', 'Stock adjustment recorded.');
    }
}
