@extends('admin.layout')

@section('title', 'Users - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Users &amp; Employees</h3>
        <div class="card-actions">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search users..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Email</th><th>Roles</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @forelse($user->roles as $role)
                            <span class="badge bg-blue-lt">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </td>
                    <td>{!! $user->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No users</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} results</p>
        <div class="ms-auto">{{ $users->links() }}</div>
    </div>
    @endif
</div>
@endsection
