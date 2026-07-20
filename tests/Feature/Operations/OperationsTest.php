<?php

use App\Models\Customer;
use App\Models\KdsOrder;
use App\Models\KdsStation;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\PosDrawer;
use App\Models\RestaurantTable;
use App\Models\TableSession;
use App\Models\TableZone;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

// POS Drawers
test('pos drawer can be opened and closed', function () {
    $user = User::factory()->create();
    $drawer = PosDrawer::create(['name' => 'Main Register']);

    $drawer->open(500.00, $user->id);
    expect($drawer->fresh()->status)->toBe('open');

    $drawer->close(750.00, $user->id);
    expect($drawer->fresh()->status)->toBe('closed');
    expect((float) $drawer->fresh()->closing_balance)->toBe(750.0);
});

// KDS
test('kds station can create and bump orders', function () {
    $station = KdsStation::create(['name' => 'Main Kitchen', 'type' => 'kitchen']);
    $order = Order::create(['order_number' => 'KDS-001', 'source' => 'pos', 'ordered_at' => now()]);

    $kdsOrder = KdsOrder::create([
        'order_id' => $order->id,
        'kds_station_id' => $station->id,
    ]);

    expect($kdsOrder->status)->toBe('pending');

    $kdsOrder->bump();
    expect($kdsOrder->fresh()->status)->toBe('bumped');
    expect($kdsOrder->station->name)->toBe('Main Kitchen');
});

// Tables
test('table zone can have tables', function () {
    $zone = TableZone::create(['name' => 'Patio', 'color' => 'green']);
    $table = RestaurantTable::create([
        'zone_id' => $zone->id,
        'table_number' => 'P1',
        'capacity' => 4,
    ]);

    expect($zone->tables)->toHaveCount(1);
    expect($table->zone->name)->toBe('Patio');
});

test('table session lifecycle', function () {
    $zone = TableZone::create(['name' => 'Main Hall']);
    $table = RestaurantTable::create([
        'zone_id' => $zone->id,
        'table_number' => 'T1',
        'capacity' => 2,
    ]);

    $session = TableSession::create([
        'restaurant_table_id' => $table->id,
        'guest_count' => 0,
    ]);

    $session->start(2);
    expect($table->fresh()->status)->toBe('occupied');
    expect($session->fresh()->status)->toBe('active');

    $session->end();
    expect($table->fresh()->status)->toBe('available');
    expect($session->fresh()->status)->toBe('closed');
});

// Loyalty
test('loyalty points can be added and balance retrieved', function () {
    $customer = Customer::factory()->create();

    LoyaltyPoint::addPoints($customer->id, 50, 'order', 'Order #123');
    LoyaltyPoint::addPoints($customer->id, 30, 'signup', 'Welcome bonus');

    expect(LoyaltyPoint::getBalance($customer->id))->toBe(80);
    expect(LoyaltyPoint::count())->toBe(2);
});
