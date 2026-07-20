<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MenuItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'ingredients',
        'image_url',
        'base_price',
        'cost_price',
        'prep_time_minutes',
        'calories',
        'is_active',
        'is_featured',
        'has_variants',
        'channel_visibility',
        'unit_type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_variants' => 'boolean',
            'prep_time_minutes' => 'integer',
            'calories' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuItemVariant::class, 'menu_item_id');
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class, 'menu_item_modifier_group')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVisibleOnChannel($query, string $channel)
    {
        return $query->where(function ($q) use ($channel) {
            $q->where('channel_visibility', 'all')
                ->orWhere('channel_visibility', $channel);
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('menu_item_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    protected static function booted(): void
    {
        static::creating(function (MenuItem $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name);
            }
        });
    }
}
