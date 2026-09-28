@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="package" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Product Details</h4>
                                <p class="text-white-50 mb-0">Complete information about product #{{ $product->id }}</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Main information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="info" class="me-2"></i>
                                        General Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>ID:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-primary">#{{ $product->id }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Category:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($product->category)
                                                <span class="badge bg-info">{{ $product->category->name }}</span>
                                            @else
                                                <span class="text-muted">Not defined</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Period:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $product->period ?? 'Not defined' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Points:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($product->point)
                                                <span class="badge bg-primary">{{ $product->point }} pts</span>
                                            @else
                                                <span class="text-muted">0 pts</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Video:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $product->video ?? 'Not defined' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Promo Points:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $product->promo_points ?? 'Not defined' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher and country information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="users" class="me-2"></i>
                                        Teacher & Country
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Teacher:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($product->teacher)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i data-feather="user" class="text-muted" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $product->teacher->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">Not defined</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Country:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($product->country)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i data-feather="globe" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $product->country->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">Not defined</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Government:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($product->goverrnement)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Price:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($product->prix)
                                                <strong class="text-success">${{ number_format($product->prix, 2) }}</strong>
                                            @else
                                                <span class="text-muted">Free</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Start date:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $product->date_debut ?? 'Not defined' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>End date:</strong>
                                        </div>
                                        <div class="col-sm-8">
                                            {{ $product->date_fin ?? 'Not defined' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Language variations -->
                    @if($product->variations && $product->variations->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Language Variations
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($product->variations as $variation)
                                                <div class="col-md-6">
                                                    <div class="card border">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">
                                                                <i data-feather="{{ $variation->langue == 'ar' ? 'align-right' : 'align-left' }}" class="me-2"></i>
                                                                {{ $variation->langue == 'ar' ? 'Arabic' : 'English' }}
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Name:</strong></div>
                                                                <div class="col-sm-8">{{ $variation->name }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Slug:</strong></div>
                                                                <div class="col-sm-8"><code>{{ $variation->slug }}</code></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Short description:</strong></div>
                                                                <div class="col-sm-8">{{ $variation->short_description }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Course types -->
                    @if($product->types && $product->types->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                        <i data-feather="tag" class="me-2"></i>
                                        Course Types
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($product->types as $type)
                                                <div class="col-md-6">
                                                    <div class="card border">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">
                                                                <i data-feather="{{ $type->type == 'online' ? 'wifi' : 'home' }}" class="me-2"></i>
                                                                {{ $type->type == 'online' ? 'En ligne' : 'Présentiel' }}
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Content:</strong></div>
                                                                <div class="col-sm-8">{{ $type->content }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Meta title:</strong></div>
                                                                <div class="col-sm-8">{{ $type->meta_title }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Meta keywords:</strong></div>
                                                                <div class="col-sm-8">{{ $type->meta_keyword }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-sm-4"><strong>Meta description:</strong></div>
                                                                <div class="col-sm-8">{{ $type->meta_description }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Students -->
                    @if($product->studies && $product->studies->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                        <i data-feather="users" class="me-2"></i>
                                        Students ({{ $product->studies->count() }})
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nom (AR)</th>
                                                        <th>Nom (EN)</th>
                                                        <th>Language</th>
                                                        <th>Resource</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->studies as $study)
                                                        <tr>
                                                            <td>{{ $study->name_ar }}</td>
                                                            <td>{{ $study->name_en }}</td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $study->lang }}</span>
                                                            </td>
                                                            <td>
                                                                @if($study->resource)
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                            <i data-feather="file" class="text-white" style="width: 14px; height: 14px;"></i>
                                                                        </div>
                                                                        <span class="fw-medium">{{ basename($study->resource->file) }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">No resource</span>
                                                                @endif
                                                            </td>
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

                    <!-- Creation details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="clock" class="me-2"></i>
                                        Creation Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <strong>Created at:</strong>
                                                </div>
                                                <div class="col-sm-8">
                                                    {{ $product->created_at->format('d/m/Y H:i:s') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <strong>Updated at:</strong>
                                                </div>
                                                <div class="col-sm-8">
                                                    {{ $product->updated_at->format('d/m/Y H:i:s') }}
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
        </div>
    </div>
</div>
@endsection
