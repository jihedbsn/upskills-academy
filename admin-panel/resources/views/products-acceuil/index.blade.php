@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="home" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Homepage Products Management</h4>
                                <p class="text-white-50 mb-0">Manage products displayed on the homepage</p>
                            </div>
                        </div>
                        <a href="{{ route('products-acceuil.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            Add Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Homepage products table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Added Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productsAcceuil as $productAcceuil)
                                    <tr>
                                        <td>{{ $productAcceuil->id }}</td>
                                        <td>
                                            @if($productAcceuil->product && $productAcceuil->product->image)
                                                <img src="{{ asset('uploads/products/images/' . $productAcceuil->product->image) }}" alt="Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i data-feather="package" class="text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($productAcceuil->product)
                                                @php
                                                    $variation = $productAcceuil->product->variations->first();
                                                @endphp
                                                <div class="fw-bold">{{ $variation ? $variation->name : 'Product without variation' }}</div>
                                            @else
                                                <span class="text-danger">Deleted product</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($productAcceuil->product)
                                                <span class="badge bg-success">${{ $productAcceuil->product->prix }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $productAcceuil->order }}</span>
                                        </td>
                                        <td>
                                            @if($productAcceuil->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $productAcceuil->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products-acceuil.show', $productAcceuil) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('products-acceuil.edit', $productAcceuil) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('products-acceuil.destroy', $productAcceuil) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this product from the homepage?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="home" class="mb-2" style="width: 48px; height: 48px;"></i>
                                                <p>No products on the homepage</p>
                                                <a href="{{ route('products-acceuil.create') }}" class="btn btn-primary">
                                                    <i data-feather="plus" class="me-2"></i>
                                                    Add the first product
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($productsAcceuil->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $productsAcceuil->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6e6b7b;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 6px;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
