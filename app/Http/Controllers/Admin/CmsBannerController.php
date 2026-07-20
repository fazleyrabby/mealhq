<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsBanner;
use App\Services\AdminListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsBannerController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            CmsBanner::query(),
            ['title', 'subtitle'],
            ['is_active' => ['1', '0']],
            'sort_order',
            'asc'
        );

        return view('admin.cms.banners.index', $result + ['banners' => $result['items']]);
    }

    public function create()
    {
        return view('admin.cms.banners.form', ['banner' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $validated['image_url'] = Storage::url($request->file('image')->store('banners', 'public'));
        }

        CmsBanner::create($validated);

        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner created.');
    }

    public function edit(CmsBanner $banner)
    {
        return view('admin.cms.banners.form', compact('banner'));
    }

    public function update(Request $request, CmsBanner $banner)
    {
        $validated = $this->validateBanner($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($banner->image_url);
            $validated['image_url'] = Storage::url($request->file('image')->store('banners', 'public'));
        }

        $banner->update($validated);

        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(CmsBanner $banner)
    {
        $this->deleteImage($banner->image_url);
        $banner->delete();

        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner deleted.');
    }

    public function deleteImageAction(CmsBanner $banner)
    {
        $this->deleteImage($banner->image_url);
        $banner->update(['image_url' => null]);

        return redirect()->route('admin.cms.banners.edit', $banner)->with('success', 'Image deleted.');
    }

    private function validateBanner(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:50',
            'cta_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a JPEG, PNG, JPG, or WEBP file.',
            'image.max' => 'The image may not be larger than 4 MB.',
        ]);
    }

    private function deleteImage(?string $url): void
    {
        if ($url && str_starts_with($url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $url));
        }
    }
}
