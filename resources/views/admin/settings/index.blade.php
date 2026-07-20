@extends('admin.layout')

@section('title', 'Settings - MealHQ')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Company Profile</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings') }}">
                    @csrf
                    <div class="row g-3">
                        @foreach($company as $setting)
                        <div class="col-md-6">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', Str::after($setting->key, 'company_'))) }}</label>
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        </div>
                        @endforeach
                        @foreach($orders as $setting)
                        <div class="col-md-4">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            @if(in_array($setting->key, ['enable_delivery', 'enable_takeaway', 'enable_online_ordering', 'enable_reservations']))
                            <select class="form-select" name="settings[{{ $setting->key }}]">
                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Disabled</option>
                            </select>
                            @else
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                            @endif
                        </div>
                        @endforeach
                        @foreach($billing as $setting)
                        <div class="col-md-4">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        </div>
                        @endforeach
                        @foreach($reservations as $setting)
                        <div class="col-md-4">
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Business Hours</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.hours') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead><tr><th>Day</th><th>Opening Time</th><th>Closing Time</th><th>Closed</th></tr></thead>
                            <tbody>
                                @foreach($hours as $hour)
                                <tr>
                                    <td class="text-capitalize fw-medium">{{ $hour->day_of_week }}</td>
                                    <td><input type="time" class="form-control" name="hours[{{ $hour->id }}][opening_time]" value="{{ $hour->opening_time ? \Carbon\Carbon::parse($hour->opening_time)->format('H:i') : '09:00' }}"></td>
                                    <td><input type="time" class="form-control" name="hours[{{ $hour->id }}][closing_time]" value="{{ $hour->closing_time ? \Carbon\Carbon::parse($hour->closing_time)->format('H:i') : '22:00' }}"></td>
                                    <td><label class="form-check"><input type="checkbox" class="form-check-input" name="hours[{{ $hour->id }}][is_closed]" value="1" {{ $hour->is_closed ? 'checked' : '' }}></label></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Update Hours</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Tax Rates</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Name</th><th>Rate</th><th>Type</th><th>Default</th><th>Active</th></tr></thead>
                    <tbody>
                        @foreach($taxRates as $tax)
                        <tr>
                            <td>{{ $tax->name }}</td>
                            <td>{{ $tax->rate }}%</td>
                            <td>{{ ucfirst($tax->type) }}</td>
                            <td>{!! $tax->is_default ? '<span class="badge bg-green">Yes</span>' : '' !!}</td>
                            <td>{!! $tax->is_active ? '<span class="badge bg-green">Active</span>' : '<span class="badge bg-red">Inactive</span>' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.tax-rates') }}" class="row g-3">
                    @csrf
                    <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Tax name" required></div>
                    <div class="col-md-3"><input type="number" step="0.01" class="form-control" name="rate" placeholder="Rate %" required></div>
                    <div class="col-md-3">
                        <select class="form-select" name="type">
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Add</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('input[type=checkbox][name$="[is_closed]"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const row = this.closest('tr');
        row.querySelectorAll('input[type=time]').forEach(inp => inp.disabled = this.checked);
    });
});
</script>
@endpush
