@extends('admin.layout')

@section('title', 'Banners - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Home Banners / Slider</h3>
        <div class="card-actions">
            <a href="{{ route('admin.cms.banners.create') }}" class="btn btn-primary">Add Banner</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search banners..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th style="width:120px">Image</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>CTA</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $banner)
                <tr>
                    <td>
                        @if($banner->image_url)
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="rounded" style="width:110px;height:60px;object-fit:cover;">
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </td>
                    <td class="fw-medium">{{ $banner->title }}</td>
                    <td class="text-muted">{{ $banner->subtitle ?? '—' }}</td>
                    <td>
                        @if($banner->cta_text)
                            <span class="badge bg-secondary">{{ $banner->cta_text }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $banner->sort_order }}</td>
                    <td>{!! $banner->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.cms.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.cms.banners.destroy', $banner) }}" class="d-inline" onsubmit="return confirm('Delete this banner?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No banners yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($banners->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $banners->firstItem() }}–{{ $banners->lastItem() }} of {{ $banners->total() }} results</p>
        <ul class="pagination m-0 ms-auto">
            {{ $banners->links() }}
        </ul>
    </div>
    @endif
</div>
@endsection
