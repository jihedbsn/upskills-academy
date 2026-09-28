@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fa fa-user me-2"></i>
            Quiz History - {{ $student->full_name }}
        </h1>
        <div class="page-actions">
            <a href="{{ route('historique-quiz.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to All History
            </a>
            <a href="{{ route('students.show', $student) }}" class="btn btn-primary">
                <i class="fa fa-id-card me-2"></i> Student Profile
            </a>
        </div>
    </div>

    <!-- Informations de l'étudiant -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-user me-2"></i>
                        Student Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @if($student->image)
                            <img src="{{ $student->imageUrl }}" alt="{{ $student->full_name }}" class="rounded-circle me-3" width="64" height="64">
                        @else
                            <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <i class="fa fa-user fa-2x text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="mb-1">{{ $student->full_name }}</h4>
                            <p class="text-muted mb-1">{{ $student->email }}</p>
                            @if($student->phone)
                                <p class="text-muted mb-0">{{ $student->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-chart-bar me-2"></i>
                        Performance Overview
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="display-6 fw-bold text-primary">{{ number_format($stats['average_score'], 1) }}%</div>
                        <div class="text-muted">Average Score</div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-medium text-success">{{ $stats['successful'] }}</div>
                            <div class="text-muted small">Passed</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-medium text-danger">{{ $stats['failed'] }}</div>
                            <div class="text-muted small">Failed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques détaillées -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-list fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    <small>Total Attempts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-check-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['successful'] }}</h4>
                    <small>Successful</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fa fa-book fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_courses'] }}</h4>
                    <small>Courses</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fa fa-question-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_quizzes'] }}</h4>
                    <small>Quizzes</small>
                </div>
            </div>
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
                    <form method="GET" action="{{ route('historique-quiz.by-student', $student->id) }}" class="row">
                        <div class="col-md-4">
                            <label for="course_filter" class="form-label">Course</label>
                            <select class="form-select" id="course_filter" name="course">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->titre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_filter" class="form-label">Status</label>
                            <select class="form-select" id="status_filter" name="status">
                                <option value="">All</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="{{ route('historique-quiz.by-student', $student->id) }}" class="btn btn-outline-secondary d-block w-100">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de l'historique -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-history me-2"></i>
                        Quiz History ({{ $historiqueQuiz->total() }})
                    </h4>
                </div>
                <div class="card-body p-0">
                    @if($historiqueQuiz->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Attempts</th>
                                        <th>Duration</th>
                                        <th>Completed</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historiqueQuiz as $history)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $history->product->titre }}</div>
                                                    @if($history->product->variation_title)
                                                        <small class="text-muted">{{ $history->product->variation_title }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $history->quiz->name_en ?? 'Unknown Quiz' }}</div>
                                                @if($history->quiz->name_ar)
                                                    <small class="text-muted">{{ $history->quiz->name_ar }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-medium me-2">{{ $history->score }}/100</span>
                                                    <div class="progress" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar {{ $history->success ? 'bg-success' : 'bg-danger' }}" 
                                                             style="width: {{ $history->score }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($history->success)
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-check me-1"></i> Passed
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fa fa-times me-1"></i> Failed
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $history->attempts }}</span>
                                            </td>
                                            <td>
                                                <small>{{ $history->formatted_duration }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $history->completed_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $history->completed_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('historique-quiz.show', $history) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('historique-quiz.by-course', $history->product_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Course History">
                                                        <i class="fa fa-book"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No quiz history found</h5>
                            <p class="text-muted">This student hasn't attempted any quizzes yet.</p>
                        </div>
                    @endif
                </div>
                @if($historiqueQuiz->hasPages())
                    <div class="card-footer">
                        {{ $historiqueQuiz->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
