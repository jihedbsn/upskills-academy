@extends('layouts.app')

@section('title', 'Abandoned Carts Report')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">🛒 Abandoned Carts Report</h2>
                        <p class="text-muted mb-0">Track and recover abandoned shopping carts</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" onclick="exportData()">
                            <i class="fas fa-download"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Abandoned</h6>
                                <h3 class="mb-0">{{ $stats['total_abandoned'] }}</h3>
                            </div>
                            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Converted</h6>
                                <h3 class="mb-0">{{ $stats['converted'] }}</h3>
                                <small>({{ $stats['conversion_rate'] }}%)</small>
                            </div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Recovered Revenue</h6>
                                <h3 class="mb-0">${{ number_format($stats['recovered_revenue'], 2) }}</h3>
                            </div>
                            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Potential Revenue</h6>
                                <h3 class="mb-0">${{ number_format($stats['potential_revenue'], 2) }}</h3>
                            </div>
                            <i class="fas fa-coins fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reminder Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                        <h5>First Reminders</h5>
                        <h3>{{ $stats['first_reminders_sent'] }}</h3>
                        <small class="text-muted">Sent after 1 hour</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                        <h5>Second Reminders</h5>
                        <h3>{{ $stats['second_reminders_sent'] }}</h3>
                        <small class="text-muted">Sent after 24 hours</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-2x text-warning mb-2"></i>
                        <h5>Third Reminders</h5>
                        <h3>{{ $stats['third_reminders_sent'] }}</h3>
                        <small class="text-muted">Sent after 3 days (with discount)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Abandoned vs Converted Carts (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="abandonedCartsChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('abandoned-carts.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                            <option value="not_converted" {{ request('status') === 'not_converted' ? 'selected' : '' }}>Not Converted</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Abandoned Carts Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Abandoned Carts List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Abandoned At</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Reminders</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abandonedCarts as $cart)
                                <tr>
                                    <td>{{ $cart->student->first_name }} {{ $cart->student->last_name }}</td>
                                    <td>{{ $cart->student->email }}</td>
                                    <td>{{ $cart->abandoned_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $cart->items_count }}</td>
                                    <td>${{ number_format($cart->total_amount, 2) }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <span class="badge bg-{{ $cart->first_reminder_sent_at ? 'success' : 'secondary' }}" title="First reminder">1</span>
                                            <span class="badge bg-{{ $cart->second_reminder_sent_at ? 'success' : 'secondary' }}" title="Second reminder">2</span>
                                            <span class="badge bg-{{ $cart->third_reminder_sent_at ? 'success' : 'secondary' }}" title="Third reminder">3</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($cart->converted)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Converted
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('abandoned-carts.show', $cart->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if(!$cart->converted)
                                            <form action="{{ route('abandoned-carts.send-reminder', $cart->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" title="Send next reminder">
                                                    <i class="fas fa-paper-plane"></i> Send
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No abandoned carts found for the selected period.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $abandonedCarts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart
const ctx = document.getElementById('abandonedCartsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [
            {
                label: 'Abandoned Carts',
                data: {!! json_encode($chartData['abandoned']) !!},
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4
            },
            {
                label: 'Converted',
                data: {!! json_encode($chartData['converted']) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("abandoned-carts.export") }}?' + params.toString();
}
</script>

<style>
.opacity-50 {
    opacity: 0.5;
}
.gap-1 {
    gap: 0.25rem;
}
</style>
@endsection
