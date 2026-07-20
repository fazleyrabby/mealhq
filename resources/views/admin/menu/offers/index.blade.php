@extends('admin.layout')

@section('title', 'Special Offers - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Special Offers</h3>
        <div class="card-actions">
            <a href="{{ route('admin.menu.items.create') }}" class="btn btn-primary">Add Menu Item</a>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <form method="GET" class="d-inline">
            <label class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="home_only" value="1" onchange="this.form.submit()" {{ request()->filled('home_only') ? 'checked' : '' }}>
                <span class="form-check-label">Show in Home Offers only</span>
            </label>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th style="width:80px">Image</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Base Price</th>
                    <th>Offer Price</th>
                    <th>Discount</th>
                    <th>Home Offers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($offers as $offer)
                <tr>
                    <td>
                        @if($offer->image_url)
                            <img src="{{ $offer->image_url }}" alt="{{ $offer->name }}" class="rounded" style="width:60px;height:60px;object-fit:cover;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="fw-medium">{{ $offer->name }}</td>
                    <td class="text-muted">{{ $offer->category->name ?? '—' }}</td>
                    <td>${{ number_format((float) $offer->base_price, 2) }}</td>
                    <td>${{ number_format((float) $offer->special_price, 2) }}</td>
                    <td><span class="badge text-bg-success">Save {{ $offer->discountPercent() }}%</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.menu.items.toggle-home-offer', $offer) }}">
                            @csrf
                            <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="show_on_home_offers" value="1"
                                    {{ $offer->show_on_home_offers ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                            </label>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.menu.items.edit', $offer) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No items on special yet. Set a Special Price on a menu item to create an offer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($offers->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $offers->firstItem() }}–{{ $offers->lastItem() }} of {{ $offers->total() }} results</p>
        <ul class="pagination m-0 ms-auto">
            {{ $offers->links() }}
        </ul>
    </div>
    @endif
</div>
@endsection
