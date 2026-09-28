@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gestion des Réponses aux Questions</h1>
        <div class="page-actions">
            <a href="{{ route('reponse-questions.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                Ajouter une Réponse
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="check-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Liste des Réponses</h4>
                            <p class="text-muted mb-0">Gérez toutes les réponses aux questions</p>
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

                    @if(session('error'))
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre (Arabe)</th>
                                    <th>Titre (Anglais)</th>
                                    <th>Question</th>
                                    <th>Quiz</th>
                                    <th>Correcte</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reponses as $reponse)
                                    <tr>
                                        <td>{{ $reponse->id }}</td>
                                        <td>{{ $reponse->titre_ar }}</td>
                                        <td>{{ $reponse->titre_en }}</td>
                                        <td>
                                            @if($reponse->question)
                                                <strong>{{ $reponse->question->name_ar }}</strong><br>
                                                <small class="text-muted">{{ $reponse->question->name_en }}</small>
                                            @else
                                                <span class="text-muted">Question supprimée</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reponse->question && $reponse->question->quiz)
                                                <strong>{{ $reponse->question->quiz->name_ar }}</strong><br>
                                                <small class="text-muted">{{ $reponse->question->quiz->name_en }}</small>
                                            @else
                                                <span class="text-muted">Quiz supprimé</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reponse->is_correcte)
                                                <span class="badge bg-success">Correcte</span>
                                            @else
                                                <span class="badge bg-secondary">Incorrecte</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('reponse-questions.show', $reponse) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <a href="{{ route('reponse-questions.edit', $reponse) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <form action="{{ route('reponse-questions.destroy', $reponse) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Supprimer" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')">
                                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune réponse trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $reponses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
