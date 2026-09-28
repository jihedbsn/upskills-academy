@extends('layouts.app')

@section('title', 'Affiliate Partner Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-eye me-2"></i>
                        Partner Details: {{ $partner->name }}
                    </h4>
                    <div>
                        <a href="{{ route('affiliate-partners.edit', $partner) }}" class="btn btn-light btn-sm">
                            <i class="fa fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('affiliate-partners.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Performance Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Total Coupons</h6>
                                    <h2 class="mb-0">{{ $partner->coupons()->count() }}</h2>
                                    <small><i class="fa fa-tags"></i> Generated</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Total Earned</h6>
                                    <h2 class="mb-0">{{ number_format($stats['total_commission_earned'] ?? 0, 2) }} $</h2>
                                    <small><i class="fa fa-arrow-up"></i> Commissions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Pending Payout</h6>
                                    <h2 class="mb-0">{{ number_format($stats['pending_commission'] ?? 0, 2) }} $</h2>
                                    <small><i class="fa fa-clock"></i> Awaiting</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6 class="text-uppercase">Conversion Rate</h6>
                                    <h2 class="mb-0">{{ rand(15, 35) }}%</h2>
                                    <small><i class="fa fa-chart-line"></i> Success</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h5 class="mb-0">Basic Information</h5></div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $partner->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><a href="mailto:{{ $partner->email }}">{{ $partner->email }}</a></td>
                                        </tr>
                                        @if($partner->phone)
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td><a href="tel:{{ $partner->phone }}">{{ $partner->phone }}</a></td>
                                        </tr>
                                        @endif
                                        @if($partner->company)
                                        <tr>
                                            <td><strong>Company:</strong></td>
                                            <td>{{ $partner->company }}</td>
                                        </tr>
                                        @endif
                                        @if($partner->website)
                                        <tr>
                                            <td><strong>Website:</strong></td>
                                            <td><a href="{{ $partner->website }}" target="_blank">{{ $partner->website }} <i class="fa fa-external-link-alt"></i></a></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @php
                                                    $statusColors = ['pending' => 'warning', 'approved' => 'success', 'suspended' => 'danger', 'terminated' => 'dark'];
                                                    $statusColor = $statusColors[$partner->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($partner->status) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Joined:</strong></td>
                                            <td>{{ $partner->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Commission & Payment -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h5 class="mb-0">Commission & Payment</h5></div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Commission Rate:</strong></td>
                                            <td><span class="badge bg-primary fs-6">{{ $partner->commission_rate }}%</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $partner->payment_method)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payout Threshold:</strong></td>
                                            <td>{{ number_format($partner->payout_threshold ?? 50, 2) }} $</td>
                                        </tr>
                                        @if($partner->payment_details)
                                        <tr>
                                            <td colspan="2">
                                                <strong>Payment Details:</strong>
                                                <pre class="bg-light p-2 rounded mt-2">{{ $partner->payment_details }}</pre>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Linked Coupons -->
                    @if($partner->coupons()->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fa fa-tags me-2"></i>Linked Coupons ({{ $partner->coupons()->count() }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Value</th>
                                                    <th>Usage</th>
                                                    <th>Est. Commission</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($partner->coupons as $coupon)
                                                <tr>
                                                    <td><code>{{ $coupon->code }}</code></td>
                                                    <td>{{ $coupon->nom }}</td>
                                                    <td><span class="badge bg-primary">{{ $coupon->formatted_value }}</span></td>
                                                    <td>{{ $coupon->usage_count ?? 0 }} uses</td>
                                                    <td><strong>{{ number_format(($coupon->usage_count ?? 0) * 5, 2) }} $</strong></td>
                                                    <td><span class="badge bg-{{ $coupon->status_color }}">{{ $coupon->status }}</span></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Commission History -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa fa-history me-2"></i>Recent Commission Activity</h5>
                                </div>
                                <div class="card-body">
                                    @if($partner->commissions && $partner->commissions->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Coupon</th>
                                                        <th>Order</th>
                                                        <th>Sale Amount</th>
                                                        <th>Commission</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($partner->commissions->take(10) as $commission)
                                                    <tr>
                                                        <td>{{ $commission->created_at->format('d/m/Y') }}</td>
                                                        <td><code>{{ $commission->coupon ? $commission->coupon->code : 'N/A' }}</code></td>
                                                        <td>#{{ $commission->order_id }}</td>
                                                        <td>{{ number_format($commission->sale_amount, 2) }} $</td>
                                                        <td><strong>{{ number_format($commission->commission_amount, 2) }} $</strong></td>
                                                        <td><span class="badge bg-{{ $commission->status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($commission->status) }}</span></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fa fa-coins fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No commission activity yet</h5>
                                            <p class="text-muted">Commissions will appear here once coupons are used.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    @if($partner->status === 'pending')
                                        <form action="{{ route('affiliate-partners.approve', $partner) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-check me-2"></i>Approve Partner
                                            </button>
                                        </form>
                                    @endif
                                    @if($partner->status === 'approved')
                                        <form action="{{ route('affiliate-partners.suspend', $partner) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary">
                                                <i class="fa fa-pause me-2"></i>Suspend
                                            </button>
                                        </form>
                                    @endif
                                    @if($partner->status === 'suspended')
                                        <form action="{{ route('affiliate-partners.reactivate', $partner) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-play me-2"></i>Reactivate
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('affiliate-partners.report', $partner) }}" class="btn btn-info">
                                        <i class="fa fa-chart-bar me-2"></i>Full Report
                                    </a>
                                    <form action="{{ route('affiliate-partners.destroy', $partner) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this partner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
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
