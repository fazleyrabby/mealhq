@extends('admin.layout')

@section('title', $group ? 'Edit Modifier Group' : 'Add Modifier Group')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $group ? 'Edit Modifier Group' : 'Add Modifier Group' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $group ? route('admin.menu.modifiers.update', $group) : route('admin.menu.modifiers.store') }}">
            @csrf @if($group) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $group->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type">
                        <option value="single" {{ old('type', $group->type ?? '') === 'single' ? 'selected' : '' }}>Single Select</option>
                        <option value="multiple" {{ old('type', $group->type ?? '') === 'multiple' ? 'selected' : '' }}>Multiple Select</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Min Selection</label>
                    <input type="number" class="form-control" name="min_selection" value="{{ old('min_selection', $group->min_selection ?? 0) }}" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Max Selection</label>
                    <input type="number" class="form-control" name="max_selection" value="{{ old('max_selection', $group->max_selection ?? 1) }}" min="1">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $group->is_active ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Active</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">{{ $group ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.menu.modifiers.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

{{-- Modifier Items --}}
@if($group)
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Modifier Items for {{ $group->name }}</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($group->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{!! $item->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <form method="POST" action="#" class="d-inline" onsubmit="alert('Coming soon'); return false;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No items in this group</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <form method="POST" action="{{ route('admin.menu.modifiers.items.store', $group) }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Item name" required>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" value="0" required>
                </div>
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
@endif
@endsection
