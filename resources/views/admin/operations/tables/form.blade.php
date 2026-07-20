@extends('admin.layout')

@section('title', $table ? 'Edit Table' : 'Add Table')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $table ? 'Edit Table' : 'Add Table' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $table ? route('admin.operations.tables.update', $table) : route('admin.operations.tables.store') }}">
            @csrf @if($table) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Table Name/Number</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $table->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Zone</label>
                    <select class="form-select @error('zone_id') is-invalid @enderror" name="zone_id">
                        <option value="">No Zone</option>
                        @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ old('zone_id', $table->zone_id ?? '') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Capacity</label>
                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" name="capacity" value="{{ old('capacity', $table->capacity ?? 4) }}" required min="1">
                    @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $table->is_active ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Active</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">{{ $table ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.operations.tables.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
