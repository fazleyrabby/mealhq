@extends('admin.layout')

@section('title', $recipe ? 'Edit Recipe' : 'Add Recipe')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $recipe ? 'Edit Recipe' : 'Add Recipe' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $recipe ? route('admin.inventory.recipes.update', $recipe) : route('admin.inventory.recipes.store') }}">
            @csrf @if($recipe) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Recipe Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $recipe->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Linked Menu Item (optional)</label>
                    <select class="form-select @error('menu_item_id') is-invalid @enderror" name="menu_item_id">
                        <option value="">Not linked</option>
                        @foreach($menuItems as $mi)
                        <option value="{{ $mi->id }}" {{ old('menu_item_id', $recipe->menu_item_id ?? '') == $mi->id ? 'selected' : '' }}>{{ $mi->name }}</option>
                        @endforeach
                    </select>
                    @error('menu_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Yield Amount</label>
                    <input type="number" step="0.01" class="form-control @error('yield_amount') is-invalid @enderror" name="yield_amount" value="{{ old('yield_amount', $recipe->yield_amount ?? '1') }}">
                    @error('yield_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Yield Unit</label>
                    <input type="text" class="form-control" name="yield_unit" value="{{ old('yield_unit', $recipe->yield_unit ?? 'servings') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $recipe->is_active ?? true) ? 'checked' : '' }}>
                        <span class="form-check-label">Active</span>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Instructions</label>
                <textarea class="form-control" name="instructions" rows="4">{{ old('instructions', $recipe->instructions ?? '') }}</textarea>
            </div>

            <hr>
            <h4>Recipe Ingredients</h4>

            <div id="recipe-ingredients">
                @if($recipe && $recipe->ingredients->count() > 0)
                    @foreach($recipe->ingredients as $i => $ri)
                    <div class="row g-2 mb-2 recipe-ingredient-row">
                        <div class="col-md-4">
                            <select class="form-select" name="ingredients[{{ $i }}][ingredient_id]" required>
                                <option value="">Select ingredient</option>
                                @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}" {{ $ri->ingredient_id == $ing->id ? 'selected' : '' }}>{{ $ing->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control" name="ingredients[{{ $i }}][quantity]" value="{{ $ri->quantity }}" placeholder="Qty" required>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.0001" class="form-control" name="ingredients[{{ $i }}][cost]" value="{{ $ri->cost }}" placeholder="Cost" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" class="form-control" name="ingredients[{{ $i }}][waste_percentage]" value="{{ $ri->waste_percentage ?? 0 }}" placeholder="Waste %">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.recipe-ingredient-row').remove()">×</button>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="row g-2 mb-2 recipe-ingredient-row">
                    <div class="col-md-4">
                        <select class="form-select" name="ingredients[0][ingredient_id]" required>
                            <option value="">Select ingredient</option>
                            @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" name="ingredients[0][quantity]" placeholder="Qty" required>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.0001" class="form-control" name="ingredients[0][cost]" placeholder="Cost" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" name="ingredients[0][waste_percentage]" placeholder="Waste %" value="0">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.recipe-ingredient-row').remove()">×</button>
                    </div>
                </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addIngredientRow()">+ Add Ingredient</button>

            <div>
                <button type="submit" class="btn btn-primary">{{ $recipe ? 'Update' : 'Create' }}</button>
                <a href="{{ route('admin.inventory.recipes.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let ingIdx = {{ ($recipe && $recipe->ingredients->count() > 0) ? $recipe->ingredients->count() : 1 }};
function addIngredientRow() {
    const html = `<div class="row g-2 mb-2 recipe-ingredient-row">
        <div class="col-md-4">
            <select class="form-select" name="ingredients[${ingIdx}][ingredient_id]" required>
                <option value="">Select ingredient</option>
                @foreach($ingredients as $ing)
                <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" class="form-control" name="ingredients[${ingIdx}][quantity]" placeholder="Qty" required>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" step="0.0001" class="form-control" name="ingredients[${ingIdx}][cost]" placeholder="Cost" required>
            </div>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" class="form-control" name="ingredients[${ingIdx}][waste_percentage]" placeholder="Waste %" value="0">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.recipe-ingredient-row').remove()">×</button>
        </div>
    </div>`;
    document.getElementById('recipe-ingredients').insertAdjacentHTML('beforeend', html);
    ingIdx++;
}
</script>
@endpush
@endsection
