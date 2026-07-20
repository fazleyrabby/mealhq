@extends('admin.layout')

@section('title', 'Roles - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Roles &amp; Permissions</h3>
        <div class="card-actions">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Add Role</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search roles..."
            :per-page="$perPage ?? 20"
            :filters="[]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Permissions</th><th>Users</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td class="fw-medium">{{ $role->name }}</td>
                    <td><span class="badge text-bg-blue-lt">{{ $role->permissions_count }}</span></td>
                    <td><span class="badge text-bg-secondary">{{ $role->users_count }}</span></td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Delete this role?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No roles</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($roles->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $roles->firstItem() }}–{{ $roles->lastItem() }} of {{ $roles->total() }} results</p>
        <div class="ms-auto">{{ $roles->links() }}</div>
    </div>
    @endif
</div>
@endsection
