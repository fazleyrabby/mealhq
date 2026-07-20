<?php

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Unit;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('unit can be created', function () {
    $unit = Unit::create(['name' => 'Kilogram', 'short_code' => 'kg']);

    expect($unit->short_code)->toBe('kg');
});

test('ingredient can be created with unit', function () {
    $unit = Unit::create(['name' => 'Liter', 'short_code' => 'L']);
    $ingredient = Ingredient::create([
        'name' => 'Olive Oil',
        'unit_id' => $unit->id,
        'cost_per_unit' => 5.99,
        'stock_quantity' => 20,
        'min_stock_level' => 5,
    ]);

    expect($ingredient->unit->short_code)->toBe('L');
    expect($ingredient->isLowStock())->toBeFalse();
});

test('ingredient low stock detection', function () {
    $unit = Unit::create(['name' => 'Piece', 'short_code' => 'pc']);
    $ingredient = Ingredient::create([
        'name' => 'Tomato',
        'unit_id' => $unit->id,
        'cost_per_unit' => 0.50,
        'stock_quantity' => 3,
        'min_stock_level' => 10,
    ]);

    expect($ingredient->isLowStock())->toBeTrue();
    expect(Ingredient::lowStock()->count())->toBe(1);
});

test('recipe can be created for menu item', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);
    $recipe = Recipe::create([
        'menu_item_id' => $item->id,
        'name' => 'Burger Recipe',
        'instructions' => 'Grill patty, assemble burger',
    ]);

    expect($recipe->menuItem->id)->toBe($item->id);
});

test('recipe ingredients with cost calculation', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);
    $recipe = Recipe::create(['menu_item_id' => $item->id, 'name' => 'Salad Recipe']);

    $unit = Unit::create(['name' => 'Gram', 'short_code' => 'g']);
    $lettuce = Ingredient::create([
        'name' => 'Lettuce',
        'unit_id' => $unit->id,
        'cost_per_unit' => 0.02,
        'stock_quantity' => 1000,
        'min_stock_level' => 100,
    ]);

    RecipeIngredient::create([
        'recipe_id' => $recipe->id,
        'ingredient_id' => $lettuce->id,
        'quantity' => 50,
        'waste_percentage' => 10,
    ]);

    $recipe->calculateCost();

    // 50g * $0.02 = $1.00, +10% waste = $1.10
    expect((float) $recipe->fresh()->total_cost)->toEqual(1.10);
});

test('recipe cascade delete removes ingredients', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);
    $recipe = Recipe::create(['menu_item_id' => $item->id, 'name' => 'Recipe']);

    $unit = Unit::create(['name' => 'Piece', 'short_code' => 'pc']);
    $ing = Ingredient::create(['name' => 'Item', 'unit_id' => $unit->id, 'cost_per_unit' => 1]);
    RecipeIngredient::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ing->id, 'quantity' => 1]);

    $recipe->delete();

    expect(RecipeIngredient::count())->toBe(0);
});

test('ingredient soft delete works', function () {
    $unit = Unit::create(['name' => 'Piece', 'short_code' => 'pc']);
    $ingredient = Ingredient::create(['name' => 'Onion', 'unit_id' => $unit->id, 'cost_per_unit' => 0.30]);

    $ingredient->delete();

    expect(Ingredient::count())->toBe(0);
    expect(Ingredient::withTrashed()->count())->toBe(1);
});
