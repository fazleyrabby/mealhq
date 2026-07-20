<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPromotion;
use Illuminate\Http\Request;

class CmsPromotionController extends Controller
{
    public function index()
    {
        $promotions = CmsPromotion::withTrashed()->latest()->paginate(20);

        return view('admin.cms.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.cms.promotions.form', ['promotion' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'promo_code' => 'nullable|string|max:30',
            'discount_type' => 'nullable|in:percentage,fixed,bogo,free_delivery',
            'discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'cta_url' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:50',
        ]);

        CmsPromotion::create($validated);

        return redirect()->route('admin.cms.promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(CmsPromotion $promotion)
    {
        return view('admin.cms.promotions.form', compact('promotion'));
    }

    public function update(Request $request, CmsPromotion $promotion)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'promo_code' => 'nullable|string|max:30',
            'discount_type' => 'nullable|in:percentage,fixed,bogo,free_delivery',
            'discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'cta_url' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:50',
        ]);

        $promotion->update($validated);

        return redirect()->route('admin.cms.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(CmsPromotion $promotion)
    {
        $promotion->delete();

        return redirect()->route('admin.cms.promotions.index')->with('success', 'Promotion deleted.');
    }
}
