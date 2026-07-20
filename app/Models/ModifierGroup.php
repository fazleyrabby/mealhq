<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierGroup extends Model
{
    protected $fillable = [
        'name',
        'type',
        'max_selections',
        'min_selections',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_selections' => 'integer',
            'min_selections' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModifierItem::class, 'modifier_group_id');
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_modifier_group')
            ->withTimestamps();
    }
}
