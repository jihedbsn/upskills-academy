@extends('layouts.app')

@section('title', 'Package Statistics - Marketing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-chart-bar me-2"></i>
                        Package Statistics
                    </h4>
                    <a href="{{ route('packages.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-arrow-left me-1"></i>Back to Packages
                    </a>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Packages</h6>
                                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-box fa-2x"></i>
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
                                            <h6 class="card-title">Active Packages</h6>
                                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Inactive Packages</h6>
                                            <h3 class="mb-0">{{ $stats['inactive'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-pause-circle fa-2x"></i>
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
                                            <h6 class="card-title">Total Products</h6>
                                            <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-cube fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Active Products</h6>
                                            <h3 class="mb-0">{{ $stats['active_products'] }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-play-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Average Reduction</h6>
                                            <h3 class="mb-0">{{ number_format($stats['average_reduction'], 1) }}%</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-percentage fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Statistics Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Package Details</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $packages = \App\Models\Package::with('packageProducts.product')->get();
                            @endphp
                            
                            @if($packages->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Package</th>
                                                <th>Products</th>
                                                <th>Avg Reduction</th>
                                                <th>Total Original Price</th>
                                                <th>Total Savings</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($packages as $package)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($package->image)
                                                            <img src="{{ asset('/uploads/package/' . $package->image) }}" 
                                                                 alt="{{ $package->title }}" 
                                                                 class="rounded me-3" 
                                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                                 style="width: 50px; height: 50px;">
                                                                <i class="fa fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <strong>{{ $package->title }}</strong>
                                                            @if($package->description)
                                                                <br><small class="text-muted">{{ Str::limit($package->description, 30) }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $package->packageProducts->count() }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ number_format($package->packageProducts->avg('valeur_reduction'), 1) }}%</span>
                                                </td>
                                                <td>
                                                    <strong>${{ number_format($package->packageProducts->sum(function($pp) { return $pp->product->prix; }), 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold">${{ number_format($package->packageProducts->sum('discount_amount'), 2) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $package->status_color }}">
                                                        {{ $package->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No packages found</h5>
                                    <p class="text-muted">Create packages to see statistics.</p>
                                    <a href="{{ route('packages.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus me-2"></i>Create Package
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
