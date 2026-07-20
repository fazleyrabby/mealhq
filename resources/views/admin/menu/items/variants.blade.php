@extends('admin.layout')

@section('title', 'Variants - ' . $item->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Variants for {{ $item->name }}</h3>
        <div class="card-actions">
            <a href="{{ route('admin.menu.items.index') }}" class="btn btn-secondary">Back to Items</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Price Adjustment</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($item->variants as $variant)
                <tr>
                    <td>{{ $variant->name }}</td>
                    <td>{{ $variant->price_adjustment > 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 2) }}</td>
                    <td>{!! $variant->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                    <td>
                        <form method="POST" action="#" class="d-inline" onsubmit="alert('Variant editing coming soon'); return false;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No variants</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <form method="POST" action="{{ route('admin.menu.items.variants.store', $item) }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Variant name" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="price_adjustment" class="form-control" placeholder="Price adjustment" value="0">
            </div>
            <div class="col-md-3">
                <label class="form-check mt-2">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                    <span class="form-check-label">Active</span>
                </label>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </form>
    </div>
</div>
@endsection
