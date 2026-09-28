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
                                    <i data-feather="users" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Students Management</h1>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('students.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                New Student
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('students.index') }}" class="d-flex">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Search students..."
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <span class="text-muted me-3">
                                    <i data-feather="users" class="me-1" style="width: 16px; height: 16px;"></i>
                                    Total: {{ $students->total() }} students
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="list" class="me-2"></i>
                                        <h4 class="card-title mb-1">Students List</h4>
                                        <p class="text-white-50 mb-0 ms-3">Manage all students and learners</p>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    @if($students->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>
                                                            <i data-feather="image" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Avatar
                                                        </th>
                                                        <th>
                                                            <i data-feather="user" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Student
                                                        </th>
                                                        <th>
                                                            <i data-feather="mail" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Email
                                                        </th>
                                                        <th>
                                                            <i data-feather="phone" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Phone
                                                        </th>
                                                        <th>
                                                            <i data-feather="globe" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Country
                                                        </th>
                                                        <th>
                                                            <i data-feather="calendar" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Registration Date
                                                        </th>
                                                        <th class="text-center">
                                                            <i data-feather="settings" class="me-1" style="width: 16px; height: 16px;"></i>
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($students as $student)
                                                        <tr>
                                                            <td>
                                                                <div class="avatar avatar-sm">
                                                                    @if($student->image)
                                                                        <img src="{{ $student->image_url }}"
                                                                             alt="{{ $student->full_name }}"
                                                                             class="rounded-circle"
                                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                                    @else
                                                                        <div class="avatar avatar-sm bg-light text-dark rounded-circle d-flex align-items-center justify-content-center">
                                                                            <i data-feather="user" style="width: 20px; height: 20px;"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-column">
                                                                    <span class="fw-bold text-dark">{{ $student->full_name }}</span>
                                                                    <small class="text-muted">ID: #{{ $student->id }}</small>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-primary">{{ $student->email }}</span>
                                                            </td>
                                                            <td>
                                                                <span>{{ $student->phone ?? 'Not provided' }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">{{ $student->country ?? 'Not specified' }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ $student->created_at->format('d/m/Y H:i') }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('students.show', $student) }}"
                                                                       class="btn btn-sm btn-outline-info"
                                                                       title="View Details">
                                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                                    </a>
                                                                    <a href="{{ route('students.edit', $student) }}"
                                                                       class="btn btn-sm btn-outline-warning"
                                                                       title="Edit">
                                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                                    </a>
                                                                    <a href="{{ rtrim(config('app.file_url'), '/') }}/en?id={{ $student->id }}&email={{ urlencode($student->email) }}"
                                                                       class="btn btn-sm btn-outline-secondary"
                                                                       title="Login" target="_blank">
                                                                        <i data-feather="log-in" style="width: 14px; height: 14px;"></i>
                                                                    </a>
                                                                    <form action="{{ route('students.destroy', $student) }}"
                                                                          method="POST"
                                                                          class="d-inline"
                                                                          onsubmit="return confirm('Are you sure you want to delete this student?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-outline-danger"
                                                                                title="Delete">
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
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i data-feather="users" style="width: 64px; height: 64px;" class="text-muted"></i>
                                            </div>
                                            <h5 class="text-muted">No students found</h5>
                                            <p class="text-muted">Start by creating your first student</p>
                                            <a href="{{ route('students.create') }}" class="btn btn-primary">
                                                <i data-feather="plus" class="me-2"></i>
                                                Create a student
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="row mt-4">
                            <div class="col-12">
                                <nav aria-label="Students pagination">
                                    {{ $students->links() }}
                                </nav>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();
</script>
@endpush
