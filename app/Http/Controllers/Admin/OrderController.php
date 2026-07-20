<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            Order::with('customer'),
            ['order_number'],
            [
                'status' => ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'],
                'source' => ['web', 'pos', 'kiosk', 'phone'],
                'type' => ['dine_in', 'takeaway', 'delivery'],
            ],
            'created_at',
            'desc'
        );

        return view('admin.orders.index', $result + ['orders' => $result['items']]);
    }

    public function show(Order $order)
    {
        $order->load(['items.modifiers', 'items.menuItem', 'customer', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['items.modifiers', 'items.menuItem', 'customer', 'user']);

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'discount_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:order_items,id',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.variant_name' => 'nullable|string|max:100',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.modifiers' => 'nullable|array',
        ]);

        // Sync items: remove missing, update existing, add new
        $keptIds = collect($validated['items'])->pluck('id')->filter()->all();
        $order->items()->whereNotIn('id', $keptIds)->each(function ($item) {
            $item->modifiers()->delete();
            $item->delete();
        });

        foreach ($validated['items'] as $itemData) {
            if (! empty($itemData['id'])) {
                $item = $order->items()->find($itemData['id']);
                if ($item) {
                    $modTotal = collect($itemData['modifiers'] ?? [])->sum('price_adjustment');
                    $item->update([
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => ((float) $itemData['unit_price'] + (float) $modTotal) * (int) $itemData['quantity'],
                        'special_instructions' => $itemData['special_instructions'] ?? $item->special_instructions,
                    ]);
                    continue;
                }
            }
            $modTotal = collect($itemData['modifiers'] ?? [])->sum('price_adjustment');
            $order->items()->create([
                'menu_item_id' => $itemData['menu_item_id'],
                'item_name' => $itemData['item_name'],
                'variant_name' => $itemData['variant_name'] ?? null,
                'unit_price' => $itemData['unit_price'],
                'quantity' => $itemData['quantity'],
                'subtotal' => ((float) $itemData['unit_price'] + (float) $modTotal) * (int) $itemData['quantity'],
                'special_instructions' => $itemData['special_instructions'] ?? null,
            ]);
        }

        $order->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);
        $order->recalculateTotals();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated.');
    }

    public function destroy(Order $order)
    {
        $order->items()->each(function ($item) {
            $item->modifiers()->delete();
            $item->delete();
        });
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'completed') {
            $order->update(['completed_at' => now()]);
        }

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
