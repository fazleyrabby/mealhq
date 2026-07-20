@extends('admin.layout')

@section('title', 'Orders - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Orders</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Order #</th><th>Source</th><th>Type</th><th>Status</th><th>Customer</th><th>Total</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td><span class="badge bg-secondary">{{ ucfirst($order->source) }}</span></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->type)) }}</td>
                    <td><span class="badge bg-{{ ['pending'=>'yellow','confirmed'=>'blue','preparing'=>'orange','ready'=>'green','completed'=>'green','cancelled'=>'red'][$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span></td>
                    <td>{{ $order->customer->name ?? $order->customer->email ?? 'Guest' }}</td>
                    <td>\${{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No orders</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())<div class="card-footer">{{ $orders->links() }}</div>@endif
</div>
@endsection
