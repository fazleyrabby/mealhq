<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    protected $fillable = [
        'zone_id', 'table_number', 'capacity',
        'qr_code', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['capacity' => 'integer', 'sort_order' => 'integer'];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TableZone::class, 'zone_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TableSession::class, 'restaurant_table_id');
    }
}
