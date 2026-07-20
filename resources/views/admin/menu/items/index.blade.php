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
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Channel</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td>\${{ number_format($item->base_price, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ str_replace('_', ' ', $item->channel_visibility) }}</span></td>
                    <td>{!! $item->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
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
    @if($items->hasPages())<div class="card-footer">{{ $items->links() }}</div>@endif
</div>
@endsection
