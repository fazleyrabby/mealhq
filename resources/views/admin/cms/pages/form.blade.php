@extends('admin.layout')

@section('title', $page ? 'Edit Page' : 'Create Page')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $page ? 'Edit Page' : 'Create Page' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $page ? route('admin.cms.pages.update', $page) : route('admin.cms.pages.store') }}">
            @csrf @if($page) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $page->title ?? '') }}">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $page->slug ?? '') }}">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="8">{{ old('content', $page->content ?? '') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Meta Title</label>
                    <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Meta Description</label>
                    <input type="text" class="form-control" name="meta_description" value="{{ old('meta_description', $page->meta_description ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" {{ old('is_active', $page->is_active ?? true) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $page->is_active ?? true) ? '' : 'selected' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">{{ $page ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
