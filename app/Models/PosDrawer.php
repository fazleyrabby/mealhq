<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosDrawer extends Model
{
    protected $fillable = [
        'name', 'status', 'opening_balance', 'closing_balance',
        'expected_balance', 'opened_by', 'closed_by',
        'opened_at', 'closed_at', 'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'expected_balance' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function open(float $balance, int $userId): void
    {
        $this->update([
            'status' => 'open',
            'opening_balance' => $balance,
            'opened_by' => $userId,
            'opened_at' => now(),
        ]);
    }

    public function close(float $actualBalance, int $userId): void
    {
        $this->update([
            'status' => 'closed',
            'closing_balance' => $actualBalance,
            'closed_by' => $userId,
            'closed_at' => now(),
        ]);
    }
}
