<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $fillable = [
        'ingredient_id', 'type', 'quantity', 'unit_cost',
        'reason', 'adjusted_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public static function adjustStock(int $ingredientId, string $type, float $quantity, ?string $reason = null, ?int $userId = null): StockAdjustment
    {
        $ingredient = Ingredient::findOrFail($ingredientId);

        $adjustment = static::create([
            'ingredient_id' => $ingredientId,
            'type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $ingredient->cost_per_unit,
            'reason' => $reason,
            'adjusted_by' => $userId,
        ]);

        if (in_array($type, ['removal', 'waste'])) {
            $ingredient->decrement('stock_quantity', $quantity);
        } else {
            $ingredient->increment('stock_quantity', $quantity);
        }

        return $adjustment;
    }
}
