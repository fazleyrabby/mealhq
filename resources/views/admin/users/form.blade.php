@extends('admin.layout')

@section('title', $user ? 'Edit User' : 'Add User')

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">{{ $user ? 'Edit User' : 'Add User' }}</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf @if($user) @method('PUT') @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label required">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label {{ $user ? '' : 'required' }}">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" {{ $user ? '' : 'required' }} autocomplete="new-password">
                    @if($user)<small class="form-hint">Leave blank to keep the current password.</small>@endif
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label {{ $user ? '' : 'required' }}">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" {{ $user ? '' : 'required' }} autocomplete="new-password">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Roles</label>
                <div class="row g-2">
                    @foreach($roles as $roleName)
                    <div class="col-md-4">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $roleName }}"
                                {{ in_array($roleName, old('roles', $user ? $user->roles->pluck('name')->all() : [])) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ $roleName }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('roles')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Active</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">{{ $user ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
