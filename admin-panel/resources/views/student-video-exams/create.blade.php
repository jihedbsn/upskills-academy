@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Nouvel Examen Vidéo</h1>
        <div class="page-actions">
            <a href="{{ route('student-video-exams.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i data-feather="video" class="me-2"></i>
                        Informations de l'examen vidéo
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student-video-exams.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Étudiant <span class="text-danger">*</span></label>
                                    <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un étudiant</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->variation_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="lien" class="form-label">Lien de la vidéo <span class="text-danger">*</span></label>
                            <input type="url" name="lien" id="lien" 
                                   class="form-control @error('lien') is-invalid @enderror" 
                                   value="{{ old('lien') }}"
                                   placeholder="https://www.youtube.com/watch?v=..." required>
                            <div class="form-text">URL complète de la vidéo (YouTube, Vimeo, etc.)</div>
                            @error('lien')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="video_description" class="form-label">Description de la vidéo <span class="text-danger">*</span></label>
                            <textarea name="video_description" id="video_description" rows="4" 
                                      class="form-control @error('video_description') is-invalid @enderror" 
                                      placeholder="Décrivez le contenu de l'examen vidéo..." required>{{ old('video_description') }}</textarea>
                            @error('video_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="is_valid" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="is_valid" id="is_valid" class="form-select @error('is_valid') is-invalid @enderror" required>
                                <option value="0" {{ old('is_valid', '0') == '0' ? 'selected' : '' }}>En attente</option>
                                <option value="1" {{ old('is_valid') == '1' ? 'selected' : '' }}>Validé</option>
                                <option value="-1" {{ old('is_valid') == '-1' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                            @error('is_valid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('student-video-exams.index') }}" class="btn btn-secondary">
                                <i data-feather="x" class="me-2"></i>
                                Annuler
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
