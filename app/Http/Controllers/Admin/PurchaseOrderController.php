<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            PurchaseOrder::with('supplier', 'items'),
            ['order_number'],
            ['status' => ['draft', 'ordered', 'partial', 'received', 'cancelled']],
            'created_at',
            'desc'
        );

        return view('admin.inventory.purchase-orders.index', $result + ['orders' => $result['items']]);
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('admin.inventory.purchase-orders.form', ['po' => null, 'suppliers' => $suppliers, 'ingredients' => $ingredients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => 'nullable|string|max:50|unique:purchase_orders,order_number',
            'notes' => 'nullable|string',
        ]);

        $po = PurchaseOrder::create($validated + ['status' => 'draft']);

        // Save items
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (! empty($item['ingredient_id']) && ! empty($item['quantity'])) {
                    $po->items()->create([
                        'ingredient_id' => $item['ingredient_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'] ?? 0,
                        'received_quantity' => $item['received_quantity'] ?? 0,
                    ]);
                }
            }
        }

        // Auto-place order
        $po->update(['status' => 'ordered', 'ordered_at' => now()]);

        return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase order created and placed.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.ingredient.unit');

        return view('admin.inventory.purchase-orders.show', ['po' => $purchaseOrder]);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $ingredients = Ingredient::where('is_active', true)->with('unit')->orderBy('name')->get();
        $purchaseOrder->load('items');

        return view('admin.inventory.purchase-orders.form', ['po' => $purchaseOrder, 'suppliers' => $suppliers, 'ingredients' => $ingredients]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => 'nullable|string|max:50|unique:purchase_orders,order_number,'.$purchaseOrder->id,
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder->update($validated);

        // Sync items
        $purchaseOrder->items()->delete();
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (! empty($item['ingredient_id']) && ! empty($item['quantity'])) {
                    $purchaseOrder->items()->create([
                        'ingredient_id' => $item['ingredient_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'] ?? 0,
                        'received_quantity' => $item['received_quantity'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase order updated.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->receiveItems();

        return back()->with('success', 'Items received successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase order deleted.');
    }
}
