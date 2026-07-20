@extends('admin.layout')

@section('title', 'Tables - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Restaurant Tables</h3>
        <div class="card-actions">
            <a href="{{ route('admin.operations.tables.create') }}" class="btn btn-primary">Add Table</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search tables..."
            :per-page="$perPage ?? 20"
            :filters="[
                ['field' => 'status', 'label' => 'Status', 'options' => ['available' => 'Available', 'occupied' => 'Occupied', 'reserved' => 'Reserved', 'cleaning' => 'Cleaning', 'maintenance' => 'Maintenance']],
                ['field' => 'zone_id', 'label' => 'Zone', 'options' => []],
            ]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Name</th><th>Zone</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($tables as $table)
                <tr>
                    <td>{{ $table->name }}</td>
                    <td>{{ $table->zone->name ?? '-' }}</td>
                    <td>{{ $table->capacity }} seats</td>
                    <td>
                        <span class="badge bg-{{ ['available'=>'green','occupied'=>'red','reserved'=>'yellow','cleaning'=>'orange','maintenance'=>'secondary'][$table->status] ?? 'secondary' }}">
                            {{ ucfirst($table->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.operations.tables.edit', $table) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.operations.tables.destroy', $table) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No tables</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tables->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $tables->firstItem() }}–{{ $tables->lastItem() }} of {{ $tables->total() }} results</p>
        <div class="ms-auto">{{ $tables->links() }}</div>
    </div>
    @endif
</div>
@endsection
