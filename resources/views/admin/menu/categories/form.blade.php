@extends('admin.layout')

@section('title', $category ? 'Edit Category' : 'Add Category')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $category ? 'Edit Category' : 'Add Category' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $category ? route('admin.menu.categories.update', $category) : route('admin.menu.categories.store') }}">
            @csrf @if($category) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $category->name ?? '') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Slug (leave blank for auto-generation)</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $category->slug ?? '') }}">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Parent Category</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" name="parent_id">
                        <option value="">None (Top Level)</option>
                        @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_visible_on_web" value="1" {{ old('is_visible_on_web', $category->is_visible_on_web ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Visible on Web</span>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_visible_on_pos" value="1" {{ old('is_visible_on_pos', $category->is_visible_on_pos ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Visible on POS</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $category ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.menu.categories.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
