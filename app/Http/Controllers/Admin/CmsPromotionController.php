<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPromotion;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class CmsPromotionController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            CmsPromotion::query(),
            ['title', 'coupon_code'],
            ['is_active' => ['1', '0']],
            'created_at',
            'desc'
        );

        return view('admin.cms.promotions.index', $result + ['promotions' => $result['items']]);
    }

    public function create()
    {
        return view('admin.cms.promotions.form', ['promotion' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
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
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
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
