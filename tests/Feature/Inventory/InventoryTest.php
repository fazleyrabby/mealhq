<?php

use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\Unit;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('supplier can be created', function () {
    $supplier = Supplier::create([
        'name' => 'Fresh Foods Co.',
        'email' => 'orders@freshfoods.test',
        'phone' => '+1234567890',
    ]);

    expect($supplier->name)->toBe('Fresh Foods Co.');
});

test('purchase order can be created with items', function () {
    $supplier = Supplier::create(['name' => 'Supplier A']);
    $unit = Unit::create(['name' => 'Kg', 'short_code' => 'kg']);
    $ingredient = Ingredient::create([
        'name' => 'Flour',
        'unit_id' => $unit->id,
        'cost_per_unit' => 2.00,
        'stock_quantity' => 50,
        'min_stock_level' => 10,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-001',
        'supplier_id' => $supplier->id,
        'status' => 'draft',
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'ingredient_id' => $ingredient->id,
        'quantity' => 10,
        'unit_cost' => 2.00,
        'total_cost' => 20.00,
    ]);

    expect($po->items)->toHaveCount(1);
    expect($po->supplier->name)->toBe('Supplier A');
});

test('purchase order receiving updates stock and status', function () {
    $supplier = Supplier::create(['name' => 'Supplier B']);
    $unit = Unit::create(['name' => 'L', 'short_code' => 'L']);
    $ingredient = Ingredient::create([
        'name' => 'Oil',
        'unit_id' => $unit->id,
        'cost_per_unit' => 3.00,
        'stock_quantity' => 10,
        'min_stock_level' => 5,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-002',
        'supplier_id' => $supplier->id,
        'status' => 'ordered',
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'ingredient_id' => $ingredient->id,
        'quantity' => 20,
        'unit_cost' => 3.00,
        'total_cost' => 60.00,
    ]);

    $po->receiveItems([$ingredient->id => 20]);

    expect($po->fresh()->status)->toBe('received');
    expect((float) $ingredient->fresh()->stock_quantity)->toBe(30.0);
});

test('stock adjustment addition increases stock', function () {
    $unit = Unit::create(['name' => 'Kg', 'short_code' => 'kg']);
    $ingredient = Ingredient::create([
        'name' => 'Sugar',
        'unit_id' => $unit->id,
        'cost_per_unit' => 1.50,
        'stock_quantity' => 10,
        'min_stock_level' => 5,
    ]);

    StockAdjustment::adjustStock($ingredient->id, 'addition', 25, 'New shipment received');

    expect((float) $ingredient->fresh()->stock_quantity)->toBe(35.0);
});

test('stock adjustment removal decreases stock', function () {
    $unit = Unit::create(['name' => 'Kg', 'short_code' => 'kg']);
    $ingredient = Ingredient::create([
        'name' => 'Spice',
        'unit_id' => $unit->id,
        'cost_per_unit' => 5.00,
        'stock_quantity' => 20,
        'min_stock_level' => 5,
    ]);

    StockAdjustment::adjustStock($ingredient->id, 'removal', 5, 'Spoiled');

    expect((float) $ingredient->fresh()->stock_quantity)->toBe(15.0);
});
