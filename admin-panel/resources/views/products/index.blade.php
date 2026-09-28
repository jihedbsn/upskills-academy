@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="search" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Search Products</h4>
                            <p class="text-white-50 mb-0">Quickly find the products you are looking for</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}" class="row">
                        <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                    placeholder="Search by category name, teacher, country, language or status..." 
                    value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search" class="me-1"></i> Search
                            </button>
                        </div>
                    </form>
                    @if(request('search'))
                        <div class="mt-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i data-feather="x" class="me-1"></i> Clear search
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products Table -->
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
                                <h4 class="card-title mb-1">
                                    @if(request('search'))
                                        Search results for "{{ request('search') }}"
                                    @else
                                        Product List
                                    @endif
                                </h4>
                                <p class="text-white-50 mb-0">
                                    {{ $products->total() }} product(s) found
                                    @php
                                        $productsWithImages = $products->where('image', '!=', null)->count();
                                    @endphp
                                    <span class="badge bg-success ms-2">{{ $productsWithImages }} with image</span>
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            Add product
                        </a>
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

                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Period</th>
                                        <th>Teacher</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th>Breuillant</th>
                                        <th>Creation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <div class="product-image-container">
                                                    @if($product->image)
                                                        <img src="{{ asset('uploads/products/images/' . $product->image) }}" 
                                                             alt="Product Image" 
                                                             class="product-image rounded shadow-sm" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #e9ecef;"
                                                             onerror="this.src='{{ asset('assets/img/courses/1.jpg') }}'">
                                                        <div class="image-overlay">
                                                            <span>View</span>
                                                        </div>
                                                        <div class="position-absolute top-0 end-0">
                                                            <span class="badge bg-success" style="font-size: 8px;">IMG</span>
                                                        </div>
                                                    @else
                                                        <div class="no-image-placeholder avatar bg-light rounded d-flex align-items-center justify-content-center shadow-sm" 
                                                             style="width: 60px; height: 60px; border: 2px dashed #dee2e6;">
                                                            <i data-feather="image" class="text-muted" style="width: 24px; height: 24px;"></i>
                                                        </div>
                                                        <div class="position-absolute top-0 end-0">
                                                            <span class="badge bg-warning" style="font-size: 8px;">NO IMG</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->category)
                                                    <span class="badge bg-info">{{ $product->category->titre }}</span>
                                                @else
                                                    <span class="text-muted">Not defined</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bold text-primary">
                                                        @if($product->variations->where('langue', 'ar')->first())
                                                            {{ $product->variations->where('langue', 'ar')->first()->name }}
                                                        @else
                                                            <span class="text-muted">Arabic name not defined</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small">
                                                        @if($product->variations->where('langue', 'en')->first())
                                                            {{ $product->variations->where('langue', 'en')->first()->name }}
                                                        @else
                                                            <span class="text-muted">English name not defined</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->prix)
                                                    <strong class="text-success">${{ number_format($product->prix, 2) }}</strong>
                                                @else
                                                    <span class="badge bg-success">Free</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->period ?? 'Not defined' }}</td>
                                            <td>
                                                @if($product->teacher)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i data-feather="user" class="text-muted" style="width: 14px; height: 14px;"></i>
                                                        </div>
                                                        <span class="fw-medium">{{ $product->teacher->nom }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not defined</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->country)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i data-feather="globe" class="text-white" style="width: 14px; height: 14px;"></i>
                                                        </div>
                                                        <span class="fw-medium">{{ $product->country->titre }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not defined</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->iscach)
                                                    <span class="badge bg-success">Visible</span>
                                                @else
                                                    <span class="badge bg-secondary">Hidden</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->breuillant)
                                                    <span class="badge bg-warning">
                                                        <i data-feather="edit" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Brouillon
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i data-feather="check-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Finalisé
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="View details">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('products.duplicate', $product) }}" class="btn btn-sm btn-outline-primary" title="Duplicate course">
                                                        <i data-feather="copy" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('products.quizzes.index', $product) }}" class="btn btn-sm btn-outline-success" title="Quizzes and Exams">
                                                        <i data-feather="help-circle" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="package" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">
                                @if(request('search'))
                                    No product found
                                @else
                                    No product available
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if(request('search'))
                                    No product matches your search "{{ request('search') }}".
                                    <br>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-2">
                                        <i data-feather="list" class="me-1"></i> View all products
                                    </a>
                                @else
                                    There are currently no products in the database.
                                    <br>
                                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">
                                        <i data-feather="plus" class="me-1"></i> Create the first product
                                    </a>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for image preview -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Product image" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* Styles for image display */
.product-image {
    transition: all 0.3s ease;
    cursor: pointer;
}

.product-image:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 10;
    position: relative;
}

.product-image-container {
    position: relative;
    display: inline-block;
}

.product-image-container:hover .image-overlay {
    opacity: 1;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
    font-size: 10px;
    font-weight: bold;
}

.no-image-placeholder {
    transition: all 0.3s ease;
    cursor: pointer;
}

.no-image-placeholder:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff !important;
    transform: scale(1.05);
}

/* Table enhancements */
.table td {
    vertical-align: middle;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6e6b7b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image click to open modal
    const productImages = document.querySelectorAll('.product-image');
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    const modalImage = document.getElementById('modalImage');
    
    productImages.forEach(function(img) {
        img.addEventListener('click', function() {
            modalImage.src = this.src;
            modal.show();
        });
    });
    
    // Handle click on "no image" placeholders
    const noImagePlaceholders = document.querySelectorAll('.no-image-placeholder');
    noImagePlaceholders.forEach(function(placeholder) {
        placeholder.addEventListener('click', function() {
            alert('This product has no image. You can add one by editing the product.');
        });
    });
});
</script>
