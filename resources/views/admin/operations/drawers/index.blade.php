@extends('admin.layout')

@section('title', 'POS Drawers - MealHQ')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Open New Drawer</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.operations.drawers.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Opening Balance</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('opening_balance') is-invalid @enderror" name="opening_balance" value="{{ old('opening_balance', '0') }}" required>
                            @error('opening_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <input type="text" class="form-control" name="notes" value="{{ old('notes') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Open Drawer</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Drawers</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Opened By</th><th>Opened At</th><th>Opening Balance</th><th>Expected</th><th>Actual</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($drawers as $drawer)
                        <tr>
                            <td>{{ $drawer->user->name ?? '-' }}</td>
                            <td>{{ $drawer->opened_at->format('M d, H:i') }}</td>
                            <td>\${{ number_format($drawer->opening_balance, 2) }}</td>
                            <td>\${{ number_format($drawer->expected_closing_balance ?? 0, 2) }}</td>
                            <td>\${{ number_format($drawer->actual_closing_balance ?? 0, 2) }}</td>
                            <td><span class="badge bg-{{ $drawer->is_open ? 'green' : 'secondary' }}">{{ $drawer->is_open ? 'Open' : 'Closed' }}</span></td>
                            <td>
                                @if($drawer->is_open)
                                <form method="POST" action="{{ route('admin.operations.drawers.close', $drawer) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning">Close</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No drawers</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
