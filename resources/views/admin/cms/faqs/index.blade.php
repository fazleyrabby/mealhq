@extends('admin.layout')

@section('title', 'FAQs - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">FAQs</h3>
        <div class="card-actions">
            <a href="{{ route('admin.cms.faqs.create') }}" class="btn btn-primary">Add FAQ</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search FAQs..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'question', 'direction' => ($sortField ?? 'sort_order') === 'question' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Question {!! ($sortField ?? 'sort_order') === 'question' ? '<span class="text-muted">' . (($sortDir ?? 'asc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'sort_order', 'direction' => ($sortField ?? 'sort_order') === 'sort_order' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Order {!! ($sortField ?? 'sort_order') === 'sort_order' ? '<span class="text-muted">' . (($sortDir ?? 'asc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr>
                    <td>{{ Str::limit($faq->question, 60) }}</td>
                    <td>{{ $faq->sort_order }}</td>
                    <td>{!! $faq->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.cms.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.cms.faqs.destroy', $faq) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No FAQs yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($faqs->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $faqs->firstItem() }}–{{ $faqs->lastItem() }} of {{ $faqs->total() }} results</p>
        <div class="ms-auto">{{ $faqs->links() }}</div>
    </div>
    @endif
</div>
@endsection
