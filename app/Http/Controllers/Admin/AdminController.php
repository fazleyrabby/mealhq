<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPromotion;
use App\Models\ContactInquiry;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;
use App\Services\ReportService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::whereIn('status', ['pending', 'confirmed'])->count(),
            'menu_items' => MenuItem::count(),
            'low_stock' => Ingredient::lowStock()->count(),
            'unread_inquiries' => ContactInquiry::unread()->count(),
            'active_promotions' => CmsPromotion::active()->count(),
        ];

        $report = app(ReportService::class);
        $recentOrders = Order::latest()->take(10)->get();
        $salesByDay = $report->salesByDay(7);

        return view('admin.dashboard', compact('stats', 'recentOrders', 'salesByDay'));
    }
}
