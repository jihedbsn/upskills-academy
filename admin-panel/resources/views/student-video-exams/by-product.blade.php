@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Video Exams by Product</h1>
        <div class="page-actions">
            <a href="{{ route('student-video-exams.index') }}" class="btn btn-secondary">
                <i class="fa fa-list me-2"></i>
                List View
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-filter me-2"></i>
                        Filters
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('student-video-exams.by-product') }}" class="row">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="Product, student, description...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Validated</option>
                                <option value="-1" {{ request('status') == '-1' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-video-camera fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedVideoExams->count() }}</h4>
                    <small>Courses with Video Exams</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedVideoExams->flatten()->count() }}</h4>
                    <small>Total Video Exams</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fa fa-clock fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedVideoExams->flatten()->where('is_valid', 0)->count() }}</h4>
                    <small>Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fa fa-check-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $groupedVideoExams->flatten()->where('is_valid', 1)->count() }}</h4>
                    <small>Validated</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des examens vidéo par cours -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-video-camera me-2"></i>
                        Video Exams by Course
                    </h5>
                </div>
                <div class="card-body">
                    @if($groupedVideoExams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Course Name</th>
                                        <th>Number of Students</th>
                                        <th>Pending</th>
                                        <th>Validated</th>
                                        <th>Rejected</th>
                                        <th>Creation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupedVideoExams as $index => $videoExams)
                                        @php
                                            $firstExam = $videoExams->first();
                                            $product = $firstExam ? $firstExam->product : null;
                                            $pendingCount = $videoExams->where('is_valid', 0)->count();
                                            $validatedCount = $videoExams->where('is_valid', 1)->count();
                                            $rejectedCount = $videoExams->where('is_valid', -1)->count();
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fa fa-video-camera text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $product ? ($product->variation_title ?? 'Course #' . $product->id) : 'Course (not available)' }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $product ? $product->id : 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ $videoExams->count() }} student(s)
                                                </span>
                                            </td>
                                            <td>
                                                @if($pendingCount > 0)
                                                    <span class="badge bg-warning">{{ $pendingCount }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($validatedCount > 0)
                                                    <span class="badge bg-success">{{ $validatedCount }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rejectedCount > 0)
                                                    <span class="badge bg-danger">{{ $rejectedCount }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $product && $product->created_at ? $product->created_at->format('d/m/Y') : '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($product)
                                                    <a href="{{ route('student-video-exams.index', ['product_id' => $product->id]) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-eye me-1"></i>
                                                        View Details
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-video-camera fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No video exams found</h5>
                            <p class="text-muted">
                                There are no video exams matching your criteria.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

.card.bg-primary:hover, .card.bg-success:hover, .card.bg-warning:hover, .card.bg-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card.bg-primary .card-body, 
.card.bg-success .card-body, 
.card.bg-warning .card-body, 
.card.bg-info .card-body {
    padding: 1.5rem;
}

.card.bg-primary h4, 
.card.bg-success h4, 
.card.bg-warning h4, 
.card.bg-info h4 {
    font-weight: 600;
    font-size: 1.75rem;
}

.card.bg-primary small, 
.card.bg-success small, 
.card.bg-warning small, 
.card.bg-info small {
    font-size: 0.875rem;
    opacity: 0.9;
}
</style>
