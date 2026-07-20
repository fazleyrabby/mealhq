<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'unit_id',
        'cost_per_unit',
        'stock_quantity',
        'min_stock_level',
        'category',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost_per_unit' => 'decimal:2',
            'stock_quantity' => 'decimal:2',
            'min_stock_level' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }
}
