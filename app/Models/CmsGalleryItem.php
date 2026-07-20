<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CmsGalleryItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'cms_gallery_items';

    protected $fillable = [
        'album_id',
        'title',
        'caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(CmsGalleryAlbum::class, 'album_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery_media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
