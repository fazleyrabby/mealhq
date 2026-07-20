@extends('admin.layout')

@section('title', 'Zones - MealHQ')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Add Zone</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.operations.zones.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Zone Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (optional)</label>
                        <input type="text" class="form-control" name="description" value="{{ old('description') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Zone</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Zones</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Name</th><th>Description</th><th>Tables</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($zones as $zone)
                        <tr>
                            <td>{{ $zone->name }}</td>
                            <td>{{ $zone->description ?? '-' }}</td>
                            <td>{{ $zone->tables->count() }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.operations.zones.destroy', $zone) }}" class="d-inline" onsubmit="return confirm('Delete zone?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No zones</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
