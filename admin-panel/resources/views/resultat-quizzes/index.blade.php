@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Quiz Results Management</h1>
        <div class="page-actions">
            <a href="{{ route('admin.resultat-quizzes.create') }}" class="btn btn-primary">
                <i class="fa fa-plus me-2"></i> New Result
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-trophy fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    <small>Total Results</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-check-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['successful'] }}</h4>
                    <small>Passed</small>
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
                    <i class="fa fa-bar-chart fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ number_format($stats['average_score'], 1) }}%</h4>
                    <small>Average Score</small>
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
                    <form method="GET" action="{{ route('admin.resultat-quizzes.index') }}" class="row">
                        @if(request('product'))
                            {{-- Filtres pour la page avec produit sélectionné --}}
                            <div class="col-md-3">
                                <label for="product_filter" class="form-label">Course</label>
                                <select class="form-select" id="product_filter" name="product">
                                    <option value="">All Courses</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="quiz_filter" class="form-label">
                                    <i class="fa fa-question-circle me-1"></i> Quiz
                                </label>
                                <select class="form-select" id="quiz_filter" name="quiz">
                                    <option value="">All Quizzes</option>
                                    @foreach($quizzes as $quiz)
                                        <option value="{{ $quiz->id }}" {{ request('quiz') == $quiz->id ? 'selected' : '' }}>
                                            {{ $quiz->name_en ?? $quiz->name_ar ?? 'Quiz #' . $quiz->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="success_filter" class="form-label">Status</label>
                                <select class="form-select" id="success_filter" name="success">
                                    <option value="">All</option>
                                    <option value="1" {{ request('success') == '1' ? 'selected' : '' }}>Passed</option>
                                    <option value="0" {{ request('success') == '0' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="student_filter" class="form-label">Student Email</label>
                                <input type="email" class="form-control" id="student_filter" name="student" placeholder="student@email.com" value="{{ request('student') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <a href="{{ route('admin.resultat-quizzes.index') }}" class="btn btn-outline-secondary d-block w-100">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </div>
                        @else
                            {{-- Filtre pour la page principale (seulement Course) --}}
                            <div class="col-md-6">
                                <label for="product_filter" class="form-label">Course</label>
                                <select class="form-select" id="product_filter" name="product">
                                    <option value="">All Courses</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>{{ $product->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="fa fa-search me-1"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <a href="{{ route('admin.resultat-quizzes.index') }}" class="btn btn-outline-secondary d-block w-100">
                                    <i class="fa fa-refresh me-1"></i> Reset
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des résultats de quiz par cours -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-trophy me-2"></i>
                        @if(request('product'))
                            Quiz Results Details - Course #{{ request('product') }}
                        @else
                            Quiz Results by Course
                        @endif
                    </h5>
                    @if(request('product'))
                        <div class="card-actions">
                            <a href="{{ route('admin.resultat-quizzes.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left me-1"></i>
                                Back to Overview
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($results->count() > 0)
                        @if(request('product'))
                            <!-- Vue détaillée pour un produit spécifique -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Student</th>
                                            <th>Quiz</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Attempts</th>
                                            <th>Last Quiz</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            <tr>
                                                <td>{{ $result->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <span class="avatar-content">{{ substr($result->student->first_name ?? 'A', 0, 1) }}{{ substr($result->student->last_name ?? 'B', 0, 1) }}</span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $result->student->first_name ?? 'N/A' }} {{ $result->student->last_name ?? 'N/A' }}</h6>
                                                            <small class="text-muted">{{ $result->student->email ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $result->quiz->name_en ?? 'Quiz #' . $result->quiz_id }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar {{ $result->score >= 50 ? 'bg-success' : 'bg-danger' }}" 
                                                                 style="width: {{ $result->score }}%"></div>
                                                        </div>
                                                        <span class="fw-bold">{{ number_format($result->score, 1) }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($result->success)
                                                        <span class="badge bg-success">Passed</span>
                                                    @else
                                                        <span class="badge bg-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $result->attempts >= 3 ? 'bg-warning' : 'bg-info' }}">
                                                        {{ $result->attempts }}/3
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($result->last_quiz_at)
                                                        {{ $result->last_quiz_at->format('d/m/Y H:i') }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.resultat-quizzes.show', $result) }}" 
                                                           class="btn btn-sm btn-outline-info" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.resultat-quizzes.edit', $result) }}" 
                                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        @if(!$result->success)
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-success mark-success-btn" 
                                                                    title="Mark as 100% Success"
                                                                    data-result-id="{{ $result->id }}"
                                                                    data-student-name="{{ $result->student->first_name }} {{ $result->student->last_name }}"
                                                                    data-quiz-name="{{ $result->quiz->name_en ?? 'Quiz #' . $result->quiz_id }}">
                                                                <i class="fa fa-trophy"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Vue groupée par cours -->
                            @php
                                $groupedResults = $results->groupBy('product_id');
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Course Name</th>
                                            <th>Number of Students</th>
                                            <th>Passed</th>
                                            <th>Failed</th>
                                            <th>Average Score</th>
                                            <th>Last Quiz</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupedResults as $index => $courseResults)
                                            @php
                                                $product = $courseResults->first()->product;
                                                $successfulCount = $courseResults->where('success', true)->count();
                                                $failedCount = $courseResults->where('success', false)->count();
                                                $averageScore = $courseResults->avg('score');
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i class="fa fa-trophy text-white"></i>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $product->titre ?? 'Course #' . $product->id }}</strong>
                                                            <br>
                                                            <small class="text-muted">ID: {{ $product->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary fs-6">
                                                        {{ $courseResults->count() }} student(s)
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($successfulCount > 0)
                                                        <span class="badge bg-success">{{ $successfulCount }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($failedCount > 0)
                                                        <span class="badge bg-danger">{{ $failedCount }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar {{ $averageScore >= 50 ? 'bg-success' : 'bg-danger' }}" 
                                                                 style="width: {{ $averageScore }}%"></div>
                                                        </div>
                                                        <span class="fw-bold">{{ number_format($averageScore, 1) }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $courseLastQuiz = $courseResults->max('last_quiz_at');
                                                    @endphp
                                                    <small class="text-muted">
                                                        {{ $courseLastQuiz ? $courseLastQuiz->format('d/m/Y H:i') : '—' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.resultat-quizzes.index', ['product' => $product->id]) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-eye me-1"></i>
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Pagination -->
                        @if($results->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $results->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-trophy fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No results found</h5>
                            <p class="text-muted">
                                There are no quiz results matching your criteria.
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

.card.bg-primary, .card.bg-success, .card.bg-danger, .card.bg-info {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease-in-out;
}

.card.bg-primary:hover, .card.bg-success:hover, .card.bg-danger:hover, .card.bg-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card.bg-primary .card-body, 
.card.bg-success .card-body, 
.card.bg-danger .card-body, 
.card.bg-info .card-body {
    padding: 1.5rem;
}

.card.bg-primary h4, 
.card.bg-success h4, 
.card.bg-danger h4, 
.card.bg-info h4 {
    font-weight: 600;
    font-size: 1.75rem;
}

.card.bg-primary small, 
.card.bg-success small, 
.card.bg-danger small, 
.card.bg-info small {
    font-size: 0.875rem;
    opacity: 0.9;
}

/* Style pour le bouton de succès */
.mark-success-btn {
    transition: all 0.3s ease;
}

.mark-success-btn:hover {
    background-color: #198754 !important;
    color: white !important;
    transform: scale(1.05);
}

.mark-success-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer le clic sur les boutons "Mark as Success"
    document.querySelectorAll('.mark-success-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const resultId = this.getAttribute('data-result-id');
            const studentName = this.getAttribute('data-student-name');
            const quizName = this.getAttribute('data-quiz-name');
            
            // Confirmation avant d'exécuter l'action
            if (confirm(`Are you sure you want to mark this quiz as 100% success?\n\nStudent: ${studentName}\nQuiz: ${quizName}\n\nThis will:\n- Set the score to 100%\n- Mark as success\n- Create/update student success record`)) {
                
                // Désactiver le bouton pendant la requête
                this.disabled = true;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                
                // Envoyer la requête AJAX
                fetch(`/admin/resultat-quizzes/${resultId}/mark-success`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Afficher un message de succès
                        alert('✅ ' + data.message);
                        
                        // Recharger la page pour voir les changements
                        window.location.reload();
                    } else {
                        // Afficher un message d'erreur
                        alert('❌ ' + data.message);
                        
                        // Réactiver le bouton
                        this.disabled = false;
                        this.innerHTML = '<i class="fa fa-trophy"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ An error occurred while processing your request.');
                    
                    // Réactiver le bouton
                    this.disabled = false;
                    this.innerHTML = '<i class="fa fa-trophy"></i>';
                });
            }
        });
    });
});
</script>
