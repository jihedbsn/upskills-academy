@extends('layouts.app')

@section('title', 'Affiliate Performance Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-chart-bar me-2"></i>
                        Affiliate Performance Report
                    </h4>
                    <div>
                        <button class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fa fa-print me-1"></i>Print
                        </button>
                        <a href="{{ route('affiliate-partners.export') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-download me-1"></i>Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-calendar me-2"></i>Date Range Filter</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('affiliate-partners.report') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                               value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                               value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="partner_id" class="form-label">Specific Partner</label>
                                        <select class="form-select" id="partner_id" name="partner_id">
                                            <option value="">All Partners</option>
                                            @foreach($partners ?? [] as $partner)
                                                <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                                                    {{ $partner->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fa fa-filter me-1"></i>Generate Report
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Overview Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Total Earnings</h6>
                                    <h2 class="mb-0">{{ number_format($overallStats['total_earnings'] ?? 0, 2) }} $</h2>
                                    <small><i class="fa fa-chart-line"></i> Earned</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Total Paid</h6>
                                    <h2 class="mb-0">{{ number_format($overallStats['total_paid'] ?? 0, 2) }} $</h2>
                                    <small><i class="fa fa-money-bill"></i> Paid Out</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Active Partners</h6>
                                    <h2 class="mb-0">{{ $overallStats['active_partners'] ?? 0 }}</h2>
                                    <small><i class="fa fa-users"></i> Partners</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Pending Amount</h6>
                                    <h2 class="mb-0">{{ number_format($overallStats['pending_amount'] ?? 0, 2) }} $</h2>
                                    <small><i class="fa fa-calculator"></i> Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performers -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa fa-trophy me-2"></i>Top Performing Partners</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Partner</th>
                                                    <th>Coupons</th>
                                                    <th>Total Uses</th>
                                                    <th>Revenue Generated</th>
                                                    <th>Commission Earned</th>
                                                    <th>Conversion Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($partnerPerformance as $index => $performance)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') }} fs-6">
                                                            #{{ $index + 1 }}
                                                        </span>
                                                    </td>
                                                    <td><strong>{{ $performance['partner']->name }}</strong></td>
                                                    <td>{{ $performance['coupons_count'] }}</td>
                                                    <td>{{ $performance['total_uses'] }}</td>
                                                    <td><strong>{{ number_format($performance['revenue_generated'], 2) }} $</strong></td>
                                                    <td><span class="badge bg-success">{{ number_format($performance['commission_earned'], 2) }} $</span></td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-primary" style="width: {{ min(100, $performance['conversion_rate']) }}%">
                                                                {{ $performance['conversion_rate'] }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Breakdown - Removed fake data, to be implemented with real monthly statistics later -->
                    {{-- Monthly breakdown section temporarily disabled until real monthly data aggregation is implemented --}}
                        </div>
                    </div>
                    --}}

                    <!-- Coupon Performance -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0"><i class="fa fa-tags me-2"></i>Top Performing Coupons</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Partner</th>
                                                    <th>Type</th>
                                                    <th>Uses</th>
                                                    <th>Revenue</th>
                                                    <th>Commission</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topCoupons ?? [] as $coupon)
                                                <tr>
                                                    <td><code class="bg-light p-1 rounded">{{ $coupon->code }}</code></td>
                                                    <td>{{ $coupon->affiliatePartner->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            {{ $coupon->type === 'percentage' ? $coupon->valeur.'%' : '$'.$coupon->valeur }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $coupon->usage_count }}</td>
                                                    <td><strong>{{ number_format($coupon->total_revenue ?? 0, 2) }} $</strong></td>
                                                    <td><span class="badge bg-success">{{ number_format($coupon->total_commission ?? 0, 2) }} $</span></td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        <i class="fa fa-info-circle me-2"></i>No coupon data available for the selected period.
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .card-header .btn, nav, .sidebar { display: none !important; }
    .card { border: 1px solid #ddd !important; page-break-inside: avoid; }
}
</style>
@endpush
