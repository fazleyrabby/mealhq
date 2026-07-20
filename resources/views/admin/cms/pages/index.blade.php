@extends('admin.layout')

@section('title', 'CMS Pages - MealHQ')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pages</h3>
        <div class="card-actions">
            <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary">Add Page</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr><th>Title</th><th>Slug</th><th>Sections</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>{{ $page->sections->count() }}</td>
                    <td>{!! $page->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.cms.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.cms.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No pages yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pages->hasPages())<div class="card-footer">{{ $pages->links() }}</div>@endif
</div>
@endsection
