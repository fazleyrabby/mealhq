<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CmsGalleryAlbum extends Model
{
    use HasFactory;

    protected $table = 'cms_gallery_albums';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(CmsGalleryItem::class, 'album_id');
    }

    protected static function booted(): void
    {
        static::creating(function (CmsGalleryAlbum $album) {
            if (empty($album->slug)) {
                $album->slug = Str::slug($album->name);
            }
        });
    }
}
