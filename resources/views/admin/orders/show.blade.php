@extends('admin.layout')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Order Items</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Item</th><th>Variant</th><th>Qty</th><th>Price</th><th>Modifiers</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->variant_name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>\${{ number_format($item->unit_price, 2) }}</td>
                            <td>
                                @foreach($item->modifiers as $mod)
                                <span class="badge bg-secondary">{{ $mod->modifier_item_name }} (+${{ number_format($mod->price_adjustment, 2) }})</span>
                                @endforeach
                            </td>
                            <td>\${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="5" class="text-end">Subtotal</th><th>\${{ number_format($order->subtotal, 2) }}</th></tr>
                        <tr><td colspan="5" class="text-end">Tax</td><td>\${{ number_format($order->tax_amount, 2) }}</td></tr>
                        <tr><td colspan="5" class="text-end">Service Charge</td><td>\${{ number_format($order->service_charge, 2) }}</td></tr>
                        <tr><td colspan="5" class="text-end">Discount</td><td>-\${{ number_format($order->discount_amount, 2) }}</td></tr>
                        <tr><th colspan="5" class="text-end">Total</th><th>\${{ number_format($order->total_amount, 2) }}</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Details</h3></div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5">Order #</dt>
                    <dd class="col-7">{{ $order->order_number }}</dd>
                    <dt class="col-5">Source</dt>
                    <dd class="col-7">{{ ucfirst($order->source) }}</dd>
                    <dt class="col-5">Type</dt>
                    <dd class="col-7">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</dd>
                    <dt class="col-5">Customer</dt>
                    <dd class="col-7">{{ $order->customer->name ?? $order->customer->email ?? 'Guest' }}</dd>
                    <dt class="col-5">Staff</dt>
                    <dd class="col-7">{{ $order->user->name ?? '-' }}</dd>
                    <dt class="col-5">Notes</dt>
                    <dd class="col-7">{{ $order->notes ?? '-' }}</dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">Status</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge bg-{{ ['pending'=>'yellow','confirmed'=>'blue','preparing'=>'orange','ready'=>'green','served'=>'green','completed'=>'green','cancelled'=>'red'][$order->status] ?? 'secondary' }} fs-5">{{ ucfirst($order->status) }}</span>
                </div>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf
                    <select class="form-select mb-2" name="status">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="served" {{ $order->status == 'served' ? 'selected' : '' }}>Served</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100 mt-3">Back to Orders</a>
    </div>
</div>
@endsection
