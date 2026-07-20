<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KdsStation extends Model
{
    protected $fillable = ['name', 'display_name', 'type', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(KdsOrder::class, 'kds_station_id');
    }
}
