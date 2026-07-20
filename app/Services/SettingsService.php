<?php

namespace App\Services;

use App\Models\BusinessHour;
use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
    }

    public function getGroup(string $group): array
    {
        return Setting::getGroup($group);
    }

    public function getAll(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return Setting::all()->groupBy('group')->toArray();
        });
    }

    public function flushCache(): void
    {
        Cache::forget('settings.all');
    }

    public function getDefaultTaxRate(): ?TaxRate
    {
        return TaxRate::getDefault();
    }

    public function getBusinessHours(): array
    {
        return BusinessHour::orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get()
            ->all();
    }

    public function isOpenNow(): bool
    {
        return BusinessHour::isOpenNow();
    }
}
