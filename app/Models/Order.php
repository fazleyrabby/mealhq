<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'source', 'status', 'type', 'customer_id',
        'user_id', 'table_session_id', 'subtotal', 'tax_amount',
        'service_charge', 'discount_amount', 'total_amount',
        'notes', 'promo_code', 'ordered_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'ordered_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum(function (OrderItem $item) {
            $modifierTotal = $item->modifiers->sum('price_adjustment');

            return ($item->unit_price + $modifierTotal) * $item->quantity;
        });

        $taxRate = TaxRate::getDefault();
        $taxAmount = $taxRate ? $subtotal * ($taxRate->rate / 100) : 0;
        $serviceCharge = (float) Setting::get('service_charge_percentage', 0);
        $serviceChargeAmount = $subtotal * ($serviceCharge / 100);
        $total = $subtotal + $taxAmount + $serviceChargeAmount - $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'service_charge' => $serviceChargeAmount,
            'total_amount' => max(0, $total),
        ]);
    }
}
