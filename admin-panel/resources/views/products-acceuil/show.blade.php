@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="home" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Détails du produit d'accueil</h4>
                            <p class="text-white-50 mb-0">Informations complètes sur le produit affiché sur la page d'accueil</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($productAcceuil->product)
                        <!-- Informations générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="info" class="me-2"></i>
                                            Informations générales
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label fw-bold">ID de l'entrée</label>
                                                <p class="mb-0">{{ $productAcceuil->id }}</p>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label fw-bold">Ordre d'affichage</label>
                                                <p class="mb-0">{{ $productAcceuil->order }}</p>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label fw-bold">Statut</label>
                                                <span class="badge {{ $productAcceuil->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $productAcceuil->is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label fw-bold">Date d'ajout</label>
                                                <p class="mb-0">{{ $productAcceuil->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du produit -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
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
                                                <label class="form-label fw-bold">Nom du produit</label>
                                                <p class="mb-3">{{ $variation ? $variation->name : 'Produit sans variation' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Langue</label>
                                                <p class="mb-3">{{ $variation ? ($variation->langue ?? 'Non définie') : 'Non définie' }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Prix</label>
                                                <p class="mb-3">${{ $productAcceuil->product->prix }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Statut du produit</label>
                                                <span class="badge {{ $productAcceuil->product->is_active ?? false ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $productAcceuil->product->is_active ?? false ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image du produit -->
                        @if($productAcceuil->product->image)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">
                                                <i data-feather="image" class="me-2"></i>
                                                Image du produit
                                            </h5>
                                        </div>
                                        <div class="card-body text-center">
                                            <img src="{{ asset('uploads/products/images/' . $productAcceuil->product->image) }}" 
                                                 alt="Image du produit" class="img-fluid rounded" style="max-height: 300px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Description du produit -->
                        @php
                            $variation = $productAcceuil->product->variations->first();
                        @endphp
                        @if($variation && ($variation->short_description || $variation->ad))
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-white">
                                            <h5 class="mb-0">
                                                <i data-feather="file-text" class="me-2"></i>
                                                Description du produit
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($variation->short_description)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Description courte</label>
                                                    <div class="bg-light p-3 rounded">
                                                        {{ $variation->short_description }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($variation->ad)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Description détaillée</label>
                                                    <div class="bg-light p-3 rounded">
                                                        {{ $variation->ad }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Métadonnées -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="clock" class="me-2"></i>
                                            Métadonnées
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Ajouté le</label>
                                                <p class="mb-3">{{ $productAcceuil->created_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Dernière modification</label>
                                                <p class="mb-3">{{ $productAcceuil->updated_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('products-acceuil.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Retour à la liste
                                    </a>
                                    <a href="{{ route('products-acceuil.edit', $productAcceuil) }}" class="btn btn-warning">
                                        <i data-feather="edit" class="me-2"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('products-acceuil.destroy', $productAcceuil) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir retirer ce produit de la page d\'accueil ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i data-feather="trash-2" class="me-2"></i>
                                            Retirer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
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

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
}
</style>
@endsection
