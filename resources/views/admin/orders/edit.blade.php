@extends('admin.layout')

@section('title', 'Edit Order '.$order->order_number)

@section('content')
<form method="POST" action="{{ route('admin.orders.update', $order) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title">Edit Items</h3>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary ms-auto">Cancel</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table align-middle">
                        <thead><tr><th>Item</th><th style="width:90px">Qty</th><th style="width:120px">Unit Price</th><th>Modifiers</th><th class="text-end">Subtotal</th><th style="width:40px"></th></tr></thead>
                        <tbody>
                            @foreach($order->items as $i => $item)
                            <tr>
                                <td>
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $i }}][menu_item_id]" value="{{ $item->menu_item_id }}">
                                    <input type="hidden" name="items[{{ $i }}][item_name]" value="{{ $item->item_name }}">
                                    <input type="hidden" name="items[{{ $i }}][variant_name]" value="{{ $item->variant_name }}">
                                    <div class="fw-semibold">{{ $item->item_name }}</div>
                                    @if($item->variant_name)<div class="text-muted small">{{ $item->variant_name }}</div>@endif
                                </td>
                                <td>
                                    <input type="number" min="1" class="form-control form-control-sm" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}">
                                    </div>
                                </td>
                                <td>
                                    @foreach($item->modifiers as $mod)
                                        <span class="badge bg-secondary">{{ $mod->modifier_item_name }} (+${{ number_format($mod->price_adjustment, 2) }})</span>
                                    @endforeach
                                </td>
                                <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-ghost-danger" onclick="this.closest('tr').remove()" title="Remove line">&times;</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th colspan="4" class="text-end">Subtotal</th><th class="text-end">${{ number_format($order->subtotal, 2) }}</th><th></th></tr>
                            <tr><td colspan="4" class="text-end">Tax</td><td class="text-end">${{ number_format($order->tax_amount, 2) }}</td><td></td></tr>
                            <tr><td colspan="4" class="text-end">Service Charge</td><td class="text-end">${{ number_format($order->service_charge, 2) }}</td><td></td></tr>
                            <tr><td colspan="4" class="text-end">Discount</td><td class="text-end">-${{ number_format($order->discount_amount, 2) }}</td><td></td></tr>
                            <tr><th colspan="4" class="text-end">Total</th><th class="text-end">${{ number_format($order->total_amount, 2) }}</th><th></th></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">Order Settings</h3></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="served" {{ $order->status == 'served' ? 'selected' : '' }}>Served</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="discount_amount" value="{{ $order->discount_amount }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3">{{ $order->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">Back to Orders</a>
        </div>
    </div>
</form>
@endsection
