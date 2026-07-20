@extends('admin.layout')

@section('title', 'Ingredients - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ingredients</h3>
        <div class="card-actions">
            <a href="{{ route('admin.inventory.ingredients.create') }}" class="btn btn-primary">Add Ingredient</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search ingredients..."
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
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => ($sortField ?? 'created_at') === 'name' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Name {!! ($sortField ?? 'created_at') === 'name' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Unit</th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'stock_quantity', 'direction' => ($sortField ?? 'created_at') === 'stock_quantity' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Stock {!! ($sortField ?? 'created_at') === 'stock_quantity' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->except(['sort', 'direction']), ['sort' => 'cost_per_unit', 'direction' => ($sortField ?? 'created_at') === 'cost_per_unit' && ($sortDir ?? 'desc') === 'asc' ? 'desc' : 'asc'])) }}">
                            Cost {!! ($sortField ?? 'created_at') === 'cost_per_unit' ? '<span class="text-muted">' . (($sortDir ?? 'desc') === 'asc' ? '↑' : '↓') . '</span>' : '' !!}
                        </a>
                    </th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingredients as $ingredient)
                <tr>
                    <td>{{ $ingredient->name }}</td>
                    <td>{{ $ingredient->unit->name ?? '-' }}</td>
                    <td>{{ $ingredient->stock_quantity }} @if($ingredient->low_stock_threshold && $ingredient->stock_quantity <= $ingredient->low_stock_threshold)<span class="badge text-bg-danger ms-1">Low</span>@endif</td>
                    <td>${{ number_format($ingredient->cost_per_unit, 4) }}</td>
                    <td>{!! $ingredient->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-danger">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.inventory.ingredients.edit', $ingredient) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.inventory.ingredients.destroy', $ingredient) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No ingredients</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ingredients->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $ingredients->firstItem() }}–{{ $ingredients->lastItem() }} of {{ $ingredients->total() }} results</p>
        <div class="ms-auto">{{ $ingredients->links() }}</div>
    </div>
    @endif
</div>
@endsection
