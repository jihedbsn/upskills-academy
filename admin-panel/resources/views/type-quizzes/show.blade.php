@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            <i data-feather="tag" class="me-2"></i>
                            Détails du Type de Quiz
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('type-quizzes.index') }}">Types de Quiz</a></li>
                                <li class="breadcrumb-item active">Détails</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i data-feather="more-horizontal" class="me-1"></i>
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('type-quizzes.edit', $typeQuiz) }}">
                                    <i data-feather="edit" class="me-1"></i>
                                    Modifier
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('quizzes.create') }}">
                                    <i data-feather="plus" class="me-1"></i>
                                    Ajouter un quiz
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('type-quizzes.index') }}">
                                    <i data-feather="list" class="me-1"></i>
                                    Retour à la liste
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- En-tête du Type -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="tag" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-1">{{ $typeQuiz->titre }}</h4>
                            <p class="text-muted mb-0">Type de quiz</p>
                        </div>
                        <div class="text-end">
                            <div class="badge rounded-pill bg-light-primary fs-6">
                                <i data-feather="help-circle" class="me-1"></i>
                                {{ $typeQuiz->quizzes->count() }} quiz
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
                                Informations du Type
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Titre du Type</label>
                                        <div class="form-control-plaintext fw-bolder">{{ $typeQuiz->titre }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Nombre de Quiz</label>
                                        <div class="form-control-plaintext">
                                            <span class="badge rounded-pill bg-light-primary fs-6">
                                                {{ $typeQuiz->quizzes->count() }} quiz
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des Quiz associés -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">
                                    <i data-feather="help-circle" class="me-2"></i>
                                    Quiz associés
                                </h4>
                                <a href="{{ route('quizzes.create') }}" class="btn btn-primary btn-sm">
                                    <i data-feather="plus" class="me-1"></i>
                                    Ajouter un quiz
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($typeQuiz->quizzes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom (Arabe)</th>
                                                <th>Nom (Anglais)</th>
                                                <th>Score</th>
                                                <th>Date de création</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($typeQuiz->quizzes as $quiz)
                                                <tr>
                                                    <td>{{ $quiz->id }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-light-primary rounded me-2">
                                                                <span class="avatar-content">
                                                                    <i data-feather="help-circle" class="text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <span class="fw-bolder">{{ $quiz->name_ar }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $quiz->name_en }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-fill me-2" style="height: 6px;">
                                                                <div class="progress-bar bg-{{ $quiz->score >= 80 ? 'success' : ($quiz->score >= 60 ? 'warning' : 'danger') }}" 
                                                                     style="width: {{ $quiz->score }}%"></div>
                                                            </div>
                                                            <span class="fw-bolder">{{ $quiz->score }}/100</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $quiz->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                                                <i data-feather="more-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('quizzes.show', $quiz) }}">
                                                                        <i data-feather="eye" class="me-1"></i>
                                                                        Voir
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('quizzes.edit', $quiz) }}">
                                                                        <i data-feather="edit" class="me-1"></i>
                                                                        Modifier
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-2">
                                        <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                                    </div>
                                    <h5 class="text-muted">Aucun quiz associé</h5>
                                    <p class="text-muted">Ce type de quiz n'a pas encore de quiz associé.</p>
                                    <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                                        <i data-feather="plus" class="me-1"></i>
                                        Créer un quiz
                                    </a>
                                </div>
                            @endif
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
                                    <span class="text-muted">ID du Type</span>
                                    <span class="fw-bolder">#{{ $typeQuiz->id }}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Date de création</span>
                                    <span class="fw-bolder">{{ $typeQuiz->created_at->format('d/m/Y') }}</span>
                                </div>
                                <small class="text-muted">{{ $typeQuiz->created_at->format('H:i') }}</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Dernière modification</span>
                                    <span class="fw-bolder">{{ $typeQuiz->updated_at->format('d/m/Y') }}</span>
                                </div>
                                <small class="text-muted">{{ $typeQuiz->updated_at->format('H:i') }}</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted">Quiz associés</span>
                                    <span class="badge rounded-pill bg-light-primary">
                                        {{ $typeQuiz->quizzes->count() }}
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
                                <a href="{{ route('type-quizzes.edit', $typeQuiz) }}" class="btn btn-warning">
                                    <i data-feather="edit" class="me-2"></i>
                                    Modifier le type
                                </a>
                                <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                                    <i data-feather="plus" class="me-2"></i>
                                    Ajouter un quiz
                                </a>
                                <a href="{{ route('type-quizzes.index') }}" class="btn btn-outline-secondary">
                                    <i data-feather="list" class="me-2"></i>
                                    Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
