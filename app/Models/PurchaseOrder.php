<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'supplier_id', 'status', 'total_amount',
        'order_date', 'expected_delivery', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'order_date' => 'date',
            'expected_delivery' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receiveItems(array $received): void
    {
        foreach ($received as $ingredientId => $quantity) {
            $item = $this->items()->where('ingredient_id', $ingredientId)->first();
            if ($item) {
                $item->update(['received_quantity' => $quantity]);
                $ingredient = $item->ingredient;
                $ingredient->increment('stock_quantity', $quantity);
            }
        }
        $this->updateStatus();
    }

    protected function updateStatus(): void
    {
        $totalItems = $this->items()->count();
        $fullyReceived = $this->items()->whereColumn('received_quantity', '>=', 'quantity')->count();

        if ($fullyReceived === 0) {
            $this->update(['status' => 'ordered']);
        } elseif ($fullyReceived >= $totalItems) {
            $this->update(['status' => 'received']);
        } else {
            $this->update(['status' => 'partial']);
        }
    }
}
