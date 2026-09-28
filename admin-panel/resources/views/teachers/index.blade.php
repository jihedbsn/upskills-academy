@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Teachers Management</h1>
        <div class="page-actions">
            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                New Teacher
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="users" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Teachers List</h4>
                            <p class="text-white-50 mb-0">Manage all teachers and professors</p>
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

                    @if($teachers->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Teacher</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Creation Date</th>
                                        <th class="actions-column">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teachers as $teacher)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#{{ $teacher->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3" style="width: 36px; height: 36px;">
                                                        <img src="{{ $teacher->image_url }}" alt="Photo" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;" />
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium text-dark">{{ $teacher->nom }}</div>
                                                        <small class="text-muted">Teacher</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="mail" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ $teacher->email }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="phone" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ $teacher->telephone ?? 'Not provided' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="calendar" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ $teacher->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="actions-column">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-outline-info" title="View details">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this teacher?')">
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
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $teachers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="users" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">No teachers found</h5>
                            <p class="text-muted">Start by creating your first teacher</p>
                            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Create a teacher
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
