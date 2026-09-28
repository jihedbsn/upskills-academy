@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fa fa-eye me-2"></i>
            Quiz History Details
        </h1>
        <div class="page-actions">
            <a href="{{ route('historique-quiz.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to History
            </a>
            <a href="{{ route('historique-quiz.by-student', $historiqueQuiz->student_id) }}" class="btn btn-primary">
                <i class="fa fa-user me-2"></i> Student History
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations générales -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-info-circle me-2"></i>
                        Quiz Attempt Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium text-muted">Student:</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($historiqueQuiz->student->image)
                                                <img src="{{ $historiqueQuiz->student->imageUrl }}" alt="{{ $historiqueQuiz->student->full_name }}" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fa fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $historiqueQuiz->student->full_name }}</div>
                                                <small class="text-muted">{{ $historiqueQuiz->student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Course:</td>
                                    <td>
                                        <div class="fw-medium">{{ $historiqueQuiz->product->titre }}</div>
                                        @if($historiqueQuiz->product->variation_title)
                                            <small class="text-muted">{{ $historiqueQuiz->product->variation_title }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Quiz:</td>
                                    <td>
                                        <div class="fw-medium">{{ $historiqueQuiz->quiz->name_en ?? 'Unknown Quiz' }}</div>
                                        @if($historiqueQuiz->quiz->name_ar)
                                            <small class="text-muted">{{ $historiqueQuiz->quiz->name_ar }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Score:</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-medium me-2 fs-5">{{ $historiqueQuiz->score }}/100</span>
                                            <div class="progress" style="width: 100px; height: 10px;">
                                                <div class="progress-bar {{ $historiqueQuiz->success ? 'bg-success' : 'bg-danger' }}" 
                                                     style="width: {{ $historiqueQuiz->score }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Submission:</td>
                                    <td>
                                        <span class="badge bg-{{ \App\Support\QuizSubmissionStatus::badgeClass($submissionStatus) }} fs-6">
                                            {{ \App\Support\QuizSubmissionStatus::label($submissionStatus) }}
                                        </span>
                                        @if(isset($answeredCount))
                                            <small class="text-muted ms-2">({{ $answeredCount }}/{{ $historiqueQuiz->total_questions }} answered)</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Status:</td>
                                    <td>
                                        @if($historiqueQuiz->success)
                                            <span class="badge bg-success fs-6">
                                                <i class="fa fa-check me-1"></i> Passed
                                            </span>
                                        @else
                                            <span class="badge bg-danger fs-6">
                                                <i class="fa fa-times me-1"></i> Failed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium text-muted">Attempt Number:</td>
                                    <td>
                                        <span class="badge bg-info fs-6">{{ $historiqueQuiz->attempts }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Duration:</td>
                                    <td>
                                        <span class="fw-medium">{{ $historiqueQuiz->formatted_duration }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Started:</td>
                                    <td>
                                        <div>{{ $historiqueQuiz->started_at->format('d/m/Y H:i:s') }}</div>
                                        <small class="text-muted">{{ $historiqueQuiz->started_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Completed:</td>
                                    <td>
                                        <div>{{ $historiqueQuiz->completed_at->format('d/m/Y H:i:s') }}</div>
                                        <small class="text-muted">{{ $historiqueQuiz->completed_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">IP Address:</td>
                                    <td>
                                        <code>{{ $historiqueQuiz->ip_address ?? 'Unknown' }}</code>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques et actions -->
        <div class="col-md-4">
            <!-- Score Visualization -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-chart-pie me-2"></i>
                        Score Breakdown
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="display-6 fw-bold {{ $historiqueQuiz->success ? 'text-success' : 'text-danger' }}">
                            {{ $historiqueQuiz->score }}%
                        </div>
                        <div class="text-muted">Final Score</div>
                    </div>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar {{ $historiqueQuiz->success ? 'bg-success' : 'bg-danger' }}" 
                             style="width: {{ $historiqueQuiz->score }}%">
                            {{ $historiqueQuiz->score }}%
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="fw-medium">Success Rate</div>
                            <div class="text-{{ $historiqueQuiz->success ? 'success' : 'danger' }}">
                                {{ $historiqueQuiz->success ? '100%' : '0%' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fw-medium">Time Efficiency</div>
                            <div class="text-info">{{ $historiqueQuiz->formatted_duration }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-bolt me-2"></i>
                        Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('historique-quiz.by-student', $historiqueQuiz->student_id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fa fa-user me-2"></i>
                            View Student History
                        </a>
                        <a href="{{ route('historique-quiz.by-course', $historiqueQuiz->product_id) }}" 
                           class="btn btn-outline-info">
                            <i class="fa fa-book me-2"></i>
                            View Course History
                        </a>
                        <a href="{{ route('students.show', $historiqueQuiz->student_id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="fa fa-id-card me-2"></i>
                            Student Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails des réponses (si disponibles) -->
    @if($historiqueQuiz->answers && count($historiqueQuiz->answers) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fa fa-list-check me-2"></i>
                            Quiz Answers
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($historiqueQuiz->answers as $index => $answer)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title">Question {{ $index + 1 }}</h6>
                                            @if(isset($answer['question']))
                                                <p class="text-muted mb-2">{{ $answer['question'] }}</p>
                                            @endif
                                            @if(isset($answer['selected_answer']))
                                                <div class="mb-2">
                                                    <strong>Selected:</strong> 
                                                    <span class="badge bg-primary">{{ $answer['selected_answer'] }}</span>
                                                </div>
                                            @endif
                                            @if(isset($answer['correct_answer']))
                                                <div class="mb-2">
                                                    <strong>Correct:</strong> 
                                                    <span class="badge bg-success">{{ $answer['correct_answer'] }}</span>
                                                </div>
                                            @endif
                                            @if(isset($answer['is_correct']))
                                                <div>
                                                    <span class="badge {{ $answer['is_correct'] ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $answer['is_correct'] ? 'Correct' : 'Incorrect' }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informations techniques -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-cog me-2"></i>
                        Technical Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium text-muted">Quiz ID:</td>
                                    <td><code>{{ $historiqueQuiz->quiz_id }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Product ID:</td>
                                    <td><code>{{ $historiqueQuiz->product_id }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Student ID:</td>
                                    <td><code>{{ $historiqueQuiz->student_id }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Attempt ID:</td>
                                    <td><code>{{ $historiqueQuiz->id }}</code></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium text-muted">Time Spent (seconds):</td>
                                    <td><code>{{ $historiqueQuiz->time_spent ?? 'N/A' }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">User Agent:</td>
                                    <td>
                                        @if($historiqueQuiz->user_agent)
                                            <small class="text-muted">{{ Str::limit($historiqueQuiz->user_agent, 50) }}</small>
                                        @else
                                            <code>Unknown</code>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Created:</td>
                                    <td>{{ $historiqueQuiz->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Updated:</td>
                                    <td>{{ $historiqueQuiz->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
