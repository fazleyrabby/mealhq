@extends('admin.layout')

@section('title', $item ? 'Edit Menu Item' : 'Add Menu Item')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $item ? 'Edit Menu Item' : 'Add Menu Item' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $item ? route('admin.menu.items.update', $item) : route('admin.menu.items.store') }}" enctype="multipart/form-data">
            @csrf @if($item) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $item->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug (leave blank for auto-generation)</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $item->slug ?? '') }}">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $item->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Base Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" name="base_price" value="{{ old('base_price', $item->base_price ?? '') }}" required>
                        @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Channel Visibility</label>
                    <select class="form-select @error('channel_visibility') is-invalid @enderror" name="channel_visibility">
                        <option value="both" {{ old('channel_visibility', $item->channel_visibility ?? '') === 'both' ? 'selected' : '' }}>Both</option>
                        <option value="web" {{ old('channel_visibility', $item->channel_visibility ?? '') === 'web' ? 'selected' : '' }}>Web Only</option>
                        <option value="pos" {{ old('channel_visibility', $item->channel_visibility ?? '') === 'pos' ? 'selected' : '' }}>POS Only</option>
                    </select>
                    @error('channel_visibility')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_featured" value="1" {{ old('is_featured', $item->is_featured ?? false) ? 'checked' : '' }}>
                        <span class="form-check-label">Featured</span>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_taxable" value="1" {{ old('is_taxable', $item->is_taxable ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Taxable</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $item ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.menu.items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
