@extends('admin.layout')

@section('title', 'Purchase Orders - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Purchase Orders</h3>
        <div class="card-actions">
            <a href="{{ route('admin.inventory.purchase-orders.create') }}" class="btn btn-primary">New Order</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search POs..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'status', 'label' => 'Status', 'options' => ['draft' => 'Draft', 'ordered' => 'Ordered', 'partial' => 'Partial', 'received' => 'Received', 'cancelled' => 'Cancelled']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>PO #</th><th>Supplier</th><th>Items</th><th>Total</th><th>Status</th><th>Ordered</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($orders as $po)
                <tr>
                    <td>{{ $po->order_number }}</td>
                    <td>{{ $po->supplier->name ?? '-' }}</td>
                    <td>{{ $po->items->count() }}</td>
                    <td>${{ number_format($po->items->sum(fn($i) => $i->quantity * $i->unit_price), 2) }}</td>
                    <td><span class="badge bg-{{ ['draft'=>'secondary','ordered'=>'blue','partial'=>'orange','received'=>'green','cancelled'=>'red'][$po->status] ?? 'secondary' }}">{{ ucfirst($po->status) }}</span></td>
                    <td>{{ $po->ordered_at?->format('M d, Y') ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.inventory.purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if(in_array($po->status, ['ordered', 'partial']))
                        <form method="POST" action="{{ route('admin.inventory.purchase-orders.receive', $po) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-success">Receive</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No purchase orders</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} results</p>
        <div class="ms-auto">{{ $orders->links() }}</div>
    </div>
    @endif
</div>
@endsection
