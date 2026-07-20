<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'ingredients',
        'image_url',
        'base_price',
        'special_price',
        'cost_price',
        'prep_time_minutes',
        'calories',
        'is_active',
        'is_featured',
        'show_on_home_offers',
        'has_variants',
        'channel_visibility',
        'unit_type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'special_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'show_on_home_offers' => 'boolean',
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

    public function scopeOnSpecial($query)
    {
        return $query->whereNotNull('special_price')
            ->whereColumn('special_price', '<', 'base_price');
    }

    public function scopeShowOnHomeOffers($query)
    {
        return $query->where('show_on_home_offers', true);
    }

    public function scopeVisibleOnChannel($query, string $channel)
    {
        return $query->where(function ($q) use ($channel) {
            $q->where('channel_visibility', 'all')
                ->orWhere('channel_visibility', $channel);
        });
    }

    public function isOnSpecial(): bool
    {
        return $this->special_price !== null
            && $this->special_price >= 0
            && $this->special_price < $this->base_price;
    }

    public function effectivePrice(): float
    {
        return $this->isOnSpecial() ? (float) $this->special_price : (float) $this->base_price;
    }

    public function discountPercent(): int
    {
        if (! $this->isOnSpecial() || (float) $this->base_price <= 0) {
            return 0;
        }

        return (int) round((($this->base_price - $this->special_price) / $this->base_price) * 100);
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
