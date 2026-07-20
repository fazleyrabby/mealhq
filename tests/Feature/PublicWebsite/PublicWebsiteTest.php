<?php

use App\Models\CmsPromotion;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('home page loads successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Welcome to');
    $response->assertSee('Our Menu');
});

test('menu page loads successfully', function () {
    $response = $this->get('/menu');

    $response->assertStatus(200);
    $response->assertSee('Our Menu');
});

test('about page loads successfully', function () {
    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertSee('About Us');
});

test('contact page loads successfully', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
    $response->assertSee('Contact Us');
});

test('contact form can be submitted', function () {
    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Question',
        'message' => 'Hello, I have a question about your menu.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('contact form validation fails with missing fields', function () {
    $response = $this->post('/contact', []);

    $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
});

test('promotions are shown on homepage', function () {
    CmsPromotion::create([
        'title' => 'Test Promotion',
        'subtitle' => 'Test subtitle',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'start_date' => now()->subDay(),
        'end_date' => now()->addMonth(),
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Test Promotion');
});

test('expired promotions are not shown on homepage', function () {
    CmsPromotion::create([
        'title' => 'Expired Promo',
        'subtitle' => 'Old promo',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'start_date' => now()->subMonths(2),
        'end_date' => now()->subMonth(),
    ]);

    $response = $this->get('/');

    $response->assertDontSee('Expired Promo');
});
