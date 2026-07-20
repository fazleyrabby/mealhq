<?php

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('reports page loads for authenticated admin', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get(route('admin.reports.index'));

    $response->assertOk();
    $response->assertSee('Reports');
    $response->assertSee('Top Selling Items');
});

test('reports page is protected from guests', function () {
    $response = $this->get(route('admin.reports.index'));

    $response->assertRedirect('/admin/login');
});

test('reports page reflects completed orders in the period', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $menuItem = MenuItem::factory()->create();

    $order = Order::create([
        'order_number' => 'RPT-010',
        'source' => 'pos',
        'status' => 'completed',
        'total_amount' => 120.00,
        'ordered_at' => now(),
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'menu_item_id' => $menuItem->id,
        'item_name' => 'Burger',
        'unit_price' => 120.00,
        'quantity' => 2,
        'subtotal' => 240.00,
    ]);

    $response = $this->actingAs($user)->get(route('admin.reports.index', ['range' => 30]));

    $response->assertOk();
    $response->assertSee('$120.00'); // revenue KPI
    $response->assertSee('Burger');  // top selling item
});
