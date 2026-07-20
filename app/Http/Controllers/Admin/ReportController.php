<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $report)
    {
        $range = (int) $request->get('range', 30);
        $range = in_array($range, [7, 30, 90], true) ? $range : 30;

        $sales = $report->salesByDay($range);
        $topItems = $report->topSellingItems(10, $range);
        $bySource = $report->ordersBySource($range);
        $inventoryValue = $report->inventoryValue();

        $totalRevenue = collect($sales)->sum('total_sales');
        $totalOrders = collect($sales)->sum('total_orders');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $maxSales = collect($sales)->max('total_sales') ?: 1;

        return view('admin.reports.index', compact(
            'range', 'sales', 'topItems', 'bySource', 'inventoryValue',
            'totalRevenue', 'totalOrders', 'avgOrderValue', 'maxSales'
        ));
    }
}
