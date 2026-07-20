@extends('admin.layout')

@section('title', 'Dashboard - MealHQ')

@section('content')
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Orders</div>
                </div>
                <div class="h1 mb-3">{{ $stats['total_orders'] }}</div>
                <div class="d-flex mb-2">
                    <div>Pending: <strong>{{ $stats['pending_orders'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Menu Items</div>
                </div>
                <div class="h1 mb-3">{{ $stats['menu_items'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Low Stock Items</div>
                </div>
                <div class="h1 mb-3 {{ $stats['low_stock'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Inquiries</div>
                </div>
                <div class="h1 mb-3">{{ $stats['unread_inquiries'] }}</div>
                <div class="d-flex mb-2">
                    <div>Active Promos: <strong>{{ $stats['active_promotions'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($order->source) }}</span></td>
                            <td>
                                @php
                                    $statusColors = ['pending' => 'yellow', 'confirmed' => 'blue', 'preparing' => 'orange', 'ready' => 'green', 'completed' => 'green', 'cancelled' => 'red'];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>\${{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->created_at->format('M d, H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
