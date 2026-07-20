<?php

use App\Models\BusinessHour;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Services\SettingsService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('settings can be stored and retrieved', function () {
    Setting::set('company_name', 'Test Restaurant');

    expect(Setting::get('company_name'))->toBe('Test Restaurant');
});

test('settings return default value when not found', function () {
    expect(Setting::get('nonexistent', 'fallback'))->toBe('fallback');
});

test('settings can be grouped', function () {
    Setting::set('key_one', 'value1');
    Setting::set('key_two', 'value2');

    Setting::where('key', 'key_one')->update(['group' => 'test']);
    Setting::where('key', 'key_two')->update(['group' => 'test']);

    $group = Setting::getGroup('test');
    expect($group['key_one'])->toBe('value1');
    expect($group['key_two'])->toBe('value2');
});

test('default settings exist after migration', function () {
    expect(Setting::get('company_name'))->toBe('My Restaurant');
    expect(Setting::get('company_email'))->toBe('info@restaurant.test');
    expect(Setting::get('min_order_amount'))->toBe('0');
});

test('settings service can retrieve values', function () {
    $service = app(SettingsService::class);

    expect($service->get('company_name'))->toBe('My Restaurant');
    expect($service->get('nonexistent', 'fallback'))->toBe('fallback');
});

test('tax rate can be created and retrieved', function () {
    $tax = TaxRate::create([
        'name' => 'Sales Tax',
        'rate' => 8.25,
        'type' => 'percentage',
        'is_default' => false,
    ]);

    expect($tax->rate == 8.25)->toBeTrue();
});

test('default tax rate exists', function () {
    $default = TaxRate::getDefault();
    expect($default)->not->toBeNull();
    expect((string) $default->rate)->toBe('10.00');
});

test('business hours exist for all days', function () {
    $hours = BusinessHour::all();
    expect($hours)->toHaveCount(7);
});

test('business hours can determine if currently open', function () {
    $isOpen = BusinessHour::isOpenNow();
    expect($isOpen)->toBeBool();
});

test('business hours check for specific day and time', function () {
    $isOpen = BusinessHour::isOpenOn('monday', '12:00:00');
    expect($isOpen)->toBeTrue();

    $isClosed = BusinessHour::isOpenOn('monday', '23:00:00');
    expect($isClosed)->toBeFalse();
});

test('sunday is marked as closed by default', function () {
    $sunday = BusinessHour::where('day_of_week', 'sunday')->first();
    expect($sunday->is_closed)->toBeTrue();
});
