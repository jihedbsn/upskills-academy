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
                                    <i data-feather="image" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Carousel Management</h4>
                                <p class="text-white-50 mb-0">Manage your multilingual carousels</p>
                            </div>
                        </div>
                        <a href="{{ route('carousels.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            New Carousel
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

                    <!-- Filters and search -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('carousels.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search in carousels..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i data-feather="search"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Carousels table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Slug (AR)</th>
                                    <th>Slug (EN)</th>
                                    <th>Description (AR)</th>
                                    <th>Description (EN)</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($carousels as $carousel)
                                    <tr>
                                        <td>{{ $carousel->id }}</td>
                                        <td>
                                            @if($carousel->image)
                                                <img src="{{ asset('uploads/carousels/' . $carousel->image) }}" alt="Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i data-feather="image" class="text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ Str::limit($carousel->slug_ar, 30) }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ Str::limit($carousel->slug_en, 30) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ Str::limit($carousel->description_ar, 50) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ Str::limit($carousel->description_en, 50) }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $carousel->order }}</span>
                                        </td>
                                        <td>
                                            @if($carousel->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('carousels.show', $carousel) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('carousels.edit', $carousel) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('carousels.destroy', $carousel) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this carousel?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="image" class="mb-2" style="width: 48px; height: 48px;"></i>
                                                <p>No carousels found</p>
                                                <a href="{{ route('carousels.create') }}" class="btn btn-primary">
                                                    <i data-feather="plus" class="me-2"></i>
                                                    Create the first carousel
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($carousels->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $carousels->appends(request()->query())->links() }}
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
