@extends('layouts.app')

@section('title', 'Packages Management - Marketing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-box me-2"></i>
                        Packages Management - Marketing
                    </h4>
                    <div>
                        <a href="{{ route('packages.create') }}" class="btn btn-light btn-sm">
                            <i class="fa fa-plus me-1"></i>New Package
                        </a>
                        <a href="{{ route('packages.statistics') }}" class="btn btn-outline-light btn-sm">
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

                    <!-- Quick Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Packages</h6>
                                            <h3 class="mb-0">{{ $packages->total() }}</h3>
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
                                            <h3 class="mb-0">{{ $packages->where('is_active', true)->count() }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-check-circle fa-2x"></i>
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
                                            <h3 class="mb-0">{{ $packages->sum(function($package) { return $package->packageProducts->count(); }) }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-cube fa-2x"></i>
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
                                            <h6 class="card-title">Avg Reduction</h6>
                                            <h3 class="mb-0">{{ number_format($packages->avg(function($package) { return $package->packageProducts->avg('valeur_reduction'); }), 1) }}%</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-percentage fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Packages Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Products</th>
                                    <th>Avg Reduction</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                <tr>
                                    <td>
                                        @if($package->image)
                                            <img src="{{ asset('/uploads/package/' . $package->image) }}"
                                                 alt="{{ $package->title }}"
                                                 class="rounded"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px;">
                                                <i class="fa fa-image text-muted fa-2x"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $package->title }}</strong>
                                        @if($package->description)
                                            <br><small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $package->packageProducts->count() }} product(s)</span>
                                        @if($package->packageProducts->count() <= 3)
                                            <br>
                                            @foreach($package->packageProducts->take(3) as $packageProduct)
                                                <small class="d-block">
                                                    {{ $packageProduct->product->titre }}
                                                    @if($packageProduct->discount_type === 'fixed')
                                                        <span class="text-success">(-${{ number_format($packageProduct->fixed_discount, 2) }})</span>
                                                    @else
                                                        <span class="text-success">(-{{ $packageProduct->valeur_reduction }}%)</span>
                                                    @endif
                                                </small>
                                            @endforeach
                                        @else
                                            <br><small class="text-muted">See details</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $hasPercentage = $package->packageProducts->where('discount_type', 'percentage')->count() > 0;
                                            $hasFixed = $package->packageProducts->where('discount_type', 'fixed')->count() > 0;

                                            if ($hasPercentage && !$hasFixed) {
                                                $avgPercentage = $package->packageProducts->where('discount_type', 'percentage')->avg('valeur_reduction');
                                                echo '<span class="badge bg-primary fs-6">' . number_format($avgPercentage, 1) . '%</span>';
                                            } elseif ($hasFixed && !$hasPercentage) {
                                                $avgFixed = $package->packageProducts->where('discount_type', 'fixed')->avg('fixed_discount');
                                                echo '<span class="badge bg-primary fs-6">$' . number_format($avgFixed, 2) . '</span>';
                                            } else {
                                                echo '<span class="badge bg-info fs-6">Mixed</span>';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $package->status_color }}">
                                            {{ $package->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $package->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('packages.show', $package) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('packages.edit', $package) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('packages.toggle', $package) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $package->is_active ? 'secondary' : 'success' }}" title="{{ $package->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fa fa-{{ $package->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package?')">
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
                                            <i class="fa fa-box fa-3x mb-3"></i>
                                            <h5>No packages found</h5>
                                            <p>Start by creating your first product package.</p>
                                            <a href="{{ route('packages.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus me-2"></i>Create Package
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($packages->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $packages->links() }}
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
