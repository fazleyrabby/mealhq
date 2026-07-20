<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\ModifierGroup;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemModifier;
use App\Models\RestaurantTable;
use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosOrderController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['menuItems' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->orderBy('sort_order')->get();

        $products = MenuItem::with(['category', 'modifierGroups.items'])
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(50);

        $tables = RestaurantTable::with('zone')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn ($t) => $t->zone?->name ?? 'General');

        $modifierGroups = ModifierGroup::with('items')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $taxRate = TaxRate::where('is_default', true)->first();
        $serviceChargeRate = (float) Setting::get('service_charge_rate', 0);

        $allItems = MenuItem::with(['category:id,name', 'variants', 'modifierGroups.items'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'image_url', 'base_price', 'category_id', 'is_featured', 'is_active', 'prep_time_minutes']);

        // Today's POS orders for the current cashier
        $todayOrders = Order::with(['items.modifiers', 'customer', 'tableSession.restaurantTable'])
            ->whereDate('created_at', today())
            ->where('user_id', auth()->id())
            ->whereIn('source', ['pos', 'walk_in'])
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.pos.index', compact(
            'categories',
            'products',
            'tables',
            'modifierGroups',
            'taxRate',
            'serviceChargeRate',
            'allItems',
            'todayOrders'
        ));
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $products = MenuItem::with(['category', 'modifierGroups.items'])
            ->where('is_active', true)
            ->when($query, fn ($q) => $q->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            }))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(50);

        $html = view('admin.pos.partials.products', ['products' => $products])->render();

        return response()->json([
            'html' => $html,
            'has_more' => $products->hasMorePages(),
            'next_page' => $products->currentPage() + 1,
        ]);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $customers = Customer::where('is_active', true)
            ->when($query, fn ($q) => $q->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            }))
            ->limit(20)
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json($customers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.variant_name' => 'nullable|string|max:100',
            'items.*.menu_item_variant_id' => 'nullable|exists:menu_item_variants,id',
            'items.*.special_instructions' => 'nullable|string|max:500',
            'items.*.modifiers' => 'nullable|array',
            'items.*.modifiers.*.group_name' => 'required|string',
            'items.*.modifiers.*.item_name' => 'required|string',
            'items.*.modifiers.*.price_adjustment' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'table_session_id' => 'nullable|integer',
            'type' => 'required|in:dine_in,takeaway,delivery',
            'source' => 'required|in:pos,walk_in',
            'notes' => 'nullable|string|max:1000',
            'discount_amount' => 'nullable|numeric|min:0',
            'promo_code' => 'nullable|string|max:30',
        ]);

        $orderNumber = 'POS-' . now()->format('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'order_number' => $orderNumber,
            'source' => $validated['source'],
            'status' => 'pending',
            'type' => $validated['type'],
            'customer_id' => $validated['customer_id'] ?? null,
            'user_id' => auth()->id(),
            'table_session_id' => $validated['table_session_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'promo_code' => $validated['promo_code'] ?? null,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'ordered_at' => now(),
        ]);

        $this->syncOrderItems($order, $validated['items']);
        $order->recalculateTotals();

        return response()->json([
            'success' => true,
            'order' => $order->fresh()->load('items.modifiers'),
            'message' => __('Order #:number created.', ['number' => $order->order_number]),
        ]);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        if (! in_array($order->status, ['pending', 'confirmed', 'preparing'])) {
            return response()->json(['success' => false, 'message' => 'Cannot edit a completed or cancelled order.'], 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.variant_name' => 'nullable|string|max:100',
            'items.*.menu_item_variant_id' => 'nullable|exists:menu_item_variants,id',
            'items.*.special_instructions' => 'nullable|string|max:500',
            'items.*.modifiers' => 'nullable|array',
            'items.*.modifiers.*.group_name' => 'required|string',
            'items.*.modifiers.*.item_name' => 'required|string',
            'items.*.modifiers.*.price_adjustment' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'table_session_id' => 'nullable|integer',
            'type' => 'sometimes|in:dine_in,takeaway,delivery',
            'notes' => 'nullable|string|max:1000',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $order->update([
            'customer_id' => $validated['customer_id'] ?? $order->customer_id,
            'table_session_id' => $validated['table_session_id'] ?? $order->table_session_id,
            'type' => $validated['type'] ?? $order->type,
            'notes' => $validated['notes'] ?? $order->notes,
            'discount_amount' => $validated['discount_amount'] ?? $order->discount_amount,
        ]);

        // Remove existing items and replace
        $order->items()->each(function (OrderItem $item) {
            $item->modifiers()->delete();
            $item->delete();
        });

        $this->syncOrderItems($order, $validated['items']);
        $order->recalculateTotals();

        return response()->json([
            'success' => true,
            'order' => $order->fresh()->load('items.modifiers'),
            'message' => __('Order #:number updated.', ['number' => $order->order_number]),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json($order->load('items.modifiers', 'customer', 'tableSession.restaurantTable'));
    }

    public function todayOrders(): JsonResponse
    {
        $orders = Order::with(['items', 'customer', 'tableSession.restaurantTable'])
            ->whereDate('created_at', today())
            ->where('user_id', auth()->id())
            ->whereIn('source', ['pos', 'walk_in'])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($orders);
    }

    private function syncOrderItems(Order $order, array $items): void
    {
        foreach ($items as $itemData) {
            $modifierTotal = 0;
            $modifierDetails = [];

            if (! empty($itemData['modifiers'])) {
                foreach ($itemData['modifiers'] as $mod) {
                    $modifierTotal += (float) $mod['price_adjustment'];
                    $modifierDetails[] = $mod;
                }
            }

            $itemSubtotal = ((float) $itemData['unit_price'] + $modifierTotal) * (int) $itemData['quantity'];

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $itemData['menu_item_id'],
                'menu_item_variant_id' => $itemData['menu_item_variant_id'] ?? null,
                'item_name' => $itemData['item_name'],
                'variant_name' => $itemData['variant_name'] ?? null,
                'unit_price' => $itemData['unit_price'],
                'quantity' => $itemData['quantity'],
                'subtotal' => $itemSubtotal,
                'special_instructions' => $itemData['special_instructions'] ?? null,
            ]);

            foreach ($modifierDetails as $mod) {
                OrderItemModifier::create([
                    'order_item_id' => $orderItem->id,
                    'modifier_group_name' => $mod['group_name'],
                    'modifier_item_name' => $mod['item_name'],
                    'price_adjustment' => $mod['price_adjustment'],
                ]);
            }
        }
    }
}
