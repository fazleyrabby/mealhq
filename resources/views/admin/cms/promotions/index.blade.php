@extends('admin.layout')

@section('title', 'Promotions - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Promotions</h3>
        <div class="card-actions">
            <a href="{{ route('admin.cms.promotions.create') }}" class="btn btn-primary">Add Promotion</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search promotions..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Discount</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promotions as $promo)
                <tr>
                    <td>{{ $promo->title }}</td>
                    <td><span class="badge text-bg-secondary">{{ ucfirst($promo->discount_type) }}</span></td>
                    <td>{{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : '$' . number_format($promo->discount_value, 2) }}</td>
                    <td>
                        @if($promo->starts_at && $promo->ends_at)
                            {{ $promo->starts_at->format('M d') }} - {{ $promo->ends_at->format('M d, Y') }}
                        @elseif($promo->starts_at)
                            From {{ $promo->starts_at->format('M d, Y') }}
                        @elseif($promo->ends_at)
                            Until {{ $promo->ends_at->format('M d, Y') }}
                        @else
                            <span class="text-muted">Always</span>
                        @endif
                    </td>
                    <td>{!! $promo->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.cms.promotions.edit', $promo) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.cms.promotions.destroy', $promo) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No promotions yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($promotions->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $promotions->firstItem() }}–{{ $promotions->lastItem() }} of {{ $promotions->total() }} results</p>
        <div class="ms-auto">{{ $promotions->links() }}</div>
    </div>
    @endif
</div>
@endsection
