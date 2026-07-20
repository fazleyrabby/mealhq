@extends('admin.layout')

@section('title', 'Pages - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pages</h3>
        <div class="card-actions">
            <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary">Add Page</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search pages..."
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
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'title', 'direction' => ($sortField ?? 'created_at') === 'title' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Title {!! ($sortField ?? 'created_at') === 'title' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'slug', 'direction' => ($sortField ?? 'created_at') === 'slug' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Slug {!! ($sortField ?? 'created_at') === 'slug' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Sections</th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'is_active', 'direction' => ($sortField ?? 'created_at') === 'is_active' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Status {!! ($sortField ?? 'created_at') === 'is_active' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>{{ $page->sections->count() }}</td>
                    <td>{!! $page->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.cms.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.cms.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No pages yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pages->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $pages->firstItem() }}–{{ $pages->lastItem() }} of {{ $pages->total() }} results</p>
        <div class="ms-auto">{{ $pages->links() }}</div>
    </div>
    @endif
</div>
@endsection
