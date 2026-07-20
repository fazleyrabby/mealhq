@extends('admin.layout')

@section('title', $role ? 'Edit Role' : 'Add Role')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $role ? 'Edit Role' : 'Add Role' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $role ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
            @csrf @if($role) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label required">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $role->name ?? '') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Permissions</label>
                @foreach($permissions as $group => $names)
                <div class="mb-3">
                    <div class="form-label fw-bold text-uppercase small">{{ $group }}</div>
                    <div class="row g-2">
                        @foreach($names as $name)
                        <div class="col-md-4 col-lg-3">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $name }}"
                                    {{ in_array($name, old('permissions', $assigned ?? [])) ? 'checked' : '' }}>
                                <span class="form-check-label">{{ $name }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @error('permissions')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ $role ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
