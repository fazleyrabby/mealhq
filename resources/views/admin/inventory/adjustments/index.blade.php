@extends('admin.layout')

@section('title', 'Stock Adjustments - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Stock Adjustments</h3>
        <div class="card-actions">
            <a href="{{ route('admin.inventory.adjustments.create') }}" class="btn btn-primary">New Adjustment</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search adjustments..."
            :per-page="$perPage ?? 20"
            :filters="[['field' => 'type', 'label' => 'Type', 'options' => ['addition' => 'Addition', 'removal' => 'Removal', 'correction' => 'Correction']]]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Ingredient</th><th>Type</th><th>Quantity</th><th>Reason</th><th>User</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($adjustments as $adj)
                <tr>
                    <td>{{ $adj->ingredient->name ?? '-' }}</td>
                    <td><span class="badge bg-{{ $adj->type === 'addition' ? 'green' : ($adj->type === 'removal' ? 'red' : 'orange') }}">{{ ucfirst($adj->type) }}</span></td>
                    <td>{{ $adj->quantity }} {{ $adj->ingredient->unit->abbreviation ?? '' }}</td>
                    <td>{{ $adj->reason ?? '-' }}</td>
                    <td>{{ $adj->adjustedBy->name ?? '-' }}</td>
                    <td>{{ $adj->created_at->format('M d, H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No adjustments</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($adjustments->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $adjustments->firstItem() }}–{{ $adjustments->lastItem() }} of {{ $adjustments->total() }} results</p>
        <div class="ms-auto">{{ $adjustments->links() }}</div>
    </div>
    @endif
</div>
@endsection
