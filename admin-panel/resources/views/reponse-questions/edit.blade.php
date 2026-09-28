@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier la Réponse</h3>
                    <div class="card-tools">
                        <a href="{{ route('reponse-questions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reponse-questions.update', $reponseQuestion) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="question_id">Question <span class="text-danger">*</span></label>
                                    <select name="question_id" id="question_id" class="form-control @error('question_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner une question</option>
                                        @foreach($questions as $question)
                                            <option value="{{ $question->id }}" {{ old('question_id', $reponseQuestion->question_id) == $question->id ? 'selected' : '' }}>
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
                                        <input type="checkbox" class="custom-control-input" id="is_correcte" name="is_correcte" value="1" {{ old('is_correcte', $reponseQuestion->is_correcte) ? 'checked' : '' }}>
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
                                           value="{{ old('titre_ar', $reponseQuestion->titre_ar) }}" required>
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
                                           value="{{ old('titre_en', $reponseQuestion->titre_en) }}" required>
                                    @error('titre_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('reponse-questions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
