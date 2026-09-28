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
                                <i data-feather="eye" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Détails de la page</h4>
                            <p class="text-white-50 mb-0">Informations complètes sur la page "{{ $page->titre_en }}"</p>
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
                                            <label class="form-label fw-bold">ID de la page</label>
                                            <div class="form-control-plaintext">
                                                <span class="badge bg-primary">{{ $page->id }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Slug (URL)</label>
                                            <div class="form-control-plaintext">
                                                <code class="text-primary">{{ $page->slug }}</code>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Statut</label>
                                            <div class="form-control-plaintext">
                                                @if($page->is_active)
                                                    <span class="badge bg-success">
                                                        <i data-feather="check-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Actif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i data-feather="x-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                        Inactif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">URL complète</label>
                                            <div class="form-control-plaintext">
                                                <a href="/pages/{{ $page->slug }}" target="_blank" class="text-primary">
                                                    /pages/{{ $page->slug }}
                                                    <i data-feather="external-link" style="width: 14px; height: 14px;"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu Arabe -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Contenu Arabe
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Titre Arabe</label>
                                            <div class="form-control-plaintext">
                                                <h6 class="mb-0">{{ $page->titre_ar }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Title Arabe</label>
                                            <div class="form-control-plaintext">
                                                <span>{{ $page->meta_title_ar }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Description Arabe</label>
                                            <div class="form-control-plaintext">
                                                <div class="bg-light p-3 rounded" style="direction: rtl; text-align: right;">
                                                    {!! $page->description_ar !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu Anglais -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Contenu Anglais
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Titre Anglais</label>
                                            <div class="form-control-plaintext">
                                                <h6 class="mb-0">{{ $page->titre_en }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Meta Title Anglais</label>
                                            <div class="form-control-plaintext">
                                                <span>{{ $page->meta_title_en }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-bold">Description Anglais</label>
                                            <div class="form-control-plaintext">
                                                <div class="bg-light p-3 rounded" style="direction: ltr; text-align: left;">
                                                    {!! $page->description_en !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations temporelles -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="clock" class="me-2"></i>
                                        Informations temporelles
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Créée le</label>
                                            <div class="form-control-plaintext">
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="calendar" class="me-2 text-muted" style="width: 16px; height: 16px;"></i>
                                                    <span>{{ $page->created_at->format('d/m/Y à H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Modifiée le</label>
                                            <div class="form-control-plaintext">
                                                <div class="d-flex align-items-center">
                                                    <i data-feather="edit" class="me-2 text-muted" style="width: 16px; height: 16px;"></i>
                                                    <span>{{ $page->updated_at->format('d/m/Y à H:i') }}</span>
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
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Retour à la liste
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pages.edit', $page) }}" class="btn btn-warning">
                                        <i data-feather="edit" class="me-2"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('pages.destroy', $page) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette page ?')">
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
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.form-control-plaintext {
    padding: 0.375rem 0;
    margin-bottom: 0;
    color: #6e6b7b;
    background-color: transparent;
    border: solid transparent;
    border-width: 1px 0;
}

.form-label {
    color: #5e5873;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.bg-light {
    background-color: #f8f9fa !important;
}

code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}
</style>
@endsection
