<?php

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Unit;
use App\Services\ReportService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('report service returns sales data', function () {
    $order = Order::create([
        'order_number' => 'RPT-001',
        'source' => 'web',
        'status' => 'completed',
        'total_amount' => 50.00,
        'ordered_at' => now(),
    ]);

    $report = app(ReportService::class);
    $sales = $report->salesByDay(7);

    expect($sales)->toHaveCount(1);
    expect($sales[0]['total_sales'] == 50)->toBeTrue();
});

test('report service returns top selling items', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);
    $order = Order::create([
        'order_number' => 'RPT-002',
        'source' => 'web',
        'status' => 'completed',
        'ordered_at' => now(),
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'menu_item_id' => $item->id,
        'item_name' => $item->name,
        'unit_price' => 10.00,
        'quantity' => 3,
        'subtotal' => 30.00,
    ]);

    $report = app(ReportService::class);
    $top = $report->topSellingItems(5);

    expect($top)->toHaveCount(1);
    expect($top[0]['total_quantity'] == 3)->toBeTrue();
});

test('report service calculates inventory value', function () {
    $unit = Unit::create(['name' => 'Kg', 'short_code' => 'kg']);
    Ingredient::create(['name' => 'Item A', 'unit_id' => $unit->id, 'cost_per_unit' => 2.00, 'stock_quantity' => 10, 'min_stock_level' => 5]);
    Ingredient::create(['name' => 'Item B', 'unit_id' => $unit->id, 'cost_per_unit' => 5.00, 'stock_quantity' => 20, 'min_stock_level' => 10]);

    $report = app(ReportService::class);
    $value = $report->inventoryValue();

    expect($value == 120)->toBeTrue();
});

test('report service returns orders by source', function () {
    Order::create(['order_number' => 'RPT-003', 'source' => 'web', 'status' => 'completed', 'ordered_at' => now()]);
    Order::create(['order_number' => 'RPT-004', 'source' => 'pos', 'status' => 'completed', 'ordered_at' => now()]);

    $report = app(ReportService::class);
    $sources = $report->ordersBySource();

    expect($sources)->toHaveCount(2);
});
