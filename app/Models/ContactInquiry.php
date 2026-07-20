<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactInquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'status',
        'assigned_user_id',
        'notes',
    ];

    protected $attributes = [
        'status' => 'unread',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }
}
