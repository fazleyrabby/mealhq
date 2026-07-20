<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('menuItem')->latest()->paginate(20);

        return view('admin.inventory.recipes.index', compact('recipes'));
    }

    public function create()
    {
        $menuItems = MenuItem::active()->get();
        $ingredients = Ingredient::active()->with('unit')->get();

        return view('admin.inventory.recipes.form', ['recipe' => null, 'menuItems' => $menuItems, 'ingredients' => $ingredients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id|unique:recipes,menu_item_id',
            'name' => 'required|string|max:150',
            'instructions' => 'nullable|string',
            'yield_quantity' => 'integer|min:1',
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $recipe = Recipe::create([
            'menu_item_id' => $validated['menu_item_id'],
            'name' => $validated['name'],
            'instructions' => $validated['instructions'],
            'yield_quantity' => $validated['yield_quantity'] ?? 1,
        ]);

        if (! empty($validated['ingredients'])) {
            foreach ($validated['ingredients'] as $ri) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ri['ingredient_id'],
                    'quantity' => $ri['quantity'],
                    'waste_percentage' => $ri['waste_percentage'] ?? 0,
                ]);
            }
            $recipe->calculateCost();
        }

        return redirect()->route('admin.inventory.recipes.index')->with('success', 'Recipe created.');
    }

    public function edit(Recipe $recipe)
    {
        $menuItems = MenuItem::active()->get();
        $ingredients = Ingredient::active()->with('unit')->get();

        return view('admin.inventory.recipes.form', compact('recipe', 'menuItems', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'instructions' => 'nullable|string',
            'yield_quantity' => 'integer|min:1',
        ]);

        $recipe->update($validated);

        return redirect()->route('admin.inventory.recipes.index')->with('success', 'Recipe updated.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('admin.inventory.recipes.index')->with('success', 'Recipe deleted.');
    }
}
