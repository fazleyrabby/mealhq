<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsFaq extends Model
{
    protected $table = 'cms_faqs';

    protected $fillable = [
        'category',
        'question',
        'answer',
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
}
