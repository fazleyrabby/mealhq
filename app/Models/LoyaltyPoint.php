<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    protected $fillable = ['customer_id', 'points', 'action', 'description'];

    protected function casts(): array
    {
        return ['points' => 'integer'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public static function addPoints(int $customerId, int $points, string $action, ?string $description = null): self
    {
        return static::create([
            'customer_id' => $customerId,
            'points' => $points,
            'action' => $action,
            'description' => $description,
        ]);
    }

    public static function getBalance(int $customerId): int
    {
        return static::where('customer_id', $customerId)->sum('points');
    }
}
