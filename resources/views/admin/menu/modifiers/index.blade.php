@extends('admin.layout')

@section('title', 'Modifier Groups - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Modifier Groups</h3>
        <div class="card-actions">
            <a href="{{ route('admin.menu.modifiers.create') }}" class="btn btn-primary">Add Group</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search modifier groups..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr><th>Name</th><th>Type</th><th>Min</th><th>Max</th><th>Items</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr>
                    <td>{{ $group->name }}</td>
                    <td><span class="badge text-bg-secondary">{{ ucfirst($group->type) }}</span></td>
                    <td>{{ $group->min_selection }}</td>
                    <td>{{ $group->max_selection }}</td>
                    <td>{{ $group->items->count() }}</td>
                    <td>{!! $group->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.menu.modifiers.edit', $group) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.menu.modifiers.destroy', $group) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No modifier groups</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($groups->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $groups->firstItem() }}–{{ $groups->lastItem() }} of {{ $groups->total() }} results</p>
        <div class="ms-auto">{{ $groups->links() }}</div>
    </div>
    @endif
</div>
@endsection
