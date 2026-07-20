@extends('admin.layout')

@section('title', 'Menu Items - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Items</h3>
        <div class="card-actions">
            <a href="{{ route('admin.menu.items.create') }}" class="btn btn-primary">Add Item</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search menu items..."
            :per-page="$perPage ?? 20"
            :filters="[
                ['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']],
                ['field' => 'channel_visibility', 'label' => 'Channel', 'options' => ['all' => 'All', 'web_only' => 'Web Only', 'pos_only' => 'POS Only', 'qr_only' => 'QR Only']],
            ]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => ($sortField ?? 'name') === 'name' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Name {!! ($sortField ?? 'name') === 'name' ? '<span class="text-muted">' . (($sortDir ?? 'asc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Category</th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'base_price', 'direction' => ($sortField ?? 'name') === 'base_price' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Price {!! ($sortField ?? 'name') === 'base_price' ? '<span class="text-muted">' . (($sortDir ?? 'asc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>${{ number_format($item->base_price, 2) }}</td>
                    <td><span class="badge text-bg-secondary">{{ str_replace('_', ' ', $item->channel_visibility) }}</span></td>
                    <td>{!! $item->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.menu.items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="{{ route('admin.menu.items.variants', $item) }}" class="btn btn-sm btn-outline-secondary">Variants</a>
                        <form method="POST" action="{{ route('admin.menu.items.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No menu items</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} results</p>
        <div class="ms-auto">{{ $items->links() }}</div>
    </div>
    @endif
</div>
@endsection
