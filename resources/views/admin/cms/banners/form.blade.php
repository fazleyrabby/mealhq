@extends('admin.layout')

@section('title', $banner ? 'Edit Banner' : 'Add Banner')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $banner ? 'Edit Banner' : 'Add Banner' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $banner ? route('admin.cms.banners.update', $banner) : route('admin.cms.banners.store') }}" enctype="multipart/form-data">
            @csrf @if($banner) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $banner->title ?? '') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <input type="text" class="form-control @error('subtitle') is-invalid @enderror" name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}">
                @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">CTA Text</label>
                    <input type="text" class="form-control" name="cta_text" value="{{ old('cta_text', $banner->cta_text ?? '') }}" placeholder="e.g. Order Now">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CTA URL</label>
                    <input type="text" class="form-control" name="cta_url" value="{{ old('cta_url', $banner->cta_url ?? '') }}" placeholder="https://... or /menu">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Banner Image</label>
                @if(!empty($banner?->image_url))
                    <div class="mb-2 d-flex align-items-center gap-3">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="rounded" style="width:160px;height:90px;object-fit:cover;">
                        <button type="submit" form="delete-image-form" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Delete this image? This cannot be undone.')">Delete Image</button>
                    </div>
                @endif
                <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/jpeg,image/png,image/jpg,image/webp">
                <small class="text-muted">Recommended size: 1920×1080. JPEG, PNG, JPG or WEBP, max 4 MB.</small>
                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ $banner ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@if(!empty($banner?->image_url))
<form id="delete-image-form" method="POST" action="{{ route('admin.cms.banners.image.delete', $banner) }}" class="d-none">
    @csrf @method('DELETE')
</form>
@endif
@endsection
