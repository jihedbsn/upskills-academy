@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Types de Quiz</h1>
        <div class="page-actions">
            <a href="{{ route('type-quizzes.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                Nouveau Type
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="tag" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Liste des Types de Quiz</h4>
                            <p class="text-white-50 mb-0">Gérez tous les types de quiz</p>
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

                    @if($types->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <th>Nombre de Quiz</th>
                                        <th>Date de création</th>
                                        <th class="actions-column">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($types as $type)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#{{ $type->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i data-feather="tag" class="text-white" style="width: 14px; height: 14px;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium text-dark">{{ $type->titre }}</div>
                                                        <small class="text-muted">Type de quiz</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light-primary">
                                                    {{ $type->quizzes_count }} quiz
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="calendar" class="me-2 text-muted" style="width: 14px; height: 14px;"></i>
                                                    <span>{{ $type->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </td>
                                            <td class="actions-column">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('type-quizzes.show', $type) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                    </a>
                                                    <a href="{{ route('type-quizzes.edit', $type) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
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
                            {{ $types->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-2">
                                <i data-feather="tag" class="text-muted" style="width: 64px; height: 64px;"></i>
                            </div>
                            <h4 class="text-muted">Aucun type de quiz trouvé</h4>
                            <p class="text-muted">Commencez par créer votre premier type de quiz.</p>
                            <a href="{{ route('type-quizzes.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-1"></i>
                                Créer un type
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
