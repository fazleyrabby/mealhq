@extends('admin.layout')

@section('title', 'Recipes - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recipes</h3>
        <div class="card-actions">
            <a href="{{ route('admin.inventory.recipes.create') }}" class="btn btn-primary">Add Recipe</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search recipes..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'is_active', 'label' => 'Status', 'options' => ['1' => 'Active', '0' => 'Inactive']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr><th>Name</th><th>Menu Item</th><th>Ingredients</th><th>Total Cost</th><th>Yield</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($recipes as $recipe)
                <tr>
                    <td>{{ $recipe->name }}</td>
                    <td>{{ $recipe->menuItem->name ?? '-' }}</td>
                    <td>{{ $recipe->ingredients->count() }}</td>
                    <td>${{ number_format($recipe->ingredients->sum(fn($ri) => $ri->cost * $ri->quantity * (1 + ($ri->waste_percentage ?? 0) / 100)), 4) }}</td>
                    <td>{{ $recipe->yield_amount }} {{ $recipe->yield_unit ?? 'units' }}</td>
                    <td>{!! $recipe->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.inventory.recipes.edit', $recipe) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.inventory.recipes.destroy', $recipe) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No recipes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($recipes->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $recipes->firstItem() }}–{{ $recipes->lastItem() }} of {{ $recipes->total() }} results</p>
        <div class="ms-auto">{{ $recipes->links() }}</div>
    </div>
    @endif
</div>
@endsection
