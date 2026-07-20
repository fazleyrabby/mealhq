@extends('admin.layout')

@section('title', 'Reports & Analytics - MealHQ')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Reports &amp; Analytics</h2>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group" aria-label="Date range">
                    @foreach([7,30,90] as $r)
                        <a href="{{ route('admin.reports.index', ['range' => $r]) }}"
                           class="btn btn-sm {{ $range == $r ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $r }} days</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        {{-- KPI cards --}}
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar"><svg class="icon" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Revenue ({{ $range }}d)</div>
                                <div class="text-muted">${{ number_format($totalRevenue, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-blue text-white avatar"><svg class="icon" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Orders ({{ $range }}d)</div>
                                <div class="text-muted">{{ $totalOrders }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-cyan text-white avatar"><svg class="icon" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Avg Order Value</div>
                                <div class="text-muted">${{ number_format($avgOrderValue, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-orange text-white avatar"><svg class="icon" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4v6c0 5-3.5 7.5-9 10-5.5-2.5-9-5-9-10z"/></svg></span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Inventory Value</div>
                                <div class="text-muted">${{ number_format($inventoryValue, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards mb-3">
            {{-- Sales chart --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sales — last {{ $range }} days</h3>
                    </div>
                    <div class="card-body">
                        @if(count($sales))
                        <div class="d-flex align-items-end" style="height:220px;gap:2px">
                            @foreach($sales as $day)
                                @php $h = $maxSales > 0 ? max(2, ($day['total_sales'] / $maxSales) * 100) : 2; @endphp
                                <div class="flex-fill text-center" style="height:100%;display:flex;flex-direction:column;justify-content:flex-end" title="{{ $day['date'] }}: ${{ number_format($day['total_sales'], 2) }} ({{ $day['total_orders'] }} orders)">
                                    <div style="height:{{ $h }}%;background:#206bc4;border-radius:3px 3px 0 0" class="position-relative">
                                        @if($loop->last || $loop->first)
                                        <span class="text-muted" style="position:absolute;bottom:100%;left:50%;transform:translateX(-50%);font-size:.625rem;white-space:nowrap">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center text-muted py-5">No completed or served orders in this period.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Orders by source --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Orders by Source</h3></div>
                    <div class="card-body">
                        @php $sourceTotal = collect($bySource)->sum('count') ?: 1; @endphp
                        @forelse($bySource as $s)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-capitalize">{{ str_replace('_', ' ', $s['source']) }}</span>
                                    <span class="text-muted">{{ $s['count'] }} ({{ number_format(($s['count'] / $sourceTotal) * 100, 0) }}%)</span>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-primary" style="width:{{ ($s['count'] / $sourceTotal) * 100 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">No data</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Top selling items --}}
        <div class="card">
            <div class="card-header"><h3 class="card-title">Top Selling Items ({{ $range }}d)</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Qty Sold</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topItems as $item)
                        <tr>
                            <td>{{ $item['item_name'] }}</td>
                            <td class="text-end">{{ $item['total_quantity'] }}</td>
                            <td class="text-end">${{ number_format($item['total_revenue'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No sales yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
