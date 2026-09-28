@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Course Sessions</h4>
                    <a href="{{ route('admin.course-sessions.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add New Session
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-feather="check-circle" class="me-1"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i data-feather="alert-circle" class="me-1"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.course-sessions.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Course</label>
                                    <select name="product_id" class="form-select">
                                        <option value="">All Courses</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Type</label>
                                    <select name="session_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="theory" {{ request('session_type') == 'theory' ? 'selected' : '' }}>Theory</option>
                                        <option value="practical" {{ request('session_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                                        <option value="online" {{ request('session_type') == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="classroom" {{ request('session_type') == 'classroom' ? 'selected' : '' }}>Classroom</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Date & Time</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Instructor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>
                                            <strong>{{ $session->title }}</strong>
                                            @if($session->description)
                                                <br><small class="text-muted">{{ Str::limit($session->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $session->product->titre }}</td>
                                        <td>
                                            <div>{{ $session->formatted_date }}</div>
                                            <small class="text-muted">{{ $session->formatted_time }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $session->getTypeLabel() }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $session->getStatusBadgeColor() }}">
                                                {{ $session->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $session->instructor_name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.course-sessions.show', $session) }}" class="btn btn-sm btn-info" title="View">
                                                    <i data-feather="eye"></i>
                                                </a>
                                                <a href="{{ route('admin.course-sessions.edit', $session) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i data-feather="edit"></i>
                                                </a>
                                                <form action="{{ route('admin.course-sessions.destroy', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this session? Students will be notified.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted">No sessions found.</p>
                                            <a href="{{ route('admin.course-sessions.create') }}" class="btn btn-primary">
                                                <i data-feather="plus" class="me-1"></i> Create First Session
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($sessions->hasPages())
                        <div class="mt-3">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
