@foreach($products as $item)
    <div class="product-card position-relative @if(!$item->is_active) out-of-stock @endif" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->base_price }}" data-image="{{ $item->getFirstMediaUrl('menu_image') ?: '/placeholder.svg' }}">
        <div class="position-relative">
            <img src="{{ $item->getFirstMediaUrl('menu_image') ?: '/placeholder.svg' }}" alt="{{ $item->name }}" class="product-img" loading="lazy">
            @if($item->is_featured)
                <span class="product-badge" style="background:#ff9800;color:#000">Featured</span>
            @endif
        </div>
        <div class="p-2">
            <div class="fw-semibold text-truncate" style="color:#e8eaed">{{ $item->name }}</div>
            <div class="text-muted small text-truncate">{{ $item->category?->name ?? '' }}</div>
            <div class="d-flex align-items-center justify-content-between mt-1">
                <span style="color:#e8eaed;font-weight:600">${{ number_format($item->base_price, 2) }}</span>
                <button class="btn btn-sm btn-primary rounded-circle" style="width:1.75rem;height:1.75rem;padding:0;font-size:.875rem;line-height:1" onclick="window.posAddItem({{ $item->id }})" title="Add to cart">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </button>
            </div>
        </div>
    </div>
@endforeach
@if($products->isEmpty())
    <div class="col-12 text-center text-muted py-5">No products found</div>
@endif
