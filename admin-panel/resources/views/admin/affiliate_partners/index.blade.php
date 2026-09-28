@extends('layouts.app')

@section('title', 'Affiliate Partners Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-handshake me-2"></i>
                        Affiliate Partners Management
                    </h4>
                    <div>
                        <a href="{{ route('affiliate-partners.create') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-plus me-1"></i>New Partner
                        </a>
                        <a href="{{ route('affiliate-partners.report') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-chart-bar me-1"></i>Performance Report
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Quick Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Partners</h6>
                                            <h3 class="mb-0">{{ $partners->total() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-users fa-2x"></i>
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
                                            <h6 class="card-title">Active Partners</h6>
                                            <h3 class="mb-0">{{ $partners->where('status', 'approved')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-check-circle fa-2x"></i>
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
                                            <h6 class="card-title">Pending Approval</h6>
                                            <h3 class="mb-0">{{ $partners->where('status', 'pending')->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-clock fa-2x"></i>
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
                                            <h6 class="card-title">Total Commissions</h6>
                                            <h3 class="mb-0">{{ number_format($totalCommissions ?? 0, 0) }} $</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa fa-filter me-2"></i>Filters & Search
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('affiliate-partners.index') }}">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                               placeholder="Name, email, or company..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="sort" class="form-label">Sort By</label>
                                        <select class="form-select" id="sort" name="sort">
                                            <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                                            <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                                            <option value="commission_desc" {{ request('sort') == 'commission_desc' ? 'selected' : '' }}>Highest Commission</option>
                                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fa fa-search me-1"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Partners Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Partner</th>
                                    <th>Contact</th>
                                    <th>Commission Rate</th>
                                    <th>Total Earned</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partners as $partner)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $partner->name }}</strong>
                                                @if($partner->company)
                                                    <br><small class="text-muted">{{ $partner->company }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fa fa-envelope me-1"></i>{{ $partner->email }}<br>
                                            @if($partner->phone)
                                                <i class="fa fa-phone me-1"></i>{{ $partner->phone }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $partner->commission_rate }}%</span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($partner->total_earned ?? 0, 2) }} $</strong>
                                        <br><small class="text-muted">{{ $partner->coupons_count ?? 0 }} coupons</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'active' => 'success',
                                                'suspended' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$partner->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            {{ ucfirst($partner->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $partner->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('affiliate-partners.show', $partner) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('affiliate-partners.edit', $partner) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            @if($partner->status === 'pending')
                                                <form action="{{ route('affiliate-partners.approve', $partner) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Approve">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($partner->status === 'approved')
                                                <form action="{{ route('affiliate-partners.suspend', $partner) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Suspend">
                                                        <i class="fa fa-pause"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($partner->status === 'suspended')
                                                <form action="{{ route('affiliate-partners.reactivate', $partner) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Reactivate">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('affiliate-partners.destroy', $partner) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this partner?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-handshake fa-3x mb-3"></i>
                                            <h5>No affiliate partners found</h5>
                                            <p>Start by adding your first affiliate partner.</p>
                                            <a href="{{ route('affiliate-partners.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus me-2"></i>Add Partner
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($partners->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $partners->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
@endpush
