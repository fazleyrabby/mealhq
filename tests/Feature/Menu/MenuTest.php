<?php

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\ModifierGroup;
use App\Models\ModifierItem;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

// Categories

test('category can be created with auto-slug', function () {
    $category = Category::create(['name' => 'Beverages']);

    expect($category->slug)->toBe('beverages');
});

test('category can have parent-child relationship', function () {
    $parent = Category::factory()->create(['name' => 'Drinks']);
    $child = Category::factory()->create(['name' => 'Soft Drinks', 'parent_id' => $parent->id]);

    expect($parent->children)->toHaveCount(1);
    expect($child->parent->id)->toBe($parent->id);
});

test('category active scope works', function () {
    Category::factory()->create(['name' => 'Active', 'is_active' => true]);
    Category::factory()->create(['name' => 'Inactive', 'is_active' => false]);

    expect(Category::active()->count())->toBe(1);
});

// Menu Items

test('menu item can be created with category', function () {
    $category = Category::factory()->create();
    $item = MenuItem::create([
        'category_id' => $category->id,
        'name' => 'Classic Burger',
        'base_price' => 12.99,
        'description' => 'Delicious beef burger',
    ]);

    expect($item->slug)->toBe('classic-burger');
    expect($item->category->id)->toBe($category->id);
});

test('menu item scopes by channel visibility', function () {
    $category = Category::factory()->create();

    MenuItem::create(['category_id' => $category->id, 'name' => 'Web Item', 'channel_visibility' => 'all']);
    MenuItem::create(['category_id' => $category->id, 'name' => 'POS Only', 'channel_visibility' => 'pos_only']);

    expect(MenuItem::visibleOnChannel('web')->count())->toBe(1);
});

test('featured scope returns featured items', function () {
    $category = Category::factory()->create();

    MenuItem::factory()->create(['category_id' => $category->id, 'is_featured' => true]);
    MenuItem::factory()->create(['category_id' => $category->id, 'is_featured' => false]);

    expect(MenuItem::featured()->count())->toBe(1);
});

// Variants

test('menu item can have variants', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);
    $variant = MenuItemVariant::create([
        'menu_item_id' => $item->id,
        'name' => 'Large',
        'price_adjustment' => 2.00,
    ]);

    expect($item->variants)->toHaveCount(1);
    expect($variant->menuItem->id)->toBe($item->id);
});

// Modifier Groups & Items

test('modifier group can be created with items', function () {
    $group = ModifierGroup::create(['name' => 'Extra Toppings', 'type' => 'select_multiple']);
    $item = ModifierItem::create([
        'modifier_group_id' => $group->id,
        'name' => 'Extra Cheese',
        'price_adjustment' => 1.50,
    ]);

    expect($group->items)->toHaveCount(1);
    expect($item->group->id)->toBe($group->id);
});

test('modifier group can be attached to menu item', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);
    $group = ModifierGroup::create(['name' => 'Size Options']);

    $item->modifierGroups()->attach($group);

    expect($item->modifierGroups)->toHaveCount(1);
    expect($group->menuItems)->toHaveCount(1);
});

test('category with menu items cascade on force delete', function () {
    $category = Category::factory()->create();
    MenuItem::factory()->create(['category_id' => $category->id]);

    $category->forceDelete();

    expect(MenuItem::count())->toBe(0);
    expect(Category::count())->toBe(0);
});
