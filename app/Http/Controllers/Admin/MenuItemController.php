<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            MenuItem::with('category'),
            ['name', 'slug'],
            ['is_active' => ['1', '0'], 'channel_visibility' => ['both', 'web', 'pos']],
            'name',
            'asc'
        );

        return view('admin.menu.items.index', $result + ['items' => $result['items']]);
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.menu.items.form', ['item' => null, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:menu_items,slug',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'channel_visibility' => 'required|in:both,web,pos',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_taxable' => 'boolean',
        ]);

        MenuItem::create($validated);

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $item)
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.menu.items.form', compact('item', 'categories'));
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:menu_items,slug,'.$item->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'channel_visibility' => 'required|in:both,web,pos',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_taxable' => 'boolean',
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
        $item->load('variants');

        return view('admin.menu.items.variants', compact('item'));
    }

    public function storeVariant(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price_adjustment' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $item->variants()->create($validated);

        return back()->with('success', 'Variant added.');
    }
}
