@extends('admin.layout')

@section('title', $po ? 'Edit Purchase Order' : 'New Purchase Order')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $po ? 'Edit Purchase Order' : 'New Purchase Order' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $po ? route('admin.inventory.purchase-orders.update', $po) : route('admin.inventory.purchase-orders.store') }}">
            @csrf @if($po) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select class="form-select @error('supplier_id') is-invalid @enderror" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id', $po->supplier_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Order Number (leave blank for auto)</label>
                    <input type="text" class="form-control" name="order_number" value="{{ old('order_number', $po->order_number ?? '') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" name="notes" rows="2">{{ old('notes', $po->notes ?? '') }}</textarea>
            </div>

            <hr>
            <h4>Items</h4>
            <div id="po-items">
                @if($po && $po->items->count() > 0)
                    @foreach($po->items as $i => $item)
                    <div class="row g-2 mb-2 po-item-row">
                        <div class="col-md-5">
                            <select class="form-select" name="items[{{ $i }}][ingredient_id]" required>
                                <option value="">Select ingredient</option>
                                @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}" {{ $item->ingredient_id == $ing->id ? 'selected' : '' }}>{{ $ing->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" placeholder="Qty" required>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" placeholder="Price" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control" name="items[{{ $i }}][received_quantity]" value="{{ $item->received_quantity }}" placeholder="Received">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.po-item-row').remove()">×</button>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="row g-2 mb-2 po-item-row">
                    <div class="col-md-5">
                        <select class="form-select" name="items[0][ingredient_id]" required>
                            <option value="">Select ingredient</option>
                            @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" name="items[0][quantity]" placeholder="Qty" required>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" name="items[0][unit_price]" placeholder="Price" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" name="items[0][received_quantity]" placeholder="Received" value="0">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.po-item-row').remove()">×</button>
                    </div>
                </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addPoItemRow()">+ Add Item</button>

            <div>
                <button type="submit" class="btn btn-primary">{{ $po ? 'Update' : 'Create Order' }}</button>
                <a href="{{ route('admin.inventory.purchase-orders.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let poIdx = {{ ($po && $po->items->count() > 0) ? $po->items->count() : 1 }};
function addPoItemRow() {
    const html = `<div class="row g-2 mb-2 po-item-row">
        <div class="col-md-5">
            <select class="form-select" name="items[${poIdx}][ingredient_id]" required>
                <option value="">Select ingredient</option>
                @foreach($ingredients as $ing)
                <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" class="form-control" name="items[${poIdx}][quantity]" placeholder="Qty" required>
        </div>
        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.01" class="form-control" name="items[${poIdx}][unit_price]" placeholder="Price" required>
            </div>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" class="form-control" name="items[${poIdx}][received_quantity]" placeholder="Received" value="0">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.po-item-row').remove()">×</button>
        </div>
    </div>`;
    document.getElementById('po-items').insertAdjacentHTML('beforeend', html);
    poIdx++;
}
</script>
@endpush
@endsection
