<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'menu_item_id',
        'name',
        'instructions',
        'yield_quantity',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'yield_quantity' => 'integer',
            'total_cost' => 'decimal:2',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class, 'recipe_id');
    }

    public function calculateCost(): void
    {
        $total = $this->ingredients->sum(function (RecipeIngredient $ri) {
            $ingredientCost = $ri->ingredient->cost_per_unit * $ri->quantity;
            $wasteMultiplier = 1 + ($ri->waste_percentage / 100);

            return $ingredientCost * $wasteMultiplier;
        });

        $this->update(['total_cost' => $total]);
    }
}
