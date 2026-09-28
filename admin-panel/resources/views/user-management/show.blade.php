@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('user-management.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                                <i data-feather="arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="card-title mb-1">User Details</h4>
                                <p class="text-white-50 mb-0">View user information and permissions</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('user-management.edit', $user) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit User
                            </a>
                            @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                                <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                                    <i data-feather="trash-2" class="me-2"></i>
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i data-feather="user" class="me-2"></i>
                                        Personal Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Full Name</label>
                                            <div class="fw-bold">{{ $user->full_name }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Email</label>
                                            <div class="fw-bold">{{ $user->email }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Phone</label>
                                            <div class="fw-bold">{{ $user->phone ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Role</label>
                                            <div>
                                                <span class="badge bg-primary">{{ $user->getRoleName() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Status</label>
                                            <div>
                                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Last Login</label>
                                            <div class="fw-bold">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Never' }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Created At</label>
                                            <div class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-muted small">Updated At</label>
                                            <div class="fw-bold">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i data-feather="shield" class="me-2"></i>
                                        Permissions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($user->roleRelation && $user->roleRelation->permissions->count() > 0)
                                        @php
                                            $groupedPermissions = $user->roleRelation->permissions->groupBy('group');
                                        @endphp

                                        <div class="row">
                                            @foreach($groupedPermissions as $group => $permissions)
                                                <div class="col-md-6 mb-4">
                                                    <h6 class="text-primary mb-3">{{ ucfirst($group) }}</h6>
                                                    <div class="list-group">
                                                        @foreach($permissions as $permission)
                                                            <div class="list-group-item border-0 px-0 py-2">
                                                                <i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                                                {{ $permission->display_name }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i data-feather="alert-circle" class="text-muted mb-2" style="width: 48px; height: 48px;"></i>
                                            <p class="text-muted">No permissions assigned through role</p>
                                        </div>
                                    @endif

                                    @if($user->custom_permissions && $user->custom_permissions != '[]')
                                        <div class="border-top pt-3 mt-3">
                                            <h6 class="text-warning mb-3">Custom Permissions (Legacy)</h6>
                                            <p class="text-muted small">These are custom permissions assigned directly to the user</p>
                                            <div class="alert alert-warning">
                                                <small>{{ $user->custom_permissions }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i data-feather="activity" class="me-2"></i>
                                        Quick Stats
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Account Age</span>
                                            <span class="fw-bold">{{ $user->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Permissions</span>
                                            <span class="fw-bold">{{ $user->roleRelation ? $user->roleRelation->permissions->count() : 0 }}</span>
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-primary" style="width: {{ min(($user->roleRelation ? $user->roleRelation->permissions->count() : 0) * 5, 100) }}%"></div>
                                        </div>
                                    </div>

                                    @if($user->last_login_at)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Last Active</span>
                                                <span class="fw-bold">{{ $user->last_login_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-info" style="width: 75%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Role Information -->
                            @if($user->roleRelation)
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i data-feather="briefcase" class="me-2"></i>
                                            Role Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="mb-2">{{ $user->roleRelation->display_name }}</h6>
                                        <p class="text-muted small mb-3">{{ $user->roleRelation->description }}</p>

                                        <div class="d-grid">
                                            <a href="{{ route('roles.show', $user->roleRelation) }}" class="btn btn-outline-primary btn-sm">
                                                <i data-feather="eye" class="me-2"></i>
                                                View Role Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete-form-{{ $user->id }}" action="{{ route('user-management.destroy', $user) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endsection
