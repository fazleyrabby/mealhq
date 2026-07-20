<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class CmsFaqController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            CmsFaq::query(),
            ['question', 'answer'],
            ['is_active' => ['1', '0']],
            'sort_order',
            'asc'
        );

        return view('admin.cms.faqs.index', $result + ['faqs' => $result['items']]);
    }

    public function create()
    {
        return view('admin.cms.faqs.form', ['faq' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        CmsFaq::create($validated);

        return redirect()->route('admin.cms.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(CmsFaq $faq)
    {
        return view('admin.cms.faqs.form', compact('faq'));
    }

    public function update(Request $request, CmsFaq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.cms.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(CmsFaq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.cms.faqs.index')->with('success', 'FAQ deleted.');
    }
}
