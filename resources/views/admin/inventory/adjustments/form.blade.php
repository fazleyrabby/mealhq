@extends('admin.layout')

@section('title', 'New Stock Adjustment')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">New Stock Adjustment</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.inventory.adjustments.store') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Ingredient</label>
                    <select class="form-select @error('ingredient_id') is-invalid @enderror" name="ingredient_id" required>
                        <option value="">Select Ingredient</option>
                        @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}" {{ old('ingredient_id') == $ing->id ? 'selected' : '' }}>
                            {{ $ing->name }} (Stock: {{ $ing->stock_quantity }} {{ $ing->unit->abbreviation ?? '' }})
                        </option>
                        @endforeach
                    </select>
                    @error('ingredient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                        <option value="addition" {{ old('type') === 'addition' ? 'selected' : '' }}>Addition</option>
                        <option value="removal" {{ old('type') === 'removal' ? 'selected' : '' }}>Removal</option>
                        <option value="correction" {{ old('type') === 'correction' ? 'selected' : '' }}>Correction</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" name="quantity" value="{{ old('quantity') }}" required>
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason</label>
                <textarea class="form-control @error('reason') is-invalid @enderror" name="reason" rows="2">{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">Save Adjustment</button>
            <a href="{{ route('admin.inventory.adjustments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
