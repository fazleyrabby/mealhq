@extends('admin.layout')

@section('title', 'PO ' . $po->order_number)

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Purchase Order: {{ $po->order_number }}</h3>
        <div class="card-actions">
            <a href="{{ route('admin.inventory.purchase-orders.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-2">Supplier</dt>
            <dd class="col-sm-4">{{ $po->supplier->name ?? '-' }}</dd>
            <dt class="col-sm-2">Status</dt>
            <dd class="col-sm-4"><span class="badge bg-{{ ['draft'=>'secondary','ordered'=>'blue','partial'=>'orange','received'=>'green','cancelled'=>'red'][$po->status] ?? 'secondary' }}">{{ ucfirst($po->status) }}</span></dd>
            <dt class="col-sm-2">Ordered At</dt>
            <dd class="col-sm-4">{{ $po->ordered_at?->format('M d, Y H:i') ?? '-' }}</dd>
            <dt class="col-sm-2">Notes</dt>
            <dd class="col-sm-10">{{ $po->notes ?? '-' }}</dd>
        </dl>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Ingredient</th><th>Quantity</th><th>Unit Price</th><th>Total</th><th>Received</th></tr></thead>
            <tbody>
                @foreach($po->items as $item)
                <tr>
                    <td>{{ $item->ingredient->name ?? '-' }}</td>
                    <td>{{ $item->quantity }} {{ $item->ingredient->unit->abbreviation ?? '' }}</td>
                    <td>\${{ number_format($item->unit_price, 2) }}</td>
                    <td>\${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    <td>{{ $item->received_quantity ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">Total:</td>
                    <td>\${{ number_format($po->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
        @if(in_array($po->status, ['ordered', 'partial']))
        <form method="POST" action="{{ route('admin.inventory.purchase-orders.receive', $po) }}" class="d-inline">
            @csrf
            <button class="btn btn-success">Receive Items</button>
        </form>
        @endif
    </div>
</div>
@endsection
