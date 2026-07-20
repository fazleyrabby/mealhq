<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            Category::with('parent')->withTrashed(),
            ['name', 'slug'],
            ['is_active' => ['1', '0']],
            'sort_order',
            'asc'
        );

        return view('admin.menu.categories.index', $result + ['categories' => $result['items']]);
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();

        return view('admin.menu.categories.form', ['category' => null, 'parents' => $parents]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'is_visible_on_web' => 'boolean',
            'is_visible_on_pos' => 'boolean',
        ]);

        Category::create($validated);

        return redirect()->route('admin.menu.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();

        return view('admin.menu.categories.form', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'is_visible_on_web' => 'boolean',
            'is_visible_on_pos' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.menu.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.menu.categories.index')->with('success', 'Category deleted.');
    }
}
