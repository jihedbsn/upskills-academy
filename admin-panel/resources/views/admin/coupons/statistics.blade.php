@extends('layouts.app')

@section('title', 'Coupon Statistics - Marketing Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-chart-bar me-2"></i>
                        Coupon Statistics & Analytics
                    </h4>
                    <div>
                        <a href="{{ route('coupons.index') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-arrow-left me-1"></i>Back to Coupons
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Period Filter -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex gap-2">
                                <select name="period" class="form-select" onchange="this.form.submit()">
                                    <option value="30" {{ request('period', 30) == 30 ? 'selected' : '' }}>Last 30 Days</option>
                                    <option value="7" {{ request('period') == 7 ? 'selected' : '' }}>Last 7 Days</option>
                                    <option value="90" {{ request('period') == 90 ? 'selected' : '' }}>Last 90 Days</option>
                                    <option value="365" {{ request('period') == 365 ? 'selected' : '' }}>Last Year</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Overview Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Uses</h6>
                                            <h3 class="mb-0">{{ number_format($stats['total_uses']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-ticket-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Discount</h6>
                                            <h3 class="mb-0">${{ number_format($stats['total_discount_given'], 2) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Revenue Generated</h6>
                                            <h3 class="mb-0">${{ number_format($stats['total_revenue'], 2) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Stacked Uses</h6>
                                            <h3 class="mb-0">{{ number_format($stats['stacked_usage']) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-layer-group fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Coupons -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fa fa-trophy me-2"></i>Top Performing Coupons
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($stats['top_coupons']->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Uses</th>
                                                        <th>Revenue</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($stats['top_coupons'] as $coupon)
                                                    <tr>
                                                        <td><code>{{ $coupon['code'] }}</code></td>
                                                        <td>{{ $coupon['name'] }}</td>
                                                        <td>{{ $coupon['uses'] }}</td>
                                                        <td>${{ number_format($coupon['revenue'], 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center py-3">No coupon usage data for this period.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fa fa-chart-area me-2"></i>Daily Usage Trend
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($stats['daily_usage']) && $stats['daily_usage']->count() > 0)
                                        <canvas id="dailyUsageChart" width="400" height="200"></canvas>
                                    @else
                                        <p class="text-muted text-center py-3">No daily usage data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Analysis -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Customer Type Distribution</h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($stats['customer_distribution']))
                                        @foreach($stats['customer_distribution'] as $type => $count)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-capitalize">{{ $type }}</span>
                                            <span class="badge bg-primary">{{ $count }}</span>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No customer data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Discount Type Performance</h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($stats['discount_types']))
                                        @foreach($stats['discount_types'] as $type => $count)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-capitalize">{{ $type }}</span>
                                            <span class="badge bg-success">{{ $count }}</span>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No discount type data available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Quick Stats</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Average Discount:</span>
                                        <span class="badge bg-info">
                                            ${{ $stats['total_uses'] > 0 ? number_format($stats['total_discount_given'] / $stats['total_uses'], 2) : '0.00' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Avg Revenue per Use:</span>
                                        <span class="badge bg-warning">
                                            ${{ $stats['total_uses'] > 0 ? number_format($stats['total_revenue'] / $stats['total_uses'], 2) : '0.00' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Stacking Rate:</span>
                                        <span class="badge bg-secondary">
                                            {{ $stats['total_uses'] > 0 ? number_format(($stats['stacked_usage'] / $stats['total_uses']) * 100, 1) : '0' }}%
                                        </span>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($stats['daily_usage']) && $stats['daily_usage']->count() > 0)
    // Daily Usage Chart
    const ctx = document.getElementById('dailyUsageChart').getContext('2d');
    const dailyUsageChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['daily_usage']->pluck('date')->toArray()) !!},
            datasets: [{
                label: 'Daily Coupon Uses',
                data: {!! json_encode($stats['daily_usage']->pluck('uses')->toArray()) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif
});
</script>
@endpush
