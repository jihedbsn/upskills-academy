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
                                    <i data-feather="star" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Customer Testimonials Management</h4>
                                <p class="text-white-50 mb-0">Manage customer testimonials for the homepage</p>
                            </div>
                        </div>
                        <a href="{{ route('avis-acceuil.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            New Testimonial
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
                            <form method="GET" action="{{ route('avis-acceuil.index') }}" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Search testimonials..." value="{{ request('search') }}">
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

                    <!-- Testimonials table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Client</th>
                                    <th>Testimonial (EN)</th>
                                    <th>Testimonial (AR)</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($avis as $avi)
                                    <tr>
                                        <td>{{ $avi->id }}</td>
                                        <td>
                                            @if($avi->image)
                                                <img src="{{ asset('uploads/avis/' . $avi->image) }}" alt="Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i data-feather="user" class="text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $avi->client }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ Str::limit($avi->avis_en, 50) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-muted">{{ Str::limit($avi->avis_ar, 50) }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $avi->order }}</span>
                                        </td>
                                        <td>
                                            @if($avi->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('avis-acceuil.show', $avi) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('avis-acceuil.edit', $avi) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('avis-acceuil.destroy', $avi) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this testimonial?')">
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
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="star" class="mb-2" style="width: 48px; height: 48px;"></i>
                                                <p>No testimonials found</p>
                                                <a href="{{ route('avis-acceuil.create') }}" class="btn btn-primary">
                                                    <i data-feather="plus" class="me-2"></i>
                                                    Create the first testimonial
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($avis->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $avis->appends(request()->query())->links() }}
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
