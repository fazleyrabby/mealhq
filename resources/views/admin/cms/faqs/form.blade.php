@extends('admin.layout')

@section('title', $faq ? 'Edit FAQ' : 'Add FAQ')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $faq ? 'Edit FAQ' : 'Add FAQ' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $faq ? route('admin.cms.faqs.update', $faq) : route('admin.cms.faqs.store') }}">
            @csrf @if($faq) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Question</label>
                <input type="text" class="form-control @error('question') is-invalid @enderror" name="question" value="{{ old('question', $faq->question ?? '') }}" required>
                @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Answer</label>
                <textarea class="form-control @error('answer') is-invalid @enderror" name="answer" rows="5" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
                @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Sort Order</label>
                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $faq ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.cms.faqs.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
