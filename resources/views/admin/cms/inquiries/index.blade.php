@extends('admin.layout')

@section('title', 'Contact Inquiries - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Contact Inquiries</h3>
    </div>

    <div class="card-body border-bottom py-3">
        <x-admin.listing-toolbar
            :search="$search ?? ''"
            search-placeholder="Search inquiries..."
            :per-page="$perPage ?? 20"
            :filters="[
                ['field' => 'is_read', 'label' => 'Read', 'options' => ['1' => 'Read', '0' => 'Unread']],
                ['field' => 'is_replied', 'label' => 'Replied', 'options' => ['1' => 'Replied', '0' => 'Not Replied']],
            ]"
            :applied-filters="$appliedFilters ?? []"
        />
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Subject</th><th>Read</th><th>Replied</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                <tr class="{{ !$inq->is_read ? 'table-active' : '' }}">
                    <td>{{ $inq->name }}</td>
                    <td>{{ $inq->email }}</td>
                    <td>{{ Str::limit($inq->subject ?? 'No subject', 40) }}</td>
                    <td>{!! $inq->is_read ? '<span class="badge text-bg-success">Yes</span>' : '<span class="badge text-bg-warning">New</span>' !!}</td>
                    <td>{!! $inq->is_replied ? '<span class="badge text-bg-success">Yes</span>' : '<span class="badge text-bg-secondary">No</span>' !!}</td>
                    <td>{{ $inq->created_at->format('M d, H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.cms.inquiries.show', $inq) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <form method="POST" action="{{ route('admin.cms.inquiries.destroy', $inq) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No inquiries</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($inquiries->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">Showing {{ $inquiries->firstItem() }}–{{ $inquiries->lastItem() }} of {{ $inquiries->total() }} results</p>
        <div class="ms-auto">{{ $inquiries->links() }}</div>
    </div>
    @endif
</div>
@endsection
