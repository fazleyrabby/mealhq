<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            CmsPage::with('sections'),
            ['title', 'slug'],
            ['is_active' => ['1', '0']],
            'created_at',
            'desc'
        );

        $pages = $result['items'];
        $sortField = $result['sortField'];
        $sortDir = $result['sortDir'];
        $search = $result['search'];
        $appliedFilters = $result['appliedFilters'];
        $perPage = $result['perPage'];

        return view('admin.cms.pages.index', compact(
            'pages', 'sortField', 'sortDir', 'search', 'appliedFilters', 'perPage'
        ));
    }

    public function create()
    {
        return view('admin.cms.pages.form', ['page' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:cms_pages,slug',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:255',
        ]);

        CmsPage::create($validated);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page created.');
    }

    public function edit(CmsPage $page)
    {
        return view('admin.cms.pages.form', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:cms_pages,slug,'.$page->id,
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $page->update($validated);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page deleted.');
    }
}
