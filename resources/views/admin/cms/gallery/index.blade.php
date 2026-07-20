@extends('admin.layout')

@section('title', 'Gallery - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gallery Albums</h3>
        <div class="card-actions">
            <a href="#" class="btn btn-primary" onclick="alert('Gallery management coming soon')">Add Album</a>
        </div>
    </div>
    <div class="card-body text-center text-muted py-5">
        <p>Gallery management is coming in a future update.</p>
        <p>You can manage gallery albums and items here, including image uploads via Spatie Media Library.</p>
    </div>
</div>
@endsection
