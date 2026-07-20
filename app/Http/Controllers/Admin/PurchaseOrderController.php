<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier', 'createdBy')->withTrashed()->latest()->paginate(20);

        return view('admin.inventory.purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->get();
        $ingredients = Ingredient::active()->with('unit')->get();

        return view('admin.inventory.purchase-orders.form', ['po' => null, 'suppliers' => $suppliers, 'ingredients' => $ingredients]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string|max:30|unique:purchase_orders,order_number',
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'nullable|date',
            'expected_delivery' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::create([
            'order_number' => $validated['order_number'],
            'supplier_id' => $validated['supplier_id'],
            'order_date' => $validated['order_date'],
            'expected_delivery' => $validated['expected_delivery'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                ]);
            }
        }

        return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.ingredient', 'supplier', 'createdBy');

        return view('admin.inventory.purchase-orders.show', ['po' => $purchaseOrder]);
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'received' => 'required|array',
            'received.*' => 'required|numeric|min:0',
        ]);

        $purchaseOrder->receiveItems($validated['received']);

        return redirect()->route('admin.inventory.purchase-orders.show', $purchaseOrder)->with('success', 'Items received.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('admin.inventory.purchase-orders.index')->with('success', 'Purchase order deleted.');
    }
}
