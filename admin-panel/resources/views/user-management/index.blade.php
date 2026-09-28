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
                                <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="users" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">User Management</h4>
                                <p class="text-white-50 mb-0">Manage system users and assign roles</p>
                            </div>
                        </div>
                        <a href="{{ route('user-management.create') }}" class="btn btn-success">
                            <i data-feather="plus" class="me-2"></i>
                            New User
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
                        <form method="GET" action="{{ route('user-management.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                                <select name="role_id" class="form-select">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i data-feather="filter" class="me-2" style="width: 14px; height: 14px;"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary">
                                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <span class="text-white fw-bold">{{ strtoupper(substr($user->first_name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $user->full_name }}</div>
                                                        <small class="text-muted">{{ $user->phone }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary me-2">{{ $user->getRoleName() }}</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                            onclick="openRoleModal({{ $user->id }}, '{{ $user->full_name }}', {{ $user->role_id }})"
                                                            title="Change Role">
                                                        <i data-feather="edit-2" style="width: 12px; height: 12px;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                           onchange="toggleUserStatus({{ $user->id }})"
                                                           {{ $user->is_active ? 'checked' : '' }}
                                                           {{ $user->isSuperAdmin() || $user->id === auth()->id() ? 'disabled' : '' }}>
                                                    <label class="form-check-label">
                                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Never' }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('user-management.show', $user) }}" class="btn btn-sm btn-outline-info" title="View">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('user-management.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser({{ $user->id }})" title="Delete">
                                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <form id="delete-form-{{ $user->id }}" action="{{ route('user-management.destroy', $user) }}" method="POST" style="display:none;">
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
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-lg bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="users" class="text-muted" style="width: 32px; height: 32px;"></i>
                            </div>
                            <h5 class="text-muted">No users found</h5>
                            <a href="{{ route('user-management.create') }}" class="btn btn-success mt-3">
                                <i data-feather="plus" class="me-2"></i>
                                Create User
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Assignment Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Assign role to: <strong id="userName"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Select Role</label>
                    <select id="roleSelect" class="form-select">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="assignRole()">Assign Role</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;

function openRoleModal(userId, userName, currentRoleId) {
    currentUserId = userId;
    document.getElementById('userName').textContent = userName;
    document.getElementById('roleSelect').value = currentRoleId;
    new bootstrap.Modal(document.getElementById('roleModal')).show();
}

function assignRole() {
    const roleId = document.getElementById('roleSelect').value;

    fetch(`/user-management/${currentUserId}/assign-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ role_id: roleId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function toggleUserStatus(userId) {
    fetch(`/user-management/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

// Initialize Feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endsection
