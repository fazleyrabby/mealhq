@extends('admin.layout')

@section('title', 'Inquiry - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Inquiry from {{ $inquiry->name }}</h3>
        <div class="card-actions">
            <a href="{{ route('admin.cms.inquiries.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-2">Name</dt>
            <dd class="col-sm-10">{{ $inquiry->name }}</dd>
            <dt class="col-sm-2">Email</dt>
            <dd class="col-sm-10">{{ $inquiry->email }}</dd>
            <dt class="col-sm-2">Phone</dt>
            <dd class="col-sm-10">{{ $inquiry->phone ?? '-' }}</dd>
            <dt class="col-sm-2">Subject</dt>
            <dd class="col-sm-10">{{ $inquiry->subject ?? '-' }}</dd>
            <dt class="col-sm-2">Message</dt>
            <dd class="col-sm-10">{{ $inquiry->message }}</dd>
            <dt class="col-sm-2">Received</dt>
            <dd class="col-sm-10">{{ $inquiry->created_at->format('F d, Y H:i') }}</dd>
            <dt class="col-sm-2">Status</dt>
            <dd class="col-sm-10">
                {!! $inquiry->is_read ? '<span class="badge bg-green">Read</span>' : '<span class="badge bg-yellow">Unread</span>' !!}
                {!! $inquiry->is_replied ? '<span class="badge bg-green">Replied</span>' : '<span class="badge bg-secondary">Not Replied</span>' !!}
            </dd>
        </dl>
    </div>
    <div class="card-footer">
        <form method="POST" action="{{ route('admin.cms.inquiries.read', $inquiry) }}" class="d-inline">
            @csrf
            <button class="btn btn-outline-primary" {{ $inquiry->is_read ? 'disabled' : '' }}>Mark as Read</button>
        </form>
        <form method="POST" action="{{ route('admin.cms.inquiries.replied', $inquiry) }}" class="d-inline">
            @csrf
            <button class="btn btn-outline-success" {{ $inquiry->is_replied ? 'disabled' : '' }}>Mark as Replied</button>
        </form>
    </div>
</div>
@endsection
