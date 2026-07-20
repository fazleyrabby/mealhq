<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Services\AdminListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            MenuItem::with('category'),
            ['name', 'slug'],
            ['is_active' => ['1', '0'], 'channel_visibility' => ['all', 'web_only', 'pos_only', 'qr_only']],
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
            'special_price' => 'nullable|numeric|min:0',
            'channel_visibility' => 'required|in:all,web_only,pos_only,qr_only',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'show_on_home_offers' => 'boolean',
            'is_taxable' => 'boolean',
        ], $this->imageMessages());

        if ($request->hasFile('image')) {
            $validated['image_url'] = Storage::url($request->file('image')->store('menu-items', 'public'));
        }

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
            'special_price' => 'nullable|numeric|min:0',
            'channel_visibility' => 'required|in:all,web_only,pos_only,qr_only',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'show_on_home_offers' => 'boolean',
            'is_taxable' => 'boolean',
        ], $this->imageMessages());

        if ($request->hasFile('image')) {
            $this->deleteImage($item->image_url);
            $validated['image_url'] = Storage::url($request->file('image')->store('menu-items', 'public'));
        }

        $item->update($validated);

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $item)
    {
        $item->delete();

        return redirect()->route('admin.menu.items.index')->with('success', 'Menu item deleted.');
    }

    public function deleteImageAction(MenuItem $item)
    {
        $this->deleteImage($item->image_url);
        $item->update(['image_url' => null]);

        return redirect()->route('admin.menu.items.edit', $item)->with('success', 'Image deleted.');
    }

    public function toggleHomeOffer(Request $request, MenuItem $item)
    {
        $item->update([
            'show_on_home_offers' => $request->boolean('show_on_home_offers'),
        ]);

        return back()->with('success', 'Home offers visibility updated.');
    }

    private function imageMessages(): array
    {
        return [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a JPEG, PNG, JPG, or WEBP file.',
            'image.max' => 'The image may not be larger than 2 MB.',
        ];
    }

    private function deleteImage(?string $url): void
    {
        if ($url && str_starts_with($url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $url));
        }
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
