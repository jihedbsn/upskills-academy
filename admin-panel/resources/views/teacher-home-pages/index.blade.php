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
                                    <i class="fa fa-graduation-cap text-white" style="font-size: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Homepage Teachers Management</h4>
                                <p class="text-white-50 mb-0">Manage teachers displayed on the homepage</p>
                            </div>
                        </div>
                        <a href="{{ route('teacher-home-pages.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>
                            Add Teacher
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Teachers table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name (Arabic)</th>
                                    <th>Name (English)</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $teacher)
                                    <tr>
                                        <td>{{ $teacher->id }}</td>
                                        <td>
                                            @if($teacher->image)
                                                <img src="{{ $teacher->image_url }}" alt="Teacher" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fa fa-user text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $teacher->name_ar }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $teacher->name_en }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $teacher->order }}</span>
                                        </td>
                                        <td>
                                            @if($teacher->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('teacher-home-pages.show', $teacher) }}" class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('teacher-home-pages.edit', $teacher) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('teacher-home-pages.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this teacher from the homepage?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
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
                                                <i class="fa fa-graduation-cap mb-2" style="font-size: 48px;"></i>
                                                <p>No teachers on the homepage</p>
                                                <a href="{{ route('teacher-home-pages.create') }}" class="btn btn-primary">
                                                    <i class="fa fa-plus me-2"></i>
                                                    Add the first teacher
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($teachers->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $teachers->links() }}
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

