@extends('layouts.app')

@section('title', 'Coupons Management - Marketing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-tags me-2"></i>
                        Coupons Management - Marketing
                    </h4>
                    <div>
                        <a href="{{ route('coupons.create') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-plus me-1"></i>New Coupon
                        </a>
                        <a href="{{ route('coupons.statistics') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-chart-bar me-1"></i>Statistics
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

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>{{ session('warning') }}
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
                                            <h6 class="card-title">Total Coupons</h6>
                                            <h3 class="mb-0">{{ $coupons->total() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-tags fa-2x"></i>
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
                                            <h6 class="card-title">Active Coupons</h6>
                                            <h3 class="mb-0">{{ $coupons->where('is_active', true)->count() }}</h3>
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
                                            <h6 class="card-title">Expired Coupons</h6>
                                            <h3 class="mb-0">{{ $coupons->where('date_fin', '<', now())->count() }}</h3>
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
                                            <h6 class="card-title">Total Usage</h6>
                                            <h3 class="mb-0">{{ $coupons->sum('usage_count') }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa fa-filter me-2"></i>Filters & Search
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('coupons.index') }}">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Search</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                               placeholder="Code or name..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">All</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="type" class="form-label">Discount Type</label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="">All</option>
                                            <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="customer_type" class="form-label">Customer Type</label>
                                        <select class="form-select" id="customer_type" name="customer_type">
                                            <option value="">All</option>
                                            <option value="all" {{ request('customer_type') == 'all' ? 'selected' : '' }}>All Customers</option>
                                            <option value="new" {{ request('customer_type') == 'new' ? 'selected' : '' }}>New</option>
                                            <option value="returning" {{ request('customer_type') == 'returning' ? 'selected' : '' }}>Returning</option>
                                            <option value="vip" {{ request('customer_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 mb-3">
                                        <label class="form-label d-block">Stackable</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_stackable"
                                                   name="is_stackable" value="1" {{ request('is_stackable') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_stackable">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label d-block">Auto-Apply</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="auto_apply"
                                                   name="auto_apply" value="1" {{ request('auto_apply') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_apply">Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-redo me-1"></i>Reset
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search me-1"></i>Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Coupons Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Period</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Usage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                <tr>
                                    <td>
                                        <code class="bg-light p-1 rounded">{{ $coupon->code }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $coupon->nom }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ $coupon->formatted_value }}</span>
                                        @if($coupon->type === 'percentage')
                                            <br><small class="text-muted">Percentage</small>
                                        @else
                                            <br><small class="text-muted">Fixed Amount</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $coupon->validity_period }}</small>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span class="badge bg-primary fs-6">
                                                <i class="fa fa-box me-1"></i>
                                                {{ $coupon->detailles->count() }}
                                                {{ $coupon->detailles->count() == 1 ? 'Product' : 'Products' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->status_color }}">
                                            {{ $coupon->status }}
                                        </span>
                                        @if($coupon->is_active)
                                            <br><small class="text-success">✓ Active</small>
                                        @else
                                            <br><small class="text-danger">✗ Inactive</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php $limit = $coupon->limit_utilise; @endphp
                                        <strong>{{ $coupon->usage_count }}</strong>
                                        @if($limit)
                                            / {{ $limit }}
                                        @else
                                            <small class="text-muted">(unlimited)</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('coupons.duplicate', $coupon) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicate">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('coupons.toggle', $coupon) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $coupon->is_active ? 'secondary' : 'success' }}" title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fa fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
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
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-tags fa-3x mb-3"></i>
                                            <h5>No coupons found</h5>
                                            <p>Start by creating your first discount coupon.</p>
                                            <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus me-2"></i>Create Coupon
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($coupons->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $coupons->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coupon Deactivation Modal -->
<div class="modal fade" id="deactivationModal" tabindex="-1" aria-labelledby="deactivationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="deactivationModalLabel">
                    <i class="fa fa-exclamation-triangle me-2"></i>Coupon Deactivated
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    <strong>The coupon could not be deleted because it has been used in orders.</strong>
                </p>
                <p class="mb-0">
                    To preserve order history integrity, the coupon has been automatically deactivated instead.
                    It will no longer be available for new purchases, but existing order records remain intact.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-check me-2"></i>I Understand
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Show deactivation popup if triggered
    @if(session('show_popup'))
        $('#deactivationModal').modal('show');
    @endif
});
</script>
@endpush
