@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fa fa-history me-2"></i>
            Quiz History Management
        </h1>
        <div class="page-actions">
            <a href="{{ route('historique-quiz.statistics') }}" class="btn btn-info">
                <i class="fa fa-chart-bar me-2"></i> Statistics
            </a>
        </div>
    </div>

    <!-- Statistiques -->
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
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fa fa-times-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['failed'] }}</h4>
                    <small>Failed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fa fa-chart-line fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ number_format($stats['average_score'], 1) }}%</h4>
                    <small>Average Score</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques additionnelles -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fa fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_students'] }}</h4>
                    <small>Unique Students</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-question-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['unique_quizzes'] }}</h4>
                    <small>Unique Quizzes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body text-center">
                    <i class="fa fa-calendar-day fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['today'] }}</h4>
                    <small>Today</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light text-dark">
                <div class="card-body text-center">
                    <i class="fa fa-calendar-week fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['this_week'] }}</h4>
                    <small>This Week</small>
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
                    <form method="GET" action="{{ route('historique-quiz.index') }}" class="row">
                        <div class="col-md-2">
                            <label for="student_filter" class="form-label">Student</label>
                            <input type="text" class="form-control" id="student_filter" name="student" placeholder="Name or email" value="{{ request('student') }}">
                        </div>
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <label for="quiz_filter" class="form-label">Quiz</label>
                            <select class="form-select" id="quiz_filter" name="quiz">
                                <option value="">All Quizzes</option>
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}" {{ request('quiz') == $quiz->id ? 'selected' : '' }}>
                                        {{ $quiz->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status_filter" class="form-label">Status</label>
                            <select class="form-select" id="status_filter" name="status">
                                <option value="">All</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="min_score_filter" class="form-label">Min Score</label>
                            <input type="number" class="form-control" id="min_score_filter" name="min_score" placeholder="0" min="0" max="100" value="{{ request('min_score') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <a href="{{ route('historique-quiz.index') }}" class="btn btn-outline-secondary d-block w-100">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de l'historique des quiz -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-list me-2"></i>
                        Quiz History ({{ $historiqueQuiz->total() }})
                    </h4>
                </div>
                <div class="card-body p-0">
                    @if($historiqueQuiz->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
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
                                                <div class="d-flex align-items-center">
                                                    @if($history->student->image)
                                                        <img src="{{ $history->student->imageUrl }}" alt="{{ $history->student->full_name }}" class="rounded-circle me-2" width="32" height="32">
                                                    @else
                                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="fa fa-user text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $history->student->full_name }}</div>
                                                        <small class="text-muted">{{ $history->student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
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
                                                    <a href="{{ route('historique-quiz.by-student', $history->student_id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Student History">
                                                        <i class="fa fa-user"></i>
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
                            <p class="text-muted">No quiz attempts match your current filters.</p>
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
