@extends('layouts.app')

@section('title', 'Package Details - Marketing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-box me-2"></i>
                        Package Details
                    </h4>
                    <div>
                        <a href="{{ route('packages.edit', $package) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('packages.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-arrow-left me-1"></i>Back to Packages
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Package Information -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Package Information</h5>
                                </div>
                                <div class="card-body">
                                    @if($package->image)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset('/uploads/package/' . $package->image) }}"
                                                 alt="{{ $package->title }}"
                                                 class="img-fluid rounded"
                                                 style="max-height: 200px;">
                                        </div>
                                    @endif

                                    <h4 class="card-title">{{ $package->title }}</h4>

                                    @if($package->description)
                                        <p class="card-text">{{ $package->description }}</p>
                                    @endif

                                    <div class="mb-3">
                                        <span class="badge bg-{{ $package->status_color }} fs-6">
                                            {{ $package->status }}
                                        </span>
                                    </div>

                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                <h6 class="mb-1 text-primary">{{ $package->packageProducts->count() }}</h6>
                                                <small class="text-muted">Products</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-2">
                                                @php
                                                    $hasPercentage = $package->packageProducts->where('discount_type', 'percentage')->count() > 0;
                                                    $hasFixed = $package->packageProducts->where('discount_type', 'fixed')->count() > 0;

                                                    if ($hasPercentage && !$hasFixed) {
                                                        $avgPercentage = $package->packageProducts->where('discount_type', 'percentage')->avg('valeur_reduction');
                                                        echo '<h6 class="mb-1 text-success">' . number_format($avgPercentage, 1) . '%</h6>';
                                                    } elseif ($hasFixed && !$hasPercentage) {
                                                        $avgFixed = $package->packageProducts->where('discount_type', 'fixed')->avg('fixed_discount');
                                                        echo '<h6 class="mb-1 text-success">$' . number_format($avgFixed, 2) . '</h6>';
                                                    } else {
                                                        echo '<h6 class="mb-1 text-success">Mixed</h6>';
                                                    }
                                                @endphp
                                                <small class="text-muted">Avg Reduction</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <small class="text-muted">
                                        <i class="fa fa-calendar me-1"></i>
                                        Created: {{ $package->created_at->format('d/m/Y H:i') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fa fa-edit me-1"></i>
                                        Updated: {{ $package->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Products List -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Products in Package</h5>
                                </div>
                                <div class="card-body">
                                    @if($package->packageProducts->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Original Price</th>
                                                        <th>Reduction</th>
                                                        <th>Savings</th>
                                                        <th>Discounted Price</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($package->packageProducts as $packageProduct)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($packageProduct->product->image)
                                                                    <img src="{{ asset('/uploads/products/images/' . $packageProduct->product->image) }}"
                                                                         alt="{{ $packageProduct->product->titre }}"
                                                                         class="rounded me-3"
                                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                         style="width: 50px; height: 50px;">
                                                                        <i class="fa fa-image text-muted"></i>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $packageProduct->product->titre }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $packageProduct->product->type_course }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold">${{ number_format($packageProduct->product->prix, 2) }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning fs-6">
                                                                @if($packageProduct->discount_type === 'fixed')
                                                                    -${{ number_format($packageProduct->fixed_discount, 2) }}
                                                                @else
                                                                    -{{ $packageProduct->valeur_reduction }}%
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $originalPrice = $packageProduct->product->prix;
                                                                if ($packageProduct->discount_type === 'fixed') {
                                                                    $discountAmount = $packageProduct->fixed_discount ?? 0;
                                                                } else {
                                                                    $discountAmount = ($originalPrice * ($packageProduct->valeur_reduction ?? 0)) / 100;
                                                                }
                                                            @endphp
                                                            <span class="text-success fw-bold">
                                                                ${{ number_format($discountAmount, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $discountedPrice = $originalPrice - $discountAmount;
                                                            @endphp
                                                            <span class="fw-bold text-success">
                                                                ${{ number_format($discountedPrice, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $packageProduct->status_color }}">
                                                                {{ $packageProduct->status }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Summary -->
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h5 class="text-primary">Total Original Price</h5>
                                                        @php
                                                            $totalOriginal = $package->packageProducts->sum(function($pp) {
                                                                return $pp->product->prix;
                                                            });
                                                        @endphp
                                                        <h3 class="mb-0">${{ number_format($totalOriginal, 2) }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center">
                                                        <h5 class="text-success">Total Savings</h5>
                                                        @php
                                                            $totalSavings = $package->packageProducts->sum(function($pp) {
                                                                $originalPrice = $pp->product->prix;
                                                                if ($pp->discount_type === 'fixed') {
                                                                    return $pp->fixed_discount ?? 0;
                                                                } else {
                                                                    return ($originalPrice * ($pp->valeur_reduction ?? 0)) / 100;
                                                                }
                                                            });
                                                        @endphp
                                                        <h3 class="mb-0">${{ number_format($totalSavings, 2) }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fa fa-cube fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No products in this package</h5>
                                            <p class="text-muted">Add products to make this package useful.</p>
                                        </div>
                                    @endif
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
