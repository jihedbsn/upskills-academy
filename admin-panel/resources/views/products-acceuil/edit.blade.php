@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Modifier le produit d'accueil</h4>
                            <p class="text-white-50 mb-0">Modifiez les paramètres d'affichage du produit</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <h6 class="mb-1">Erreurs de validation</h6>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($productAcceuil->product)
                        <form action="{{ route('products-acceuil.update', $productAcceuil) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Section Informations du Produit -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i data-feather="package" class="me-2"></i>
                                                Informations du Produit
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @php
                                                        $variation = $productAcceuil->product->variations->first();
                                                    @endphp
                                                    <label class="form-label fw-bold">Produit</label>
                                                    <p class="mb-3">{{ $variation ? $variation->name : 'Produit sans variation' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Prix</label>
                                                    <p class="mb-3">${{ $productAcceuil->product->prix }}</p>
                                                </div>
                                            </div>
                                            @if($productAcceuil->product->image)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label fw-bold">Image</label>
                                                        <div class="mt-2">
                                                            <img src="{{ asset('uploads/products/images/' . $productAcceuil->product->image) }}" 
                                                                 alt="Image du produit" class="img-thumbnail" style="max-width: 200px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Options -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">
                                                <i data-feather="settings" class="me-2"></i>
                                                Options d'Affichage
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="order" class="form-label">Ordre d'affichage</label>
                                                        <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                                               id="order" name="order" value="{{ old('order', $productAcceuil->order) }}" 
                                                               min="0" placeholder="0">
                                                        @error('order')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                                   {{ old('is_active', $productAcceuil->is_active) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_active">
                                                                Actif
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('products-acceuil.index') }}" class="btn btn-secondary">
                                            <i data-feather="arrow-left" class="me-2"></i>
                                            Annuler
                                        </a>
                                        <button type="submit" class="btn btn-warning">
                                            <i data-feather="save" class="me-2"></i>
                                            Mettre à jour
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <h6 class="mb-1">Produit introuvable</h6>
                                    <p class="mb-0">Le produit associé à cette entrée a été supprimé.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('products-acceuil.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Retour à la liste
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.modern-alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
}

.form-control:focus {
    border-color: #7367f0;
    box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
}

.form-check-input:checked {
    background-color: #7367f0;
    border-color: #7367f0;
}

.img-thumbnail {
    border-radius: 8px;
}
</style>
@endsection
