@extends('admin.layout')

@section('title', 'Categories - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Categories</h3>
        <div class="card-actions">
            <a href="{{ route('admin.menu.categories.create') }}" class="btn btn-primary">Add Category</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search categories..."
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
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => ($sortField ?? 'sort_order') === 'name' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Name {!! ($sortField ?? 'sort_order') === 'name' ? '<span class="text-muted">' . (($sortDir ?? 'asc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Slug</th>
                    <th>Parent</th>
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
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td><code>{{ $cat->slug }}</code></td>
                    <td>{{ $cat->parent->name ?? '-' }}</td>
                    <td>{{ $cat->sort_order }}</td>
                    <td>{!! $cat->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.menu.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.menu.categories.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No categories</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} results</p>
        <div class="ms-auto">{{ $categories->links() }}</div>
    </div>
    @endif
</div>
@endsection
