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
                                <i data-feather="star" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Détails de l'avis client</h4>
                            <p class="text-white-50 mb-0">Informations complètes sur l'avis de "{{ $avisAcceuil->client }}"</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
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
                                            <label class="form-label fw-bold">ID de l'avis</label>
                                            <p class="mb-0">{{ $avisAcceuil->id }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Nom du client</label>
                                            <p class="mb-0">{{ $avisAcceuil->client }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Ordre d'affichage</label>
                                            <p class="mb-0">{{ $avisAcceuil->order }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Statut</label>
                                            <span class="badge {{ $avisAcceuil->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $avisAcceuil->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo du client -->
                    @if($avisAcceuil->image)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i data-feather="user" class="me-2"></i>
                                            Photo du client
                                        </h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="{{ asset('uploads/avis/' . $avisAcceuil->image) }}" 
                                             alt="Photo du client" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Avis en Anglais -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Témoignage en Anglais
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="bg-light p-3 rounded">
                                        {{ $avisAcceuil->avis_en }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Avis en Arabe -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Témoignage en Arabe
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="bg-light p-3 rounded">
                                        {{ $avisAcceuil->avis_ar }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                            <label class="form-label fw-bold">Créé le</label>
                                            <p class="mb-3">{{ $avisAcceuil->created_at->format('d/m/Y à H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Dernière modification</label>
                                            <p class="mb-3">{{ $avisAcceuil->updated_at->format('d/m/Y à H:i') }}</p>
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
                                <a href="{{ route('avis-acceuil.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Retour à la liste
                                </a>
                                <a href="{{ route('avis-acceuil.edit', $avisAcceuil) }}" class="btn btn-warning">
                                    <i data-feather="edit" class="me-2"></i>
                                    Modifier
                                </a>
                                <form action="{{ route('avis-acceuil.destroy', $avisAcceuil) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i data-feather="trash-2" class="me-2"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
