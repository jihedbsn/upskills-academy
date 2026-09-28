@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Gestion des Certificats</h1>
        <div class="page-actions">
            <a href="{{ route('certifs.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i>
                Nouveau Certificat
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
                                <i data-feather="award" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Liste des Certificats</h4>
                            <p class="text-white-50 mb-0">Gérez tous les certificats disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($certifs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                                                            <th>ID</th>
                                    <th>Nom du Certificat</th>
                                    <th>Fichier</th>
                                    <th>Statut</th>
                                    <th>Date de Création</th>
                                    <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certifs as $certif)
                                        <tr>
                                            <td>{{ $certif->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-light-primary rounded me-2">
                                                        <i data-feather="file-text" class="text-primary" style="width: 16px; height: 16px;"></i>
                                                    </div>
                                                    <span class="fw-semibold">{{ $certif->nom }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                                        @if(file_exists(public_path($certif->file_url)))
                            <a href="{{ route('certifs.download', $certif) }}" class="btn btn-sm btn-outline-primary" onclick="console.log('Téléchargement du certificat ID: {{ $certif->id }}');">
                                <i data-feather="download" class="me-1"></i>
                                Télécharger
                            </a>
                        @else
                            <span class="text-muted">Fichier non trouvé</span>
                        @endif
                                            </td>
                                            <td>
                                                @if($certif->is_active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>{{ $certif->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('certifs.show', $certif) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                    <a href="{{ route('certifs.edit', $certif) }}" class="btn btn-sm btn-outline-warning" title="Éditer le template">
                                                        <i data-feather="edit-3"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-success" title="Générer un certificat" onclick="generateCertificate({{ $certif->id }})">
                                                        <i data-feather="award"></i>
                                                    </button>
                                                    <form action="{{ route('certifs.destroy', $certif) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce certificat ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                            <i data-feather="trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $certifs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl bg-light-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i data-feather="award" class="text-secondary" style="width: 48px; height: 48px;"></i>
                            </div>
                            <h4 class="text-secondary mb-2">Aucun certificat trouvé</h4>
                            <p class="text-muted mb-4">Commencez par créer votre premier certificat</p>
                            <a href="{{ route('certifs.create') }}" class="btn btn-primary">
                                <i data-feather="plus" class="me-2"></i>
                                Créer un Certificat
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
