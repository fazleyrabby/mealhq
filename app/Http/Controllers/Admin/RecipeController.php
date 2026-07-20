<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Recipe;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            Recipe::with('menuItem', 'ingredients'),
            ['name'],
            ['is_active' => ['1', '0']],
            'name',
            'asc'
        );

        return view('admin.inventory.recipes.index', $result + ['recipes' => $result['items']]);
    }

    public function create()
    {
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('admin.inventory.recipes.form', ['recipe' => null, 'menuItems' => $menuItems, 'ingredients' => $ingredients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'yield_amount' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $recipe = Recipe::create($validated);

        // Save recipe ingredients
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ri) {
                if (! empty($ri['ingredient_id']) && ! empty($ri['quantity'])) {
                    $recipe->ingredients()->create([
                        'ingredient_id' => $ri['ingredient_id'],
                        'quantity' => $ri['quantity'],
                        'cost' => $ri['cost'] ?? 0,
                        'waste_percentage' => $ri['waste_percentage'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.inventory.recipes.index')->with('success', 'Recipe created.');
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load('ingredients');
        $menuItems = MenuItem::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('admin.inventory.recipes.form', compact('recipe', 'menuItems', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'menu_item_id' => 'nullable|exists:menu_items,id',
            'yield_amount' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $recipe->update($validated);

        // Sync recipe ingredients
        $recipe->ingredients()->delete();
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ri) {
                if (! empty($ri['ingredient_id']) && ! empty($ri['quantity'])) {
                    $recipe->ingredients()->create([
                        'ingredient_id' => $ri['ingredient_id'],
                        'quantity' => $ri['quantity'],
                        'cost' => $ri['cost'] ?? 0,
                        'waste_percentage' => $ri['waste_percentage'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.inventory.recipes.index')->with('success', 'Recipe updated.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('admin.inventory.recipes.index')->with('success', 'Recipe deleted.');
    }
}
