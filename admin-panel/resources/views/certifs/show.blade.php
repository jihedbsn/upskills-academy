@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="award" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Détails du Certificat</h4>
                                <p class="text-white-50 mb-0">Informations complètes du certificat</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('certifs.edit', $certif) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
                            <form action="{{ route('certifs.destroy', $certif) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce certificat ?')">
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i data-feather="award" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Nom du Certificat
                                </label>
                                <p class="form-control-plaintext">{{ $certif->nom }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Date de Création
                                </label>
                                <p class="form-control-plaintext">{{ $certif->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Dernière Modification
                                </label>
                                <p class="form-control-plaintext">{{ $certif->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i data-feather="hash" class="me-2" style="width: 16px; height: 16px;"></i>
                                    ID du Certificat
                                </label>
                                <p class="form-control-plaintext">{{ $certif->id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i data-feather="file" class="me-2" style="width: 16px; height: 16px;"></i>
                            Fichier du Certificat
                        </label>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-light-primary rounded me-3">
                                <i data-feather="file-text" class="text-primary" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-semibold">{{ basename($certif->file_url) }}</p>
                                <p class="mb-0 text-muted small">Fichier stocké dans le système</p>
                            </div>
                            <a href="{{ route('certifs.download', $certif) }}" class="btn btn-primary">
                                <i data-feather="download" class="me-2"></i>
                                Télécharger
                            </a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('certifs.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Retour à la Liste
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('certifs.edit', $certif) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
                            <a href="{{ route('certifs.download', $certif) }}" class="btn btn-primary">
                                <i data-feather="download" class="me-2"></i>
                                Télécharger
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
