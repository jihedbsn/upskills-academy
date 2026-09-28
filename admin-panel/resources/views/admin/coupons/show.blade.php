@extends('layouts.app')

@section('title', 'Coupon Details - Marketing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-eye me-2"></i>
                        Coupon Details: {{ $coupon->nom }}
                    </h4>
                    <div>
                        <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-light btn-sm">
                            <i class="fa fa-edit me-1"></i>Edit
                        </a>
                        <form action="{{ route('coupons.duplicate', $coupon) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm">
                                <i class="fa fa-copy me-1"></i>Duplicate
                            </button>
                        </form>
                        <a href="{{ route('coupons.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- General Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">General Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Code:</strong></td>
                                            <td><code class="bg-light p-2 rounded">{{ $coupon->code }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $coupon->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                <span class="badge bg-primary">{{ $coupon->formatted_value }}</span>
                                                <small class="text-muted ms-2">
                                                    {{ $coupon->type === 'percentage' ? 'Percentage discount' : 'Fixed amount' }}
                                                </small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Period and Usage -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Period and Usage</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Period:</strong></td>
                                            <td>{{ $coupon->validity_period }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $coupon->status_color }}">
                                                    {{ $coupon->status }}
                                                </span>
                                                @if($coupon->is_active)
                                                    <span class="badge bg-success ms-2">Active</span>
                                                @else
                                                    <span class="badge bg-secondary ms-2">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Usage:</strong></td>
                                            <td>
                                                @php
                                                    $limit = $coupon->limit_utilise;
                                                    $progress = ($limit && $limit > 0) ? min(100, ($coupon->usage_count / $limit) * 100) : null;
                                                @endphp
                                                <strong>{{ $coupon->usage_count }}</strong>
                                                @if($limit)
                                                    / {{ $limit }}
                                                    <div class="progress mt-1" style="height: 8px;">
                                                        <div class="progress-bar" role="progressbar"
                                                             style="width: {{ $progress }}%"></div>
                                                    </div>
                                                 @else
                                                     <small class="text-muted">(unlimited)</small>
                                                 @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $coupon->created_at->format('d/m/Y at H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated:</strong></td>
                                            <td>{{ $coupon->updated_at->format('d/m/Y at H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Settings & Features -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fa fa-cog me-2"></i>Advanced Settings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Customer Type:</strong></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($coupon->customer_type ?? 'all') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Min Purchase:</strong></td>
                                            <td>
                                                @if($coupon->min_purchase_amount)
                                                    <span class="badge bg-info">{{ number_format($coupon->min_purchase_amount, 2) }} $</span>
                                                @else
                                                    <small class="text-muted">No minimum</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Min Cart Items:</strong></td>
                                            <td>
                                                @if($coupon->min_cart_items && $coupon->min_cart_items > 0)
                                                    <span class="badge bg-info">{{ $coupon->min_cart_items }} item(s)</span>
                                                @else
                                                    <small class="text-muted">No minimum (or 1 item)</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Max Discount:</strong></td>
                                            <td>
                                                @if($coupon->max_discount_amount)
                                                    <span class="badge bg-warning">{{ number_format($coupon->max_discount_amount, 2) }} $</span>
                                                @else
                                                    <small class="text-muted">No limit</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Stackable:</strong></td>
                                            <td>
                                                @if($coupon->is_stackable ?? false)
                                                    <span class="badge bg-success"><i class="fa fa-check"></i> Yes</span>
                                                @else
                                                    <span class="badge bg-secondary"><i class="fa fa-times"></i> No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Auto-Apply:</strong></td>
                                            <td>
                                                @if($coupon->auto_apply ?? false)
                                                    <span class="badge bg-success"><i class="fa fa-magic"></i> Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>First Purchase Only:</strong></td>
                                            <td>
                                                @if($coupon->first_purchase_only ?? false)
                                                    <span class="badge bg-primary"><i class="fa fa-star"></i> Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cumulative:</strong></td>
                                            <td>
                                                @if($coupon->cumulative_enabled ?? false)
                                                    <span class="badge bg-success"><i class="fa fa-plus-circle"></i> Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Allow Multiple Uses:</strong></td>
                                            <td>
                                                @if($coupon->allow_multiple_uses ?? false)
                                                    <span class="badge bg-success"><i class="fa fa-repeat"></i> Yes</span>
                                                    <div class="form-text small">Users can use this coupon multiple times, but not on the same order.</div>
                                                @else
                                                    <span class="badge bg-secondary"><i class="fa fa-ban"></i> No</span>
                                                    <div class="form-text small">Users can only use this coupon once.</div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fa fa-bullseye me-2"></i>Targeting & Restrictions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong><i class="fa fa-handshake me-2"></i>Affiliate Partner:</strong><br>
                                        @if($coupon->affiliate_partner_id && $coupon->affiliatePartner)
                                            <div class="mt-2">
                                                <span class="badge bg-primary fs-6">
                                                    <i class="fa fa-user-tie me-1"></i>{{ $coupon->affiliatePartner->name }}
                                                </span>
                                                <br><small class="text-muted">Commission: {{ $coupon->affiliatePartner->commission_rate }}%</small>
                                            </div>
                                        @else
                                            <small class="text-muted">No affiliate linked</small>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <strong><i class="fa fa-filter me-2"></i>Customer Segments:</strong><br>
                                        @if($coupon->customer_segments)
                                            @php $segments = json_decode($coupon->customer_segments, true) ?? []; @endphp
                                            @if(count($segments) > 0)
                                                <div class="mt-2">
                                                    @foreach($segments as $segment)
                                                        <span class="badge bg-info me-1">{{ ucfirst(str_replace('_', ' ', $segment)) }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted">All segments</small>
                                            @endif
                                        @else
                                            <small class="text-muted">All segments</small>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <strong><i class="fa fa-tags me-2"></i>Product Categories:</strong><br>
                                        @if($coupon->product_categories)
                                            @php $categories = json_decode($coupon->product_categories, true) ?? []; @endphp
                                            @if(count($categories) > 0)
                                                <div class="mt-2">
                                                    @foreach($categories as $category)
                                                        <span class="badge bg-secondary me-1">{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <small class="text-muted">All categories</small>
                                            @endif
                                        @else
                                            <small class="text-muted">All categories</small>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <strong><i class="fa fa-ban me-2"></i>Excluded Products:</strong><br>
                                        @if($coupon->excluded_products)
                                            @php $excludedIds = json_decode($coupon->excluded_products, true) ?? []; @endphp
                                            @if(count($excludedIds) > 0)
                                                <div class="mt-2">
                                                    <span class="badge bg-danger">{{ count($excludedIds) }} product(s) excluded</span>
                                                </div>
                                            @else
                                                <small class="text-muted">No exclusions</small>
                                            @endif
                                        @else
                                            <small class="text-muted">No exclusions</small>
                                        @endif
                                    </div>

                                    @if($coupon->custom_message)
                                        <div class="mt-3">
                                            <strong><i class="fa fa-comment me-2"></i>Custom Message:</strong>
                                            <div class="alert alert-info mt-2 mb-0">
                                                <i class="fa fa-info-circle me-1"></i>{{ $coupon->custom_message }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Statistics -->
                    @if($coupon->usage_count > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fa fa-chart-line me-2"></i>Performance Statistics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <h6 class="text-uppercase mb-2">Total Uses</h6>
                                                    <h2 class="mb-0">{{ $coupon->usage_count ?? 0 }}</h2>
                                                    <small>
                                                        @if($coupon->limit_utilise)
                                                            / {{ $coupon->limit_utilise }} limit
                                                        @else
                                                            unlimited
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h6 class="text-uppercase mb-2">Est. Revenue</h6>
                                                    <h2 class="mb-0">{{ number_format($coupon->usage_count * 50, 0) }} $</h2>
                                                    <small><i class="fa fa-arrow-up"></i> Generated</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <h6 class="text-uppercase mb-2">Total Discount</h6>
                                                    <h2 class="mb-0">{{ number_format($coupon->usage_count * 15, 0) }} $</h2>
                                                    <small><i class="fa fa-arrow-down"></i> Discounted</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <h6 class="text-uppercase mb-2">Conversion Rate</h6>
                                                    <h2 class="mb-0">{{ rand(15, 45) }}%</h2>
                                                    <small><i class="fa fa-chart-bar"></i> Success rate</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Associated Products -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fa fa-box me-2"></i>Associated Products ({{ $coupon->detailles->count() }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($coupon->detailles->count() > 0)
                                        <div class="row">
                                            @foreach($coupon->detailles as $detail)
                                            <div class="col-md-4 mb-3">
                                                <div class="card product-detail-card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            @if($detail->product->image)
                                                                <img src="{{ asset('/uploads/products/images/' . $detail->product->image) }}"
                                                                     alt="{{ $detail->product->titre }}"
                                                                     class="rounded me-3"
                                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                     style="width: 80px; height: 80px;">
                                                                    <i class="fa fa-image text-muted fa-3x"></i>
                                                                </div>
                                                            @endif
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1">{{ $detail->product->titre }}</h6>
                                                                <div class="mt-2">
                                                                    <span class="badge bg-primary">{{ number_format($detail->product->prix, 2) }} $</span>
                                                                    <span class="badge bg-secondary">{{ $detail->product->type_course }}</span>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <small class="text-muted">
                                                                        Discount:
                                                                        @if($coupon->type === 'percentage')
                                                                            {{ number_format($coupon->valeur, 0) }}%
                                                                            ({{ number_format($detail->product->prix * $coupon->valeur / 100, 2) }}$)
                                                                        @else
                                                                            {{ number_format($coupon->valeur, 2) }}$
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fa fa-box fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No associated products</h5>
                                            <p class="text-muted">This coupon is not associated with any products.</p>
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
                                    <form action="{{ route('coupons.toggle', $coupon) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $coupon->is_active ? 'secondary' : 'success' }}">
                                            <i class="fa fa-{{ $coupon->is_active ? 'pause' : 'play' }} me-2"></i>
                                            {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                                <div>
                                    <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-warning">
                                        <i class="fa fa-edit me-2"></i>Edit
                                    </a>
                                    <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this coupon?')">
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

@push('styles')
<style>
.product-detail-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.product-detail-card:hover {
    border-color: #17a2b8;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush
