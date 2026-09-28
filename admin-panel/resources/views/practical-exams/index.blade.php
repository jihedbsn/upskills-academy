@extends('layouts.app')

@section('title', 'Practical Exam Results')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-clipboard-check"></i>
            </span>
            Practical Exam Results
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Practical Exams</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <h5 class="mb-0">{{ $pendingCount }}</h5>
                    <p class="mb-0">Pending Review</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <h5 class="mb-0">{{ $attempts->where('status', 'passed')->count() }}</h5>
                    <p class="mb-0">Passed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-danger text-white">
                <div class="card-body">
                    <h5 class="mb-0">{{ $attempts->where('status', 'failed')->count() }}</h5>
                    <p class="mb-0">Failed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <h5 class="mb-0">{{ $attempts->total() }}</h5>
                    <p class="mb-0">Total Attempts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('practical-exams.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->titre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending_submission" {{ request('status') == 'pending_submission' ? 'selected' : '' }}>Pending Submission</option>
                        <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
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

                <div class="col-md-3">
                    <label class="form-label">Search Student</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or Email" value="{{ request('search') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('practical-exams.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-refresh"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Training Case</th>
                            <th>Attempt</th>
                            <th>Exam Type</th>
                            <th>Submission Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            <tr>
                                <td>
                                    <div class="fw-500">{{ trim(($attempt->user->first_name ?? '') . ' ' . ($attempt->user->last_name ?? '')) ?: 'N/A' }}</div>
                                    <small class="text-muted">{{ $attempt->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $attempt->product->titre ?? 'N/A' }}</td>
                                <td>{{ $attempt->trainingCase->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary">#{{ $attempt->attempt_number }}</span></td>
                                <td>
                                    @if($attempt->product && $attempt->product->practical_exam_type === 'online')
                                        <span class="badge bg-info">ONLINE</span>
                                        @if($attempt->video_url)
                                            <br>
                                            <a href="{{ $attempt->video_url }}" target="_blank" class="text-primary small">
                                                <i class="mdi mdi-play-circle"></i> View Video
                                            </a>
                                        @endif
                                    @elseif($attempt->product && $attempt->product->practical_exam_type === 'classroom')
                                        <span class="badge bg-success">CLASSROOM</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->submitted_at)
                                        {{ $attempt->submitted_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $attempt->submitted_at->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">Not submitted</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->status === 'passed')
                                        <span class="badge bg-success">Passed</span>
                                    @elseif($attempt->status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @elseif($attempt->status === 'pending_review')
                                        <span class="badge bg-warning">Pending Review</span>
                                    @elseif($attempt->status === 'pending_submission')
                                        <span class="badge bg-info">Pending Submission</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->status === 'pending')
                                        <a href="{{ route('practical-exams.show', $attempt->id) }}" class="btn btn-sm btn-gradient-primary">
                                            <i class="mdi mdi-clipboard-check"></i> Grade
                                        </a>
                                    @else
                                        <a href="{{ route('practical-exams.show', $attempt->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="mdi mdi-information-outline" style="font-size: 48px;"></i>
                                    <p class="mt-2 mb-0">No practical exam attempts found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4 px-3">
                <div class="text-muted">
                    Showing {{ $attempts->firstItem() ?? 0 }} to {{ $attempts->lastItem() ?? 0 }} of {{ $attempts->total() }} entries
                </div>
                <div>
                    {{ $attempts->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card.bg-gradient-warning,
.card.bg-gradient-success,
.card.bg-gradient-danger,
.card.bg-gradient-info {
    border: none;
}
</style>
@endsection
