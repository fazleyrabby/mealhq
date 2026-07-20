@extends('admin.layout')

@section('title', $supplier ? 'Edit Supplier' : 'Add Supplier')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $supplier ? 'Edit Supplier' : 'Add Supplier' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $supplier ? route('admin.inventory.suppliers.update', $supplier) : route('admin.inventory.suppliers.store') }}">
            @csrf @if($supplier) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Company Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $supplier->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $supplier->contact_name ?? '') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $supplier->email ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Active</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">{{ $supplier ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.inventory.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
