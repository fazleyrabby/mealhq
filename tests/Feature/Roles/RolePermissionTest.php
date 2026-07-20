<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('owner has all permissions', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    expect($user->can('cms.pages.view'))->toBeTrue();
    expect($user->can('cms.pages.create'))->toBeTrue();
    expect($user->can('cms.pages.delete'))->toBeTrue();
    expect($user->can('orders.view'))->toBeTrue();
    expect($user->can('orders.cancel'))->toBeTrue();
    expect($user->can('menu.manage'))->toBeTrue();
    expect($user->can('inventory.manage'))->toBeTrue();
    expect($user->can('kds.view'))->toBeTrue();
    expect($user->can('pos.open'))->toBeTrue();
    expect($user->can('reports.view'))->toBeTrue();
    expect($user->can('settings.manage'))->toBeTrue();
    expect($user->can('customers.manage'))->toBeTrue();
});

test('cashier has limited permissions', function () {
    $user = User::factory()->create();
    $user->assignRole('Cashier');

    // Should have
    expect($user->can('orders.view'))->toBeTrue();
    expect($user->can('orders.create'))->toBeTrue();
    expect($user->can('menu.view'))->toBeTrue();
    expect($user->can('pos.open'))->toBeTrue();
    expect($user->can('pos.process'))->toBeTrue();

    // Should NOT have
    expect($user->can('cms.pages.create'))->toBeFalse();
    expect($user->can('cms.pages.update'))->toBeFalse();
    expect($user->can('menu.manage'))->toBeFalse();
    expect($user->can('inventory.manage'))->toBeFalse();
    expect($user->can('reports.view'))->toBeFalse();
    expect($user->can('settings.manage'))->toBeFalse();
});

test('waiter cannot access admin features', function () {
    $user = User::factory()->create();
    $user->assignRole('Waiter');

    // Should have
    expect($user->can('tables.view'))->toBeTrue();
    expect($user->can('tables.session.manage'))->toBeTrue();
    expect($user->can('orders.create'))->toBeTrue();

    // Should NOT have
    expect($user->can('cms.promotions.manage'))->toBeFalse();
    expect($user->can('pos.open'))->toBeFalse();
    expect($user->can('settings.view'))->toBeFalse();
    expect($user->can('inventory.manage'))->toBeFalse();
});

test('chef can only access kds and menu', function () {
    $user = User::factory()->create();
    $user->assignRole('Chef');

    expect($user->can('kds.view'))->toBeTrue();
    expect($user->can('kds.bump'))->toBeTrue();
    expect($user->can('menu.view'))->toBeTrue();

    expect($user->can('orders.create'))->toBeFalse();
    expect($user->can('pos.open'))->toBeFalse();
    expect($user->can('cms.pages.view'))->toBeFalse();
    expect($user->can('inventory.manage'))->toBeFalse();
});

test('kitchen staff has minimal kds access', function () {
    $user = User::factory()->create();
    $user->assignRole('Kitchen Staff');

    expect($user->can('kds.view'))->toBeTrue();
    expect($user->can('kds.bump'))->toBeTrue();

    expect($user->can('kds.recall'))->toBeFalse();
    expect($user->can('kds.stations.manage'))->toBeFalse();
    expect($user->can('orders.view'))->toBeFalse();
    expect($user->can('menu.view'))->toBeFalse();
});

test('inventory manager has inventory access', function () {
    $user = User::factory()->create();
    $user->assignRole('Inventory Manager');

    expect($user->can('inventory.view'))->toBeTrue();
    expect($user->can('inventory.manage'))->toBeTrue();
    expect($user->can('inventory.adjust'))->toBeTrue();
    expect($user->can('inventory.reports'))->toBeTrue();

    expect($user->can('cms.pages.view'))->toBeFalse();
    expect($user->can('pos.open'))->toBeFalse();
    expect($user->can('settings.manage'))->toBeFalse();
});

test('guest user is redirected to login', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('user without role has no permissions', function () {
    $user = User::factory()->create();

    expect($user->can('menu.view'))->toBeFalse();
    expect($user->can('orders.view'))->toBeFalse();
    expect($user->can('kds.view'))->toBeFalse();
});
