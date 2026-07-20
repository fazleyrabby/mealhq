<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->withTrashed()->orderBy('sort_order')->paginate(20);

        return view('admin.menu.items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::active()->get();

        return view('admin.menu.items.form', ['item' => null, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:menu_items,slug',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'channel_visibility' => 'required|in:all,web_only,pos_only,qr_only',
            'unit_type' => 'required|in:each,per_plate,per_kg,per_liter,per_dozen',
            'sort_order' => 'integer|min:0',
        ]);

        MenuItem::create($validated);

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $item)
    {
        $categories = Category::active()->get();

        return view('admin.menu.items.form', ['item' => $item, 'categories' => $categories]);
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:menu_items,slug,'.$item->id,
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'channel_visibility' => 'required|in:all,web_only,pos_only,qr_only',
            'unit_type' => 'required|in:each,per_plate,per_kg,per_liter,per_dozen',
            'sort_order' => 'integer|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $item)
    {
        $item->delete();

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item deleted.');
    }

    public function variants(MenuItem $item)
    {
        return view('admin.menu.items.variants', compact('item'));
    }

    public function storeVariant(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price_adjustment' => 'required|numeric',
            'sku' => 'nullable|string|max:50|unique:menu_item_variants,sku',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $item->variants()->create($validated);

        return redirect()->route('admin.menu.items.variants', $item)->with('success', 'Variant added.');
    }
}
