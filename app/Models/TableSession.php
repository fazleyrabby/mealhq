<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableSession extends Model
{
    protected $fillable = [
        'restaurant_table_id', 'customer_id', 'status',
        'guest_count', 'started_at', 'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function start(int $guestCount = 1): void
    {
        $this->update([
            'status' => 'active',
            'guest_count' => $guestCount,
            'started_at' => now(),
        ]);
        $this->restaurantTable->update(['status' => 'occupied']);
    }

    public function end(): void
    {
        $this->update([
            'status' => 'closed',
            'ended_at' => now(),
        ]);
        $this->restaurantTable->update(['status' => 'available']);
    }
}
