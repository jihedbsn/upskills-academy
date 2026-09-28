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
                                    <i data-feather="shield" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Roles & Permissions</h4>
                                <p class="text-white-50 mb-0">Manage user roles and their permissions</p>
                            </div>
                        </div>
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-2"></i>
                            New Role
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i data-feather="check-circle" class="me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i data-feather="alert-circle" class="me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search and Filter -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('roles.index') }}" class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control" placeholder="Search roles..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Per Page</label>
                                <select name="per_page" class="form-select">
                                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i data-feather="filter" class="me-2" style="width: 14px; height: 14px;"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Role</th>
                                        <th>Description</th>
                                        <th>Permissions</th>
                                        <th>Users</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i data-feather="shield" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $role->display_name }}</div>
                                                        <small class="text-muted">{{ $role->name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ Str::limit($role->description, 50) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $role->admins->count() }} admins</span>
                                            </td>
                                            <td>
                                                @if($role->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="View">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    @if($role->name !== 'super_admin')
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRole({{ $role->id }})" title="Delete">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role) }}" method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }} results
                            </div>
                            <div>
                                {{ $roles->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="shield" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">No roles found</h5>
                            <p class="text-muted">Start by creating your first role</p>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Create Role
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteRole(id) {
    if (confirm('Are you sure you want to delete this role?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

// Initialize Feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endsection
