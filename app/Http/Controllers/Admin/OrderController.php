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
