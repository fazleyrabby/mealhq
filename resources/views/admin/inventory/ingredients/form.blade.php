@extends('admin.layout')

@section('title', $ingredient ? 'Edit Ingredient' : 'Add Ingredient')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $ingredient ? 'Edit Ingredient' : 'Add Ingredient' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $ingredient ? route('admin.inventory.ingredients.update', $ingredient) : route('admin.inventory.ingredients.store') }}">
            @csrf @if($ingredient) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $ingredient->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit</label>
                    <select class="form-select @error('unit_id') is-invalid @enderror" name="unit_id" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $ingredient->unit_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                        @endforeach
                    </select>
                    @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" step="0.01" class="form-control @error('stock_quantity') is-invalid @enderror" name="stock_quantity" value="{{ old('stock_quantity', $ingredient->stock_quantity ?? '0') }}" required>
                    @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cost Per Unit</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.0001" class="form-control @error('cost_per_unit') is-invalid @enderror" name="cost_per_unit" value="{{ old('cost_per_unit', $ingredient->cost_per_unit ?? '0') }}" required>
                        @error('cost_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" step="0.01" class="form-control" name="low_stock_threshold" value="{{ old('low_stock_threshold', $ingredient->low_stock_threshold ?? '') }}" placeholder="Optional">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $ingredient->is_active ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Active</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">{{ $ingredient ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.inventory.ingredients.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
