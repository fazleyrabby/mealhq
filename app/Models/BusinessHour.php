<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $fillable = ['day_of_week', 'opening_time', 'closing_time', 'is_closed'];

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
        ];
    }

    public static function isOpenNow(): bool
    {
        $today = strtolower(Carbon::now()->format('l'));
        $hours = static::where('day_of_week', $today)->first();

        if (! $hours || $hours->is_closed) {
            return false;
        }

        $now = Carbon::now()->format('H:i:s');
        $open = $hours->opening_time;
        $close = $hours->closing_time;

        return $now >= $open && $now <= $close;
    }

    public static function isOpenOn(string $day, string $time): bool
    {
        $hours = static::where('day_of_week', strtolower($day))->first();

        if (! $hours || $hours->is_closed) {
            return false;
        }

        return $time >= $hours->opening_time && $time <= $hours->closing_time;
    }
}
