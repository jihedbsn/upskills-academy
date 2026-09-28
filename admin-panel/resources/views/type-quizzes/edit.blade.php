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
                            <i data-feather="edit" class="me-2"></i>
                            Modifier le Type de Quiz
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('type-quizzes.index') }}">Types de Quiz</a></li>
                                <li class="breadcrumb-item active">Modifier</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Modifier le Type de Quiz</h4>
                            <p class="text-muted mb-0">Modifiez les informations du type de quiz</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <strong>Erreurs de validation</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('type-quizzes.update', $typeQuiz) }}" id="typeQuizForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card border-info">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="settings" class="me-2"></i>
                                            <h6 class="mb-0">Informations du Type</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Titre du Type *
                                            </label>
                                            <input type="text" name="titre" class="form-control" 
                                                   value="{{ old('titre', $typeQuiz->titre) }}" required 
                                                   placeholder="Ex: Quiz de culture générale, Quiz de mathématiques...">
                                            <div class="form-text">Donnez un nom descriptif à ce type de quiz</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations du Type -->
                                <div class="card border-secondary mt-3">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="info" class="me-2"></i>
                                            <h6 class="mb-0">Informations du Type</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">ID du Type</label>
                                                    <input type="text" class="form-control" value="{{ $typeQuiz->id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Date de création</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $typeQuiz->created_at->format('d/m/Y H:i') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('type-quizzes.index') }}" class="btn btn-outline-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Retour
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('type-quizzes.show', $typeQuiz) }}" class="btn btn-outline-info">
                                            <i data-feather="eye" class="me-2"></i>
                                            Voir
                                        </a>
                                        <button type="submit" class="btn btn-warning">
                                            <i data-feather="save" class="me-2"></i>
                                            Mettre à jour
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.getElementById('typeQuizForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });

    // Validation en temps réel
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.hasAttribute('required') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush
