@extends('layouts.app')

@section('title', 'Partnership Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-handshake me-2"></i>
                        Partnership Requests
                    </h4>
                    <div class="d-flex gap-2">
                        @if(\App\Models\Partnership::where('is_read', false)->count() > 0)
                            <a href="{{ route('partnerships.mark-all-read') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-check-double me-1"></i>
                                Mark All as Read
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filters -->
                    <form method="GET" action="{{ route('partnerships.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    <input type="text" class="form-control" name="search"
                                           placeholder="Search by institution, email..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-filter me-1"></i> Filter
                                </button>
                            </div>
                            @if(request('search') || request('status'))
                                <div class="col-md-2">
                                    <a href="{{ route('partnerships.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fa fa-times me-1"></i> Clear
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center py-3">
                                    <h3 class="mb-0">{{ \App\Models\Partnership::count() }}</h3>
                                    <small class="text-muted">Total Requests</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning bg-opacity-25">
                                <div class="card-body text-center py-3">
                                    <h3 class="mb-0 text-warning">{{ \App\Models\Partnership::pending()->count() }}</h3>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success bg-opacity-25">
                                <div class="card-body text-center py-3">
                                    <h3 class="mb-0 text-success">{{ \App\Models\Partnership::approved()->count() }}</h3>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger bg-opacity-25">
                                <div class="card-body text-center py-3">
                                    <h3 class="mb-0 text-danger">{{ \App\Models\Partnership::rejected()->count() }}</h3>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>Institution</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Courses</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partnerships as $partnership)
                                    <tr class="{{ !$partnership->is_read ? 'table-warning' : '' }}">
                                        <td>
                                            @if(!$partnership->is_read)
                                                <span class="badge bg-primary rounded-pill" title="New">
                                                    <i class="fa fa-circle" style="font-size: 8px;"></i>
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $partnership->institution_name }}</strong>
                                            @if($partnership->website)
                                                <br><small class="text-muted">
                                                    <a href="{{ $partnership->website }}" target="_blank">{{ Str::limit($partnership->website, 30) }}</a>
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $partnership->email }}</td>
                                        <td>{{ $partnership->phone }}</td>
                                        <td>
                                            @if($partnership->requested_courses)
                                                <span class="badge bg-info">{{ count($partnership->requested_courses) }} courses</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $partnership->status_badge_class }}">
                                                {{ ucfirst($partnership->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $partnership->created_at->format('M d, Y') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $partnership->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('partnerships.show', $partnership) }}"
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if($partnership->profile_file)
                                                    <a href="{{ route('partnerships.download', $partnership) }}"
                                                       class="btn btn-outline-secondary" title="Download File">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger"
                                                        title="Delete"
                                                        onclick="confirmDelete({{ $partnership->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $partnership->id }}"
                                                  action="{{ route('partnerships.destroy', $partnership) }}"
                                                  method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No partnership requests found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $partnerships->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this partnership request?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection
