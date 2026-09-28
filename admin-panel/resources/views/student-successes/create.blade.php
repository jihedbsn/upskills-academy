@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Nouveau Succès Étudiant</h4>
                            <p class="text-white-50 mb-0">Création d'un nouveau succès étudiant</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du Succès</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student-successes.store') }}">
                        @csrf

                        <!-- Étudiant -->
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Étudiant <span class="text-danger">*</span></label>
                            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id">
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

                        <!-- Produit -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Produit <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
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

                        <!-- Lien vidéo -->
                        <div class="mb-3">
                            <label for="lien_video" class="form-label">Lien Vidéo</label>
                            <input type="url" class="form-control @error('lien_video') is-invalid @enderror" 
                                   id="lien_video" name="lien_video" 
                                   value="{{ old('lien_video') }}" 
                                   placeholder="https://example.com/video">
                            @error('lien_video')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes admin -->
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Notes de l'administrateur</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                      id="admin_notes" name="admin_notes" rows="4" 
                                      placeholder="Notes ou commentaires sur ce succès...">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save"></i> Créer
                            </button>
                            <a href="{{ route('student-successes.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="x"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informations -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i data-feather="info" class="me-2"></i>
                        <strong>Note:</strong> Le succès sera créé avec le statut "En attente" par défaut.
                    </div>
                    <div class="alert alert-warning">
                        <i data-feather="alert-triangle" class="me-2"></i>
                        <strong>Attention:</strong> Assurez-vous de sélectionner le bon étudiant et produit.
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student-successes.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="list"></i> Voir tous les succès
                        </a>
                        <a href="{{ route('student-successes.by-product') }}" class="btn btn-outline-info">
                            <i data-feather="grid"></i> Vue par produit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
