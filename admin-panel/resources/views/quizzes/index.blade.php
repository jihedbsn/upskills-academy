@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gestion des Quiz</h1>
        <div class="page-actions">
            <a href="{{ route('reponse-questions.index') }}" class="btn btn-info me-2">
                <i data-feather="check-circle" class="me-2"></i>
                Réponses aux Questions
            </a>
            <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                Nouveau Quiz
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="filter" class="me-2"></i>
                        Filtres
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('quizzes.index') }}" class="row">
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="search" class="form-control" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nom arabe, anglais ou type...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type de Quiz</label>
                            <select name="type_id" class="form-select">
                                <option value="">Tous les types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->titre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Score min</label>
                            <input type="number" name="score_min" class="form-control" 
                                   value="{{ request('score_min') }}" min="0" max="100">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Score max</label>
                            <input type="number" name="score_max" class="form-control" 
                                   value="{{ request('score_max') }}" min="0" max="100">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i data-feather="search" class="me-1"></i>
                                    Filtrer
                                </button>
                                <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                                    <i data-feather="refresh-cw"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="help-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Liste des Quiz</h4>
                            <p class="text-white-50 mb-0">Gérez tous les quiz de l'application</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($quizzes->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom (Arabe)</th>
                                        <th>Nom (Anglais)</th>
                                        <th>Type</th>
                                        <th>Score</th>
                                        <th>Date de création</th>
                                        <th class="actions-column">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizzes as $quiz)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#{{ $quiz->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i data-feather="help-circle" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium text-dark">{{ $quiz->name_ar }}</div>
                                                        <small class="text-muted">Quiz en arabe</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-medium text-dark">{{ $quiz->name_en }}</div>
                                                <small class="text-muted">Quiz en anglais</small>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light-info">
                                                    {{ $quiz->type->titre }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-fill me-2" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $quiz->score >= 80 ? 'success' : ($quiz->score >= 60 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $quiz->score }}%"></div>
                                                    </div>
                                                    <span class="fw-bolder">{{ $quiz->score }}/100</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="calendar" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ $quiz->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="actions-column">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $quizzes->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-2">
                                <i data-feather="help-circle" class="text-muted" style="width: 64px; height: 64px;"></i>
                            </div>
                            <h4 class="text-muted">Aucun quiz trouvé</h4>
                            <p class="text-muted">Commencez par créer votre premier quiz.</p>
                            <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-1"></i>
                                Créer un quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
