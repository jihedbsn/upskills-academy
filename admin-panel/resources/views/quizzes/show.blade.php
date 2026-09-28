@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Détails du Quiz</h1>
        <div class="page-actions">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i data-feather="more-horizontal" class="me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('quizzes.edit', $quiz) }}">
                            <i data-feather="edit" class="me-1"></i>
                            Modifier
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('reponse-questions.index') }}">
                            <i data-feather="check-circle" class="me-1"></i>
                            Gérer les Réponses
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('quizzes.index') }}">
                            <i data-feather="list" class="me-1"></i>
                            Retour à la liste
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
            <!-- En-tête du Quiz -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="help-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-1">{{ $quiz->name_ar }}</h4>
                            <p class="text-muted mb-0">{{ $quiz->name_en }}</p>
                        </div>
                        <div class="text-end">
                            <div class="badge rounded-pill bg-light-info fs-6">
                                <i data-feather="tag" class="me-1"></i>
                                {{ $quiz->type->titre }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Informations principales -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="info" class="me-2"></i>
                                Informations du Quiz
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Nom (Arabe)</label>
                                        <div class="form-control-plaintext fw-bolder">{{ $quiz->name_ar }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Nom (Anglais)</label>
                                        <div class="form-control-plaintext fw-bolder">{{ $quiz->name_en }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Type de Quiz</label>
                                        <div class="form-control-plaintext">
                                            <span class="badge rounded-pill bg-light-info">
                                                {{ $quiz->type->titre }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Score Maximum</label>
                                        <div class="form-control-plaintext">
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-fill me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $quiz->score >= 80 ? 'success' : ($quiz->score >= 60 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $quiz->score }}%"></div>
                                                </div>
                                                <span class="fw-bolder">{{ $quiz->score }}/100</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques et métadonnées -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="bar-chart-2" class="me-2"></i>
                                Statistiques
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">ID du Quiz</span>
                                    <span class="fw-bolder">#{{ $quiz->id }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Date de création</span>
                                    <span class="fw-bolder">{{ $quiz->created_at->format('d/m/Y') }}</span>
                                </div>
                                <small class="text-muted">{{ $quiz->created_at->format('H:i') }}</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Dernière modification</span>
                                    <span class="fw-bolder">{{ $quiz->updated_at->format('d/m/Y') }}</span>
                                </div>
                                <small class="text-muted">{{ $quiz->updated_at->format('H:i') }}</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Niveau de difficulté</span>
                                    <span class="badge rounded-pill bg-{{ $quiz->score >= 80 ? 'success' : ($quiz->score >= 60 ? 'warning' : 'danger') }}">
                                        @if($quiz->score >= 80)
                                            Facile
                                        @elseif($quiz->score >= 60)
                                            Moyen
                                        @else
                                            Difficile
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i data-feather="zap" class="me-2"></i>
                                Actions rapides
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-warning">
                                    <i data-feather="edit" class="me-2"></i>
                                    Modifier le quiz
                                </a>
                                <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                                    <i data-feather="list" class="me-2"></i>
                                    Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions du Quiz -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">
                            <i data-feather="list" class="me-2"></i>
                            Questions du Quiz
                        </h4>
                        <span class="badge bg-light-info">{{ $quiz->questions->count() }} question(s)</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($quiz->questions->count() > 0)
                        <div class="row">
                            @foreach($quiz->questions as $index => $question)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">
                                                <i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Question #{{ $index + 1 }}
                                            </h6>
                                            <span class="badge bg-light-primary">{{ $question->point }} pts</span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <label class="form-label text-muted small">
                                                <i data-feather="align-right" class="me-1" style="width: 12px; height: 12px;"></i>
                                                Arabe
                                            </label>
                                            <div class="form-control-plaintext fw-bolder">{{ $question->name_ar }}</div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <label class="form-label text-muted small">
                                                <i data-feather="align-left" class="me-1" style="width: 12px; height: 12px;"></i>
                                                Anglais
                                            </label>
                                            <div class="form-control-plaintext">{{ $question->name_en }}</div>
                                        </div>
                                        
                                        <!-- Réponses de la question -->
                                        @if($question->reponses->count() > 0)
                                            <div class="mt-3">
                                                <label class="form-label text-muted small">
                                                    <i data-feather="list" class="me-1" style="width: 12px; height: 12px;"></i>
                                                    Réponses ({{ $question->reponses->count() }})
                                                </label>
                                                <div class="reponses-list">
                                                    @foreach($question->reponses as $reponseIndex => $reponse)
                                                        <div class="reponse-item d-flex align-items-center justify-content-between p-2 mb-1 border rounded {{ $reponse->is_correcte ? 'bg-light-success' : 'bg-light-secondary' }}">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-2">
                                                                    @if($reponse->is_correcte)
                                                                        <i data-feather="check-circle" class="text-success" style="width: 14px; height: 14px;"></i>
                                                                    @else
                                                                        <i data-feather="circle" class="text-muted" style="width: 14px; height: 14px;"></i>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <div class="fw-medium small">{{ $reponse->titre_ar }}</div>
                                                                    <div class="text-muted small">{{ $reponse->titre_en }}</div>
                                                                </div>
                                                            </div>
                                                            @if($reponse->is_correcte)
                                                                <span class="badge bg-success small">Correcte</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="mt-3">
                                                <div class="alert alert-warning py-2 mb-0">
                                                    <i data-feather="alert-triangle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                    <small>Aucune réponse définie</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                            <p class="text-muted mt-2">Aucune question définie pour ce quiz</p>
                            <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-outline-primary btn-sm">
                                <i data-feather="plus" class="me-1"></i>
                                Ajouter des questions
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Type de Quiz associé -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="tag" class="me-2"></i>
                        Type de Quiz
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-light-info rounded me-3">
                                    <span class="avatar-content">
                                        <i data-feather="tag" class="text-info"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $quiz->type->titre }}</h6>
                                    <small class="text-muted">Type de quiz</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('type-quizzes.show', $quiz->type) }}" class="btn btn-outline-info btn-sm">
                                <i data-feather="eye" class="me-1"></i>
                                Voir le type
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
