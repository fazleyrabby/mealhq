<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('admin login page loads', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
    $response->assertSee('Sign in');
});

test('admin dashboard loads for authenticated user', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
});

test('admin dashboard redirects guest users to admin login', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/admin/login');
});

test('admin settings page loads', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get('/admin/settings');

    $response->assertStatus(200);
    $response->assertSee('Company Profile');
});

test('admin cms pages index loads', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get('/admin/cms/pages');

    $response->assertStatus(200);
    $response->assertSee('Pages');
});

test('admin menu categories index loads', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get('/admin/menu/categories');

    $response->assertStatus(200);
    $response->assertSee('Categories');
});

test('admin orders index loads', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get('/admin/orders');

    $response->assertStatus(200);
    $response->assertSee('Orders');
});

test('admin redirects to dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('Owner');

    $response = $this->actingAs($user)->get('/admin');

    $response->assertRedirect('/admin/dashboard');
});
