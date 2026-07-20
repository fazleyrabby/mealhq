<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KdsOrder extends Model
{
    protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = [
        'order_id', 'kds_station_id', 'status',
        'priority', 'prep_time_seconds', 'bumped_at',
    ];

    protected function casts(): array
    {
        return [
            'bumped_at' => 'datetime',
            'priority' => 'integer',
            'prep_time_seconds' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(KdsStation::class, 'kds_station_id');
    }

    public function bump(): void
    {
        $this->update(['status' => 'bumped', 'bumped_at' => now()]);
    }
}
