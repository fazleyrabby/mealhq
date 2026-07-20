@extends('admin.layout')

@section('title', $promotion ? 'Edit Promotion' : 'Add Promotion')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $promotion ? 'Edit Promotion' : 'Add Promotion' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $promotion ? route('admin.cms.promotions.update', $promotion) : route('admin.cms.promotions.store') }}">
            @csrf @if($promotion) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $promotion->title ?? '') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $promotion->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Discount Type</label>
                    <select class="form-select @error('discount_type') is-invalid @enderror" name="discount_type">
                        <option value="percentage" {{ old('discount_type', $promotion->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('discount_type', $promotion->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                    @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Discount Value</label>
                    <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror" name="discount_value" value="{{ old('discount_value', $promotion->discount_value ?? '') }}" required>
                    @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Coupon Code (optional)</label>
                    <input type="text" class="form-control" name="coupon_code" value="{{ old('coupon_code', $promotion->coupon_code ?? '') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Starts At</label>
                    <input type="date" class="form-control" name="starts_at" value="{{ old('starts_at', $promotion->starts_at ? $promotion->starts_at->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ends At</label>
                    <input type="date" class="form-control" name="ends_at" value="{{ old('ends_at', $promotion->ends_at ? $promotion->ends_at->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $promotion ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.cms.promotions.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
