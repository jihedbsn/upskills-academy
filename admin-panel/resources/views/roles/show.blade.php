@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">Role: {{ $role->display_name }}</h4>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit Role
                            </a>
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Role Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">Role Name:</td>
                                    <td><code>{{ $role->name }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Display Name:</td>
                                    <td>{{ $role->display_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        @if($role->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created:</td>
                                    <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Statistics</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">Total Admins:</td>
                                    <td><span class="badge bg-primary">{{ $role->admins->count() }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Permissions:</td>
                                    <td><span class="badge bg-info">{{ $role->permissions->count() }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($role->description)
                        <div class="mb-4">
                            <h6 class="text-muted">Description</h6>
                            <p class="text-muted">{{ $role->description }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Permissions ({{ $role->permissions->count() }})</h6>
                        @if($role->permissions->count() > 0)
                            <div class="row">
                                @php
                                    $groupedPermissions = $role->permissions->groupBy('group');
                                @endphp
                                @foreach($groupedPermissions as $group => $perms)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0 text-capitalize">{{ $group ?? 'General' }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($perms as $permission)
                                                        <li class="mb-2">
                                                            <i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i>
                                                            <strong>{{ $permission->display_name }}</strong>
                                                            @if($permission->description)
                                                                <br><small class="text-muted ms-4">{{ $permission->description }}</small>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No permissions assigned to this role.</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Assigned Admins ({{ $role->admins->count() }})</h6>
                        @if($role->admins->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Last Login</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($role->admins as $admin)
                                            <tr>
                                                <td>{{ $admin->full_name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    @if($admin->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : 'Never' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No admins assigned to this role yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endsection
