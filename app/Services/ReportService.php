<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function salesByDay(int $days = 30): array
    {
        return Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_sales')
        )
            ->where('created_at', '>=', now()->subDays($days))
            ->whereIn('status', ['completed', 'served'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function topSellingItems(int $limit = 10, ?int $days = null): array
    {
        return OrderItem::select(
            'menu_item_id',
            'item_name',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', function ($q) use ($days) {
                $q->whereIn('status', ['completed', 'served']);
                if ($days) {
                    $q->where('created_at', '>=', now()->subDays($days));
                }
            })
            ->groupBy('menu_item_id', 'item_name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function inventoryValue(): float
    {
        return (float) Ingredient::select(DB::raw('SUM(cost_per_unit * stock_quantity) as total_value'))
            ->value('total_value') ?? 0;
    }

    public function ordersBySource(?int $days = null): array
    {
        return Order::select(
            'source',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereIn('status', ['completed', 'served'])
            ->when($days, fn ($q) => $q->where('created_at', '>=', now()->subDays($days)))
            ->groupBy('source')
            ->get()
            ->toArray();
    }
}
