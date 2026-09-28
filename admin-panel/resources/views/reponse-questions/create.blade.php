@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Ajouter une Réponse</h1>
        <div class="page-actions">
            <a href="{{ route('reponse-questions.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Nouvelle Réponse</h4>
                            <p class="text-muted mb-0">Ajoutez une nouvelle réponse à une question</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
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

                    <form action="{{ route('reponse-questions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="question_id">Question <span class="text-danger">*</span></label>
                                    <select name="question_id" id="question_id" class="form-control @error('question_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner une question</option>
                                        @foreach($questions as $question)
                                            <option value="{{ $question->id }}" {{ old('question_id') == $question->id ? 'selected' : '' }}>
                                                {{ $question->name_ar }} - {{ $question->name_en }}
                                                @if($question->quiz)
                                                    (Quiz: {{ $question->quiz->name_ar }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('question_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_correcte">Réponse Correcte</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_correcte" name="is_correcte" value="1" {{ old('is_correcte') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_correcte">Cette réponse est correcte</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titre_ar">Titre (Arabe) <span class="text-danger">*</span></label>
                                    <input type="text" name="titre_ar" id="titre_ar" 
                                           class="form-control @error('titre_ar') is-invalid @enderror" 
                                           value="{{ old('titre_ar') }}" required>
                                    @error('titre_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titre_en">Titre (Anglais) <span class="text-danger">*</span></label>
                                    <input type="text" name="titre_en" id="titre_en" 
                                           class="form-control @error('titre_en') is-invalid @enderror" 
                                           value="{{ old('titre_en') }}" required>
                                    @error('titre_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('reponse-questions.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
