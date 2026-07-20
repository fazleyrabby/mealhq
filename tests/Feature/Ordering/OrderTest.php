<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('order can be created', function () {
    $customer = Customer::factory()->create();
    $order = Order::create([
        'order_number' => 'ORD-001',
        'source' => 'web',
        'type' => 'delivery',
        'customer_id' => $customer->id,
        'subtotal' => 25.00,
        'total_amount' => 27.50,
        'ordered_at' => now(),
    ]);

    expect($order->order_number)->toBe('ORD-001');
    expect($order->customer->id)->toBe($customer->id);
});

test('order with items can be created', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id, 'base_price' => 12.99]);

    $order = Order::create([
        'order_number' => 'ORD-002',
        'source' => 'pos',
        'type' => 'dine_in',
        'ordered_at' => now(),
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'menu_item_id' => $item->id,
        'item_name' => $item->name,
        'unit_price' => 12.99,
        'quantity' => 2,
        'subtotal' => 25.98,
    ]);

    expect($order->items)->toHaveCount(1);
    expect($orderItem->quantity)->toBe(2);
});

test('order status scopes work', function () {
    Order::create(['order_number' => 'ORD-003', 'source' => 'web', 'status' => 'pending', 'ordered_at' => now()]);
    Order::create(['order_number' => 'ORD-004', 'source' => 'web', 'status' => 'completed', 'ordered_at' => now()]);

    expect(Order::byStatus('pending')->count())->toBe(1);
    expect(Order::count())->toBe(2);
});

test('order items cascade on order delete', function () {
    $category = Category::factory()->create();
    $item = MenuItem::factory()->create(['category_id' => $category->id]);

    $order = Order::create(['order_number' => 'ORD-005', 'source' => 'web', 'ordered_at' => now()]);
    OrderItem::create(['order_id' => $order->id, 'menu_item_id' => $item->id, 'item_name' => 'Item', 'unit_price' => 10, 'subtotal' => 10]);

    $order->delete();

    expect(OrderItem::count())->toBe(0);
});

test('order can have customer', function () {
    $customer = Customer::factory()->create(['name' => 'John Doe']);
    $order = Order::create([
        'order_number' => 'ORD-006',
        'source' => 'web',
        'customer_id' => $customer->id,
        'ordered_at' => now(),
    ]);

    expect($order->customer->name)->toBe('John Doe');
});
