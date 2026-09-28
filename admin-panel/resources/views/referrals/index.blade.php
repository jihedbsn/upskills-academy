@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold">Referral Management</h3>
            <p class="text-muted mb-0">Track referrals, rewards, and conversions.</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white h-100" style="background: linear-gradient(135deg,#7367f0,#ce9ffc);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                        <small>Total Referrals</small>
                    </div>
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white h-100" style="background: linear-gradient(135deg,#ff9f43,#ffcba4);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['pending'] }}</h4>
                        <small>Pending</small>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white h-100" style="background: linear-gradient(135deg,#28c76f,#81fbb8);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['completed'] }}</h4>
                        <small>Completed</small>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white h-100" style="background: linear-gradient(135deg,#ea5455,#f08182);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">${{ number_format($stats['totalRewards'], 2) }}</h4>
                        <small>Total Rewards</small>
                    </div>
                    <i class="fas fa-gift fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white h-100" style="background: linear-gradient(135deg,#00cfe8,#79e2f2);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">${{ number_format($stats['cashRewards'], 2) }}</h4>
                        <small>Cash Rewards</small>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card text-white h-100" style="background: linear-gradient(135deg,#4839eb,#9e95f5);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">${{ number_format($stats['creditRewards'], 2) }}</h4>
                        <small>Credit Rewards</small>
                    </div>
                    <i class="fas fa-coins fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart + Filters row --}}
    <div class="row g-3 mb-4">
        {{-- Chart --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Referrals Over Time (last 30 days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="referralChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.referrals.index') }}">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reward Type</label>
                            <select name="reward_type" class="form-select">
                                <option value="">All</option>
                                <option value="cash"   {{ request('reward_type') === 'cash'   ? 'selected' : '' }}>Cash</option>
                                <option value="credit" {{ request('reward_type') === 'credit' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">From</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">To</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-1"></i> Apply
                            </button>
                            <a href="{{ route('admin.referrals.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Referrals Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Referrals</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Referrer</th>
                            <th>Referred User</th>
                            <th>Status</th>
                            <th>Reward</th>
                            <th>Reward Type</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                        <tr>
                            <td class="text-muted small">{{ $referral->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $referral->referrer?->full_name ?? '—' }}</div>
                                <div class="text-muted small">{{ $referral->referrer?->email }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $referral->referred?->full_name ?? '—' }}</div>
                                <div class="text-muted small">{{ $referral->referred?->email }}</div>
                            </td>
                            <td>
                                @if($referral->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>${{ number_format($referral->reward_amount, 2) }}</td>
                            <td>
                                @php $reward = $referral->rewards->where('role','referrer')->first(); @endphp
                                @if($reward)
                                    <span class="badge {{ $reward->type === 'cash' ? 'bg-info' : 'bg-purple' }}" style="{{ $reward->type === 'credit' ? 'background:#7367f0' : '' }}">
                                        {{ ucfirst($reward->type) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $referral->created_at->format('M d, Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#overrideModal"
                                    data-id="{{ $referral->id }}"
                                    data-status="{{ $referral->status }}"
                                    data-amount="{{ $referral->reward_amount }}"
                                    data-type="{{ $reward?->type ?? 'credit' }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.referrals.destroy', $referral->id) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this referral?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No referrals found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($referrals->hasPages())
        <div class="card-footer">
            {{ $referrals->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Override Modal --}}
<div class="modal fade" id="overrideModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="overrideForm">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Override Referral</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" id="override_status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reward Amount ($)</label>
                        <input type="number" name="reward_amount" id="override_amount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reward Type</label>
                        <select name="type" id="override_type" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart
        const ctx = document.getElementById('referralChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Total',
                        data: @json($chartData['total']),
                        backgroundColor: 'rgba(115,103,240,0.6)',
                        borderColor: '#7367f0',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Completed',
                        data: @json($chartData['completed']),
                        backgroundColor: 'rgba(40,199,111,0.6)',
                        borderColor: '#28c76f',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Override modal population
        const overrideModal = document.getElementById('overrideModal');
        if (overrideModal) {
            overrideModal.addEventListener('show.bs.modal', function (e) {
                const btn = e.relatedTarget;
                const id  = btn.dataset.id;
                document.getElementById('overrideForm').action = `/admin/referrals/${id}/override`;
                document.getElementById('override_status').value = btn.dataset.status;
                document.getElementById('override_amount').value = btn.dataset.amount;
                document.getElementById('override_type').value   = btn.dataset.type;
            });
        }
    });
</script>
@endsection
