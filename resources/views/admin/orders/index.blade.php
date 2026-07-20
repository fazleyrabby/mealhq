@extends('admin.layout')

@section('title', 'Orders - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Orders</h3>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search order #..."
            :per-page="$perPage ?? 20"
            :filters="[
                ['field' => 'status', 'label' => 'Status', 'options' => ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'preparing' => 'Preparing', 'ready' => 'Ready', 'served' => 'Served', 'completed' => 'Completed', 'cancelled' => 'Cancelled']],
                ['field' => 'source', 'label' => 'Source', 'options' => ['web' => 'Web', 'pos' => 'POS', 'kiosk' => 'Kiosk', 'phone' => 'Phone']],
                ['field' => 'type', 'label' => 'Type', 'options' => ['dine_in' => 'Dine In', 'takeaway' => 'Takeaway', 'delivery' => 'Delivery']],
            ]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'order_number', 'direction' => ($sortField ?? 'created_at') === 'order_number' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Order # {!! ($sortField ?? 'created_at') === 'order_number' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Source</th>
                    <th>Type</th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'status', 'direction' => ($sortField ?? 'created_at') === 'status' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Status {!! ($sortField ?? 'created_at') === 'status' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Customer</th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'total_amount', 'direction' => ($sortField ?? 'created_at') === 'total_amount' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Total {!! ($sortField ?? 'created_at') === 'total_amount' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                    <td><span class="badge bg-secondary">{{ ucfirst($order->source) }}</span></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->type)) }}</td>
                    <td><span class="badge bg-{{ ['pending'=>'yellow','confirmed'=>'blue','preparing'=>'orange','ready'=>'green','completed'=>'green','cancelled'=>'red'][$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span></td>
                    <td>{{ $order->customer->name ?? $order->customer->email ?? 'Guest' }}</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No orders</td></tr>
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
